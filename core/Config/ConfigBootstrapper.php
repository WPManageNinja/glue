<?php

namespace WPMNForm\Core\Config;

class ConfigBootstrapper
{
	public function booting($plugin)
	{
		$plugin->bind('config', new Config($plugin->getBaseFile()), 'Config');
	}
}