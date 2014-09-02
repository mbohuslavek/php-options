<?php

use PhpOptions\Options;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$options = new Options('a,brown,Cyan');

$argSets = array(
	array('-A'),
	array('--a'),
	array('-B'),
	array('--Brown'),
	array('-c'),
	array('--Cyan'),
);
foreach ($argSets as $args) {
	Assert::throws(function () use ($options, $args) {
		$options->parse($args);
	}, 'PhpOptions\\UnknownOptionException');
}

$argSets = array(
	array('-a'),
	array('-b'),
	array('--brown'),
	array('-C'),
	array('--cyan'),
);
Assert::count(5, $options->parse(array('-abC', '--brown', '--cyan'))->getOptions());
