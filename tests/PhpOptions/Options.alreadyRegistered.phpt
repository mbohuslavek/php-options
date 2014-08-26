<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$options = new Options('mustafa,ibrahim');

Assert::throws(function () use ($options) {
	$options->setOption('mini');
}, 'PhpOptions\OptionAlreadyRegisteredException', "Short option 'm' is already registered.");

$options->setOption('Mini');

Assert::throws(function () use ($options) {
	$options->setOption('mini', NULL);
}, 'PhpOptions\OptionAlreadyRegisteredException', "Long option 'mini' is already registered.");

$options->setOption('mini', NULL, 'nimi');

Assert::throws(function () use ($options) {
	$options->setOption('foo', NULL, NULL);
}, 'PhpOptions\InvalidArgumentException', "Either short or long option name must be set for option 'foo'.");
