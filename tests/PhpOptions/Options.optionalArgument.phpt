<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$options = new Options;

$options->setOption('foo::');

foreach ($options->parse(array('-f')) as $opt => $value) {
	Assert::false($value);
}

foreach ($options->parse(array('-f--bar')) as $opt => $value) {
	Assert::same('--bar', $value);
}
