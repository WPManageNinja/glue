<?php

namespace WPMNForm\Core\View;

class View
{
	protected $plugin = null;

	public function __construct($plugin)
	{
		$this->plugin = $plugin;
	}

	public function make($path, $data = [])
	{
		$path = str_replace('.', DIRECTORY_SEPARATOR, $path);
		$file = $this->plugin->viewPath().$path.'.php';
		if (file_exists($file)) {
			ob_start();
			extract($data);
			include $file;
			return ob_get_clean();
		}
		die("$file doesn't exists.");
	}
}