<?php

namespace Alps\CacheEvader\Modifiers;

use Illuminate\Contracts\Config\Repository;
use Statamic\Modifiers\CoreModifiers;
use Statamic\Modifiers\Modifier;

class EvadeCache extends Modifier
{
    public function index($value, $params, $context)
    {
        /** @var Repository $config */
        $config = resolve(Repository::class);

        $paramNames = $config->get('statamic.cache-evader.evade_http_parameter_names');
        $paramValue = $config->get('statamic.cache-evader.evade_cache_modifier_value');

        return (new CoreModifiers)->addQueryParam($value, [
            $paramNames[0],
            $params[0] ?? $paramValue,
        ]);
    }
}
