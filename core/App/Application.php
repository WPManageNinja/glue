<?php

namespace WPMNForm\Core\App;

use ArrayAccess;
use WPMNForm\Core\Exception\ExceptionHandler;
use WPMNForm\Core\Exception\UnResolveableEntityException;

class Application implements ArrayAccess
{
	use PathsAndUrls, AliasLoader, WPHelpers, HasAttributes;

	/**
	 * Framework Version
	 */
	const VERSION = '1.0.0';

	/**
	 * The App instance
	 * @var string
	 */
	protected static $instance = null;
	
	/**
	 * Plugin's root/entry file
	 * @var string
	 */
	protected $baseFile = null;
	
	/**
	 * The plugin.php file for namespace and namespace_map
	 * @var array
	 */
	protected $settings = [];

	/**
	 * Determins if the alias loader was registered
	 * @var boolean
	 */
	protected $isAliasLoader = false;
	
	/**
	 * Registered aliases
	 * @var array
	 */
	protected static $aliases = array();

	/**
	 * Registered components
	 * @var array
	 */
	protected static $container = array();
	
	/**
	 * Registered singleton components
	 * @var array
	 */
	protected static $singletons = array();

	/**
	 * Callbacks for booted event
	 * @var array
	 */
	protected $bootedCallbacks = array();

	/**
	 * Get application version
	 * @return string
	 */
	public function version()
	{
		return self::VERSION;
	}

	/**
	 * Init the application
	 * @param string $file (root plugin file)
	 * @param array $settings (root plugin.php)
	 */
	public function __construct($file, array $settings)
	{
		$this->boot($file, $settings);
		$this->fireCallbacks($this->bootedCallbacks);
	}

	/**
	 * Boot the application
	 * @param string $file (root plugin file)
	 * @param array $settings (root plugin.php)
	 * @return void
	 */
	public function boot($file, $settings)
	{
		$this->init($file, $settings);
		$this->setAppBaseBindings();
		$this->setExceptionHandler();
		$this->bootstrapWith($this->getEngineProviders());
		$this->bootstrapWith($this->getPluginProviders());
	}

	/**
	 * Set base dependencies of application
	 * @param string $file (root plugin file)
	 * @param array $settings (root plugin.php)
	 * @return void
	 */
	public function init($file, $settings)
	{
		$this->baseFile = $file;
		$this->settings = $settings;
	}

	/**
	 * Register application base bindings
	 * @return  void
	 */
	protected function setAppBaseBindings()
	{
		$this->bindAppInstance();
		$this->registerAppPaths();
		$this->registerAppUrls();
	}

	/**
	 * Bind application instance
	 * @return  void
	 */
	protected function bindAppInstance()
	{
		BaseAlias::setApplicationInstance($this);
	}

	/**
	 * Set Application paths
	 * @return void
	 */
	protected function registerAppPaths()
	{
		$path = plugin_dir_path($this->baseFile);
		$this->bind('path.base', $path);
		$this->bind('path.core', $path.'core/');
		$this->bind('path.app', $path.'plugin/');
		$this->bind('path.resource', $path.'resources/');
		$this->bind('path.storage', $path.'storage/');
		$this->bind('path.view', $path.'resources/views/');
		$this->bind('path.asset', $path.'resources/assets/');
	}

	/**
	 * Set Application urls
	 * @return void
	 */
	protected function registerAppUrls()
	{
		$url = plugin_dir_url($this->baseFile);
		$this->bind('url.base', $url);
		$this->bind('url.resource', $url.'resources/');
		$this->bind('url.asset', $url.'resources/assets/');
		$this->bind('url.dist', $url.'dist/');
	}

	/**
	 * Set Application Exception Handler
	 * @return void
	 */
	protected function setExceptionHandler()
	{
		return new ExceptionHandler($this);
	}

