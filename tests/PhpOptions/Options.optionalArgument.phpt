<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$options = new Options;

$options->setOption('foo::');

Assert::count(1, $options->parse(array('-f'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::false($value);
}

Assert::count(1, $options->parse(array('-f--bar'))->getOptions());
foreach ($options as $opt => $value) {
	Assert::same('--bar', $value);
}
