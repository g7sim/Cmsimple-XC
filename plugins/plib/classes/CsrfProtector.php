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
 * Flexible and unintrusive CSRF protection
 *
 * This class maintains an unguessable random CSRF token in `$_SESSION`,
 * but does not actually start the session.  So it will only work if a
 * session is started somewhere else (that is the case for administrators
 * of CMSimple_XH, and users of Register_XH and Memberpages, and maybe some
 * other plugins).
 *
 * @final
 * @since 1.5
 */
class CsrfProtector
{
    /**
     * Retrieves the CSRF token
     */
    public function token(): string
    {
        if (isset($_SESSION["plib_csrf_token"])) {
            return $_SESSION["plib_csrf_token"];
        }
        $token = base64_encode(random_bytes(15));
        $_SESSION["plib_csrf_token"] = $token;
        return $token;
    }

    /**
     * Wether the given `$token` matches the CSRF token
     */
    public function check(?string $token): bool
    {
        return $token !== null
            && isset($_SESSION["plib_csrf_token"])
            && hash_equals($_SESSION["plib_csrf_token"], $token);
    }
}
