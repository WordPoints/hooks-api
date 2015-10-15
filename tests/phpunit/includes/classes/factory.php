<?php

/**
 * Factory class for use in the unit tests.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * A registry for factories to be used in the unit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Factory extends WordPoints_Class_Registry_Persistent{

	/**
	 * The factory registry.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_PHPUnit_Factory
	 */
	public static $factory;

	/**
	 * Initialize the registry.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_PHPUnit_Factory The factory registry.
	 */
	public static function init() {
		return self::$factory = new WordPoints_PHPUnit_Factory();
	}

	/**
	 * @since 1.0.0
	 */
	function __get( $var ) {

		if ( $this->is_registered( $var ) ) {
			return $this->$var = $this->get( $var );
		}

		return null;
	}

	/**
	 * @since 1.0.0
	 */
	public function get( $slug = null ) {

		if ( ! isset( $slug ) ) {

			$items = array();

			foreach ( $this->classes as $slug => $class ) {
				$items[ $slug ] = new $class( $this );
			}

			return $items;
		}

		if ( ! isset( $this->classes[ $slug ] ) ) {
			return false;
		}

		return new $this->classes[ $slug ]( $this );
	}
}

// EOF
