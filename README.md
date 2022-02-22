<!-- statamic:hide -->
# Cache Evader

> Cache evasion for Statamic 3

<!-- /statamic:hide -->

This Addon provides a simple way to serve uncached pages based on an HTTP GET parameter and makes it possible to use Statamic forms on cached pages.

## Support

If you like the Addon consider [following me on Twitter](https://twitter.com/jakub_jo). If you've feature requests, feel free to start a discussion by opening a GitHub issue. If you've any further questions or want to discuss an opportunity with me, drop me a line at [hello@jakub.io](mailto:hello@jakub.io). 

## Installation

You can install the addon using composer:

```
composer require alpshq/statamic-cache-evader
```
<!-- statamic:hide -->

Alternatively you can install the addon by navigating to [Statamic's marketplace](https://statamic.com/addons/alps/cache-evader) within your Control Panel and searching there for `Cache Evader`.

<!-- /statamic:hide -->

## Usage

Essentially any URL to which you add the GET parameter `_nc` with *any* value will evade the cache.

### Modifier

To add the corresponding HTTP parameter name to any URL you can use the built-in modifier [evade_cache](src/Modifiers/EvadeCache.php):

```html
<a href="{{ current_url | evade_cache }}">Link to current (uncached) URL</a>
```

#### How does the cache evading work?

- The Addon will replace Statamic's default Cache middleware with the Addon's [StaticCache](src/Http/Middleware/StaticCache.php) middleware.
- The replaced middleware will check if a cache evading parameter is part of the request. If a parameter is found, the cache is evaded. If not, Statamic default behavior applies.

### Forms

To make your forms work in cached environments make sure to add the [{{ cache_evader_scripts }}](src/Tags/CacheEvaderScripts.php) tag right before the closing `</body>` tag. Add it either on every page which contains forms or add it to your layout globally:

```html
<!doctype html>
<html>
    <head>
        ...
    </head>
    <body>
        {{ template_content }}
        ...
        {{ cache_evader_scripts }}
    </body>
</html>
```

The tag will load a script which will add a hidden input field with the name `_xsrf_token` and the value of the XSRF cookie to all your forms.

The [SpoofXsrfHeader](src/Http/Middleware/SpoofXsrfHeader.php) middleware will make sure the added field gets validated by the default Laravel CSRF protection middleware. 

In order to get meaningful error and success messages for your users you need to make sure the forms redirects to an **uncached** page.
Within your forms add `_redirect` and `_error_redirect` links containing the cache evading HTTP parameter mentioned in the beginning. The simplest way to achieve this is by using the `evade_cache` modifier:

```html
{{ form:create handle="contact-form" }}
    <input type="hidden" name="_redirect" value="{{ current_url | evade_cache }}" />
    <input type="hidden" name="_error_redirect" value="{{ current_url | evade_cache }}" />
    <!-- Your fields, buttons, success & error messages ... --> 
{{ /form:create }}
```

After a submission, the above form will redirect you to the uncached version of the current page displaying all dynamic content, such as error and success messages. 

That's it. Your forms will work as if there was no cache at all.

#### How does it work in detail?

- The [script](resources/js/app.js) which you pull in with the [{{ cache_evader_scripts }}](src/Tags/CacheEvaderScripts.php) tags will loop through all your forms and add the current `XSRF-TOKEN` cookie value to the form by appending a hidden input field with the name `_xsrf_token`.
- If no such Cookie exists (This is especially the case when you're using the `full` cache strategy and the current user was served by the static file cache):
    - The script will make a lightweight fetch request to the `cache-evader.ping` route (`/cache-evader/ping`). 
    - Laravel will respond with the `XSRF-TOKEN` cookie attached. 
    - All following visits and requests have access to the `XSRF-TOKEN` cookie and no further fetch request will be made.
- The [`SpoofXsrfHeader`](src/Http/Middleware/SpoofXsrfHeader.php) middleware will populate the request's `x-xsrf-token` header with the value of the `_xsrf_token` field. It pretty much reproduces current SPA behavior, which is [mentioned in the Laravel documentation](https://laravel.com/docs/9.x/csrf#csrf-x-xsrf-token). Inspiration came from Laravel's [form method spoofing](https://laravel.com/docs/9.x/routing#form-method-spoofing)

## Configuration

You can publish the configuration file to modify the default parameter name (`_nc`), and the default value (`!`):

```
php artisan vendor:publish --tag=cache-evader-config
```

You'll find the published [configuration file](config/cache-evader.php) in `config/statamic/cache-evader.php` -- review it for explanation about the various options.

## Security

If you encounter any security related issues, please email directly jakub@alps.dev instead of opening an issue. All security related issues will be promptly addressed.

## License

MIT -- see the [license file](LICENSE.md).
