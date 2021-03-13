<?php declare(strict_types=1);
/*
 * This file is part of the IMPHP Project: https://github.com/IMPHP
 *
 * Copyright (c) 2017 Daniel Bergløv, License: MIT
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace im\http;

use im\http\msg\ResponseBuilder;
use im\http\msg\Request;

/**
 * Defines a cookie handler for the HTTP package
 */
interface CookieHandler {

    /**
     * Set `SameSite` to `Lax` mode
     *
     * @var int = 0x01
     */
    const F_LAX = 0x01;

    /**
     * Set `SameSite` to `Strict` mode
     *
     * @note
     *      This will override `F_LAX` if that is being set as well.
     *      Both cannot exist at the same time and `Strict` has higher priority.
     *
     * @var int = 0x03
     */
    const F_STRICT = 0x03;

    /**
     * Set the `Secure` flag on this cookie
     *
     * @var int = 0x08
     */
    const F_SECURE = 0x08;

    /**
     * Set the `HttpOnly` flag on this cookie
     *
     * @var int = 0x10
     */
    const F_NOSCRIPT = 0x10;

    /**
     * Write all cookies to the headers of a response object
     *
     * @param $response
     *      The response to write headers to
     */
    function writeToResponse(ResponseBuilder $response): void;

    /**
     * Extract cookies from the headers of a request object
     *
     * @param $request
     *      The request to extract headers from
     */
    function readFromRequest(Request $request): void;

    /**
     * Add a new cookie
     *
     * @param $name
     *      The name of the cookie
     *
     * @param $value
     *      The cookie value
     *
     * @param $time
     *      Time until the cookie expires as a Unix timestamp, or '0' for never
     *
     * @param $path
     *      Cookie Path
     *
     * @param $flags
     *      Restriction flags to append to the cookie
     */
    function set(string $name, string $value, int $expires = 0, string $path = null, int $flags = 0): void;

    /**
     * Get the value of a cookie.
     *
     * @param $name
     *      Name of the cookie
     *
     * @return
     *      Returns the value or `NULL` if the cookie does not exist
     */
    function get(string $name): ?string;

    /**
     * Delete a cookie set by this handle.
     *
     * Any cookie set by this handler can be deleted easily using this method.
     * No mater what options was provided when setting the cookie, this method
     * requires nothing more than the cookie name in order to delete the cookie.
     *
     * @param $name
     *      Name of the cookie to delete
     */
    function remove(string $name): void;

    /**
     * Check if a cookie exists
     *
     * @param $name
     *      Name of the cookie to look for
     */
    function has(string $name): bool;
}
