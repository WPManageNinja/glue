<?php

namespace WPMNForm\Plugin\Modules;

use WPMNForm\App;

class AjaxHandler
{
	public function handle()
	{
		$request = App::make('request');
		
		// $v = App::make('validator');

		// var_dump($v);

		// die;

		wp_send_json($request->all());
	}
}