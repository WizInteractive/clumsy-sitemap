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
        $path = __DIR__.'/../..';
        $this->package('clumsy/sitemap', 'clumsy/sitemap', $path);
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoute();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    public function registerRoute()
    {
        $this->app['router']->get('sitemap.xml', [
            'as'     => 'clumsy.sitemap',
            'before' => $this->app['config']->get('clumsy/sitemap::config.before-filter'),
            'after'  => $this->app['config']->get('clumsy/sitemap::config.after-filter'),
            'uses'   => '\Clumsy\Sitemap\Controller@render',
        ]);
    }
}
