<?php namespace Illuminato;

use Config;
use Lang;
use App;
use Artisan;
use DB;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminato\Providers\ModuleServiceProvider;

/**
 * Class that simplify module creation and which module can derivate from
 */
abstract class Module extends \Module {
	protected $namespace = '';
	//Each module got his own migrator and has migrations files
	protected $migrator = null;
	protected $migrationPath = '';

	public function __construct() {
		$this->checkName();
		//Illuminato module need to be install before installing this one
		$this->dependencies = array('illuminato');

		$details = $this->moduleDetails();
		isset($details['namespace']) ? $this->namespace = $details['namespace'] : $this->namespace = $this->name;
		if(!static::isEnabled($this->name))
			ModuleServiceProvider::loadUninstalledModuleTranslation($this->name, $this->namespace);
		isset($details['tab']) && $this->tab = $details['tab'];
		isset($details['version']) && $this->version = $details['version'];
		isset($details['author']) && $this->author = $details['author'];
		//Module are bootstrap 'aware' by default
		$this->bootstrap = true;
		parent::__construct();
		isset($details['displayName']) && $this->displayName = Lang::get($details['displayName']);
		isset($details['description']) && $this->description = Lang::get($details['description']);

		$this->createMigrator();
		if(static::isEnabled($this->name))
			$this->runMigrations();

		//Convenient Prestashop conf with prefix
		$this->conf = new Conf(strtoupper(get_class($this)));
	}

	/*
	 * Each module need to declare it's details via this function
	 * inspire from October CMS Plugin's registration file
	 * https://octobercms.com/docs/plugin/registration#registration-file
	 * @return Array
	 */
	abstract public function moduleDetails();

	public static function getInstalledModules()
	{
		return unserialize(\Configuration::get('ILLUMINATO_INSTALLED_MODULES'));
	}

	public static function setInstalledModules($modules)
	{
		\Configuration::updateValue('ILLUMINATO_INSTALLED_MODULES', serialize($modules));
	}

	public function addToModuleList()
	{
		$installed_modules = static::getInstalledModules();
		$installed_modules []= ['name' => $this->name, 'namespace' => $this->namespace];
		static::setInstalledModules($installed_modules);
	}

	public function removeFromModuleList()
	{
		$installed_modules = static::getInstalledModules();
		$modules = array_diff($installed_modules, ['name' => $this->name, 'namespace' => $this->namespace]);
		static::setInstalledModules($modules);
	}

	public function install()
	{
		// Call install parent method
		if (!parent::install())
			return false;

		$this->addToModuleList();

		if (!$this->runMigrations())
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

		$this->removeFromModuleList();

		if (!$this->resetMigrations())
			return false;

		return true;
	}

	protected function createMigrator()
	{
		$app = App::getInstance();
		//The module's migration table name is : ps_modulename_migrations
		$repo = new DatabaseMigrationRepository($app['db'], $this->name.'_migrations');
		if(!$repo->repositoryExists())
			$repo->createRepository();
		$this->migrator = new Migrator($repo, $app['db'], $app['files']);
		$this->migrationPath = _PS_MODULE_DIR_.$this->name.'/migrations';
	}

	protected function runMigrations()
	{
		$this->migrator->run($this->migrationPath);

		return true;
	}

	/**
	 * Call the artisan migrate:reset function
	 */
	protected function resetMigrations()
	{
		$files = $this->migrator->getMigrationFiles($this->migrationPath);
		$this->migrator->requireFiles($this->migrationPath, $files);
		$this->migrator->reset();

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
		$dir = _PS_MODULE_DIR_.$class_name;
		if(is_dir($dir) && is_file($dir.'/'.$class_name.'.php')) {
			$this->name = $class_name;
		}
		else {
			//@todo : better error handling...
			dd('Error directory or file not found with the name : '.$class_name);
		}
	}
}
