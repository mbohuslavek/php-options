<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$options = new Options;

$options
	->setOption('foo:')
	->setOption('bar')
	;

Assert::throws(function () use ($options) {
	$options->parse(array('-f'));
}, 'PhpOptions\MissingArgumentException', "No argument for option 'foo'.");

Assert::throws(function () use ($options) {
	$options->parse(array('-f', '--'));
}, 'PhpOptions\MissingArgumentException', "No argument for option 'foo'.");

Assert::count(1, $options->parse(array('-f--bar'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::same('--bar', $value);
}

Assert::count(1, $options->parse(array('--foo=--bar'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::same('--bar', $value);
}

Assert::throws(function () use ($options) {
	$options->parse(array('-f', '--bar'));
}, 'PhpOptions\MissingArgumentException', "No argument for option 'foo'.");

Assert::throws(function () use ($options) {
	$options->parse(array('--foo', '-b'));
}, 'PhpOptions\MissingArgumentException', "No argument for option 'foo'.");

Assert::count(1, $options->parse(array('--foo', 'bar'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::same('bar', $value);
}
