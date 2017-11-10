<?php

namespace WPMNForm\Core\App;

trait PathsAndUrls
{
	public function baseFile()
	{
		return $this->baseFile;
	}

	public function path($path, $append = '')
	{
		$path = $this->make('path.'.$path);
		if($append) {
			$path .= ltrim(str_replace('\\', '/', $append), '/');
		}

		$subStr = substr($path, strrpos($path, '/'));
		if (strpos($subStr, '.') === false) {
			return rtrim($path, '/') . '/';
		}
		return rtrim($path, '/');
	}

	public function basePath($path = '')
	{
		return $this->path('base', $path);
	}

	public function corePath($path = '')
	{
		return $this->path('core', $path);
	}

	public function appPath($path = '')
	{
		return $this->path('app', $path);
	}

	public function resourcePath($path = '')
	{
		return $this->path('resource', $path);
	}

	public function storagePath($path = '')
	{
		return $this->path('storage', $path);
	}

	public function viewPath($path = '')
	{
		return $this->path('view', $path);
	}

	public function assetPath($path = '')
	{
		return $this->path('asset', $path);
	}

	public function url($url, $append = '')
	{
		$url = $this->make('url.'.$url);
		if($append) {
			$url .= ltrim(str_replace('\\', '/', $append), '/');
		}
		return trim($url, '/');
	}

	public function baseUrl($url = '')
	{
		return $this->url('base', $url);
	}

	public function assetUrl($url = '')
	{
		return $this->url('asset', $url);
	}

	public function distUrl($url = '')
	{
		return $this->url('dist', $url);
	}

	public function resourceUrl($url = '')
	{
		return $this->url('resource', $url);
	}
}