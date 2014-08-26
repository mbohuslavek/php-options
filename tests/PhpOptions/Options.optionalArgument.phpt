<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$options = new Options;

$options->setOption('foo::');

Assert::count(3, $options->parse(array('-f', '--foo', '--foo='))->getOptions());
foreach ($options as $opt => $value) {
	Assert::false($value);
}

Assert::count(2, $options->parse(array('-f--bar', '--foo=--bar'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::same('--bar', $value);
}
