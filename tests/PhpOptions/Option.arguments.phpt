<?php

use PhpOptions\Option;
use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

Assert::throws(function () {
	new Option('');
}, 'PhpOptions\InvalidArgumentException');

$options = new Options('a,b:,c::');
list($a, $b, $c) = $options->parse(array('-ab-ignore-value', '-c'))->getOptions();

Assert::same(Option::ARG_NONE, $a->argDemand);
Assert::same(Option::ARG_REQUIRED, $b->argDemand);
Assert::same(Option::ARG_OPTIONAL, $c->argDemand);
