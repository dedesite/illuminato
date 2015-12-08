<?php namespace Illuminato;

/*
 * Simple wrapper arround some Configuration functions which add module
 * name to config values
 */
class Conf
{
	protected $prefix = "";

	public function __construct($prefix)
	{
		$this->setPrefix($prefix);
	}

	public function get($keys)
	{
		if(is_array($keys))
		{
			$properties = [];
			foreach ($keys as $ind => $key) {
				$properties[$key] = \Configuration::get($this->prefix.$key);
			}
			return $properties;	
		}
		else
		{
			return \Configuration::get($this->prefix.$keys);
		}
	}

	public function set(Array $properties)
	{
		foreach ($properties as $key => $value) {
			\Configuration::updateValue($this->prefix.$key, $value);
		}
	}

	public function del($keys)
	{
		if(is_array($keys))
		{
			foreach ($keys as $ind => $key) {
				\Configuration::deleteByName($this->prefix.$key);
			}
		}
		else
		{
			\Configuration::deleteByName($this->prefix.$keys);
		}
	}

	public function setPrefix($prefix)
	{
		$this->prefix = $prefix.'_';
	}
}