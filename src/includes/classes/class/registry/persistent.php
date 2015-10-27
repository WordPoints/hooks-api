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
	public function get( $slug = null, array $args = array() ) {

		if ( ! empty( $args ) ) {
			array_unshift( $args, $slug );
		}

		if ( ! isset( $slug ) ) {

			foreach ( $this->classes as $slug => $class ) {
				if ( ! isset( $this->objects[ $slug ] ) ) {
					if ( empty( $args ) ) {
						$this->objects[ $slug ] = new $class( $slug );
					} else {
						$this->objects[ $slug ] = wordpoints_construct_class_with_args(
							$class
							, array( $slug ) + $args
						);
					}
				}
			}

			return $this->objects;
		}

		if ( ! isset( $this->classes[ $slug ] ) ) {
			return false;
		}

		if ( ! isset( $this->objects[ $slug ] ) ) {
			if ( empty( $args ) ) {
				$this->objects[ $slug ] = new $this->classes[ $slug ]( $slug );
			} else {
				$this->objects[ $slug ] = wordpoints_construct_class_with_args(
					$this->classes[ $slug ]
					, $args
				);
			}
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
