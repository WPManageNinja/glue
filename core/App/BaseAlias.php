<?php

namespace WPMNForm\Core\App;

use WPMNForm\Core\Exception\UnResolveableEntityException;

Abstract class BaseAlias
{
	static $instance = null;

	public static function setApplicationInstance($instance)
	{
		static::$instance = $instance;
	}

    public static function __callStatic($method, $params)
	{
		try {
			return call_user_func_array([
				static::$instance->make(static::$key), $method
			], $params);
		} catch (\Exception $e) {
			throw new UnResolveableEntityException(
				'No component is registered with key ['.static::$key.'] in the container.'
			);
		}
	}
}
