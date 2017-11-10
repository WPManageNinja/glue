<?php

namespace WPMNForm\Core\Request;

class RequestBootstrapper
{
	public function booting($plugin)
	{
		$plugin->bindSingleton('request', function($plugin) {
			return new Request($_GET, $_POST);
		}, 'Request');
	}
}