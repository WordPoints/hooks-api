<?php

/**
 * Hook reactor factory class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Factory for hook reactors, for use in the unit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Factory_For_Hook_Reactor extends WP_UnitTest_Factory_For_Thing {

	/**
	 * @since 1.0.0
	 */
	function __construct( $factory = null ) {

		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'slug'  => 'test_reactor',
			'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor',
		);
	}

	/**
	 * @since 1.0.0
	 */
	function create_object( $args ) {

		$reactors = wordpoints_hooks()->reactors;

		$slug = $args['slug'];
		$class = $args['class'];

		unset( $args['slug'], $args['class'] );

		$reactors->register( $slug, $class, $args );

		return $slug;
	}

	/**
	 * @since 1.0.0
	 */
	function update_object( $object, $fields ) {
		return $object;
	}

	/**
	 * @since 1.0.0
	 */
	function get_object_by_id( $object_id ) {
		return wordpoints_hooks()->reactors->get( $object_id );
	}
}

// EOF
