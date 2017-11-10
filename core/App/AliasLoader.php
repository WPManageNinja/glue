<?php

namespace WPMNForm\Core\App;

trait AliasLoader
{
	protected function registerAliasLoader()
	{
		spl_autoload_register([$this, 'aliasLoader'], true, true);
	}
	
	protected function aliasLoader($class)
	{
		$namespace = $this->getNamespace();
		if ($namespace == substr($class, 0, strlen($namespace))) {
			$parts = explode('\\', $class);
			if (count($parts) == 2) {
				if (array_key_exists($parts[1], static::$aliases)) {
					$fileSystem = $this->make('fs');
					$containerKey = static::$aliases[$alias = $parts[1]];
					$path = $this->storagePath('framework/aliases');
					if (!$fileSystem->has($file = $path.$alias.'.php')) {
						$fileSystem->makeDirIfDoesnotExist($path);
						$fileSystem->put($file, $this->getFIleData($alias, $containerKey));
					}
					include $file;
				}
			}
		}
	}

	protected function getFIleData($alias, $key)
	{
		return str_replace(
			['DummyNamespace', 'DummyClass', 'DummyKey'],
			[$this->getNamespace(), $alias, $key],
			$this->fs->get($this->corePath('App/Alias.stub'))
		);
	}
}