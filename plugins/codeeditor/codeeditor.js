/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Codeeditor_XH.
 *
 * Codeeditor_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Codeeditor_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codeeditor_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

/* globals CodeMirror */

/**
 * The namespace.
 */
var codeeditor = {};

/**
 * The codemirror instances of the current document.
 */
codeeditor.instances = [];

/**
 * The currently active codemirror.
 */
codeeditor.current = null;


codeeditor.text = JSON.parse(document.getElementsByTagName("meta")["xh-codeeditor"].content);

/**
 * Returns all `textarea' elements with a certain class.
 *
 * @returns {Array}
 */
codeeditor.getTextareasByClass = function(name) {
    var textareas = document.getElementsByTagName('textarea'),
        pattern = new RegExp('(^|\\s)' + name + '(\\s|$)'),
        result = [], length, i;

    for (i = 0, length = textareas.length; i < length; i++) {
        let textarea = textareas[i];
        if (pattern.test(textarea.className)) {
            result.push(textarea);
        }
    }
    return result;
};

/**
 * Returns an id, which is not used in the document already.
 *
 * @returns {String}
 */
codeeditor.uniqueId = function() {
    var prefix = 'codeeditor', i = 0;

    while (document.getElementById(prefix + i) !== null) {
        i++;
    }
    return prefix + i;
};

/**
 * Returns whether the `form' element has an element for submitting.
 *
 * @param   {HTMLFormElement} form
 * @returns {Boolean}
 */
codeeditor.hasSubmit = function(form) {
    var elements, count, i, element;

    elements = form.elements;
    for (i = 0, count = elements.length; i < count; i++) {
        element = elements[i];
        if ("type" in element && element.type == "submit") {
            return true;
        }
    }
    return false;
};

/**
 * Asks to stay on the page, when modifications were made.
 *
 * @param   {Event} e
 * @returns {any}
 */
codeeditor.beforeUnload = function(e) {
    var i, count;

    for (i = 0, count = codeeditor.instances.length; i < count; i++) {
        if (!codeeditor.instances[i].isClean()) {
            e.returnValue = codeeditor.text.confirmLeave;
            return;
        }
    }
    return undefined;
};

/**
 * Inserts an URL to the current codemirror.
 *
 * To be called from the filebrowser.
 *
 * @param   {String} url
 * @returns {undefined}
 */
codeeditor.insertURI = function(url) {
    var cm = codeeditor.current;

    cm.replaceSelection(url);
    cm.focus();
};

/**
 * Makes all `textarea' elements with certain classes to CodeMirrors.
 *
 * @param   {Array} classes
 * @param   {Object} config
 * @returns {undefined}
 */
codeeditor.instantiateByClasses = function(classes, config, mayPreview) {
    var classCount, i, textareas, textareaCount, j, textarea;

    for (i = 0, classCount = classes.length; i < classCount; i++) {
        textareas = codeeditor.getTextareasByClass(classes[i]);
        for (j = 0, textareaCount = textareas.length; j < textareaCount; j++) {
            textarea = textareas[j];
            if (!textarea.id) {
                textarea.id = codeeditor.uniqueId();
            }
            codeeditor.instantiate(textarea.id, config, mayPreview);
        }
    }
};

/**
 * Makes a `textarea' element to a CodeMirror.
 *
 * @param   {String} id
 * @param   {Object} config
 * @returns {undefined}
 */
codeeditor.instantiate = function(id, config, mayPreview) {
    var textarea = /** @type {HTMLTextAreaElement} */ (document.getElementById(id));
    var height = textarea.offsetHeight;
    var cm = CodeMirror.fromTextArea(textarea, config);

    cm.cmbMayPreview = mayPreview || false;
    cm.setSize(null, height);
    cm.on("focus", function(editor) {
        codeeditor.current = editor;
    });
    cm.refresh();
    codeeditor.instances.push(cm);
    CodeMirror.on(window, "beforeunload", codeeditor.beforeUnload);
    CodeMirror.on(textarea.form, "submit", function() {
        CodeMirror.off(window, "beforeunload", codeeditor.beforeUnload);
    });
};

/**
 * Save handler for keyboard shortcuts.
 *
 * @param   {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.save = function(cm) {
    var form, submit;

    // HACK: We can't call form.submit() directly, because that might skip the
    // defined onsubmit handlers, and we don't know which form element to
    // trigger, so we temporarily create a new submit input element and click
    // this.
    form = cm.getTextArea().form;
    submit = document.createElement("input");
    submit.setAttribute("type", "submit");
    form.appendChild(submit);
    submit.click();
    form.removeChild(submit);
};

/**
 * Toggles full screen mode.
 *
 * @param   {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.toggleFullscreen = function(cm) {
    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
};

/**
 * Toggles the preview.
 *
 * @param   {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.togglePreview = function(cm) {
    var wrapper = cm.getWrapperElement(), preview;
    if (!cm.cmbMayPreview) {
        return;
    }
    if (wrapper.previousSibling && wrapper.previousSibling.className.indexOf("codeeditor_preview") >= 0) {
        preview = wrapper.previousSibling;
        preview.parentNode.removeChild(preview);
        cm.setOption("onBlur", null);
        if (cm.cmbFullscreen) {
            delete cm.cmbFullscreen;
            CodeMirror.commands.toggleFullscreen(cm);
        }
    } else {
        if (cm.getScrollerElement().className.indexOf("codeeditor_fullscreen") >= 0) {
            CodeMirror.commands.toggleFullscreen(cm);
            cm.cmbFullscreen = true;
        }
        preview = document.createElement("div");
        preview.className = "codeeditor_preview";
        preview.innerHTML = cm.getValue();
        wrapper.parentNode.insertBefore(preview, wrapper);
        cm.setOption("onBlur", function() {
            CodeMirror.commands.togglePreview(cm);
        });
    }
};

/**
 * Toggles the folding of the code.
 *
 * @param   {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.toggleFolding = function(cm) {
    cm.foldCode(cm.getCursor());
};

/**
 * Toggles the line wrapping.
 *
 * @param   {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.toogleLineWrapping = function(cm) {
    cm.setOption('lineWrapping', !cm.getOption('lineWrapping'));
};

/**
 * Opens the filebrowser for the image folder.
 *
 * @param   {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.browseImages = function(cm) {
    if (typeof codeeditor.filebrowser == 'function') {
        codeeditor.filebrowser('images');
    }
};

/**
 * Opens the filebrowser for the downloads folder.
 *
 * @param   {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.browseDownloads = function(cm) {
    if (typeof codeeditor.filebrowser == 'function') {
        codeeditor.filebrowser('downloads');
    }
};

/**
 * Opens the filebrowser for the media folder.
 *
 * @param   {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.browseMedia = function(cm) {
    if (typeof codeeditor.filebrowser == 'function') {
        codeeditor.filebrowser('media');
    }
};

/**
 * Opens the filebrowser for the userfiles folder.
 *
 * @param   {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.browseUserfiles = function(cm) {
    if (typeof codeeditor.filebrowser == 'function') {
        codeeditor.filebrowser('userfiles');
    }
};
