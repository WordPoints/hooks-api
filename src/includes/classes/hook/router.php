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
 */
class WordPoints_Hook_Router {

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
	 * The events are the indexes in the arrays for each action type, the values in
	 * the arrays are unused.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	protected $event_index = array();

	/**
	 * The reactor hit types, indexed by reactor and action type.
	 *
	 * Tells us what type of hit to tell a reactor to perform when it is hit by a
	 * fire of a particular type of action.
	 *
	 * @since 1.0.0
	 *
	 * @var string[][]
	 */
	protected $reactor_index = array();

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

		// We might normally do this in the constructor, however, the events
		// registry attempts to access the router in its own constructor. The result
		// of attempting to do this before the router itself has been fully
		// constructed is that the events registry gets null instead of the router.
		if ( ! isset( $this->actions ) ) {

			$hooks = wordpoints_hooks();

			$this->events  = $hooks->events;
			$this->actions = $hooks->actions;
		}

		foreach ( $this->action_index[ $name ]['actions'] as $slug => $data ) {

			if ( ! isset( $this->event_index[ $slug ] ) ) {
				continue;
			}

			$action_object = $this->actions->get( $slug, $args, $data );

			if ( ! ( $action_object instanceof WordPoints_Hook_ActionI ) ) {
				continue;
			}

			if ( ! $action_object->should_fire() ) {
				continue;
			}

			foreach ( $this->event_index[ $slug ] as $type => $events ) {
				foreach ( $events as $event_slug => $unused ) {

					if ( ! $this->events->is_registered( $event_slug ) ) {
						continue;
					}

					$event_args = $this->events->args->get_children( $event_slug, array( $action_object ) );

					if ( empty( $event_args ) ) {
						continue;
					}

					$event_args = new WordPoints_Hook_Event_Args( $event_args );

					$this->fire_event( $type, $event_slug, $event_args );
				}
			}
		}
	}

	/**
	 * Fire an event at each of the reactions.
	 *
	 * @since 1.0.0
	 *
	 * @param string                     $action_type The type of action triggering
	 *                                                this fire of this event.
	 * @param string                     $event_slug  The slug of the event.
	 * @param WordPoints_Hook_Event_Args $event_args  The event args.
	 */
	public function fire_event(
		$action_type,
		$event_slug,
		WordPoints_Hook_Event_Args $event_args
	) {

		$hooks = wordpoints_hooks();

		foreach ( $hooks->reaction_stores->get_all() as $reaction_stores ) {
			foreach ( $reaction_stores as $reaction_store ) {

				if ( ! $reaction_store instanceof WordPoints_Hook_Reaction_StoreI ) {
					continue;
				}

				// Allowing access to stores out-of-context would lead to strange behavior.
				if ( false === $reaction_store->get_context_id() ) {
					continue;
				}

				foreach ( $reaction_store->get_reactions_to_event( $event_slug ) as $reaction ) {

					$fire = new WordPoints_Hook_Fire(
						$action_type
						, $event_args
						, $reaction
					);

					$this->fire_reaction( $fire );
				}
			}
		}
	}

	/**
	 * Fire for a particular reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The hook fire object.
	 */
	protected function fire_reaction( $fire ) {

		$hooks = wordpoints_hooks();

		$reactor_slug = $fire->reaction->get_reactor_slug();

		if ( ! isset( $this->reactor_index[ $reactor_slug ][ $fire->action_type ] ) ) {
			return;
		}

		$hit_type = $this->reactor_index[ $reactor_slug ][ $fire->action_type ];

		$validator = new WordPoints_Hook_Reaction_Validator( $fire->reaction, true );
		$validator->validate();

		if ( $validator->had_errors() ) {
			return;
		}

		unset( $validator );

		/** @var WordPoints_Hook_Extension[] $extensions */
		$extensions = $hooks->extensions->get_all();

		foreach ( $extensions as $extension ) {
			if ( ! $extension->should_hit( $fire ) ) {
				return;
			}
		}

		$fire->hit();

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = $hooks->reactors->get( $reactor_slug );

		$reactor->hit( $hit_type, $fire );

		foreach ( $extensions as $extension ) {
			$extension->after_hit( $fire );
		}
	}

	/**
	 * Register an action with the router.
	 *
	 * The arg number will be automatically determined based on $data['arg_index']
	 * and $data['requirements']. So in most cases $arg_number may be omitted.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug of the action.
	 * @param array  $args {
	 *        Other arguments.
	 *
	 *        @type string $action     The name of the WordPress action for this hook action.
	 *        @type int    $priority   The priority for the WordPress action. Default: 10.
	 *        @type int    $arg_number The number of args the action object expects. Default: 1.
	 *        @type array  $data {
	 *              Args that will be passed to the action object's constructor.
	 *
	 *              @type int[] $arg_index    List of args (starting from 0), indexed by slug.
	 *              @type array $requirements List of requirements, indexed by arg index (from 0).
	 *        }
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

		if ( isset( $args['data'] ) ) {

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

		if ( isset( $args['arg_number'] ) ) {
			$arg_number = $args['arg_number'];
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

		add_action( $args['action'], array( $this, $method ), $priority, $arg_number );
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

	/**
	 * Hook an action type to a reactor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action_type  The slug of the action type.
	 * @param string $reactor_slug The slug of the reactor.
	 * @param string $hit_type     The type of hit the reactor should perform when
	 *                             hit by this type of event.
	 */
	public function add_action_type_to_reactor( $action_type, $reactor_slug, $hit_type ) {
		$this->reactor_index[ $reactor_slug ][ $action_type ] = $hit_type;
	}

	/**
	 * Unhook an action type from a reactor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action_type  The slug of the action type.
	 * @param string $reactor_slug The slug of the reactor.
	 */
	public function remove_action_type_from_reactor( $action_type, $reactor_slug ) {
		unset( $this->reactor_index[ $reactor_slug ][ $action_type ] );
	}

	/**
	 * Get the event index.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] The event index.
	 */
	public function get_event_index() {

		if ( empty( $this->reactor_index ) ) {
			wordpoints_hooks()->events;
		}

		return $this->event_index;
	}

	/**
	 * Get the reactor index.
	 *
	 * @since 1.0.0
	 *
	 * @return string[][]
	 */
	public function get_reactor_index() {

		if ( empty( $this->reactor_index ) ) {
			wordpoints_hooks()->reactors;
		}

		return $this->reactor_index;
	}
}

// EOF
