# [Cookie](cookie.md) / CookieHandler
 > im\http\CookieHandler
____

## Description
Defines a cookie handler for the HTTP package

## Synopsis
```php
interface CookieHandler {

    // Constants
    int F_LAX = 0x01
    int F_STRICT = 0x03
    int F_SECURE = 0x08
    int F_NOSCRIPT = 0x10

    // Methods
    writeToResponse(im\http\msg\ResponseBuilder $response): void
    readFromRequest(im\http\msg\Request $request): void
    set(string $name, string $value, int $expires = 0, null|string $path = NULL, int $flags = 0): void
    get(string $name): null|string
    remove(string $name): void
    has(string $name): bool
}
```

## Constants
| Name | Description |
| :--- | :---------- |
| [__CookieHandler&nbsp;::&nbsp;F\_LAX__](cookie-CookieHandler-prop_F_LAX.md) | Set `SameSite` to `Lax` mode |
| [__CookieHandler&nbsp;::&nbsp;F\_STRICT__](cookie-CookieHandler-prop_F_STRICT.md) | Set `SameSite` to `Strict` mode |
| [__CookieHandler&nbsp;::&nbsp;F\_SECURE__](cookie-CookieHandler-prop_F_SECURE.md) | Set the `Secure` flag on this cookie |
| [__CookieHandler&nbsp;::&nbsp;F\_NOSCRIPT__](cookie-CookieHandler-prop_F_NOSCRIPT.md) | Set the `HttpOnly` flag on this cookie |

## Methods
| Name | Description |
| :--- | :---------- |
| [__CookieHandler&nbsp;::&nbsp;writeToResponse__](cookie-CookieHandler-writeToResponse.md) | Write all cookies to the headers of a response object |
| [__CookieHandler&nbsp;::&nbsp;readFromRequest__](cookie-CookieHandler-readFromRequest.md) | Extract cookies from the headers of a request object |
| [__CookieHandler&nbsp;::&nbsp;set__](cookie-CookieHandler-set.md) | Add a new cookie |
| [__CookieHandler&nbsp;::&nbsp;get__](cookie-CookieHandler-get.md) | Get the value of a cookie |
| [__CookieHandler&nbsp;::&nbsp;remove__](cookie-CookieHandler-remove.md) | Delete a cookie set by this handle |
| [__CookieHandler&nbsp;::&nbsp;has__](cookie-CookieHandler-has.md) | Check if a cookie exists |
