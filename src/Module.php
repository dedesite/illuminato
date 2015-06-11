<?php namespace Illuminato;

use Config;
use Lang;
use App;
use Artisan;

/**
 * Class that simplify module creation and which module can derivate from
 */
class Module extends \Module {
	public function __construct() {
		$this->checkName();
		//Illuminato module need to be install before installing this one
		$this->dependencies = array('illuminato');
		$this->tab = Config::get('module.tab');
		$this->version = Config::get('module.version');
		$this->author = Config::get('module.author');
		//Module are bootstrap 'aware' by default
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = Lang::get(Config::get('module.displayName'));
		$this->description = Lang::get(Config::get('module.description'));
	}

	public function install()
	{
		// Call install parent method
		if (!parent::install())
			return false;

		if (!$this->applyMigration())
			return false;

		if(!$this->autoRegisterHooks())
			return false;

		return true;
	}

	public function uninstall()
	{
		// Call install parent method
		if (!parent::uninstall())
			return false;

		if (!$this->resetMigration())
			return false;

		return true;
	}

	/**
	 * Call the artisan migrate function
	 */
	protected function applyMigration()
	{
		try
		{
			//Use the force option to avoid confirmation
			Artisan::call('migrate', ['--force' => true]);
		}
		catch (Exception $e)
		{
			//TODO : Add some error message in admin panel
			return false;
		}

		return true;
	}

	/**
	 * Call the artisan migrate:reset function
	 */
	protected function resetMigration()
	{
		//Need to manually load all migration files
		$path = App::migrationPath();
		$app = App::getInstance();
		$files = $app['migrator']->getMigrationFiles($path);
		$app['migrator']->requireFiles($path, $files);
		try
		{
			//Use the force option to avoid confirmation
			Artisan::call('migrate:reset', ['--force' => true]);
		}
		catch (Exception $e)
		{
			//TODO : Add some error message in admin panel
			return false;
		}

		return true;
	}

	/**
	 * Find all function that start with "hook" and register them
	 */
	protected function autoRegisterHooks()
	{
		$class_methods = get_class_methods($this);
		$hook_methods = preg_grep("/^hook/", $class_methods);
		foreach ($hook_methods as $hook)
		{
			if(!$this->registerHook(substr($hook, 4)))
				return false;
		}

		return true;
	}

	protected function checkName()
	{
		$class_name = strtolower(get_class($this));
		$dir = _PS_MODULE_DIR_.'/'.$class_name;
		if(is_dir($dir) && is_file($dir.'/'.$class_name.'.php')) {
			$this->name = $class_name;
		}
		else {
			//TODO : better error handling...
			dd('Error directory or file not found with the name : '.$class_name);
		}
	}
}
