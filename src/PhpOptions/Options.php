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
	private $opts = array();
	private $shortOpts = array();
	private $longOpts = array();

	/** @var array */
	private $arguments = array();

	/** @var array */
	private $triggeredOpts = array();

	/** @var Option|NULL */
	private $wantsArg = NULL;

	public function setOption($name, $shortName = Option::INFER, $longName = Option::INFER)
	{
		$argDemand = Option::ARG_NONE;
		if (preg_match('/^(.*?)(::?)(.*)$/', $name, $m)) {
			$name = $m[1];
			$argDemand = strlen($m[2]) === 1 ? Option::ARG_REQUIRED : Option::ARG_OPTIONAL;
		}
		$opt = new Option($name, $argDemand, $shortName, $longName);
		$this->opts[$opt->name] = $opt;
		$this->shortOpts[$opt->shortName] = $opt;
		$this->longOpts[$opt->longName] = $opt;
		return $this;
	}

	/**
	 * Parses $args for options.
	 * @param  array  $args
	 * @return self
	 */
	public function parse(array $args)
	{
		$this->resetValues();
		$nonPosixArgs = array();
		while (!empty($args)) {
			$arg = array_shift($args);
			if ($this->isOptionsEnd($arg)) {
				break;

			} elseif (($name = $this->isLongOpt($arg)) !== FALSE) {
				list($name, $value) = $this->separateOptAndValue($name);
				if (!isset($this->longOpts[$name])) {
					throw new UnknownOptionException($name);
				}
				$opt = clone $this->longOpts[$name];
				$this->triggeredOpts[] = $opt; //TODO:
				if ($value !== NULL) {
					if ($opt->argDemand === Option::ARG_NONE) {
						throw new UnexpectedArgumentException($opt);
					}
					$opt->value = $value;
					continue;

				} elseif ($opt->argDemand === Option::ARG_REQUIRED) {
					$this->catchArg($opt);
					continue;
				} else {
					$opt->value = TRUE;
				}

			} elseif (($opts = $this->isShortOpt($arg)) !== FALSE) {
				$this->parseShortOpts($opts);
				continue;

			} elseif ($this->wantsArg) {
				$this->wantsArg->value = $arg;
				$this->wantsArg = NULL;

			} else {
				$nonPosixArgs[] = $arg;
			}
			$this->checkRequiredArg();
		}
		$this->checkRequiredArg();
		$this->arguments = array_merge($nonPosixArgs, $args);
		return $this;
	}

	private function resetValues()
	{
		$this->triggeredOpts = array();
		$this->wantsArg = NULL;
	}

	private function catchArg(Option $opt)
	{
		$this->checkRequiredArg(); // je to zde potřeba?
		$this->wantsArg = $opt;
	}

	private function checkRequiredArg()
	{
		if ($this->wantsArg !== NULL) {
			throw new MissingArgumentException($this->wantsArg, FALSE);
		}
	}

	/**
	 * @param  string $opts
	 * @return Option|NULL
	 */
	private function parseShortOpts($opts)
	{
		for ($i = 0; $i < strlen($opts); $i++) {
			$flag = $opts[$i];
			if (!isset($this->shortOpts[$flag])) {
				throw new UnknownOptionException($flag, FALSE);
			}
			$opt = clone $this->shortOpts[$flag];
			$this->triggeredOpts[] = $opt; //TODO:
			if ($opt->argDemand !== Option::ARG_NONE) {
				$value = substr($opts, $i+1);
				if ($value === FALSE && $opt->argDemand === Option::ARG_REQUIRED) {
					$this->catchArg($opt);
				}
				$opt->value = $value;
				return;
			} else {
				$opt->value = TRUE;
			}
		}
	}

	/**
	 * @param  string  $arg
	 * @return string|bool
	 */
	protected function isShortOpt($arg)
	{
		if (preg_match('/^-(.+)/', $arg, $m)) {
			return $m[1];
		}
		return FALSE;
	}

	/**
	 * @param  string  $arg
	 * @return string|bool
	 */
	protected function isLongOpt($arg)
	{
		if (preg_match('/^--(.+)/', $arg, $m)) {
			return $m[1];
		}
		return FALSE;
	}

	/**
	 * @param  string $name
	 * @return array
	 */
	protected function separateOptAndValue($name)
	{
		if (preg_match('/(.+?)=(.*)/', $name, $m)) {
			return array($m[1], $m[2]);
		}
		return array($name, NULL);
	}

	/**
	 * @param  string  $arg
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
		return $opt->name;
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
