<?php

namespace Clumsy\Sitemap;

use Illuminate\Support\ServiceProvider;

class SitemapServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $endpoint;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config.php', 'clumsy.sitemap');
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('clumsy/sitemap.php'),
        ], 'config');

        $this->registerRoute();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    public function registerRoute()
    {
        $this->app['router']->get('sitemap.xml', [
            'as'  => 'clumsy.sitemap',
            'middleware' => $this->app['config']->get('clumsy.sitemap.middleware'),
            'uses' => '\Clumsy\Sitemap\Controller@render',
        ]);
    }
}
