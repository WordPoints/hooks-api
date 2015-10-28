<?php

/**
 * Class for a class registry of classes grouped as children of different parents.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * A class registry where the classes are grouped together under arbitrary "parents".
 *
 * Objects are created on-the-fly, and are not reused.
 *
 * @since 1.0.0
 *
 * @see WordPoints_Class_Registry
 */
class WordPoints_Class_Registry_Children
	implements WordPoints_Class_Registry_ChildrenI {

	/**
	 * The class groups, indexed by "parent" slug.
	 *
	 * Each group is indexed by the class slugs.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	protected $classes = array();

	/**
	 * @since 1.0.0
	 */
	public function get( $parent_slug = null, $slug = null, array $args = array() ) {

		if ( ! empty( $args ) ) {
			array_unshift( $args, $slug );
		}

		if ( ! isset( $parent_slug ) ) {

			$items = array();

			foreach ( $this->classes as $parent_slug => $classes ) {
				foreach ( $classes as $slug => $class ) {
					if ( empty( $args ) ) {
						$items[ $parent_slug ][ $slug ] = new $class( $slug );
					} else {
						$items[ $parent_slug ][ $slug ] = wordpoints_construct_class_with_args(
							$class
							, array( $slug ) + $args
						);
					}
				}
			}

			return $items;
		}

		if ( ! isset( $this->classes[ $parent_slug ] ) ) {
			return false;
		}

		if ( ! isset( $slug ) ) {

			$items = array();

			foreach ( $this->classes[ $parent_slug ] as $slug => $class ) {
				if ( empty( $args ) ) {
					$items[ $slug ] = new $class( $slug );
				} else {
					$items[ $slug ] = wordpoints_construct_class_with_args(
						$class
						, array( $slug ) + $args
					);
				}
			}

			return $items;
		}

		if ( ! isset( $this->classes[ $parent_slug ][ $slug ] ) ) {
			return false;
		}

		$class = $this->classes[ $parent_slug ][ $slug ];

		if ( empty( $args ) ) {
			return new $class( $slug );
		} else {
			return wordpoints_construct_class_with_args( $class, $args );
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function register( $parent_slug, $slug, $class, array $args = array() ) {

		$this->classes[ $parent_slug ][ $slug ] = $class;

		return true;
	}

	/**
	 * @since 1.0.0
	 */
	public function deregister( $parent_slug, $slug = null ) {

		if ( isset( $slug ) ) {
			unset( $this->classes[ $parent_slug ][ $slug ] );
		} else {
			unset( $this->classes[ $parent_slug ] );
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function is_registered( $parent_slug, $slug = null ) {

		if ( isset( $slug ) ) {
			return isset( $this->classes[ $parent_slug ][ $slug ] );
		} else {
			return isset( $this->classes[ $parent_slug ] );
		}
	}
}

// EOF
