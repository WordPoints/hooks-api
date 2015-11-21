<?php

/**
 * Post type factory class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Factory for post types, for use in the unit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Factory_For_Post_Type extends WP_UnitTest_Factory_For_Thing {

	/**
	 * @since 1.0.0
	 */
	function __construct( $factory = null ) {

		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'name' => new WP_UnitTest_Generator_Sequence(
				'post_type_%s'
			),
		);
	}

	/**
	 * @since 1.0.0
	 */
	function create_object( $args ) {

		$object = register_post_type( $args['name'], $args );

		if ( ! isset( $object->name ) ) {
			return $object;
		}

		return $object->name;
	}

	/**
	 * @since 1.0.0
	 */
	function update_object( $object, $fields ) {
		return false;
	}

	/**
	 * @since 1.0.0
	 */
	function get_object_by_id( $object_id ) {
		return get_post_type_object( $object_id );
	}
}

// EOF