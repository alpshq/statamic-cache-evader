<?php

namespace Alps\CacheEvader;

use Alps\CacheEvader\Http\Controllers\RenderController;
use Alps\CacheEvader\Http\Middleware\SpoofXsrfHeader;
use Alps\CacheEvader\Http\Middleware\StaticCache;
use Alps\CacheEvader\Modifiers\EvadeCache;
use Alps\CacheEvader\Tags\CacheEvaderPartial;
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
        CacheEvaderPartial::class,
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

        $this->bootAddonConfig();
    }

    public function bootAddon()
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $router->prependMiddlewareToGroup('web', SpoofXsrfHeader::class);

        $this->registerWebRoutes(function() {
            Route::get('cache-evader/ping', function() {
                return response(null, 204);
            })->name('cache-evader.ping');

            Route::get('cache-evader/render', 'RenderController')
                ->name('cache-evader.render');
        });

        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', [
                '--tag' => 'statamic-cache-evader',
                '--force' => true,
            ]);
        });
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
