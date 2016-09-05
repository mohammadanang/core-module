<?php

namespace Apollo16\Core\Module;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Symfony\Component\Finder\Finder;

/**
 * Apollo16 module service provider.
 *
 * @author      mohammad.anang  <m.anangnur@gmail.com>
 */

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Boot the service providers
     */
    public function boot()
    {
        // find module.json on directories
        $finder = new Finder();
        $iterator = $finder->files()->name('module.json')->in(base_path('modules'));

        /**
         * @var \Apollo16\Core\Module\Factory $factory
         */
        $factory = $this->app['apollo16.modules'];

        foreach($iterator as $file)
        {
            $factory->create($file->getRealPath());
        }
    }

    /**
     * Register the service providers.
     */
    public function register()
    {
        $this->app->bind('apollo16.modules', function($app) {
            $factory = new Factory($app);

            return $factory;
        }, true);
    }
}