	/**
	 * Boot application with bootstrappers
	 * @param  array $bootstrappers/providers
	 * @return void
	 */
	protected function bootstrapWith(array $bootstrappers)
	{
		$instances = [];

		foreach ($bootstrappers as $bootstrapper) {
			$instances[] = $instance = new $bootstrapper;
			$instance->booting($this);
		}

		if (!$this->isAliasLoader) {
			$this->registerAliasLoader();
			$this->isAliasLoader = true;
		}

		foreach ($instances as $object) {
			if (method_exists($object, 'booted')) {
				$object->booted($this);
			}
		}
	}

	/**
	 * Get engine/core providers
	 * @return array
	 */
	protected function getEngineProviders()
	{
		$config = include $this->corePath('config.php');

		return $config['providers'];
	}

	/**
	 * Get plugin providers
	 * @return array
	 */
	protected function getPluginProviders()
	{
		$bootstrappers = $this->make('config')->get('providers');
		
        if (!is_admin()) {
            unset($bootstrappers['backend']);
        } else {
            unset($bootstrappers['frontend']);
        }

        return call_user_func_array('array_merge', $bootstrappers);
	}

	/**
	 * Bind an instance into application registry
	 * @param  string $key identifier
	 * @param  mixed $value
	 * @param  string $alias [optional alias]
	 * @return void
	 */
	public function bind($key, $value, $alias = null)
	{
		static::$container[$key] = $value;
		if ($alias) {
			static::$aliases[$alias] = $key;
		}
	}

	/**
	 * Bind a singleton instance into application registry
	 * @param  string $key identifier
	 * @param  mixed $value
	 * @param  string $alias [optional alias]
	 * @return void
	 */
	public function bindSingleton($key, $value, $alias = null)
	{
		static::$singletons[$key] = $value;
		if ($alias) {
			static::$aliases[$alias] = $key;
		}
	}

	/**
	 * Register an alias for a registered key/component
	 * @param  string $key
	 * @param  string $alias
	 * @return string    
	 * @throws \Core\Exception\UnResolveableEntityException    
	 */
	public function alias($key, $alias)
	{
		if (isset(static::$container[$key])) {
			return static::$aliases[$alias] = $key;
		}

		throw new UnResolveableEntityException(
			'No component is registered with key ['.$key.'] in the container.'
		);
	}

	/**
	 * Resolve in instance from application registry
	 * @param  string $key
	 * @return mixed
	 */
	public function make($key = null)
	{
		if (!$key) {
			return $this;
		}

		if (isset(static::$container[$key])) {
			return $this->resolveItem(static::$container[$key]);
		}

		if (isset(static::$singletons[$key])) {
			return static::$container[$key] = $this->resolveItem(
				static::$singletons[$key]
			);
		}
	}

	/**
	 * Normalized resolve helper
	 * @param  mixed
	 * @return mixed
	 */
	protected function resolveItem($item)
	{
		if (is_callable($item)) {
			return $item($this);
		}
		return $item;
	}

	/**
	 * Application's callbacks parser
	 * @param  mixed $args
	 * @return mixed
	 */
	public function parseHandler($args)
    {
        $args = is_array($args) ? $args : func_get_args();

        if (is_callable($args[0])) {
            return $args[0];
        } elseif (is_string($args[0])) {
            if (strpos($args[0], '@')) {
                list($class, $method) = explode('@', $args[0]);
                $instance = new $class;
                return [$instance, $method];
            } elseif (strpos($args[0], '::')) {
                list($class, $method) = explode('::', $args[0]);
                return [$class, $method];
            }
        } else {
            return $args;
        }
    }

    /**
     * Registers booted events
     * @param  mixed $callback
     * @return void
     */
    public function booted($callback)
    {
    	$this->bootedCallbacks[] = $this->parseHandler($callback);
    }

    /**
     * Fires application event's handlers
     * @param  array  $callbacks
     * @return void
     */
    protected function fireCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func_array($callback, [$this]);
        }
    }
}
