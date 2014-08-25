<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$options = new Options;

Assert::throws(function () use ($options) {
	$options->parse(array('-foo'));
}, 'PhpOptions\UnknownOptionException', "Unknown short option 'f'.");

Assert::throws(function () use ($options) {
	$options->parse(array('--foo'));
}, 'PhpOptions\UnknownOptionException', "Unknown long option 'foo'.");

// test pass
$options->parse(array('foo'));

////////////////

$options->setOption('foo');

Assert::throws(function () use ($options) {
	$options->parse(array('-fvalue'));
}, 'PhpOptions\UnknownOptionException', "Unknown short option 'v'.");

Assert::throws(function () use ($options) {
	$options->parse(array('--foo=value'));
}, 'PhpOptions\UnexpectedArgumentException', "Option 'foo' doesn't expect an argument.");

// test pass
$options->parse(array('-f', 'value'));
$options->parse(array('--foo', 'value'));
