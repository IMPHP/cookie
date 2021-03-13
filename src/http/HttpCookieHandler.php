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

use stdClass;
use im\util\MapArray;
use im\util\Map;
use im\util\StringBuilder;
use im\http\msg\Request;
use im\http\msg\ResponseBuilder;

/**
 * An implementation of the `im\http\CookieHandler` interface
 *
 * @example
 *
 *      ```php
 *      $request = new ServerRequestBuilder();
 *      $handler = new CookieHandler();
 *
 *      // Extract cookies from the request
 *      $handler->readFromRequest($request);
 *
 *      // Set a new cookie
 *      $handler->set("some_name", "Some Value", 3600 * 24, null, CookieHandler::F_SECURE);
 *
 *      // Remove a cookie
 *      $handler->remove("another_name");
 *
 *      // Write this information into the response headers
 *      $response = new HttpResponseBuilder();
 *      $handler->writeToResponse($response);
 *      ```
 */
class HttpCookieHandler implements CookieHandler {

    /** @internal */
    protected string $prefix = "im_";

    /** @internal */
    protected ?string $path = null;

    /** @internal */
    protected ?string $domain = null;

    /** @internal */
    protected MapArray $cookies;

    /**
     * @param $prefix
     *      Cookie names are always hashed, this can be used as a prefix for the cookie names before hashing
     *
     * @param $path
     *      Path used on cookies if path is not defined when setting cookies
     *
     * @param $path
     *      Domain used on cookies if domain is not defined when setting cookies
     */
    public function __construct(string $prefix=null, string $path=null, string $domain=null) {
        if ($prefix != null) {
            $this->prefix = $prefix;
        }

        $this->path = $path;
        $this->domain = $domain;
        $this->cookies = new Map();
    }

    /**
     * @inheritDoc
     */
    #[Override("im\http\CookieHandler")]
    public function writeToResponse(ResponseBuilder $response): void {
        foreach ($this->cookies as $cookie) {
            if ($cookie->flags & 0x8000) {
                $str = new StringBuilder();

                if ($cookie->expires >= 0) {
                    $str->appendFormat("%d|%s", $cookie->flags & 0xFF, urlencode($cookie->value));

                } else {
                    $str->append("deleted");
                }

                if ($cookie->domain != null) {
                    if ($cookie->expires >= 0) {
                        $str->prepend($cookie->domain, "|");
                    }

                    $str->appendFormat("; Domain=%s", $cookie->domain);

                } else if ($cookie->expires >= 0) {
                    $str->prepend("|");
                }

                if ($cookie->path != null) {
                    if ($cookie->expires >= 0) {
                        $str->prepend($cookie->path, "|");
                    }

                    $str->appendFormat("; Path=%s", $cookie->path);

                } else if ($cookie->expires >= 0) {
                    $str->prepend("|");
                }

                if ($cookie->flags & CookieHandler::F_LAX) {
                    $str->appendFormat("; SameSite=%s", $cookie->flags & CookieHandler::F_STRICT == CookieHandler::F_STRICT ? "Strict" : "Lax");
                }

                if ($cookie->expires != 0) {
                    $str->appendFormat("; Expires=%s; Max-Age=%s", gmdate("D, d M Y H:i:s T", time() + $cookie->expires), $cookie->expires);
                }

                if ($cookie->flags & CookieHandler::F_SECURE) {
                    $str->append("; Secure");
                }

                if ($cookie->flags & CookieHandler::F_NOSCRIPT) {
                    $str->append("; HttpOnly");
                }

                $str->prepend($cookie->name, "=");
                $response->addHeader("Set-Cookie", $str->toString());
            }
        }
    }

    /**
     * @inheritDoc
     */
    #[Override("im\http\CookieHandler")]
    public function readFromRequest(Request $request): void {
        $cookies = $request->getHeader("Cookie");

        foreach ($cookies as $cookie) {
            $name = trim(substr($cookie, 0, ($pos = strpos($cookie, "="))));
            $value = trim(substr($cookie, $pos+1));

            // [path|domain|flags|value]
            if (preg_match("/^(?<path>[\w\/-]+)?\|(?<domain>[\w\.]+)?\|(?<flags>[0-9]+)\|(?<value>.*)$/", $value, $matches)) {
                $this->cookies->set($name, $this->createCookie(
                    $name,
                    urldecode($matches["value"]),
                    0,
                    $matches["path"],
                    $matches["domain"],
                    0x4000|intval($matches["flags"]) // Mark as inHeader
                ));

            } else {
                $this->cookies->set($name, $this->createCookie($name, $value));
            }
        }
    }

    /**
     * @inheritDoc
     */
    #[Override("im\http\CookieHandler")]
    public function set(string $name, string $value, int $expires = 0, string $path = null, int $flags = 0): void {
        $name = $this->hashName($name);
        $cookie = $this->cookies->get($name);

        if ($cookie == null) {
            $cookie = $this->createCookie($name, $value, $expires, $path, null, (0xFF & $flags));

        } else {
            $cookie->value = $value;
            $cookie->expires = $expires;
            $cookie->path = $path ?? $this->path;
            $cookie->flags &= 0xFF00;   // Include internal flags
            $cookie->flags |= (0xFF & $flags);
        }

        $cookie->flags |= 0x8000; // Mark it as changed

        $this->cookies->set($name, $cookie);
    }

    /**
     * @inheritDoc
     */
    #[Override("im\http\CookieHandler")]
    public function get(string $name): ?string {
        $name = $this->hashName($name);
        $cookie = $this->cookies->get($name);

        if ($cookie == null
                || $cookie->expires < 0) {

            return null;
        }

        return $cookie->value;
    }

    /**
     * @inheritDoc
     */
    #[Override("im\http\CookieHandler")]
    public function remove(string $name): void {
        $name = $this->hashName($name);
        $cookie = $this->cookies->get($name);

        if ($cookie != null) {
            if ($cookie->flags & 0x4000) {
                $cookie->expires = -1 * (3600 * 24 * 365);
                $cookie->flags |= 0x8000; // Mark it as changed

            } else {
                $this->cookies->remove($name);
            }
        }
    }

    /**
     * @inheritDoc
     */
    #[Override("im\http\CookieHandler")]
    public function has(string $name): bool {
        $name = $this->hashName($name);
        $cookie = $this->cookies->get($name);

        if ($cookie == null) {
            return false;
        }

        return $cookie->expires >= 0;
    }

    /**
     * @internal
     */
    protected function createCookie(string $name, string $value, int $expires = 0, string $path = null, string $domain = null, int $flags = 0): object {
        $ret = new stdClass();
        $ret->name = $name;
        $ret->value = $value;
        $ret->expires = $expires;
        $ret->path = $path ?? $this->path;
        $ret->domain = $domain ?? $this->domain;
        $ret->flags = $flags;

        return $ret;
    }

    /**
     * @internal
     */
    protected function hashName(string $name): string {
        return substr(md5("{$this->prefix}{$name}"), 0, 12);
    }
}
