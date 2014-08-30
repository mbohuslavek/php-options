<?php

/**
 * This file is part of the PhpOptions library.
 * Copyright (c) 2014 Michal Bohuslávek
 */

namespace PhpOptions;

/**
 * @author Michal Bohuslávek
 */
class Options implements \Iterator
{
	/** @var Option[] */
	protected $shortOpts = array();

	/** @var Option[] */
	protected $longOpts = array();

	/** @var Option[] */
	protected $triggeredOpts = array();

	/** @var array */
	protected $arguments = array();

	/** @var Option|NULL */
	private $wantsArg = NULL;


	public function __construct($options = NULL)
	{
		if ($options) {
			$opts = explode(',', $options);
			foreach ($opts as $opt) {
				$this->setOption(trim($opt));
			}
		}
	}

	/**
	 * @param string $name
	 * @param string $shortName
	 * @param string $longName
	 * @throws InvalidArgumentException
	 * @throws OptionAlreadyRegisteredException
	 */
	public function setOption($name, $shortName = Option::INFER, $longName = Option::INFER)
	{
		$argDemand = $this->inferArgDemand($name);
		$this->registerOption(new Option($name, $argDemand, $shortName, $longName));
		return $this;
	}

	protected function inferArgDemand(&$name)
	{
		if (preg_match('/^(.*?)(::?)$/', $name, $m)) {
			$name = $m[1];
			return strlen($m[2]) === 1 ? Option::ARG_REQUIRED : Option::ARG_OPTIONAL;
		}
		return Option::ARG_NONE;
	}

	protected function registerOption(Option $opt)
	{
		$registered = FALSE;
		foreach (array('short', 'long') as $prefix) {
			if (($name = $opt->{'get'.lcfirst($prefix).'Name'}()) === NULL) {
				continue;
			}
			$registered = TRUE;
			if (isset($this->{$prefix.'Opts'}[$name])) {
				throw new OptionAlreadyRegisteredException($name, $prefix === 'long');
			}
			$this->{$prefix.'Opts'}[$name] = $opt;
		}
		if ($registered === FALSE) {
			throw new InvalidArgumentException("Either short or long option name must be set for option '{$opt->getName()}'.");
		}
	}

	/**
	 * Parses $args for options. If $permuteArgs is set to FALSE,
	 * the first non-option argument stops option parsing.
	 * @param array $args
	 * @throws MissingArgumentException
	 * @throws UnexpectedArgumentException
	 * @throws UnknownOptionException
	 * @return self
	 */
	public function parse(array $args, $permuteArgs = TRUE)
	{
		$this->triggeredOpts = array();
		$this->wantsArg = NULL;
		$toPermuteArgs = array();
		while (!empty($args)) {
			$arg = array_shift($args);
			if ($this->isOptionsEnd($arg)) {
				break;

			} elseif (($opt = $this->isLongOpt($arg)) !== FALSE) {
				$this->checkArgAlreadyRequired();
				$this->parseLongOpt($opt);

			} elseif (($opts = $this->isShortOpt($arg)) !== FALSE) {
				$this->checkArgAlreadyRequired();
				$this->parseShortOpts($opts);

			} elseif ($this->wantsArg) {
				$this->wantsArg->value = $arg;
				$this->wantsArg = NULL;

			} else {
				$toPermuteArgs[] = $arg;
				if ($permuteArgs === FALSE) {
					break;
				}
			}
		}
		$this->checkArgAlreadyRequired();
		$this->arguments = array_merge($toPermuteArgs, $args);
		return $this;
	}

	private function parseLongOpt($opt)
	{
		list($name, $value) = $this->separateOptAndValue($opt);
		if (!isset($this->longOpts[$name])) {
			throw new UnknownOptionException($name);
		}
		$opt = clone $this->longOpts[$name];
		$this->triggeredOpts[] = $opt;
		if ($value === '' && $opt->getArgDemand() === Option::ARG_REQUIRED) {
			throw new MissingArgumentException($opt);

		} elseif ($value != NULL) { // != intentionally
			if ($opt->getArgDemand() === Option::ARG_NONE) {
				throw new UnexpectedArgumentException($opt);
			}
			$opt->value = $value;

		} elseif ($opt->getArgDemand() === Option::ARG_REQUIRED) {
			$this->wantsArg = $opt;
		}
	}

	private function checkArgAlreadyRequired()
	{
		if ($this->wantsArg !== NULL) {
			throw new MissingArgumentException($this->wantsArg, FALSE);
		}
	}

	private function parseShortOpts($opts)
	{
		for ($i = 0; $i < strlen($opts); $i++) {
			$flag = $opts[$i];
			if (!isset($this->shortOpts[$flag])) {
				throw new UnknownOptionException($flag, FALSE);
			}
			$opt = clone $this->shortOpts[$flag];
			$this->triggeredOpts[] = $opt;
			if ($opt->getArgDemand() !== Option::ARG_NONE) {
				$value = substr($opts, $i+1);
				if ($value === FALSE && $opt->getArgDemand() === Option::ARG_REQUIRED) {
					$this->wantsArg = $opt;
				}
				$opt->value = $value;
				return;
			}
		}
	}

	/**
	 * @param string $arg
	 * @return string|FALSE
	 */
	protected function isShortOpt($arg)
	{
		if (preg_match('/^-(.+)/', $arg, $m)) {
			return $m[1];
		}
		return FALSE;
	}

	/**
	 * @param string $arg
	 * @return string|FALSE
	 */
	protected function isLongOpt($arg)
	{
		if (preg_match('/^--(.+)/', $arg, $m)) {
			return $m[1];
		}
		return FALSE;
	}

	/**
	 * @param string $opt
	 * @return array [name, value]
	 */
	protected function separateOptAndValue($opt)
	{
		if (preg_match('/(.+?)=(.*)/', $opt, $m)) {
			return array($m[1], $m[2]);
		}
		return array($opt, NULL);
	}

	/**
	 * @param string $arg
	 * @return bool
	 */
	protected function isOptionsEnd($arg)
	{
		return $arg === '--';
	}

	public function getOptions()
	{
		return $this->triggeredOpts;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	////////////////

	public function current()
	{
		$opt = current($this->triggeredOpts);
		return $opt->value;
	}

	public function key()
	{
		$opt = current($this->triggeredOpts);
		return $opt->getName();
	}

	public function next()
	{
		next($this->triggeredOpts);
	}

	public function rewind()
	{
		reset($this->triggeredOpts);
	}

	public function valid()
	{
		return current($this->triggeredOpts) !== FALSE;
	}

}
