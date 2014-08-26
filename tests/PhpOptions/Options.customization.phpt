<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

class CustomOptions extends Options
{
	protected function isShortOpt($arg)
	{
		if (preg_match('/^\.(.+)/', $arg, $m)) {
			return $m[1];
		}
		return FALSE;
	}

	protected function isLongOpt($arg)
	{
		if (preg_match('/^\/(.+)/', $arg, $m)) {
			return $m[1];
		}
		return FALSE;
	}

	protected function separateOptAndValue($name)
	{
		if (preg_match('/(.+?):(.*)/', $name, $m)) {
			return array($m[1], $m[2]);
		}
		return array($name, NULL);
	}

	protected function isOptionsEnd($arg)
	{
		return $arg === '//';
	}
}

$options = new CustomOptions('foo,bar:');

$opts = $options->parse(array('.fbbaz', '/foo', '/bar:first', '/bar', 'second', '//', 'other', 'args'))->getOptions();

Assert::count(5, $opts);

$values = array(TRUE, 'baz', TRUE, 'first', 'second');
foreach ($options as $opt => $value) {
	Assert::same(current($values), $value);
	next($values);
}

Assert::same(array('other', 'args'), $options->getArguments());
