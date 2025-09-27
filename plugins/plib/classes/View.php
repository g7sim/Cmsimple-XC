<?php

/**
 * Copyright (C) Christoph M. Becker
 *
 * This file is part of Plib_XH.
 *
 * Plib_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Plib_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Plib_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Plib;

/**
 * Rendering of HTML and plain text output
 *
 * Generally, `View` encapsulates `$plugin_tx` of the plugin.
 * In simple cases, it can be used to compose a message (`XH_message()`).
 * In more complex cases, it it used to render a view template, to which
 * arbitrary values can be passed.
 *
 * @final
 */
class View
{
    /** @var string */
    private $templateFolder;

    /** @var array<string,string> */
    private $text;

    /** @param array<string,string> $text */
    public function __construct(string $templateFolder, array $text)
    {
        $this->templateFolder = $templateFolder;
        $this->text = $text;
    }

    /** @param scalar $args */
    public function message(string $type, string $key, ...$args): string
    {
        return XH_message($type, $this->text[$key], ...$args) . "\n";
    }

    /**
     * @param scalar $args
     * @since 1.1
     */
    public function pmessage(string $type, string $key, int $count, ...$args): string
    {
        $suffix = $count === 0 ? "_0" : XH_numberSuffix($count);
        return XH_message($type, $this->text[$key . $suffix], $count, ...$args) . "\n";
    }

    /** @param scalar $args */
    public function text(string $key, ...$args): string
    {
        return $this->esc(sprintf($this->text[$key], ...$args));
    }

    /**
     * @param scalar $args
     * @since 1.1
     */
    public function plural(string $key, int $count, ...$args): string
    {
        $suffix = $count === 0 ? "_0" : XH_numberSuffix($count);
        return $this->esc(sprintf($this->text[$key . $suffix], $count, ...$args));
    }

    /** @param scalar $args */
    public function plain(string $key, ...$args): string
    {
        return sprintf($this->text[$key], ...$args);
    }

    /** @since 1.10 */
    public function date(string $key, int $timestamp): string
    {
        return $this->esc(date($this->text[$key], $timestamp));
    }

    /**
     * Renders a boolean selected attribute
     *
     * The attribute is set if `$current` is (in) `$selected`.
     *
     * @param string|list<string> $selected
     * @since 1.10
     */
    public function selected(string $current, $selected): string
    {
        $is = is_string($selected) ? $current === $selected : in_array($current, $selected, true);
        return $is ? "selected" : "";
    }

    /**
     * Renders a boolean checked attribute
     *
     * The attribute is set if `$current` is `true` or is (in) `$checked`.
     *
     * @param string|bool $current
     * @param string|list<string> $checked
     * @since 1.10
     */
    public function checked($current, $checked = []): string
    {
        $is = is_bool($current)
            ? $current
            : (is_string($checked) ? $current === $checked : in_array($current, $checked, true));
        return $is ? "checked" : "";
    }

    /**
     * @param mixed $value
     * @since 1.1
     */
    public function json($value): string
    {
        return (string) json_encode($value, JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /** @param array<string,mixed> $_data */
    public function render(string $_template, array $_data): string
    {
        extract($_data);
        ob_start();
        include $this->templateFolder . $_template . ".php";
        return (string) ob_get_clean();
    }

    /**
     * @param scalar $string
     * @since 1.8 parameter has been widened to scalar
     */
    public function esc($string): string
    {
        return XH_hsc((string) $string);
    }

    public function raw(string $string): string
    {
        return $string;
    }
}
