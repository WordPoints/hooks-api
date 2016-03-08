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
 *
 * @property-read WordPoints_PHPUnit_Factory_For_Entity $entity
 * @property-read WordPoints_PHPUnit_Factory_For_Hook_Action $hook_action
 * @property-read WordPoints_PHPUnit_Factory_For_Hook_Condition $hook_condition
 * @property-read WordPoints_PHPUnit_Factory_For_Hook_Event $hook_event
 * @property-read WordPoints_PHPUnit_Factory_For_Hook_Reaction $hook_reaction
 * @property-read WordPoints_PHPUnit_Factory_For_Hook_Reactor $hook_reactor
 * @property-read WordPoints_PHPUnit_Factory_For_Post_Type $post_type
 * @property-read WordPoints_PHPUnit_Factory_For_User_Role $user_role
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
	public function __get( $var ) {

		if ( $this->is_registered( $var ) ) {
			return $this->$var = $this->get( $var );
		}

		return null;
	}

	/**
	 * @since 1.0.0
	 */
	public function get( $slug ) {

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
