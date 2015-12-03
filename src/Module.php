<?php namespace Illuminato;

use Config;
use Lang;
use App;
use Artisan;
use DB;

/**
 * Class that simplify module creation and which module can derivate from
 */
class Module extends \Module {
	public function __construct() {
		$this->checkName();
		//Illuminato module need to be install before installing this one
		$this->dependencies = array('illuminato');
		$configName = strtolower(get_class($this));
		$this->tab = Config::get($configName.'.tab');
		$this->version = Config::get($configName.'.version');
		$this->author = Config::get($configName.'.author');
		//Module are bootstrap 'aware' by default
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = Lang::get(Config::get($configName.'.displayName'));
		$this->description = Lang::get(Config::get($configName.'.description'));
		if(static::isEnabled($this->name))
			$this->applyNewMigrations();
		//Convenient Prestashop conf with prefix
		$this->conf = new Conf(strtoupper(get_class($this)));
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

	protected function applyNewMigrations()
	{
		//Check if there are some new migrations
		if($this->isNewMigrationsAvailable())
		{
			$this->applyMigration();
		}
	}

	protected function isNewMigrationsAvailable()
	{
		$files = App::migrationFiles();
		$last_migration = last($files);
		//No migration file
		if(!$last_migration)
			return false;

		//@todo : use DB:: .It'is not available, don't know why
		$app = App::getInstance();
		$last_db_migration = $app['db']->table('migrations')
			->orderBy('migration', 'desc')
			->first();
		//No migration applied
		if($last_db_migration === null)
			return true;

		return $last_migration != $last_db_migration->migration;
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
			//@todo : Add some error message in admin panel
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
		$app = App::getInstance();
		$path = App::migrationPath();
		$files = App::migrationFiles();
		$app['migrator']->requireFiles($path, $files);
		try
		{
			//Use the force option to avoid confirmation
			Artisan::call('migrate:reset', ['--force' => true]);
		}
		catch (Exception $e)
		{
			//@todo : Add some error message in admin panel
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
			//@todo : better error handling...
			dd('Error directory or file not found with the name : '.$class_name);
		}
	}
}
