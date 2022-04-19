<!-- statamic:hide -->
# Cache Evader

> Cache evasion for Statamic 3

<!-- /statamic:hide -->

This Addon provides various simple ways to serve **uncached content** on **cached pages** and makes it possible to use Statamic forms on cached pages.

## What you can do

- [Serve whole uncached pages based on an HTTP GET parameter](#usage-cache-evading-based-on-http-get-parameter)
- [Use Statamic forms on cached pages](#usage-forms)
- **NEW**: [Inject uncached partials as part of cached pages](#usage-inject-uncached-partials-as-part-of-cached-pages)

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

## Usage: Cache evading based on HTTP GET parameter

Essentially any URL to which you add the GET parameter `_nc` with *any* value will evade the cache.

### Modifier

To add the corresponding HTTP parameter name to any URL you can use the built-in modifier [evade_cache](src/Modifiers/EvadeCache.php):

```html
<a href="{{ current_url | evade_cache }}">Link to current (uncached) URL</a>
```

#### How does the cache evading work?

- The Addon will replace Statamic's default Cache middleware with the Addon's [StaticCache](src/Http/Middleware/StaticCache.php) middleware.
- The replaced middleware will check if a cache evading parameter is part of the request. If a parameter is found, the cache is evaded. If not, Statamic default behavior applies.

## Usage: Forms

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

In order to get meaningful error and success messages for your users you need to make sure the forms redirect to an **uncached** page.
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

### How does it work in detail?

- The [script](resources/js/app.js) which you pull in with the [{{ cache_evader_scripts }}](src/Tags/CacheEvaderScripts.php) tags will loop through all your forms and add the current `XSRF-TOKEN` cookie value to the form by appending a hidden input field with the name `_xsrf_token`.
- If no such Cookie exists (This is especially the case when you're using the `full` cache strategy and the current user was served by the static file cache):
    - The script will make a lightweight fetch request to the `cache-evader.ping` route (`/cache-evader/ping`). 
    - Laravel will respond with the `XSRF-TOKEN` cookie attached. 
    - All following visits and requests have access to the `XSRF-TOKEN` cookie and no further fetch request will be made.
- The [`SpoofXsrfHeader`](src/Http/Middleware/SpoofXsrfHeader.php) middleware will populate the request's `x-xsrf-token` header with the value of the `_xsrf_token` field. It pretty much reproduces current SPA behavior, which is [mentioned in the Laravel documentation](https://laravel.com/docs/9.x/csrf#csrf-x-xsrf-token). Inspiration came from Laravel's [form method spoofing](https://laravel.com/docs/9.x/routing#form-method-spoofing)

## Usage: Inject uncached partials as part of cached pages

Wouldn't it be fine if you could utilize Statamic's full caching strategy while displaying **dynamic content** on your pages?

Look no further -- you've found the solution. With the help of **uncached** partials inside your **cached** pages you can get the best out of both worlds.

### Setting it up

To enable injecting of custom partials make sure to add the [{{ cache_evader_scripts }}](src/Tags/CacheEvaderScripts.php) tag right before the closing `</body>` tag. Add it either on every page on which you'll use the `{{ cache_evader_partial }}` tag or add it to your layout globally:

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

The tag will load a script which will fetch the contents of your uncached partials by sending an immediate `fetch` request for each partial. The fetch request will evade the cache and load any dynamic content you specifiy in your partials.

### Basic Usage

First things first: Create a simple partial which contains dynamic content: `partials/user.antlers.html`.

```html
<!-- Your partial with dynamic content -->
{{ if logged_in }}
    Welcome {{ current_user:email }}
{{ else }}
    Please login.
{{ /if }}
```

Now simply include the partial in your template using the `{{ cache_evader_partial }}` tag:

```html
<!-- Your template -->
{{ cache_evader_partial:user }}
<!--                    ^^^^ -> This is the file name of the partial. -->
```

**That's it.** Your **fully cached page** will now **always** display your user's email!

### Parameters

You can add any number of parameters to your partial. You can access the parameters as regular variables in your partial. 

> **Important:** \
> Keep in mind, parameters are publicly visible -- don't share secrets using parameters!

```html
<!-- Your partial with dynamic content -->
{{ if logged_in }}
    Welcome {{ current_user:email }}
{{ else }}
    Please <a href="{{ login_url }}">login</a>.
{{ /if }}
```

```html
<!-- Your template -->
{{ cache_evader_partial:user login_url="/login" }}
```

### Displaying a loading message

You can display a loading message or an indicator. Simply wrap your loading indicator in the tag pair:

```html
<!-- Your template -->
{{ cache_evader_partial src="user" login_url="/login" }}
    Loading login state ...
{{ /cache_evader_partial }}
```

### Placeholder element

When you include a partial using the `{{ cache_evader_partial }}` tag, a placeholder `div` will be rendered instead of the partial. The placeholder is eventually swaped out with the content of your partial.

You can change the placeholder by adding a `wrap` parameter: `{{ cache_evader_partial src="..." wrap="span" }}`.

### Wrapping of your partial's content

Your partial's content will be wrapped in a `div` element. You can avoid this behaviour by rendering a single root element in your partial.

**This will be wrapped in a `div`:**
```html
<span>
    Welcome {{ current_user:email }}!
</span>
<a href="{{ logout_url }}">Not {{ current_user:email }}?</a>
```

**This will NOT be wrapped in a `div`:**
```html
<p>
    <span>
        Welcome {{ current_user:email }}!
    </span>
    <a href="{{ logout_url }}">Not {{ current_user:email }}?</a>
</p>
```

### Script tags

Yes! You can include script tags in your partials. They'll be executed.

```html
<!-- Your partial with dynamic content -->
{{ if logged_in }}
    Welcome {{ current_user:email }}
{{ else }}
    Please <a href="{{ login_url }}">login</a>.
{{ /if }}

<script src="{{ mix src='/js/user.js' }}"></script>
```

### JavaScript Hooks

#### Before a fetch request is sent

Before each fetch request is sent to your server the `cacheEvaderBeforeInject` event is triggered on the placeholder element. You can cancel the fetch request by invoking `preventDefault()` on the event.

```js
window.addEventListener('cacheEvaderBeforeInject', ev => {
  ev.preventDefault(); // No fetch request is sent.
  // ev.target -- The placeholder element
  // ev.detail -- See below which properties are available. 
});
```

The event will have a `detail` property which contains the url to which the request is sent and also all the parameters you've supplied to the partial.

| Name | Type | Purpose |
| :--- | :--- | :--- |
| url | `string` | The URL which will render the partial's contents | 
| params | `object` | An object which contains all the parameters you've supplied to the partial |
| params.view | `string` | The path to the partial |
| params.signature | `string` | Laravel's URL signature |

#### After the content was injected

After the dynamic content of your partial was fetched & injected into the DOM the `cacheEvaderAfterInject` event is triggered on the injected element.

```js
window.addEventListener('cacheEvaderAfterInject', ev => {
  // ev.target -- See below what the target will be.
  // ev.detail -- See below which properties are available.
});
```

The value of the event `target` will be the wrapping element of your partial. If your partial does have a single root element, the value of `target` will be your root element. Otherwise it'll be a wrapping `div`.

The event will have a `detail` property which contains the url to which the request was sent to, all the parameters you've supplied to the partial and the server's response.

| Name | Type | Purpose |
| :--- | :--- | :--- |
| response | `Response` | The response object |
| url | `string` | The URL which will render the partial's contents | 
| params | `object` | An object which contains all the parameters you've supplied to the partial |
| params.view | `string` | The path to the partial |
| params.signature | `string` | Laravel's URL signature |

### How do dynamic partials work in detail?

- When using the `{{ cache_evader_partial }}` tag, a placeholder will be rendered with no actual content.
- The JavaScript you add to the browser using the `{{ cache_evader_scripts }}` tag will iterate over each placeholder and will send a fetch request to the server
- Your server renders the partial and sends it to the browser
- The placeholder will be swaped with the actual content

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
