<?php

/**
 * This file is part of the PhpOptions library.
 * Copyright (c) 2014 Michal Bohuslávek
 */

namespace PhpOptions;

/**
 * @author Michal Bohuslávek
 */
class Option
{
	const INFER = TRUE;

	const ARG_NONE = 0;
	const ARG_OPTIONAL = 1;
	const ARG_REQUIRED = 2;

	/** @var string */
	public $name, $shortName, $longName, $description;

	/** @var string */
	public $value = FALSE;

	public $argDemand;


	public function __construct($name, $argDemand = self::ARG_NONE, $shortName = self::INFER, $longName = self::INFER)
	{
		if (!is_string($name) || $name === '') {
			throw new InvalidArgumentException("Option name must be non-empty string.");
		}
		$this->name = $name;
		$this->argDemand = $argDemand;
		$this->shortName = $this->sanitizeName($shortName, $name{0});
		$this->longName = $this->sanitizeName($longName, strtolower($name));
	}

	private function sanitizeName($value, $default)
	{
		if ($value === self::INFER) {
			return $default;
		}
		return $value ?: NULL;
	}

}
