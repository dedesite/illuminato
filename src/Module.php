<?php namespace Illuminato;
/**
 * Class that simplify module creation and which module can derivate from
 */
use Config;
use Lang;

class Module extends \Module {
	public $file;
	public function __construct() {
		$trace = debug_backtrace()[0];
		//Retrieve the file informations
		//To check if the module filename and directory are the same
		if(isset($trace['file'])){
			$dir = strtolower(dirname($trace['file']));
			$dir = last(explode('/', $dir));
			$base = strtolower(basename($trace['file'], '.php'));
			if($dir == $base){
				$this->name = $base;
			}
			else {
				dd('Error dirname and filename are not the same : '.$dir.' and '.$base);
			}
		}
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
}
