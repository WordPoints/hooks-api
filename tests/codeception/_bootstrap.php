<?php

/**
 * This is global bootstrap for autoloading for the codeception tests.
 *
 * @since 1.0.0
 */

Codeception\Util\Autoload::addNamespace(
	'WordPoints\Tests\Codeception'
	, __DIR__ . '/_support'
);

// EOF
