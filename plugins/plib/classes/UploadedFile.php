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
 * A value object representing an uploaded file
 *
 * This class is not meant to be instantiated on its own;
 * instances should only be accessed via `Request::file()`.
 *
 * @since 1.5
 */
final class UploadedFile
{
    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var int */
    private $size;

    /** @var string */
    private $temp;

    /** @var int */
    private $error;

    /** @internal */
    public function __construct(string $name, string $type, int $size, string $temp, int $error)
    {
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->temp = $temp;
        $this->error = $error;
    }

    /**
     * The filename sent by the client
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * The MIME type sent by the client
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * The size, in bytes, of the uploaded file
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * The temporary filename of the file in which the uploaded file was stored on the server
     */
    public function temp(): string
    {
        return $this->temp;
    }

    /**
     * The error code associated with this file upload
     */
    public function error(): int
    {
        return $this->error;
    }
}
