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
 * Interface of model objects stored in a `DocumentStore2`
 *
 * @see DocumentStore2
 * @since 1.9
 */
interface Document2
{
    /**
     * Creates a new model object
     *
     * This method is called from {@see DocumentStore2::create()},
     * and should create and return a new model object with reasonable defaults.
     *
     * @return static
     */
    public static function new(string $key);

    /**
     * Creates a model object from its storage representation
     *
     * This method is called from {@see DocumentStore2::read()}
     * and {@see DocumentStore2::update()} with the contents of the
     * file or an empty string if the file cannot be read.
     *
     * If the storage representation is valid, create and return the
     * model object.  Otherwise return `null`.
     *
     * @return ?static
     */
    public static function fromString(string $contents, string $key);

    /**
     * Returns the storage representation of the model object
     *
     * This is method is called from {@see DocumentStore2::commit()},
     * and should return a string which could be passed to
     * {@see Document2::fromString()} yielding an equal object.
     * If serialization to a string is not possible for whatever reason,
     * the method should return `null`, so nothing will be written to the
     * file, and {@see DocumentStore2::commit()} will return `false`.
     */
    public function toString(): ?string;
}
