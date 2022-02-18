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

## Usage

Essentially any URL to which you add the GET parameter `_nc` with *any* value will evade the cache.

The Addon is meant to be used in conjunction with the [`half` static caching strategy](https://statamic.dev/static-caching#application-driver) of Statamic.
It'll technically work also with the `full` strategy, but the support in forms depends on Laravel's `XSRF-TOKEN` cookie. When your webserver responds only with pregenerated static html files no corresponding cookie will be sent. Additionally, you'd need to adapt the NGINX config.
Form support for the [`full` strategy](https://statamic.dev/static-caching#file-driver) is in the pipeline and will be added later.

### Modifier

To add the corresponding HTTP parameter name to any URL you can use the built-in modifier [`evade_cache`](src/Modifiers/EvadeCache.php):

```html
<a href="{{ current_url | evade_cache }}">Link to current (uncached) URL</a>
```

### Forms

To make your forms work in cached environments make sure to add the [`{{ cache_evader_scripts }}`](src/Tags/CacheEvaderScripts.php) tag right before the closing `</body>` tag. Add it either on every page which contains forms or add it to your layout.

The tag will load a script which will add a hidden input field with the name `_xsrf_token` and the value of the XSRF cookie to all your forms.

```html
<!doctype html>
<html lang="{{ site:short_locale }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ mix src='css/app.css' }}">
    </head>
    <body>
        {{ template_content }}
        
        <script src="{{ mix src='/js/app.js' }}"></script>

        {{ cache_evader_scripts }}
    </body>
</html>
```

Within your forms add `_redirect` and `_error_redirect` links containing the cache evading HTTP parameter. The simplest way to achieve this is by using the `evade_cache` modifier:

After a submission, the following form will redirect you to the uncached version of the current page.

```html
{{ form:create handle="contact-form" }}
    <input type="hidden" name="_redirect" value="{{ current_url | evade_cache }}" />
    <input type="hidden" name="_error_redirect" value="{{ current_url | evade_cache }}" />
    <!-- Your fields, buttons, success & error messages ... --> 
{{ /form:create }}
```

That's it. Your forms will work as if there was no cache at all.

## Configuration

You can publish the configuration file to modify the default parameter name (`_nc`), and the default value (`!`):

```
php artisan vendor:publish --tag=cache-evader-config
```

You'll find the published [configuration file](config/cache-evader.php) in `config/statamic/cache-evader.php` -- review it for explanation about the various options.

## How does it work?

It's pretty straightforward:

- The Addon will replace Statamic's default Cache middleware with the Addon's [`StaticCache`](src/Http/Middleware/StaticCache.php) middleware.
- The replaced middleware will check if a cache evading parameter is part of the request. If a parameter is found, the cache is evaded. If not, Statamic default behavior applies.
- The [script](resources/js/app.js) which you pull in with the [`{{ cache_evader_scripts }}`](src/Tags/CacheEvaderScripts.php) tags will loop through all your forms and add the current `XSRF-TOKEN` cookie value to the form by appending a hidden input field with the name `_xsrf_token`.
- The [`SpoofXsrfHeader`](src/Http/Middleware/SpoofXsrfHeader.php) middleware will populate the request's `x-xsrf-token` header with the value of the `_xsrf_token` field. It pretty much reproduces current SPA behavior, which is [mentioned in the Laravel documentation](https://laravel.com/docs/9.x/csrf#csrf-x-xsrf-token). Inspiration came from Laravel's [form method spoofing](https://laravel.com/docs/9.x/routing#form-method-spoofing)

The benefit of using the `XSRF-TOKEN` cookie value is in the absence of making additional requests to just obtain the token.

## Security

If you encounter any security related issues, please email directly jakub@alps.dev instead of opening an issue. All security related issues will be promptly addressed.

## License

MIT -- see the [license file](LICENSE.md).
