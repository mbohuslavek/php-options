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

	public function __construct($name, $argDemand, $shortName = self::INFER, $longName = self::INFER)
	{
		$this->argDemand = $argDemand;
		$this->name = $name;
		$shortName === self::INFER && $shortName = $name{0};
		$longName === self::INFER && $longName = strtolower($name);
		$this->shortName = $shortName;
		$this->longName = $longName;
	}
}
