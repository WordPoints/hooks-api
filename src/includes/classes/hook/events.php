<?php

/**
 * Hook events class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * A registry for the hook events.
 *
 * @since 1.0.0
 *
 * @property-read WordPoints_Class_Registry_Children $args The event args registry.
 */
class WordPoints_Hook_Events extends WordPoints_App_Registry {

	/**
	 * The data for the events, indexed by slug.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	protected $event_data = array();

	/**
	 * A hook router.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Router
	 */
	protected $router;

	/**
	 * A hook actions registry.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Actions
	 */
	protected $actions;

	/**
	 * @since 1.0.0
	 */
	public function __construct( $slug ) {

		$hooks = wordpoints_hooks();

		$this->router   = $hooks->router;
		$this->actions  = $hooks->actions;

		parent::__construct( $slug );
	}

	/**
	 * @since 1.0.0
	 */
	public function init() {

		$this->sub_apps->register( 'args', 'WordPoints_Class_Registry_Children' );

		parent::init();
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $slug  The slug for this event.
	 * @param string $class The name of the event class.
	 * @param array  $args  {
	 *        Other args.
	 *
	 *        @type array[] $actions The slugs of the actions that relate to this
	 *                               event, indexed by action type. If only a single
	 *                               action of a certain type is given a string may
	 *                               be provided, or an array of strings for multiple
	 *                               actions.
	 *
	 *        @type array[] $args    The args this event relates to.
	 * }
	 *
	 * @return bool Whether the event was registered.
	 */
	public function register( $slug, $class, array $args = array() ) {

		// TODO shoudl this be required?
		if ( ! isset( $args['actions'] ) ) {
			return false;
		}

		parent::register( $slug, $class, $args );

		foreach ( $args['actions'] as $type => $actions ) {
			foreach ( (array) $actions as $action_slug ) {
				$this->router->add_event_to_action( $slug, $action_slug, $type );
			}
		}

		// TODO should this be required?
		if ( isset( $args['args'] ) ) {
			foreach ( $args['args'] as $arg_slug => $class ) {
				$this->args->register( $slug, $arg_slug, $class );
			}
		}

		$this->event_data[ $slug ] = $args;

		return true;
	}

	/**
	 * @since 1.0.0
	 */
	public function deregister( $slug ) {

		if ( ! $this->is_registered( $slug ) ) {
			return;
		}

		parent::deregister( $slug );

		foreach ( (array) $this->event_data[ $slug ]['actions'] as $type => $actions ) {
			foreach ( (array) $actions as $action_slug ) {
				$this->router->remove_event_from_action( $slug, $action_slug, $type );
			}
		}

		$this->args->deregister( $slug );

		unset( $this->event_data[ $slug ] );
	}
}

// EOF
