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
 * Text encoding and decoding helpers
 *
 * Not to be confused with character encodings.
 *
 * While PHP already offers several such functions, e.g. `base64_encode()`,
 * `urlencode()`, `bin2hex()`, a couple of useful ones are not available.
 * These may be implemented as static methods in this class.
 */
class Codec
{
    /**
     * Encodes Base 64 Encoding with URL and Filename Safe Alphabet
     *
     * Padding is always skipped.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc4648#section-5
     */
    public static function encodeBase64url(string $string): string
    {
        return str_replace(["+", "/"], ["-", "_"], rtrim(base64_encode($string), "="));
    }

    /**
     * Decodes Base 64 Encoding with URL and Filename Safe Alphabet
     *
     * @see https://datatracker.ietf.org/doc/html/rfc4648#section-5
     */
    public static function decodeBase64url(string $string): ?string
    {
        $res = base64_decode(str_replace(["-", "_"], ["+", "/"], $string), true);
        if ($res === false) {
            return null;
        }
        return $res;
    }

    private const BASE32HEX_ALPHABET = "0123456789ABCDEFGHIJKLMNOPQRSTUV";

    /**
     * Encodes Base 32 Encoding with Extended Hex Alphabet
     *
     * @see https://datatracker.ietf.org/doc/html/rfc4648#section-7
     * @since 1.4
     */
    public static function encodeBase32hex(string $bytes): string
    {
        $len = strlen($bytes);
        if ($len % 5 !== 0) {
            $bytes .= "\0";
        }
        $res = "";
        for ($i = 0; $i < $len; $i += 5) {
            $res .= self::BASE32HEX_ALPHABET[(((ord($bytes[$i + 0]) << 8) | ord($bytes[$i + 1])) >> 11) & 0b11111];
            $res .= self::BASE32HEX_ALPHABET[(((ord($bytes[$i + 0]) << 8) | ord($bytes[$i + 1])) >>  6) & 0b11111];
            if ($i + 1 === $len) {
                return $res . "======";
            }
            $res .= self::BASE32HEX_ALPHABET[(((ord($bytes[$i + 0]) << 8) | ord($bytes[$i + 1])) >>  1) & 0b11111];
            $res .= self::BASE32HEX_ALPHABET[(((ord($bytes[$i + 1]) << 8) | ord($bytes[$i + 2])) >>  4) & 0b11111];
            if ($i + 2 === $len) {
                return $res . "====";
            }
            $res .= self::BASE32HEX_ALPHABET[(((ord($bytes[$i + 2]) << 8) | ord($bytes[$i + 3])) >>  7) & 0b11111];
            if ($i + 3 === $len) {
                return $res . "===";
            }
            $res .= self::BASE32HEX_ALPHABET[(((ord($bytes[$i + 2]) << 8) | ord($bytes[$i + 3])) >>  2) & 0b11111];
            $res .= self::BASE32HEX_ALPHABET[(((ord($bytes[$i + 3]) << 8) | ord($bytes[$i + 4])) >>  5) & 0b11111];
            if ($i + 4 === $len) {
                return $res . "=";
            }
            $res .= self::BASE32HEX_ALPHABET[(((ord($bytes[$i + 3]) << 8) | ord($bytes[$i + 4])) >>  0) & 0b11111];
        }
        return $res;
    }

    /**
     * Decodes Base 32 Encoding with Extended Hex Alphabet
     *
     * @see https://datatracker.ietf.org/doc/html/rfc4648#section-7
     * @since 1.4
     */
    public static function decodeBase32hex(string $string): string
    {
        $string = rtrim($string, "=");
        $len = strlen($string);
        $res = "";
        $bits = [];
        for ($i = 0; $i < $len; $i += 8) {
            $bits[0] = strpos(self::BASE32HEX_ALPHABET, $string[$i + 0]);
            $bits[1] = strpos(self::BASE32HEX_ALPHABET, $string[$i + 1]);
            $res .= chr($bits[0] << 3 | $bits[1] >> 2);
            if ($i + 2 === $len) {
                return $res;
            }
            $bits[2] = strpos(self::BASE32HEX_ALPHABET, $string[$i + 2]);
            $bits[3] = strpos(self::BASE32HEX_ALPHABET, $string[$i + 3]);
            $res .= chr($bits[1] << 6 | $bits[2] << 1 | $bits[3] >> 4);
            if ($i + 4 === $len) {
                return $res;
            }
            $bits[4] = strpos(self::BASE32HEX_ALPHABET, $string[$i + 4]);
            $res .= chr($bits[3] << 4 | $bits[4] >> 1);
            if ($i + 5 === $len) {
                return $res;
            }
            $bits[5] = strpos(self::BASE32HEX_ALPHABET, $string[$i + 5]);
            $bits[6] = strpos(self::BASE32HEX_ALPHABET, $string[$i + 6]);
            $res .= chr($bits[4] << 7 | $bits[5] << 2 | $bits[6] >> 3);
            if ($i + 7 === $len) {
                return $res;
            }
            $bits[7] = strpos(self::BASE32HEX_ALPHABET, $string[$i + 7]);
            $res .= chr($bits[6] << 5 | $bits[7] >> 0);
        }
        return $res;
    }
}
