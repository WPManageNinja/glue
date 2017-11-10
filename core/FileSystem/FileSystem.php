<?php

namespace WPMNForm\Core\FileSystem;

class FileSystem
{
	protected $baseFile = null;

	public function __construct($file)
	{
		$this->baseFile = $file;
	}

	public function has($path)
	{
		return file_exists($path);
	}

	public function isDir($path)
	{
		return $this->has($path) && is_dir($path);
	}

	public function makeDir($path)
	{
		return mkdir($path, 0777, true);
	}

	public function makeDirIfDoesnotExist($path)
	{
		if (!$this->has($path)) {
			return $this->makeDir($path);
		}
	}

	public function isFile($path)
    {
        return $this->has($path) && is_file($path);
    }

	public function put($path, $content, $flags = null)
	{
		return file_put_contents($path, $content, $flags);
	}

	public function append($path, $content)
	{
		return $this->put($path, $content, FILE_APPEND | LOCK_EX);
	}

	public function get($path)
	{
		return file_get_contents($path);
	}
}