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
 *
 * @method string create( $args = array(), $generation_definitions = null )
 * @method WordPoints_Hook_ReactorI create_and_get( $args = array(), $generation_definitions = null )
 * @method string[] create_many( $count, $args = array(), $generation_definitions = null )
 */
class WordPoints_PHPUnit_Factory_For_Hook_Reactor extends WP_UnitTest_Factory_For_Thing {

	/**
	 * @since 1.0.0
	 */
	public function __construct( $factory = null ) {

		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'slug'  => 'test_reactor',
			'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function create_object( $args ) {

		if ( ! isset( WordPoints_PHPUnit_TestCase::$backup_app ) ) {
			WordPoints_PHPUnit_TestCase::mock_apps();
		}

		$reactors = wordpoints_hooks()->reactors;

		$slug = $args['slug'];
		$class = $args['class'];

		unset( $args['slug'], $args['class'] );

		$reactors->register( $slug, $class, $args );

		// Make sure that contexts are registered, as they are needed when checking
		// whether a reaction store should be made available.
		wordpoints_entity_contexts_init( wordpoints_entities()->contexts );

		return $slug;
	}

	/**
	 * @since 1.0.0
	 */
	public function update_object( $object, $fields ) {
		return $object;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_object_by_id( $object_id ) {
		return wordpoints_hooks()->reactors->get( $object_id );
	}
}

// EOF
