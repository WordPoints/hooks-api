<?php

/**
 * User role factory class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Factory for user roles, for use in the unit tests.
 *
 * @since 1.0.0
 *
 * @method string create( $args = array(), $generation_definitions = null )
 * @method WP_Role create_and_get( $args = array(), $generation_definitions = null )
 * @method string[] create_many( $count, $args = array(), $generation_definitions = null )
 */
class WordPoints_PHPUnit_Factory_For_User_Role extends WP_UnitTest_Factory_For_Thing {

	/**
	 * @since 1.0.0
	 */
	function __construct( $factory = null ) {

		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'name' => new WP_UnitTest_Generator_Sequence(
				'user_role_%s'
			),
			'display_name' => new WP_UnitTest_Generator_Sequence(
				'User Role %s'
			),
		);
	}

	/**
	 * @since 1.0.0
	 */
	function create_object( $args ) {

		if ( ! isset( $args['capabilities'] ) ) {
			$args['capabilities'] = array();
		}

		$object = add_role(
			$args['name']
			, $args['display_name']
			, $args['capabilities']
		);

		if ( ! isset( $object->name ) ) {
			return false;
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
		return get_role( $object_id );
	}
}

// EOF
