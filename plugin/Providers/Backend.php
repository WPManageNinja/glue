<?php

namespace WPMNForm\Plugin\Providers;

class Backend
{
	public function booting($plugin)
    {
    	$plugin->activating('WPMNForm\Plugin\Modules\Activator@activate');
    	$plugin->deactivating('WPMNForm\Plugin\Modules\Activator@deactivate');
    }

	public function booted($plugin)
    {
    	// ...
    }
}