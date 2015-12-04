<?php namespace Illuminate\Foundation\Bootstrap;

use Illuminato\Config\IlluminatoRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository as RepositoryContract;

class LoadConfiguration {

	/**
	 * Bootstrap the given application.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @return void
	 */
	public function bootstrap(Application $app)
	{
		$items = [];

		// First we will see if we have a cache configuration file. If we do, we'll load
		// the configuration items from that file so that it is very quick. Otherwise
		// we will need to spin through every configuration file and load them all.
		/*if (file_exists($cached = $app->getCachedConfigPath()))
		{
			$items = require $cached;

			$loadedFromCache = true;
		}*/

		$app->instance('config', $config = new IlluminatoRepository($items));

		// Next we will spin through all of the configuration files in the configuration
		// directory and load each one into the repository. This will make all of the
		// options available to the developer for use in various parts of this app.
		if ( ! isset($loadedFromCache))
		{
			$this->loadConfigurationFiles($app->configPath(), $config);

		}

		date_default_timezone_set($config['app.timezone']);

		mb_internal_encoding('UTF-8');
	}

	/**
	 * Load all module's config files into there namespace
	 * Need to be call after all the modules have been bootstraped
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 */
	public function loadModuleConfigurationFiles(Application $app)
	{
		$config = $app['config'];
		//Now load all module's config into namespace
		foreach ($config->getModulePaths() as $namespace => $path) {
			$this->loadConfigurationFiles($path, $config, $namespace.'::');
		}
	}

	/**
	 * Load the configuration items from all of the files.
	 *
	 * @param  string  $configPath
	 * @param  \Illuminate\Contracts\Config\Repository  $config
	 * @param  string namespace
	 * @return void
	 */
	protected function loadConfigurationFiles($configPath, RepositoryContract $config, $namespace = '')
	{
		foreach ($this->getConfigurationFiles($configPath) as $key => $path)
		{
			$config->set($namespace.$key, require $path);
		}
	}

	/**
	 * Get all of the configuration files for the application.
	 *
	 * @param  string  $path
	 * @return array
	 */
	protected function getConfigurationFiles($path)
	{
		$files = [];

		foreach (Finder::create()->files()->name('*.php')->in($path) as $file)
		{
			$nesting = $this->getConfigurationNesting($path, $file);
			$files[$nesting.basename($file->getRealPath(), '.php')] = $file->getRealPath();
		}

		return $files;
	}

	/**
	 * Get the configuration file nesting path.
	 *
	 * @param string $configPath
	 * @param  \Symfony\Component\Finder\SplFileInfo  $file
	 * @return string
	 */
	private function getConfigurationNesting($configPath, SplFileInfo $file)
	{
		$directory = dirname($file->getRealPath());

		if ($tree = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR))
		{
			$tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree).'.';
		}

		return $tree;
	}

}
