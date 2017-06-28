<?php

/**
 * Actions and filters of the module.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

add_action( 'wordpoints_init_app_registry-apps-entities', 'wordpoints_taxonomy_entities_init' );

add_action( 'wordpoints_register_post_type_entities', 'wordpoints_register_post_type_taxonomy_entities' );

// EOF
