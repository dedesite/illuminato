<?php namespace Illuminato\Support;

use Illuminate\Support\ServiceProvider;

abstract class IlluminatoServiceProvider extends ServiceProvider
{
    /**
     * Register a config file namespace.
     *
     * @param  string  $path
     * @param  string  $namespace
     * @return void
     */
    protected function loadConfigsFrom($path, $namespace)
    {
        $this->app['config']->addNamespace($namespace, $path);
    }

    /**
     * Register a translation file namespace.
     *
     * @param  string  $path
     * @param  string  $namespace
     * @return void
     */
    protected static function s_loadTranslationsFrom($path, $namespace)
    {
        $app = app();
        $app['translator']->addNamespace($namespace, $path);
    }
}

