<?php namespace Illuminato;
/**
 * Class that simplify module creation and which module can derivate from
 */
class Module extends \Module {
	public $file;
	public function __construct() {
		if(isset($this->file)){
			$dir = strtolower(dirname($this->file));
			$dir = last(explode('/', $dir));
			$base = strtolower(basename($this->file, '.php'));
			if($dir == $base){
				$this->name = $base;
			}
			else {
				d('Error dirname and filename are not the same : '.$dir.' and '.$base);
			}
		}
		//Illuminato module need to be install before installing this one
		$this->dependencies = array('illuminato');
		parent::__construct();
	}
}
