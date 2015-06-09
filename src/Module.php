<?php namespace Illuminato;

use Config;
use Lang;
use App;

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

	public function install() {
		// Call install parent method
		if (!parent::install())
			return false;
		//App::make('migrate')->run('path/to/migrations');
	}

	protected function checkName() {
		$class_name = strtolower(get_class($this));
		$dir = _PS_MODULE_DIR_.'/'.$class_name;
		if(is_dir($dir) && is_file($dir.'/'.$class_name.'.php')) {
			$this->name = $class_name;
		}
		else {
			dd('Error directory or file not found with the name : '.$class_name);
		}
	}
}
