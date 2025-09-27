<?php

/**
 * Copyright (c) Christoph M. Becker
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
 * The (partial) response to a CMSimple_XH page request
 *
 * This class encapsulates access to some of CMSimple_XH globals, e.g. `$o`
 * and `$title`, as well as sending of cookies, redirecting to another page,
 * and generally calls to `header()`, and `http_response_code()`.
 *
 * A `Response` does not cause any side-effects until it is invoked.
 */
final class Response
{
    /** @var string */
    private $output = "";

    /** @var int */
    private $status = 200;

    /** @var string|null */
    private $location = null;

    /** @var string|null */
    private $title = null;

    /** @var string|null */
    private $description = null;

    /** @var array{string,string,int}|null */
    private $cookie = null;

    /** @var string|null */
    private $contentType = null;

    /** @var string|null */
    private $attachment = null;

    /** @var int|null */
    private $length = null;

    /** @var ?string */
    private $hjs = null;

    /** @var ?string */
    private $bjs = null;

    /** @var ?list<string> */
    private $canonicalParams = null;

    public static function create(string $output = ""): self
    {
        $that = new self();
        $that->output = $output;
        return $that;
    }

    public static function error(int $status, string $output = ""): self
    {
        assert($status >= 400);
        $that = new self();
        $that->status = $status;
        $that->output = $output;
        return $that;
    }

    public static function redirect(string $location): self
    {
        $that = new self();
        $that->location = $location;
        return $that;
    }

    private function __construct()
    {
    }

    /** @return string|never */
    public function __invoke()
    {
        global $title, $hjs, $bjs, $tx, $CanonicalLinkInc;

        if ($this->status !== 200) {
            $this->purgeOutputBuffers();
            http_response_code($this->status);
            echo $this->output();
            exit;
        }
        if ($this->cookie() !== null) {
            [$name, $value, $expires] = $this->cookie();
            setcookie($name, $value, $expires, CMSIMPLE_ROOT);
        }
        if ($this->location() !== null) {
            $this->purgeOutputBuffers();
            header("Location: " . $this->location(), true, 303);
            exit;
        }
        if ($this->attachment() !== null) {
            header("Content-Disposition: attachment; filename=\"" . $this->attachment() . "\"");
        }
        if ($this->length() !== null) {
            header("Content-Length: " . $this->length());
        }
        if ($this->contentType() !== null) {
            $this->purgeOutputBuffers();
            header("Content-Type: " . $this->contentType());
            echo $this->output();
            exit;
        }
        if ($this->title() !== null) {
            $title = $this->title();
        }
        if ($this->description !== null) {
            $tx["meta"]["description"] = $this->description;
        }
        if ($this->hjs !== null) {
            $hjs .= $this->hjs;
        }
        if ($this->bjs !== null) {
            $bjs .= $this->bjs;
        }
        if ($this->canonicalParams !== null && is_array($CanonicalLinkInc)) {
            array_push($CanonicalLinkInc, ...$this->canonicalParams);
        }
        return $this->output();
    }

    public function withTitle(string $title): self
    {
        $that = clone $this;
        $that->title = $title;
        return $that;
    }

    /**
     * Changes $tx[meta][description]
     *
     * Must only be called, if there is no chance that the core language
     * will be saved during the request.
     *
     * @since 1.8
     */
    public function withDescription(string $description): self
    {
        $that = clone $this;
        $that->description = $description;
        return $that;
    }

    /**
     * Set a cookie for the whole CMSimple_XH installation
     *
     * @link https://github.com/cmb69/plib_xh/issues/1
     */
    public function withCookie(string $name, string $value, int $expires): self
    {
        $that = clone $this;
        $that->cookie = [$name, $value, $expires];
        return $that;
    }

    public function withContentType(string $contentType): self
    {
        $that = clone $this;
        $that->contentType = $contentType;
        return $that;
    }

    public function withAttachment(string $attachment): self
    {
        $that = clone $this;
        $that->attachment = $attachment;
        return $that;
    }

    public function withLength(int $length): self
    {
        $that = clone $this;
        $that->length = $length;
        return $that;
    }

    /**
     * Appends to $hjs
     *
     * This does not work from templates, and therefore is better avoided,
     * but sometimes it is just necessary.
     *
     * @since 1.3
     */
    public function withHjs(string $hjs): self
    {
        $that = clone $this;
        $that->hjs = $hjs;
        return $that;
    }

    /** @since 1.2 */
    public function withBjs(string $bjs): self
    {
        $that = clone $this;
        $that->bjs = $bjs;
        return $that;
    }

    /**
     * Adds the parameters to the canonical link of the page
     *
     * A simple wrapper over the `$CanonicalLinkInc` API that is available
     * as of CMSimple_XH 1.8, and may need to be explicitly enabled in the
     * configuration.
     *
     * @param list<string> $params
     * @since 1.8
     */
    public function withCanonicalParams(array $params): self
    {
        $that = clone $this;
        $that->canonicalParams = $params;
        return $that;
    }

    public function output(): string
    {
        return $this->output;
    }

    public function location(): ?string
    {
        return $this->location;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    /** @since 1.8 */
    public function description(): ?string
    {
        return $this->description;
    }

    public function status(): int
    {
        return $this->status;
    }

    /** @return array{string,string,int}|null */
    public function cookie(): ?array
    {
        return $this->cookie;
    }

    public function contentType(): ?string
    {
        return $this->contentType;
    }

    public function attachment(): ?string
    {
        return $this->attachment;
    }

    public function length(): ?int
    {
        return $this->length;
    }

    /** @since 1.3 */
    public function hjs(): ?string
    {
        return $this->hjs;
    }

    /** @since 1.2 */
    public function bjs(): ?string
    {
        return $this->bjs;
    }

    /**
     * @return ?list<string>
     * @since 1.8
     */
    public function canonicalParams(): ?array
    {
        return $this->canonicalParams;
    }

    /** @return void */
    private function purgeOutputBuffers()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }
}
