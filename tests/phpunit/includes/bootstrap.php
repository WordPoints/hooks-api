<?php

/**
 * Bootstrap for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

spl_autoload_register( 'wordpoints_hooks_api_phpunit_autoloader' );

$factory = WordPoints_PHPUnit_Factory::init();
$factory->register( 'entity', 'WordPoints_PHPUnit_Factory_For_Entity' );
$factory->register( 'hook_reaction', 'WordPoints_PHPUnit_Factory_For_Hook_Reaction' );
$factory->register( 'hook_reactor', 'WordPoints_PHPUnit_Factory_For_Hook_Reactor' );
$factory->register( 'hook_firer', 'WordPoints_PHPUnit_Factory_For_Hook_Firer' );
$factory->register( 'hook_event', 'WordPoints_PHPUnit_Factory_For_Hook_Event' );
$factory->register( 'hook_action', 'WordPoints_PHPUnit_Factory_For_Hook_Action' );
$factory->register( 'hook_condition', 'WordPoints_PHPUnit_Factory_For_Hook_Condition' );
$factory->register( 'post_type', 'WordPoints_PHPUnit_Factory_For_Post_Type' );
$factory->register( 'user_role', 'WordPoints_PHPUnit_Factory_For_User_Role' );

global $EZSQL_ERROR;
$EZSQL_ERROR = new WordPoints_PHPUnit_Error_Handler_Database();

// EOF
