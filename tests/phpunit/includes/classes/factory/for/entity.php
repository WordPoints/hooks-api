<?php

/**
 * Entity factory class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Factory for entities, for use in the unit tests.
 *
 * @since 1.0.0
 *
 * @method string create( $args = array(), $generation_definitions = null )
 * @method WordPoints_Entity create_and_get( $args = array(), $generation_definitions = null )
 * @method string[] create_many( $count, $args = array(), $generation_definitions = null )
 */
class WordPoints_PHPUnit_Factory_For_Entity extends WP_UnitTest_Factory_For_Thing {

	/**
	 * @since 1.0.0
	 */
	public function __construct( $factory = null ) {

		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'slug'  => 'test_entity',
			'class' => 'WordPoints_PHPUnit_Mock_Entity_Contexted',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function create_object( $args ) {

		$entities = wordpoints_entities();

		if ( $args === $this->default_generation_definitions ) {
			$entities->contexts->register(
				'test_context'
				, 'WordPoints_PHPUnit_Mock_Entity_Context'
			);
		}

		$slug = $args['slug'];
		$class = $args['class'];

		unset( $args['slug'], $args['class'] );

		$entities->register( $slug, $class, $args );

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
		return wordpoints_entities()->get( $object_id );
	}
}

// EOF
