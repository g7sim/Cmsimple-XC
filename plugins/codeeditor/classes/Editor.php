<?php

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

namespace Codeeditor;

use Plib\Request;
use Plib\Response;
use Plib\View;

class Editor
{
    /** @var string */
    private $pluginsFolder;

    /** @var string */
    private $theme;

    /** @var string */
    private $filebrowser;

    /** @var View */
    private $view;

    public function __construct(
        string $pluginsFolder,
        string $theme,
        string $filebrowser,
        View $view
    ) {
        $this->pluginsFolder = $pluginsFolder;
        $this->theme = $theme;
        $this->filebrowser = $filebrowser;
        $this->view = $view;
    }

    /**
     * @param list<string> $classes
     * @param string|false $config
     */
    public function init(
        Request $request,
        array $classes = [],
        $config = false,
        string $mode = 'php',
        bool $mayPreview = true
    ): Response {
        $response = $this->doInclude($request);
        if (empty($classes)) {
            $classes = array('xh-editor');
        }
        $classes = json_encode($classes);
        $config = $this->config($mode, (string) $config);
        $mayPreview = json_encode($mayPreview);
        return $response->withBjs(<<<EOS
<script>
CodeMirror.on(window, "load", function() {
    codeeditor.instantiateByClasses($classes, $config, $mayPreview);
})
</script>

EOS
        );
    }

    public function doInclude(Request $request): Response
    {
        $dir = $this->pluginsFolder . 'codeeditor/';
        $stylesheets = [$dir . "codemirror/codemirror-combined.css"];
        $fn = $dir . 'codemirror/theme/' . $this->theme . '.css';
        if (file_exists($fn)) {
            $stylesheets[] = $fn;
        }
        $js = $dir . "codeeditor.min.js";
        if (!file_exists($js)) {
            $js = $dir . "codeeditor.js";
        }
        return Response::create()->withHjs($this->view->render("editor", [
            "stylesheets" => $stylesheets,
            "codemirror" => $dir . "codemirror/codemirror-compressed.js",
            "codeeditor" => $request->url()->path($js)->with("v", CODEEDITOR_VERSION)->relative(),
            "text" => ["confirmLeave" => $this->view->plain("confirm_leave")],
            "filebrowser" => str_ireplace("</script", "<\\/script", $this->filebrowser($request)),
        ]));
    }

    public function replace(string $elementId, string $config = ''): string
    {
        $config = $this->config('php', $config);
        return "codeeditor.instantiate('$elementId', $config, true);";
    }

    private function config(string $mode, string $config): string
    {
        global $e;

        $config = trim($config);
        if (empty($config) || $config[0] != '{') {
            $std = in_array($config, array('full', 'medium', 'minimal', 'sidebar', ''));
            $fn = $std
                ? $this->pluginsFolder . 'codeeditor/inits/init.json'
                : $config;
            $config = ($config = file_get_contents($fn)) !== false ? $config : '{}';
        }
        $parsedConfig = json_decode($config, true);
        if (!is_array($parsedConfig)) {
            $e .= "<li><b>" . $this->view->text("error_json") . "</b><br>"
                . (isset($fn) ? $this->view->esc($fn) : $this->view->esc($config))
                . "</li>";
            return "{}";
        }
        $config = $parsedConfig;
        if (!isset($config['mode']) || $config['mode'] == '%MODE%') {
            $config['mode'] = $mode;
        }
        if (!isset($config['theme']) || $config['theme'] == '%THEME%') {
            $config['theme'] = $this->theme;
        }
        // We set the undocumented leaveSubmitMehtodAlone option; otherwise
        // multiple editors on the same form might trigger form submission
        // multiple times.
        $config['leaveSubmitMethodAlone'] = true;
        $config = (string) json_encode($config);
        return $config;
    }

    private function filebrowser(Request $request): string
    {
        // no filebrowser, if editor is called from front-end
        if (!$request->admin()) {
            return '';
        }
        $script = '';
        if ($this->filebrowser !== "") {
            $connector = $this->pluginsFolder . $this->filebrowser . '/connectors/codeeditor/codeeditor.php';
            if (is_readable($connector)) {
                include_once $connector;
                $init = $this->filebrowser . '_codeeditor_init';
                if (is_callable($init)) {
                    $script = $init();
                }
            }
        } else {
            $_SESSION['codeeditor_fb_callback'] = 'wrFilebrowser';
            // we need to request the base, due to
            // <https://cmsimpleforum.com/viewtopic.php?t=21543&p=91705#p91705>
            $url = $request->url()->path(CMSIMPLE_BASE)->with("filebrowser", "editorbrowser")
                ->with("editor", "codeeditor")->with("prefix", CMSIMPLE_BASE)
                ->with("type", "")->relative();
            $script = <<<EOS
codeeditor.filebrowser = function(type) {
    window.open("$url=" + type, "codeeditor_filebrowser",
            "toolbar=no,location=no,status=no,menubar=no," +
            "scrollbars=yes,resizable=yes,width=640,height=480");
}
EOS;
        }
        return $script;
    }

    /** @return list<string> */
    public function getThemes(): array
    {
        $themes = array('', 'default');
        $foldername = $this->pluginsFolder . 'codeeditor/codemirror/theme';
        if ($dir = opendir($foldername)) {
            while (($entry = readdir($dir)) !== false) {
                if (pathinfo($entry, PATHINFO_EXTENSION) == 'css') {
                    $themes[] = basename($entry, '.css');
                }
            }
        }
        return $themes;
    }
}
