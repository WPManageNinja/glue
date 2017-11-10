<?php

namespace WPMNForm\Core\App;

use WPMNForm\Core\App\Application;

class Bootstrap
{
	protected static $baseDir = null;
	protected static $baseFile = null;
	protected static $namespace = null;
	protected static $pluginFile = null;
	protected static $namespaceMap = null;

	public static function boot($baseFile)
	{
		return new static($baseFile);
	}

	public function __construct($baseFile)
	{
		static::$baseFile = $baseFile;
		static::$baseDir = dirname($baseFile).'/';
		$this->validatePlugin() && $this->registerLoader();
		return new Application($baseFile, static::$pluginFile);
	}

	public function validatePLugin()
	{
	    if(!file_exists($file = static::$baseDir.'plugin.php')) {
	        die('The [plugin.php] file is missing from "'.static::$baseDir.'" directory.');
	    }

	    static::$pluginFile = include $file;
	    if (!static::$namespace = @static::$pluginFile['namespace']) {
	        die('The [namespace] is not specified or invalid in "'.$file.'" file.');
	    }

	    static::$namespaceMap = @static::$pluginFile['namespace_map'];
	    if (!static::$namespaceMap || empty((array)static::$namespaceMap)) {
	        die('The [namespace_map] is not specified or invalid "'.$file.'" file.');
	    }

	    return true;
	}

	public function registerLoader()
	{
		spl_autoload_register([$this, 'loader']);
	}

	public function loader($class)
	{
        if (strpos($class, static::$namespace) === false) {
            return;
        }

        $className = str_replace(
            '\\', '/', trim(str_replace(static::$namespace, '', $class), '\\')
        );

        foreach (static::$namespaceMap as $key => $value) {
            if (strpos($className, $key) !== false) {
                $file = static::$baseDir.str_replace($key, $value, $className).'.php';
                break;
            }
        }

        if (isset($file) && is_readable($file)) {
            include $file;
        }
	}
}