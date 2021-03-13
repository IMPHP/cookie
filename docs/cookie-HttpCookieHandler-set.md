# [Cookie](cookie.md) / [HttpCookieHandler](cookie-HttpCookieHandler.md) :: set
 > im\http\HttpCookieHandler
____

## Description
Add a new cookie

## Synopsis
```php
public set(string $name, string $value, int $expires = 0, null|string $path = NULL, int $flags = 0): void
```

## Parameters
| Name | Description |
| :--- | :---------- |
| name | The name of the cookie |
| value | The cookie value |
| time | Time until the cookie expires as a Unix timestamp, or '0' for never |
| path | Cookie Path |
| flags | Restriction flags to append to the cookie |
