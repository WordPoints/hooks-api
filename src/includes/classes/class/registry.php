<?php

/**
 * Class registry class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * A class registry which creates new objects on-the-fly as they are requested.
 *
 * In other words, each time get() is called, a new object will be returned.
 *
 * Objects are passed the slug as the first parameter when they are constructed.
 *
 * @since 1.0.0
 */
class WordPoints_Class_Registry implements WordPoints_Class_RegistryI {

	/**
	 * The registered classes, indexed by slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	protected $classes = array();

	/**
	 * @since 1.0.0
	 */
	public function get( $slug = null ) {

		if ( ! isset( $slug ) ) {

			$items = array();

			foreach ( $this->classes as $slug => $class ) {
				$items[ $slug ] = new $class( $slug );
			}

			return $items;
		}

		if ( ! isset( $this->classes[ $slug ] ) ) {
			return false;
		}

		return new $this->classes[ $slug ]( $slug );
	}

	/**
	 * @since 1.0.0
	 */
	public function register( $slug, $class, array $args = array() ) {

		$this->classes[ $slug ] = $class;

		return true;
	}

	/**
	 * @since 1.0.0
	 */
	public function deregister( $slug ) {

		unset( $this->classes[ $slug ] );
	}

	/**
	 * @since 1.0.0
	 */
	public function is_registered( $slug ) {

		return isset( $this->classes[ $slug ] );
	}
}

// EOF
