<?php

/**
 * Hook action router class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Routes WordPress actions to WordPoints hook actions, and finally to hook events.
 *
 * Each WordPress action can have several different WordPoints hook actions hooked to
 * it. This router handles hooking into the WordPress action, and making sure the
 * hook actions are processed when it is fired. This allows us to hook to each action
 * once, even if multiple hook actions are registered for it.
 *
 * When a hook action is fired, the router then loops through the events which are
 * registered to fire on that hook action, and fires each of them.
 *
 * This arrangement allows for events and actions to be decoupled from WordPress
 * actions, and from each other as well. As a result, action classes don't have to
 * be loaded until the router is called on the action that they are attached to. The
 * event classes can be lazy-loaded as well.
 *
 * It also makes it possible for a hook action to abort firing any events if it
 * chooses to do so.
 *
 * @since 1.0.0
 *
 * @property-read WordPoints_Hook_ActionI $current_action The current action.
 */
class WordPoints_Hook_Router {

	/**
	 * The hook firers registry object.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Class_Registry_Persistent
	 */
	protected $firers;

	/**
	 * The actions registry object.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Actions
	 */
	protected $actions;

	/**
	 * The events registry object.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Events
	 */
	protected $events;

	/**
	 * The actions, indexed by WordPress action/filter hooks.
	 *
	 * The indexes are of this format: "$action_or_filter_name,$priority".
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $action_index = array();

	/**
	 * The events, indexed by action slug and action type.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $event_index = array();

	/**
	 * The action currently being routed.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_ActionI
	 */
	protected $current_action;

	/**
	 * @since 1.0.0
	 */
	public function __call( $name, $args ) {

		$this->route_action( $name, $args );

		// Return the first value, in case it is hooked to a filter.
		$return = null;
		if ( isset( $args[0] ) ) {
			$return = $args[0];
		}

		return $return;
	}

	/**
	 * @since 1.0.0
	 */
	public function __get( $var ) {

		if ( 'current_action' === $var ) {
			return $this->current_action;
		}

		return null;
	}

	/**
	 * Routes a WordPress action to WordPoints hook actions, and fires their events.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The action ID. This is not the slug of a hook action, but
	 *                     rather a unique ID for the WordPress action based on the
	 *                     action name and the priority.
	 * @param array  $args The args the action was fired with.
	 */
	protected function route_action( $name, $args ) {

		if ( ! isset( $this->action_index[ $name ] ) ) {
			return;
		}

		if ( ! isset( $this->actions ) ) {

			$hooks = wordpoints_apps()->hooks;

			$this->events  = $hooks->events;
			$this->actions = $hooks->actions;
			$this->firers  = $hooks->firers;
		}

		foreach ( $this->action_index[ $name ]['actions'] as $slug => $data ) {

			if ( ! isset( $this->event_index[ $slug ] ) ) {
				continue;
			}

			$action_object = $this->actions->get( $slug, $args, $data );

			if ( ! ( $action_object instanceof WordPoints_Hook_ActionI ) ) {
				continue;
			}

			$this->current_action = $action_object;

			if ( ! $action_object->should_fire() ) {
				continue;
			}

			foreach ( $this->event_index[ $slug ] as $type => $events ) {
				foreach ( $events as $event_slug => $unused ) {

					if ( ! $this->events->is_registered( $event_slug ) ) {
						continue;
					}

					$event_args = $this->events->args->get( $event_slug, null, array( $action_object ) );

					if ( false === $event_args ) {
						continue;
					}

					$hierarchy = new WordPoints_Hook_Event_Args( $event_args );

					$firer = $this->firers->get( $type );

					if ( $firer instanceof WordPoints_Hook_FirerI ) {
						$firer->do_event( $event_slug, $hierarchy );
					}
				}
			}
		}

		$this->current_action = null;
	}

	/**
	 * Register an action with the router.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug of the action.
	 * @param array  $args {
	 *        Other arguments.
	 *
	 *        @type string $action   The name of the WordPress action for this hook action.
	 *        @type int    $priority The priority for the WordPress action. Default: 10.
	 *        @type int    $args     The number of args the action object expects. Default: 1.
	 * }
	 */
	public function add_action( $slug, array $args ) {

		$priority = 10;
		if ( isset( $args['priority'] ) ) {
			$priority = $args['priority'];
		}

		if ( ! isset( $args['action'] ) ) {
			return;
		}

		$method = "{$args['action']},{$priority}";

		$this->action_index[ $method ]['actions'][ $slug ] = array();

		$arg_number = 1;

		if ( isset( $args['arg_number'] ) ) {

			$arg_number = $args['arg_number'];

		} elseif ( isset( $args['data'] ) ) {

			if ( isset( $args['data']['arg_index'] ) ) {
				$arg_number = 1 + max( $args['data']['arg_index'] );
			}

			if ( isset( $args['data']['requirements'] ) ) {
				$requirements = 1 + max( array_keys( $args['data']['requirements'] ) );

				if ( $requirements > $arg_number ) {
					$arg_number = $requirements;
				}
			}

			$this->action_index[ $method ]['actions'][ $slug ] = $args['data'];
		}

		// If this action is already being routed, and will have enough args, we
		// don't need to hook to it again.
		if (
			isset( $this->action_index[ $method ]['arg_number'] )
			&& $this->action_index[ $method ]['arg_number'] >= $arg_number
		) {
			return;
		}

		$this->action_index[ $method ]['arg_number'] = $arg_number;

		add_action( $args['action'], array( $this, $method ), $priority );
	}

	/**
	 * Deregister an action with the router.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The action slug.
	 */
	public function remove_action( $slug ) {

		foreach ( $this->action_index as $method => $data ) {
			if ( isset( $data['actions'][ $slug ] ) ) {

				unset( $this->action_index[ $method ]['actions'][ $slug ] );

				if ( empty( $this->action_index[ $method ]['actions'] ) ) {

					unset( $this->action_index[ $method ] );

					list( $action, $priority ) = explode( ',', $method );

					remove_action( $action, array( $this, $method ), $priority );
				}
			}
		}
	}

	/**
	 * Hook an event to an action.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_slug The slug of the event.
	 * @param string $action_slug The slug of the action.
	 * @param string $action_type The type of action. Default is 'fire'.
	 */
	public function add_event_to_action( $event_slug, $action_slug, $action_type = 'fire' ) {
		$this->event_index[ $action_slug ][ $action_type ][ $event_slug ] = true;
	}

	/**
	 * Unhook an event from an action.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_slug  The slug of the event.
	 * @param string $action_slug The slug of the action.
	 * @param string $action_type The type of action. Default is 'fire'.
	 */
	public function remove_event_from_action( $event_slug, $action_slug, $action_type = 'fire' ) {
		unset( $this->event_index[ $action_slug ][ $action_type ][ $event_slug ] );
	}
}

// EOF
