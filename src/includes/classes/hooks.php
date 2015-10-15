<?php

/**
 * Class for the hooks app.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Hooks app.
 *
 * The hooks API consists primarily of actions, events, reactors, args, and
 * other extensions. Events are "fired" at various reactors when actions occur.
 * The args that the event relates to is passed to any extensions, along with
 * the list of predefined reactions. The extensions can then analyse the args and
 * the reaction specifications to determine whether the reactor should "hit" or
 * "miss" the target entity.
 *
 * @since 1.0.0
 *
 * @property-read WordPoints_Hook_Actions              $actions    The actions registry.
 * @property-read WordPoints_Hook_Events               $events     The events registry.
 * @property-read WordPoints_Class_Registry_Persistent $firers     The firers registry.
 * @property-read WordPoints_Class_Registry_Persistent $reactors   The reactors registry.
 * @property-read WordPoints_Class_Registry_Persistent $extensions The extensions registry.
 * @property-read WordPoints_Class_Registry            $conditions The conditions sub-app.
 */
class WordPoints_Hooks extends WordPoints_App {

	/**
	 * Whether network-wide mode is turned on.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $network_mode = false;

	/**
	 * The hook action router.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Router
	 */
	public $router;

	/**
	 * Whether initialization was performed for the registries.
	 *
	 * @since 1.0.0
	 *
	 * @var bool[]
	 */
	protected $did_init = array();

	/**
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		$this->router = new WordPoints_Hook_Router();

		$this->init();
	}

	/**
	 * @since 1.0.0
	 */
	public function __get( $var ) {

		$value = parent::__get( $var );

		if (
			$value
			&& empty( $this->did_init[ $var ] )
			&& is_object( $value )
			&& (
				$value instanceof WordPoints_Class_Registry
				|| $value instanceof WordPoints_Class_Registry_Persistent
			)
		) {

			/**
			 * Initialization of a hook app registry.
			 *
			 * The $var is the slug of the registry, for example 'extensions' or
			 * 'conditions'.
			 *
			 * @since 1.0.0
			 *
			 * @param WordPoints_Class_Registry|WordPoints_Class_Registry_Persistent
			 *        $registry The registry object.
			 */
			do_action( "wordpoints_hook_{$var}_init", $value );

			$this->did_init[ $var ] = true;
		}

		return $value;
	}

	/**
	 * Register the sub apps when the app is constructed.
	 *
	 * @since 1.0.0
	 */
	protected function init() {

		$this->sub_apps->register( 'actions', 'WordPoints_Hook_Actions' );
		$this->sub_apps->register( 'events', 'WordPoints_Hook_Events' );
		$this->sub_apps->register( 'firers', 'WordPoints_Class_Registry_Persistent' );
		$this->sub_apps->register( 'reactors', 'WordPoints_Class_Registry_Persistent' );
		$this->sub_apps->register( 'extensions', 'WordPoints_Class_Registry_Persistent' );
		$this->sub_apps->register( 'conditions', 'WordPoints_Class_Registry' );

		/**
		 * Hooks app initialization.
		 *
		 * This is a good action to register any sub-apps on.
		 *
		 * @param WordPoints_Hooks $app The hooks app.
		 */
		do_action( 'wordpoints_hooks_init', $this );
	}

	/**
	 * Gets whether network-wide mode is on.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether network-wide mode is on.
	 */
	public function get_network_mode() {
		return $this->network_mode;
	}

	/**
	 * Sets whether network-wide mode is on.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $on Whether network-wide mode should be on (or off).
	 */
	public function set_network_mode( $on = true ) {
		$this->network_mode = (bool) $on;
	}
}

// EOF
