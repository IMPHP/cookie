<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use im\http\CookieHandler;
use im\http\HttpCookieHandler;
use im\http\msg\HttpRequestBuilder;
use im\http\msg\HttpResponseBuilder;
use im\http\msg\HttpUriBuilder;

final class CookieManagerTest extends TestCase {

    /**
     *
     */
    public function test_initiate(): CookieHandler {
        $handler = new HttpCookieHandler();
        $handler->set("myCookie", "Some Value", 3600);

        $this->assertEquals(
            "Some Value",
            $handler->get("myCookie")
        );

        return $handler;
    }

    /**
     * @depends test_initiate
     */
    public function test_request(CookieHandler $handler): CookieHandler {
        $request = new HttpRequestBuilder("GET", new HttpUriBuilder());
        $request->addHeader("Cookie", substr(md5("im_anotherCookie"), 0, 12) . "=||0|Some Other Value");
        $handler->readFromRequest($request);

        $this->assertEquals(
            "Some Other Value",
            $handler->get("anotherCookie")
        );

        return $handler;
    }

    /**
     * @depends test_request
     */
    public function test_response(CookieHandler $handler): void {
        $response = new HttpResponseBuilder();
        $handler->remove("anotherCookie");
        $handler->writeToResponse($response);

        $this->assertEquals(
            1,
            preg_match("/Set-Cookie: 1c36f192d9db=\|\|0\|Some\+Value; Expires=/", $response->toString())
        );

        $this->assertEquals(
            1,
            preg_match("/Set-Cookie: 91e812dac4bc=deleted; Expires=/", $response->toString())
        );
    }
}
