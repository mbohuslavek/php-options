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
	private $name, $shortName, $longName;

	/** @var int */
	private $argDemand;

	/** @var string|FALSE */
	public $value = FALSE;


	public function __construct($name, $argDemand = self::ARG_NONE, $shortName = self::INFER, $longName = self::INFER)
	{
		if (!is_string($name) || $name === '') {
			throw new InvalidArgumentException("Option name must be non-empty string.");
		}
		$this->name = $name;
		$this->argDemand = $argDemand;
		$this->shortName = $this->inferShortName($shortName);
		$this->longName = $this->inferLongName($longName);
	}

	protected function inferShortName($name)
	{
		if ($name === self::INFER) {
			return $this->name{0};
		}
		return $name ?: NULL;
	}

	protected function inferLongName($name)
	{
		if ($name === self::INFER) {
			return strlen($this->name) > 1 ? strtolower($this->name) : NULL;
		}
		return $name ?: NULL;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string|NULL
	 */
	public function getShortName()
	{
		return $this->shortName;
	}

	/**
	 * @return string|NULL
	 */
	public function getLongName()
	{
		return $this->longName;
	}

	/**
	 * @return int self::ARG_*
	 */
	public function getArgDemand()
	{
		return $this->argDemand;
	}

}
