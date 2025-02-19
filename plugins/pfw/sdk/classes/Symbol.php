<?php

/*
 * Copyright 2017 Christoph M. Becker
 *
 * This file is part of the Pfw_XH SDK.
 *
 * The Pfw_XH SDK is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Pfw_XH SDK is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the Pfw_XH SDK.  If not, see <http://www.gnu.org/licenses/>.
 */

class Symbol
{
    /**
     * @var int|string
     */
    private $kind;

    /**
     * @var string
     */
    private $text;

    /**
     * @var int
     */
    private $line;

    /**
     * @param array|string $token
     */
    public function __construct($token)
    {
        static $line = 0;

        if (is_array($token)) {
            list($this->kind, $this->text, $this->line) = $token;
            if ($this->line > $line) {
                $line = $this->line;
            }
        } else {
            $this->kind = $this->text = $token;
            $this->line = $line;
        }
    }

    /**
     * @return int|string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (is_string($this->kind)) {
            return "\"{$this->kind}\"";
        } elseif ($this->kind === -1) {
            return "end of file";
        } else {
            return token_name($this->kind);
        }
    }
}
