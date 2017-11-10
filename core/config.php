<?php defined('ABSPATH') or die;

return array(
	'providers' => array(
		WPMNForm\Core\App\AppBootstrapper::class,
		WPMNForm\Core\Config\ConfigBootstrapper::class,
		WPMNForm\Core\Request\RequestBootstrapper::class,
		WPMNForm\Core\FileSystem\FileSystemBootstrapper::class,
		WPMNForm\Core\Validator\ValidatorBootstrapper::class,
		WPMNForm\Core\View\ViewBootstrapper::class,
	)
);