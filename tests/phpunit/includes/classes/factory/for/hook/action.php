<?php

/**
 * Hook action factory class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Factory for hook actions, for use in the unit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Factory_For_Hook_Action extends WP_UnitTest_Factory_For_Thing {

	/**
	 * @since 1.0.0
	 */
	function __construct( $factory = null ) {

		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'slug'     => 'test_action',
			'class'    => 'WordPoints_PHPUnit_Mock_Hook_Action',
			'action'   => 'wordpoints_phpunit_factory_hook_action',
			'priority' => 10,
		);
	}

	/**
	 * @since 1.0.0
	 */
	function create_object( $args ) {

		$slug = $args['slug'];
		$class = $args['class'];

		unset( $args['slug'], $args['class'] );

		wordpoints_hooks()->actions->register( $slug, $class, $args );

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
		return wordpoints_hooks()->actions->get( $object_id );
	}
}

// EOF
