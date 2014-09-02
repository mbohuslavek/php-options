<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$options = new Options;

$options
	->setOption('foo:')
	->setOption('bar')
	;

$argSets = array(
	array('-f'),
	array('-f', '--'),
	array('-f', '--bar'),
	array('-f', '--bar', 'value'),
	array('-bf', '--bar', '-f', 'value'),
	array('--foo', '-b'),
	array('--foo=', 'value'),
);
foreach ($argSets as $args) {
	Assert::throws(function () use ($options, $args) {
		$options->parse($args);
	}, 'PhpOptions\\MissingArgumentException', "No argument for option 'foo'.");
}

Assert::count(1, $options->parse(array('-f--bar'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::same('--bar', $value);
}

Assert::count(1, $options->parse(array('--foo=--bar'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::same('--bar', $value);
}

Assert::count(1, $options->parse(array('--foo', 'bar'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::same('bar', $value);
}
