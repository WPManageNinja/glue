<?php

namespace WPMNForm\Core\Config;

class Config
{
	protected $config = [];

	public function __construct($baseFile)
	{
		$basePath = dirname($baseFile);

		if (file_exists($configFile = $basePath.'/plugin/config.php')) {
			$this->config = include $configFile;
		}
	}

	public function get($key = null, $default = null)
	{
		if (!$key) {
			return $this->config;
		} else {
			return isset($this->config[$key]) ? $this->config[$key] : $default;
		}
	}

	public function set($key, $value)
	{
		$this->config[$key] = $value;
		return $this;
	}

	public function __get($key = null)
	{
		return $this->get($key);
	}

	public function __set($key, $value)
	{
		return $this->set($key, $value);
	}
}