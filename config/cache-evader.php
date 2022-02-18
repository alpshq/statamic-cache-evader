<?php

return [
    /*
    |---------------------------------------------------------------------------
    | Cache Evading HTTP-Parameter Names
    |---------------------------------------------------------------------------
    |
    | Specify the HTTP parameter names which will cause cache evasion.
    | You can specify as many you want.
    | Everytime you add one of these parameters to your URL the
    | request will evade the static cache.
    |
    | The later value of the parameter does not matter.
    | It can be empty or have any value.
    |
    | The **first** parameter name will also be used by the
    | supplied modifier `evade_cache`.
    | You can use the modifier in your Antlers views to quickly
    | add the parameter to any url:
    |
    | ```antlers
    | {{ current_url | evade_cache }}
    | ```
    | ↳ If `current_url` is `/news?sort=date` & the first parameter
    |   name is `_nc` it'll be modified to `/news?sort=date&_nc=!`
    |
    | The default parameter name is `_nc`.
    | All requests to URLs which contain `?_nc` will evade the cache.
    |
    */
    'evade_http_parameter_names' => [
        '_nc',
    ],

    /*
    |---------------------------------------------------------------------------
    | Evade Cache Modifier Default Parameter Value
    |---------------------------------------------------------------------------
    |
    | If you really care you're free to specify the default value of
    | the `evade_cache` Antlers modifier.
    | It does not make any technical difference.
    | Your URLs will just look different.
    |
    | ```antlers
    | {{ current_url | evade_cache }}
    | ```
    | ↳ If ...
    |     - `current_url` is `/news?sort=date`
    |     - & the first parameter name is `_nc`
    |     - & you set the `evade_cache_modifier_value` to `please`
    | ... it'll be modified to `/news?sort=date&_nc=please`
    |
    */
    'evade_cache_modifier_value' => '!',
];
