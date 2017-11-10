<?php

namespace WPMNForm\Core\Validator;

class Validator
{
	protected $baseFile = null;

	public function __construct($file)
	{
		$this->baseFile = $file;
	}
}