<?php

/**
 * Bootstrap for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

spl_autoload_register( 'wordpoints_hooks_api_phpunit_autoloader' );

$factory = WordPoints_PHPUnit_Factory::init();
$factory->register( 'hook_reaction', 'WordPoints_PHPUnit_Factory_For_Hook_Reaction' );
$factory->register( 'hook_reactor', 'WordPoints_PHPUnit_Factory_For_Hook_Reactor' );
$factory->register( 'hook_event', 'WordPoints_PHPUnit_Factory_For_Hook_Event' );
$factory->register( 'hook_action', 'WordPoints_PHPUnit_Factory_For_Hook_Action' );

// EOF
