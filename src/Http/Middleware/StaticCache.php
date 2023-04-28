<?php

namespace Alps\CacheEvader\Http\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository;

class StaticCache extends \Statamic\StaticCaching\Middleware\Cache
{
    public function handle($request, Closure $next)
    {
        /** @var Repository $config */
        $config = resolve(Repository::class);

        $paramNames = $config->get('statamic.cache-evader.evade_http_parameter_names');

        if (!$request->hasAny($paramNames)) {
            return parent::handle($request, $next);
        }

        $privateNext = function($request) use ($next) {
            $response = $next($request);

            $response->headers->add([
                'X-Statamic-Private' => 'true',
            ]);

            return $response;
        };

        return parent::handle($request, $privateNext);
    }
}
