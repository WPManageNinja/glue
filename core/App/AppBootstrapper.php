<?php

namespace WPMNForm\Core\App;

class AppBootstrapper
{
	public function booting($plugin)
    {
        $plugin->bind('app', $plugin, 'App');
    }

	public function booted($plugin)
    {
    	$plugin->booted(function($plugin) {
    		include $plugin->appPath('Routes/routes.php');
    	});
    }
}