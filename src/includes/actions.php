<?php

/**
 * Actions and filters of the module.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

add_action( 'wordpoints_init_app-apps', 'wordpoints_apps_init' );
add_action( 'wordpoints_init_app-entities', 'wordpoints_entities_app_init' );

add_action( 'wordpoints_init_app_registry-apps-entities', 'wordpoints_entities_init' );
add_action( 'wordpoints_init_app_registry-entities-contexts', 'wordpoints_entity_contexts_init' );

add_action( 'wordpoints_init_app_registry-apps-data_types', 'wordpoints_data_types_init' );

add_action( 'wordpoints_init_app_registry-hooks-firers', 'wordpoints_hook_firers_init' );
add_action( 'wordpoints_init_app_registry-hooks-events', 'wordpoints_hook_events_init' );
add_action( 'wordpoints_init_app_registry-hooks-actions', 'wordpoints_hook_actions_init' );
add_action( 'wordpoints_init_app_registry-hooks-reactors', 'wordpoints_hook_reactors_init' );
add_action( 'wordpoints_init_app_registry-hooks-extensions', 'wordpoints_hook_extension_init' );
add_action( 'wordpoints_init_app_registry-hooks-conditions', 'wordpoints_hook_conditions_init' );

add_action( 'wordpoints_modules_loaded', 'wordpoints_init_hooks' );

add_filter( 'wordpoints_user_can_view_points_log', 'wordpoints_hooks_user_can_view_points_log' );

// EOF
