<?php

/**
 * Persistent class registry class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * A persistent class registry.
 *
 * The registered classes are instantiated on-the-fly, but they are saved and re-used
 * the next time that get() is called.
 *
 * @since 1.0.0
 */
class WordPoints_Class_Registry_Persistent extends WordPoints_Class_Registry {

	/**
	 * The objects which have been instantiated, indexed by slug.
	 *
	 * @since 1.0.0
	 *
	 * @var object[]
	 */
	protected $objects = array();

	/**
	 * @since 1.0.0
	 */
	public function get_all( array $args = array() ) {

		$classes = array_diff_key( $this->classes, $this->objects );

		if ( ! empty( $classes ) ) {

			$objects = WordPoints_Class_Registry::construct_with_args(
				$classes
				, $args
			);

			$this->objects = $this->objects + $objects;
		}

		return $this->objects;
	}

	/**
	 * @since 1.0.0
	 */
	public function get( $slug, array $args = array() ) {

		if ( ! isset( $this->objects[ $slug ] ) ) {

			$object = parent::get( $slug, $args );

			if ( ! $object ) {
				return false;
			}

			$this->objects[ $slug ] = $object;
		}

		return $this->objects[ $slug ];
	}

	/**
	 * @since 1.0.0
	 */
	public function register( $slug, $class, array $args = array() ) {

		unset( $this->objects[ $slug ] );

		return parent::register( $slug, $class, $args );
	}

	/**
	 * @since 1.0.0
	 */
	public function deregister( $slug ) {

		parent::deregister( $slug );

		unset( $this->objects[ $slug ] );
	}
}

// EOF
