# [Cookie](cookie.md) / HttpCookieHandler
 > im\http\HttpCookieHandler
____

## Description
An implementation of the `im\http\CookieHandler` interface

## Synopsis
```php
class HttpCookieHandler implements im\http\CookieHandler {

    // Inherited Constants
    public int F_LAX = 0x01
    public int F_STRICT = 0x03
    public int F_SECURE = 0x08
    public int F_NOSCRIPT = 0x10

    // Methods
    public __construct(null|string $prefix = NULL, null|string $path = NULL, null|string $domain = NULL)
    public writeToResponse(im\http\msg\ResponseBuilder $response): void
    public readFromRequest(im\http\msg\Request $request): void
    public set(string $name, string $value, int $expires = 0, null|string $path = NULL, int $flags = 0): void
    public get(string $name): null|string
    public remove(string $name): void
    public has(string $name): bool
}
```

## Constants
| Name | Description |
| :--- | :---------- |
| [__HttpCookieHandler&nbsp;::&nbsp;F\_LAX__](cookie-HttpCookieHandler-prop_F_LAX.md) | Set `SameSite` to `Lax` mode |
| [__HttpCookieHandler&nbsp;::&nbsp;F\_STRICT__](cookie-HttpCookieHandler-prop_F_STRICT.md) | Set `SameSite` to `Strict` mode |
| [__HttpCookieHandler&nbsp;::&nbsp;F\_SECURE__](cookie-HttpCookieHandler-prop_F_SECURE.md) | Set the `Secure` flag on this cookie |
| [__HttpCookieHandler&nbsp;::&nbsp;F\_NOSCRIPT__](cookie-HttpCookieHandler-prop_F_NOSCRIPT.md) | Set the `HttpOnly` flag on this cookie |

## Methods
| Name | Description |
| :--- | :---------- |
| [__HttpCookieHandler&nbsp;::&nbsp;\_\_construct__](cookie-HttpCookieHandler-__construct.md) |  |
| [__HttpCookieHandler&nbsp;::&nbsp;writeToResponse__](cookie-HttpCookieHandler-writeToResponse.md) | Write all cookies to the headers of a response object |
| [__HttpCookieHandler&nbsp;::&nbsp;readFromRequest__](cookie-HttpCookieHandler-readFromRequest.md) | Extract cookies from the headers of a request object |
| [__HttpCookieHandler&nbsp;::&nbsp;set__](cookie-HttpCookieHandler-set.md) | Add a new cookie |
| [__HttpCookieHandler&nbsp;::&nbsp;get__](cookie-HttpCookieHandler-get.md) | Get the value of a cookie |
| [__HttpCookieHandler&nbsp;::&nbsp;remove__](cookie-HttpCookieHandler-remove.md) | Delete a cookie set by this handle |
| [__HttpCookieHandler&nbsp;::&nbsp;has__](cookie-HttpCookieHandler-has.md) | Check if a cookie exists |

## Example 1
```php
$request = new ServerRequestBuilder();
$handler = new CookieHandler();

// Extract cookies from the request
$handler->readFromRequest($request);

// Set a new cookie
$handler->set("some_name", "Some Value", 3600 * 24, null, CookieHandler::F_SECURE);

// Remove a cookie
$handler->remove("another_name");

// Write this information into the response headers
$response = new HttpResponseBuilder();
$handler->writeToResponse($response);
```
