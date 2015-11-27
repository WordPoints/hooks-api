<?php

/**
 * Administration-side actions and filters.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

add_action( 'wordpoints_init_app-apps', 'wordpoints_hooks_register_admin_apps' );

add_action( 'admin_menu', 'wordpoints_hooks_api_admin_menu' );
add_action( 'network_admin_menu', 'wordpoints_hooks_api_admin_menu' );

add_action( 'admin_init', 'wordpoints_hooks_admin_register_scripts' );
add_action( 'admin_init', 'wordpoints_hooks_admin_ajax' );

add_filter( 'script_loader_tag', 'wordpoints_script_templates_filter', 10, 2 );

// EOF
