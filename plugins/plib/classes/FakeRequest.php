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
 * A Request fake for automated testing
 *
 * @package Fakes
 * @since 1.1
 */
final class FakeRequest extends Request // @phpstan-ignore class.extendsFinalByPhpDoc
{
    /** @var array{url:Url,header?:array<string,string>,get:array<string,mixed>,cookie?:array<string,mixed>,post?:array<string,mixed>,files?:array<string,UploadedFile>,time?:int,serverName?:string,remoteAddr?:string,username?:string,admin?:bool,s?:int,selected:string,language?:string,edit?:bool} */
    private $opts;

    /** @param array{header?:array<string,string>,url?:string,cookie?:array<string,mixed>,post?:array<string,mixed>,files?:array<string,UploadedFile>,time?:int,serverName?:string,remoteAddr?:string,username?:string,admin?:bool,s?:int,language?:string,edit?:bool} $opts */
    public function __construct(array $opts = [])
    {
        $url = $opts["url"] ?? "http://example.com/";
        $parts = explode("?", $url, 2);
        if (count($parts) === 1) {
            $opts["url"] = new Url($parts[0], "", []);
            $opts["get"] = [];
            $opts["selected"] = "";
        } else {
            $query = explode("&", $parts[1], 2);
            $su = $query[0];
            if (count($query) === 2) {
                $rest = $this->parseQuery($query[1]);
                $opts["url"] = new Url($parts[0], $su, $rest);
                $opts["get"] = $rest;
            } else {
                $opts["url"] = new Url($parts[0], $su, []);
                $opts["get"] = [];
            }
            $opts["selected"] = $su;
        }
        $this->opts = $opts;
    }

    /** @return array<string,string|array<string>> */
    private function parseQuery(string $query): array
    {
        parse_str($query, $result);
        $this->assertStringKeys($result);
        return $result;
    }

    /**
     * @param array<int|string,array<mixed>|string> $array
     * @phpstan-assert array<string,string|array<string>> $array
     */
    private function assertStringKeys(array $array): void
    {
        foreach ($array as $key => $value) {
            assert(is_string($key));
        }
    }

    public function url(): Url
    {
        return $this->opts["url"];
    }

    public function header(string $key): ?string
    {
        if (!isset($this->opts["header"][$key])) {
            return null;
        }
        return trim($this->opts["header"][$key]);
    }

    public function get(string $key): ?string
    {
        if (!isset($this->opts["get"][$key]) || !is_string($this->opts["get"][$key])) {
            return null;
        }
        return trim($this->opts["get"][$key]);
    }

    public function getArray(string $key): ?array
    {
        if (!isset($this->opts["get"][$key]) || !is_array($this->opts["get"][$key])) {
            return null;
        }
        $res = [];
        foreach ($this->opts["get"][$key] as $key => $val) {
            if (!is_string($val)) {
                return null;
            }
            $res[$key] = trim($val);
        }
        return $res;
    }

    public function cookie(string $key): ?string
    {
        if (!isset($this->opts["cookie"][$key]) || !is_string($this->opts["cookie"][$key])) {
            return null;
        }
        return trim($this->opts["cookie"][$key]);
    }

    public function post(string $key): ?string
    {
        if (!isset($this->opts["post"][$key]) || !is_string($this->opts["post"][$key])) {
            return null;
        }
        return trim($this->opts["post"][$key]);
    }

    public function postArray(string $key): ?array
    {
        if (!isset($this->opts["post"][$key]) || !is_array($this->opts["post"][$key])) {
            return null;
        }
        $res = [];
        foreach ($this->opts["post"][$key] as $key => $val) {
            if (!is_string($val)) {
                return null;
            }
            $res[$key] = trim($val);
        }
        return $res;
    }

    public function file(string $key): ?UploadedFile
    {
        if (!isset($this->opts["files"][$key])) {
            return null;
        }
        return $this->opts["files"][$key];
    }

    public function time(): int
    {
        return $this->opts["time"] ?? 1741617587;
    }

    /** @since 1.7 */
    public function serverName(): string
    {
        return $this->opts["serverName"] = "localhost";
    }

    public function remoteAddr(): string
    {
        return $this->opts["remoteAddr"] ?? "127.0.0.1";
    }

    public function username(): ?string
    {
        return $this->opts["username"] ?? null;
    }

    public function admin(): bool
    {
        return $this->opts["admin"] ?? false;
    }

    public function s(): int
    {
        return $this->opts["s"] ?? 0;
    }

    public function selected(): string
    {
        return $this->opts["selected"];
    }

    public function language(): string
    {
        return $this->opts["language"] ?? "en";
    }

    public function edit(): bool
    {
        return $this->opts["edit"] ?? false;
    }
}
