# [Cookie](cookie.md) / [HttpCookieHandler](cookie-HttpCookieHandler.md) :: remove
 > im\http\HttpCookieHandler
____

## Description
Delete a cookie set by this handle.

Any cookie set by this handler can be deleted easily using this method.
No mater what options was provided when setting the cookie, this method
requires nothing more than the cookie name in order to delete the cookie.

## Synopsis
```php
public remove(string $name): void
```

## Parameters
| Name | Description |
| :--- | :---------- |
| name | Name of the cookie to delete |
