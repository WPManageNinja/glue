<?php defined('ABSPATH') or die;

return array(
	'name' => 'glue',

	'title' => 'Glue',

	'providers' => array(
		'common' => array(
			WPMNForm\Plugin\Providers\Common::class,
		),
		'backend' => array(
			WPMNForm\Plugin\Providers\Backend::class,
		),
		'frontend' => array(
			WPMNForm\Plugin\Providers\Frontend::class,
		),
	),
);