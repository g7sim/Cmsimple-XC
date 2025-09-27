<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Twocents_XH.
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
 * A SystemChecker fake for automated testing
 *
 * @package Fakes
 * @since 1.1
 */
class FakeSystemChecker extends SystemChecker // @phpstan-ignore class.extendsFinalByPhpDoc
{
    /** @var bool */
    private $success;

    public function __construct(bool $success = false)
    {
        $this->success = $success;
    }

    public function checkVersion(string $actual, string $minimum): bool
    {
        return $this->success;
    }

    public function checkExtension(string $extension): bool
    {
        return $this->success;
    }

    public function checkGdFreetype(): bool
    {
        return $this->success;
    }

    public function checkGdPng(): bool
    {
        return $this->success;
    }

    public function checkGdFeature(string $feature): bool
    {
        return $this->success;
    }

    public function checkPlugin(string $plugin, ?string $version = null): bool
    {
        return $this->success;
    }

    public function checkWritability(string $path): bool
    {
        return $this->success;
    }
}
