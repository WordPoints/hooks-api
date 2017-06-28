<?php

/**
 * Main functions.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Register entities when the entities app is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-apps-entities
 *
 * @param WordPoints_App_Registry $entities The entities app.
 */
function wordpoints_taxonomy_entities_init( $entities ) {

	// Register entities for all of the public taxonomies.
	$taxonomies = get_taxonomies( array( 'public' => true ) );

	/**
	 * Filter which taxonomies to register entities for.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] The taxonomy slugs.
	 */
	$taxonomies = apply_filters( 'wordpoints_register_entities_for_taxonomies', $taxonomies );

	foreach ( $taxonomies as $slug ) {
		wordpoints_register_taxonomy_entities( $slug );
	}
}

/**
 * Register the entities for a taxonomy.
 *
 * @since 1.0.0
 *
 * @param string $slug The slug of the taxonomy.
 */
function wordpoints_register_taxonomy_entities( $slug ) {

	$entities = wordpoints_entities();
	$children = $entities->get_sub_app( 'children' );

	$entities->register( "term\\{$slug}", 'WordPoints_Entity_Term' );
	$children->register( "term\\{$slug}", 'id', 'WordPoints_Entity_Term_Id' );

	/**
	 * Fired when registering the entities for a taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The taxonomy's slug.
	 */
	do_action( 'wordpoints_register_taxonomy_entities', $slug );
}

/**
 * Register the taxonomies for a post type.
 *
 * @since 1.0.0
 *        
 * @WordPoints\action wordpoints_register_post_type_entities
 *
 * @param string $slug The slug of the post type.
 */
function wordpoints_register_post_type_taxonomy_entities( $slug ) {

	$children = wordpoints_entities()->get_sub_app( 'children' );
	
	foreach ( get_object_taxonomies( $slug ) as $taxonomy_slug ) {
		$children->register( "post\\{$slug}", "terms\\{$taxonomy_slug}", 'WordPoints_Entity_Post_Terms' );
	}
}

// EOF
