<?php namespace Illuminato\Config;

use Illuminate\Config\Repository;

class IlluminatoRepository extends Repository
{
    /**
     * All of the modules's config path
     *
     * @var array
     */
    protected $module_paths = [];
	/**
     * Add a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
    	$this->module_paths[$namespace] = realpath($hint);
    }

    public function getModulePaths()
    {
    	return $this->module_paths;
    }
}
