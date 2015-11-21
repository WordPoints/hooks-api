<?php

/**
 * Hook condition factory class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Factory for hook conditions, for use in the unit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Factory_For_Hook_Condition extends WP_UnitTest_Factory_For_Thing {

	/**
	 * @since 1.0.0
	 */
	function __construct( $factory = null ) {

		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'slug'      => new WP_UnitTest_Generator_Sequence( 'test_condition_%s' ),
			'class'     => 'WordPoints_PHPUnit_Mock_Hook_Condition',
			'data_type' => 'test_data_type',
		);
	}

	/**
	 * @since 1.0.0
	 */
	function create_object( $args ) {

		if ( 'unmet' === $args['slug'] )  {
			$args['class'] = 'WordPoints_PHPUnit_Mock_Hook_Condition_Unmet';
		}

		$conditions = wordpoints_hooks()->conditions;
		$conditions->register( $args['data_type'], $args['slug'], $args['class'] );
		return $conditions->get( $args['data_type'], $args['slug'] );
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
		return $object_id;
	}
}

// EOF
