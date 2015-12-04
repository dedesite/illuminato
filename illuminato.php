<?php

class Illuminato extends Module {
	public function __construct() {
		$this->name = 'illuminato';
		$this->tab = 'others';
		$this->version = '0.1';
		$this->author = 'Andréas Livet';
		parent::__construct();
		$this->displayName = $this->l('Illuminato Shop');
		$this->description = $this->l('Provide a set of tools to help developping Prestashop modules within Laravel Philosophy.');
	}

	public function install()
	{
		// Call install parent method
		if (!parent::install())
			return false;
		Configuration::updateValue('ILLUMINATO_INSTALLED_MODULES', serialize([]));

		return true;
	}
}