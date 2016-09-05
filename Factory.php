<?php

namespace Apollo16\Core\Module;

use Composer\Autoload\ClassLoader;
use Illuminate\Contracts\Foundation\Application;

/**
 * Module Factory.
 *
 * @author      mohammad.anang  <m.anangnur@gmail.com>
 */

class Factory
{
    /**
     * Laravel application.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * List of registered module.
     *
     * @var array
     */
    protected $modules = [];

    /**
     * Composer class loader.
     *
     * @var \Composer\Autoload\ClassLoader
     */
    protected $loader;

    /**
     * Create new module factory instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->loader = $this->app->make(ClassLoader::class);
    }

    /**
     * Add new module to the factory.
     *
     * @param \Apollo16\Core\Module\Json $module
     * @param boolean $boot
     */
    public function push(Json $module, $boot = true)
    {
        $this->modules[$module->getModuleName()] = $module;

        if ($boot) $this->bootIfNotBooted($module->getModuleName());
    }

    /**
     * Create new module configurator.
     *
     * @param $filePath
     * @param bool|true $register
     * @return \Apollo16\Core\Module\Json
     */
    public function create($filePath, $register = true)
    {
        $new = new Json($filePath);

        if ($register) $this->push($new);

        return $new;
    }

    /**
     * Get module by it's name.
     *
     * @param $name
     * @return null | \Apollo16\Core\Module\Json
     */
    public function get($name)
    {
        if ($this->exists($name)) return $this->modules[$name];

        return null;
    }

    /**
     * Check if the module exist.
     *
     * @param $name
     * @return bool
     */
    public function exists($name)
    {
        return array_key_exists($name, $this->modules);
    }

    /**
     * Boot Module if not booted.
     *
     * @param $name
     * @return boolean
     */
    protected function bootIfNotBooted($name)
    {
        if ($this->exists($name) AND ! in_array($name, $this->booted)) {
            // boot class Autoloader first
            $this->bootClassAutoLoader($this->get($name));

            // boot service providers
            $this->bootServiceProvider($this->get($name));

            // boot routes
            $this->bootRoutes($this->get($name));

            array_push($this->booted, $name);

            return true;
        }

        return false;
    }

    /**
     * Boot module service provider.
     *
     * @param \Apollo16\Core\Module\Json $module
     */
    protected function bootServiceProvider(Json $module)
    {
        foreach($module->getModuleServiceProviders() as $provider)
        {
            $this->app->register($provider);
        }
    }

    /**
     * Boot module class auto loader.
     *
     * @param \Apollo16\Core\Module\Json $module
     */
    protected function bootClassAutoLoader(Json $module)
    {
        $autoload = $module->getModuleAutoLoad();

        // boot classmap
        if (array_key_exists('classmap', $autoload)) {
            $this->loader->addClassMap($autoload['classmap']);
        }

        // boot psr-0
        if(array_key_exists('psr-0', $autoload)) {
            foreach($autoload['psr-0'] as $prefix => $path)
            {
                $this->loader->add($prefix, $module->getBasePath() . DIRECTORY_SEPARATOR . $path);
            }
        }

        // boot psr-4
        if(array_key_exists('psr-4', $autoload)) {
            foreach($autoload['psr-4'] as $prefix => $path)
            {
                $this->loader->addPsr4($prefix, $module->getBasePath() . DIRECTORY_SEPARATOR . $path);
            }
        }
    }

    /**
     * Boot module route configuration.
     *
     * @param \Apollo16\Core\Module\Json $module
     */
    protected function bootRoutes(Json $module)
    {
        $routes = $module->getModuleRoutes();

        foreach($routes as $route)
        {
            $this->app->make($route)->bind($this->app->make('router'));
        }
    }
}