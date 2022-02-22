<?php

namespace Alps\CacheEvader;

use Alps\CacheEvader\Http\Middleware\SpoofXsrfHeader;
use Alps\CacheEvader\Http\Middleware\StaticCache;
use Alps\CacheEvader\Modifiers\EvadeCache;
use Alps\CacheEvader\Tags\CacheEvaderScripts;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Statamic\Statamic;
use Statamic\StaticCaching\Middleware\Cache;

class ServiceProvider extends \Statamic\Providers\AddonServiceProvider
{
    protected $publishables = [
        __DIR__ . '/../dist/js' => 'js',
    ];

    protected $tags = [
        CacheEvaderScripts::class,
    ];

    protected $modifiers = [
        EvadeCache::class,
    ];

    public function register()
    {
        $this->app->bind(Cache::class, StaticCache::class);
    }

    public function boot()
    {
        parent::boot();

        $this
            ->bootAddonConfig()
            ->bootAddonMiddleware();
    }

    public function bootAddon()
    {
        $this->registerWebRoutes(function() {
            Route::get('cache-evader/ping', function() {
                return response(null, 204);
            })->name('cache-evader.ping');
        });

        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', [
                '--tag' => 'statamic-cache-evader',
                '--force' => true,
            ]);
        });
    }

    protected function bootAddonMiddleware(): self
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $router->prependMiddlewareToGroup('web', SpoofXsrfHeader::class);

        return $this;
    }

    protected function bootAddonConfig(): self
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cache-evader.php', 'statamic.cache-evader');

        $this->publishes([
            __DIR__.'/../config/cache-evader.php' => config_path('statamic/cache-evader.php'),
        ], 'cache-evader-config');

        return $this;
    }
}
