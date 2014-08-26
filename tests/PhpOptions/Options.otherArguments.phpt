<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$options = new Options;
$options
	->setOption('foo')
	->setOption('bar:');

$tests = array(
	array(TRUE, array('Mustafa', 'Ibrahim'), array('-fb', 'play', 'Mustafa', 'Ibrahim')),
	array(TRUE, array('Mustafa', 'Ibrahim'), array('Mustafa', 'Ibrahim', '-fb', 'play')),
	array(FALSE, array('Mustafa', 'Ibrahim', '-b', 'play'), array('-f', 'Mustafa', 'Ibrahim', '-b', 'play')),
	array(TRUE, array('Mustafa', 'Ibrahim', '-b', 'play'), array('-f', '--', 'Mustafa', 'Ibrahim', '-b', 'play')),
);

foreach ($tests as $test) {
	list($permuteArgs, $expected, $actual) = $test;
	Assert::same($expected, $options->parse($actual, $permuteArgs)->getArguments());
}
