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

Assert::count(2, $options->parse(array('-f', 'value', '--foo', 'value'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::same('foo', $opt);
	Assert::false($value);
}
