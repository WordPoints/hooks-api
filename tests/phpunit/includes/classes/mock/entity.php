<?php

/**
 * Mock entity class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock entity class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Entity extends WordPoints_Entity {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'test_entity';

	/**
	 * @since 1.0.0
	 */
	protected $id_field = 'id';

	/**
	 * @since 1.0.0
	 */
	protected function get_entity( $id ) {

		if ( isset( $this->getter ) ) {
			return parent::get_entity( $id );
		}

		return (object) array( 'id' => $id, 'type' => $this->slug );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return 'Mock Entity';
	}

	/**
	 * @since 1.0.0
	 */
	protected function is_entity( $entity ) {

		return (
			is_object( $entity )
			&& isset( $entity->type )
			&& $this->slug === $entity->type
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_attr_value( $entity, $attr ) {
		return $entity->$attr;
	}

	/**
	 * Set a protected property's value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $var   The property name.
	 * @param mixed  $value The property value.
	 */
	public function set( $var, $value ) {
		$this->$var = $value;
	}
}

// EOF
