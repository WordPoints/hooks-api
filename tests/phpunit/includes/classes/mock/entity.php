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

	public function call( $method, array $args = array() ) {
		return call_user_func_array( array( $this, $method ), $args );
	}
}

// EOF
