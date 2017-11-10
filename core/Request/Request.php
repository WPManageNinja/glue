<?php

namespace WPMNForm\Core\Request;

class Request
{
	protected $get = [];
	protected $post = [];
	protected $request = [];
	
	public function __construct($get, $post)
	{
		$this->request = $this->clean(array_merge(
			$this->get = $get,
			$this->post = $post
		));
	}

	public function clean($request)
	{
		$clean = [];
		foreach ($request as $key => $value) {
			$key = trim(strip_tags(stripslashes($key)));
			$clean[$key] = is_array($value) ? $this->clean($value) : $this->trimAndStrip($value);
		}
		return $clean;
	}

	public function trimAndStrip($value)
	{
		return trim(strip_tags(stripslashes($value)));
	}

	public function set($key, $value)
	{
		$this->request[$key] = $value;
		return $this;
	}

	public function all()
	{
		return $this->get();
	}

	public function get($key = null, $default = null)
	{
		if (!$key) {
			return $this->request;
		} else {
			return isset($this->request[$key]) ? $this->request[$key] : $default;
		}
	}

	public function only($args)
	{
		$values = [];
		$keys = is_array($args) ? $args : func_get_args();
		foreach ($keys as $key) {
			$values[$key] = @$this->request[$key];
		}
		return $values;
	}

	public function except($args)
	{
		$values = [];
		$keys = is_array($args) ? $args : func_get_args();
		foreach ($this->request as $key => $value) {
			if (!in_array($key, $keys)) {
				$values[$key] = $this->request[$key];
			}
		}
		return $values;
	}
}