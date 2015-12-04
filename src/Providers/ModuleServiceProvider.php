<?php namespace Illuminato\Providers;

use Illuminato\Support\IlluminatoServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;

class ModuleServiceProvider extends IlluminatoServiceProvider
{
	/**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }

	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$installed_modules = \Illuminato\Module::getInstalledModules();

		foreach ($installed_modules as $key => $module) 
		{
			$module_dir = _PS_MODULE_DIR_.$module['name'];
			if(\Module::isEnabled($module['name']))
			{
				if(is_dir($module_dir.'/resources/views'))
				$this->loadViewsFrom($module_dir.'/resources/views', $module['namespace']);
				if(is_dir($module_dir.'/resources/lang'))
					$this->loadTranslationsFrom($module_dir.'/resources/lang', $module['namespace']);
				if(is_dir($module_dir.'/config'))
					$this->loadConfigsFrom($module_dir.'.config', $module['namespace']);	
			}
		}

		//Now we can load the module's config files
		$configLoader = new LoadConfiguration();
		$configLoader->loadModuleConfigurationFiles(app());
	}

/*
 * Load translations for uninstalled modules, this way their names
 * Will be display properlly in Prestashop module list
 * A bit hacky bit it works ;)
 */
	public static function loadUninstalledModuleTranslation($module_name, $module_namespace)
	{
		$module_dir = _PS_MODULE_DIR_.$module_name;
		if(is_dir($module_dir.'/resources/lang'))
			static::s_loadTranslationsFrom($module_dir.'/resources/lang', $module_namespace);
	}
}