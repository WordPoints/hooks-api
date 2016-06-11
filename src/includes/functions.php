<?php

/**
 * Main functions.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Initialize the hooks API.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_modules_loaded
 */
function wordpoints_init_hooks() {

	$hooks = wordpoints_hooks();

	// Just accessing this causes it to be initialized. We need to do that so
	// the actions will be registered and hooked up. The rest of the API can be
	// lazy-loaded as it is needed.
	$hooks->get_sub_app( 'actions' );
}

/**
 * Register hook reactors when the reactors registry is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-hooks-reactors
 *
 * @param WordPoints_Class_Registry_Persistent $reactors The reactors registry.
 */
function wordpoints_hook_reactors_init( $reactors ) {

	$reactors->register( 'points', 'WordPoints_Hook_Reactor_Points' );
	$reactors->register( 'points_legacy', 'WordPoints_Hook_Reactor_Points_Legacy' );
}

/**
 * Register hook reaction stores when the reaction store registry is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-hooks-reaction_stores
 *
 * @param WordPoints_Class_Registry_Children $reaction_stores The store registry.
 */
function wordpoints_hook_reaction_stores_init( $reaction_stores ) {

	$reaction_stores->register(
		'standard'
		, 'points'
		, 'WordPoints_Hook_Reaction_Store_Options'
	);

	if ( is_wordpoints_network_active() ) {
		$reaction_stores->register(
			'network'
			, 'points'
			, 'WordPoints_Hook_Reaction_Store_Options_Network'
		);
	}
}

/**
 * Register hook extension when the extension registry is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-hooks-extensions
 *
 * @param WordPoints_Class_Registry_Persistent $extensions The extension registry.
 */
function wordpoints_hook_extension_init( $extensions ) {

	$extensions->register( 'blocker', 'WordPoints_Hook_Extension_Blocker' );
	$extensions->register( 'repeat_blocker', 'WordPoints_Hook_Extension_Repeat_Blocker' );
	$extensions->register( 'reversals', 'WordPoints_Hook_Extension_Reversals' );
	$extensions->register( 'reversals_legacy_points', 'WordPoints_Hook_Extension_Reversals_Legacy_Points' );
	$extensions->register( 'conditions', 'WordPoints_Hook_Extension_Conditions' );
	$extensions->register( 'periods', 'WordPoints_Hook_Extension_Periods' );
}

/**
 * Register hook conditions when the conditions registry is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-hooks-conditions
 *
 * @param WordPoints_Class_Registry_Children $conditions The conditions registry.
 */
function wordpoints_hook_conditions_init( $conditions ) {

	$conditions->register(
		'text'
		, 'contains'
		, 'WordPoints_Hook_Condition_String_Contains'
	);

	$conditions->register(
		'text'
		, 'equals'
		, 'WordPoints_Hook_Condition_Equals'
	);

	$conditions->register(
		'entity'
		, 'equals'
		, 'WordPoints_Hook_Condition_Equals'
	);

	$conditions->register(
		'entity_array'
		, 'contains'
		, 'WordPoints_Hook_Condition_Entity_Array_Contains'
	);
}

/**
 * Register hook actions when the action registry is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-hooks-actions
 *
 * @param WordPoints_Hook_Actions $actions The action registry.
 */
function wordpoints_hook_actions_init( $actions ) {

	$actions->register(
		'user_register'
		, 'WordPoints_Hook_Action'
		, array(
			'action' => 'user_register',
			'data'   => array(
				'arg_index' => array( 'user' => 0 ),
			),
		)
	);

	$actions->register(
		'user_delete'
		, 'WordPoints_Hook_Action'
		, array(
			'action' => is_multisite() ? 'wpmu_delete_user' : 'delete_user',
			'data'   => array(
				'arg_index' => array( 'user' => 0 ),
			),
		)
	);

	$actions->register(
		'user_visit'
		, 'WordPoints_Hook_Action'
		, array(
			'action' => 'wp',
		)
	);
	
	// Register actions for all of the public post types.
	$post_types = get_post_types( array( 'public' => true ) );

	/**
	 * Filter which post types to register hook actions for.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] The post type slugs ("names").
	 */
	$post_types = apply_filters( 'wordpoints_register_hook_actions_for_post_types', $post_types );

	foreach ( $post_types as $slug ) {
		wordpoints_register_post_type_hook_actions( $slug );
	}
}

/**
 * Register the hook actions for a post type.
 *
 * @since 1.0.0
 *
 * @param string $slug The slug of the post type.
 */
function wordpoints_register_post_type_hook_actions( $slug ) {

	$actions = wordpoints_hooks()->get_sub_app( 'actions' );

	if ( post_type_supports( $slug, 'comments' ) ) {

		$actions->register(
			"comment_approve\\{$slug}"
			, 'WordPoints_Hook_Action_Post_Type_Comment'
			, array(
				'action' => 'transition_comment_status',
				'data'   => array(
					'arg_index'    => array( "comment\\{$slug}" => 2 ),
					'requirements' => array( 0 => 'approved' ),
				),
			)
		);

		$actions->register(
			"comment_new\\{$slug}"
			, 'WordPoints_Hook_Action_Comment_New'
			, array(
				'action' => 'wp_insert_comment',
				'data'   => array(
					'arg_index' => array( "comment\\{$slug}" => 1 ),
				),
			)
		);

		$actions->register(
			"comment_deapprove\\{$slug}"
			, 'WordPoints_Hook_Action_Post_Type_Comment'
			, array(
				'action' => 'transition_comment_status',
				'data'   => array(
					'arg_index'    => array( "comment\\{$slug}" => 2 ),
					'requirements' => array( 1 => 'approved' ),
				),
			)
		);
	}
	
	// This works for all post types except attachments.
	if ( 'attachment' !== $slug ) {

		$actions->register(
			"post_publish\\{$slug}"
			, 'WordPoints_Hook_Action_Post_Type'
			, array(
				'action' => 'transition_post_status',
				'data'   => array(
					'arg_index'    => array( "post\\{$slug}" => 2 ),
					'requirements' => array( 0 => 'publish' ),
				),
			)
		);

		$actions->register(
			"post_depublish\\{$slug}"
			, 'WordPoints_Hook_Action_Post_Type'
			, array(
				'action' => 'transition_post_status',
				'data'   => array(
					'arg_index'    => array( "post\\{$slug}" => 2 ),
					'requirements' => array( 1 => 'publish' ),
				),
			)
		);
		
		$actions->register(
			"post_depublish_delete\\{$slug}"
			, 'WordPoints_Hook_Action_Post_Depublish_Delete'
			, array(
				'action' => 'delete_post',
				'data'   => array(
					'arg_index' => array( "post\\{$slug}" => 0 ),
				),
			)
		);
		
	} else {

		$actions->register(
			'add_attachment'
			, 'WordPoints_Hook_Action'
			, array(
				'action' => 'add_attachment',
				'data'   => array(
					'arg_index' => array( 'post\attachment' => 0 ),
				),
			)
		);

		$actions->register(
			"post_delete\\{$slug}"
			, 'WordPoints_Hook_Action_Post_Type'
			, array(
				'action' => 'delete_post',
				'data'   => array(
					'arg_index' => array( "post\\{$slug}" => 0 ),
				),
			)
		);
	}
	
	/**
	 * Fires when registering the hook actions for a post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug ("name") of the post type.
	 */
	do_action( 'wordpoints_register_post_type_hook_actions', $slug );
}

/**
 * Register hook events when the event registry is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-hooks-events
 *
 * @param WordPoints_Hook_Events $events The event registry.
 */
function wordpoints_hook_events_init( $events ) {

	$events->register(
		'user_register'
		, 'WordPoints_Hook_Event_User_Register'
		, array(
			'actions' => array(
				'toggle_on'  => 'user_register',
				'toggle_off' => 'user_delete',
			),
			'args' => array(
				'user' => 'WordPoints_Hook_Arg',
			),
		)
	);

	$events->register(
		'user_visit'
		, 'WordPoints_Hook_Event_User_Visit'
		, array(
			'actions' => array(
				'fire' => 'user_visit',
			),
			'args' => array(
				'current:user' => 'WordPoints_Hook_Arg_Current_User',
			),
		)
	);

	// Register events for all of the public post types.
	$post_types = get_post_types( array( 'public' => true ) );

	/**
	 * Filter which post types to register hook events for.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] The post type slugs ("names").
	 */
	$post_types = apply_filters( 'wordpoints_register_hook_events_for_post_types', $post_types );

	foreach ( $post_types as $slug ) {
		wordpoints_register_post_type_hook_events( $slug );
	}
}

/**
 * Register sub-apps when the Entities app is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app-entities
 *
 * @param WordPoints_App_Registry $entities The entities app.
 */
function wordpoints_entities_app_init( $entities ) {

	$sub_apps = $entities->sub_apps();
	$sub_apps->register( 'children', 'WordPoints_Class_Registry_Children' );
	$sub_apps->register( 'contexts', 'WordPoints_Class_Registry' );
}

/**
 * Register entity contexts when the registry is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-entities-contexts
 *
 * @param WordPoints_Class_RegistryI $contexts The entity context registry.
 */
function wordpoints_entity_contexts_init( $contexts ) {

	$contexts->register( 'network', 'WordPoints_Entity_Context_Network' );
	$contexts->register( 'site', 'WordPoints_Entity_Context_Site' );
}

/**
 * Register entities when the entities app is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-apps-entities
 *
 * @param WordPoints_App_Registry $entities The entities app.
 */
function wordpoints_entities_init( $entities ) {

	$children = $entities->get_sub_app( 'children' );

	$entities->register( 'user', 'WordPoints_Entity_User' );
	$children->register( 'user', 'roles', 'WordPoints_Entity_User_Roles' );

	$entities->register( 'user_role', 'WordPoints_Entity_User_Role' );

	// Register entities for all of the public post types.
	$post_types = get_post_types( array( 'public' => true ) );

	/**
	 * Filter which post types to register entities for.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] The post type slugs ("names").
	 */
	$post_types = apply_filters( 'wordpoints_register_entities_for_post_types', $post_types );

	foreach ( $post_types as $slug ) {
		wordpoints_register_post_type_entities( $slug );
	}

	// Register entities for all of the public taxonomies.
	$taxonomies = get_taxonomies( array( 'public' => true ) );

	/**
	 * Filter which taxonomies to register entities for.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] The taxonomy slugs.
	 */
	$taxonomies = apply_filters( 'wordpoints_register_entities_for_taxonomies', $taxonomies );

	foreach ( $taxonomies as $slug ) {
		wordpoints_register_taxonomy_entities( $slug );
	}
}

/**
 * Register the entities for a post type.
 *
 * @since 1.0.0
 *
 * @param string $slug The slug of the post type.
 */
function wordpoints_register_post_type_entities( $slug ) {

	$entities = wordpoints_entities();
	$children = $entities->get_sub_app( 'children' );

	$entities->register( "post\\{$slug}", 'WordPoints_Entity_Post' );
	$children->register( "post\\{$slug}", 'author', 'WordPoints_Entity_Post_Author' );

	$supports = get_all_post_type_supports( $slug );

	if ( isset( $supports['editor'] ) ) {
		$children->register( "post\\{$slug}", 'content', 'WordPoints_Entity_Post_Content' );
	}

	if ( isset( $supports['comments'] ) ) {
		$entities->register( "comment\\{$slug}", 'WordPoints_Entity_Comment' );
		$children->register( "comment\\{$slug}", "post\\{$slug}", 'WordPoints_Entity_Comment_Post' );
		$children->register( "comment\\{$slug}", 'author', 'WordPoints_Entity_Comment_Author' );
	}

	foreach ( get_object_taxonomies( $slug ) as $taxonomy_slug ) {
		$children->register( "post\\{$slug}", "terms\\{$taxonomy_slug}", 'WordPoints_Entity_Post_Terms' );
	}

	/**
	 * Fired when registering the entities for a post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug ("name") of the post type.
	 */
	do_action( 'wordpoints_register_post_type_entities', $slug );
}

/**
 * Register the hook events for a post type.
 *
 * @since 1.0.0
 *
 * @param string $slug The slug of the post type.
 */
function wordpoints_register_post_type_hook_events( $slug ) {

	$events = wordpoints_hooks()->get_sub_app( 'events' );

	if ( 'attachment' === $slug ) {

		$events->register(
			'media_upload'
			, 'WordPoints_Hook_Event_Media_Upload'
			, array(
				'actions' => array(
					'toggle_on'  => 'add_attachment',
					'toggle_off' => "post_delete\\{$slug}",
				),
				'args'    => array(
					"post\\{$slug}" => 'WordPoints_Hook_Arg',
				),
			)
		);

	} else {

		$events->register(
			"post_publish\\{$slug}"
			, 'WordPoints_Hook_Event_Post_Publish'
			, array(
				'actions' => array(
					'toggle_on'  => "post_publish\\{$slug}",
					'toggle_off' => array(
						"post_depublish\\{$slug}",
						"post_depublish_delete\\{$slug}",
					),
				),
				'args'    => array(
					"post\\{$slug}" => 'WordPoints_Hook_Arg',
				),
			)
		);
	}

	if ( post_type_supports( $slug, 'comments' ) ) {

		$events->register(
			"comment_leave\\{$slug}"
			, 'WordPoints_Hook_Event_Comment_Leave'
			, array(
				'actions' => array(
					'toggle_on'  => array(
						"comment_approve\\{$slug}",
						"comment_new\\{$slug}",
					),
					'toggle_off' => "comment_deapprove\\{$slug}",
				),
				'args' => array(
					"comment\\{$slug}" => 'WordPoints_Hook_Arg',
				),
			)
		);
	}

	/**
	 * Fires when registering the hook events for a post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug ("name") of the post type.
	 */
	do_action( 'wordpoints_register_post_type_hook_events', $slug );
}

/**
 * Register the entities for a taxonomy.
 *
 * @since 1.0.0
 *
 * @param string $slug The slug of the taxonomy.
 */
function wordpoints_register_taxonomy_entities( $slug ) {

	$entities = wordpoints_entities();
	$children = $entities->get_sub_app( 'children' );

	$entities->register( "term\\{$slug}", 'WordPoints_Entity_Term' );
	$children->register( "term\\{$slug}", 'id', 'WordPoints_Entity_Term_Id' );

	/**
	 * Fired when registering the entities for a taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The taxonomy's slug.
	 */
	do_action( 'wordpoints_register_taxonomy_entities', $slug );
}

/**
 * Register the data types with the data types registry is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app_registry-apps-data_types
 *
 * @param WordPoints_Class_RegistryI $data_types The data types registry.
 */
function wordpoints_data_types_init( $data_types ) {

	$data_types->register( 'integer', 'WordPoints_Data_Type_Integer' );
	$data_types->register( 'text', 'WordPoints_Data_Type_Text' );
}

/**
 * Check whether a user can view a points log.
 *
 * @since 1.0.0
 *
 * @WordPress\filter wordpoints_user_can_view_points_log
 *
 * @param bool   $can_view Whether the user can view the points log.
 * @param object $log      The points log's data.
 *
 * @return bool Whether the user can view the points log.
 */
function wordpoints_hooks_user_can_view_points_log( $can_view, $log ) {

	if ( ! $can_view ) {
		return $can_view;
	}

	$user_id = get_current_user_id();

	$event_slug = $log->log_type;

	/** @var WordPoints_Hook_ArgI $arg */
	foreach ( wordpoints_hooks()->get_sub_app( 'events' )->get_sub_app( 'args' )->get_children( $event_slug ) as $slug => $arg ) {

		$value = wordpoints_get_points_log_meta( $log->id, $slug, true );

		if ( ! $value ) {
			continue;
		}

		$can_view = wordpoints_entity_user_can_view(
			$user_id
			, $arg->get_entity_slug()
			, $value
		);

		if ( ! $can_view ) {
			break;
		}
	}

	return $can_view;
}

/**
 * Check whether a user can view an entity.
 *
 * @since 1.0.0
 *
 * @param int    $user_id     The user ID.
 * @param string $entity_slug The slug of the entity type.
 * @param mixed  $entity_id   The entity ID.
 *
 * @return bool Whether the user can view this entity.
 */
function wordpoints_entity_user_can_view( $user_id, $entity_slug, $entity_id ) {

	$entity = wordpoints_entities()->get( $entity_slug );

	// If this entity type is not found, we have no way of determining whether it is
	// safe for the user to view it.
	if ( ! ( $entity instanceof WordPoints_Entity ) ) {
		return false;
	}

	$can_view = true;

	if ( $entity instanceof WordPoints_Entity_Restricted_VisibilityI ) {
		$can_view = $entity->user_can_view( $user_id, $entity_id );
	}

	/**
	 * Filter whether a user can view an entity.
	 *
	 * @since 1.0.0
	 *
	 * @param bool              $can_view  Whether the user can view the entity.
	 * @param int               $user_id   The user ID.
	 * @param int               $entity_id The entity ID.
	 * @param WordPoints_Entity $entity    The entity object.
	 */
	return apply_filters(
		'wordpoints_entity_user_can_view'
		, $can_view
		, $user_id
		, $entity_id
		, $entity
	);
}

/**
 * Get the main WordPoints app.
 *
 * @since 1.0.0
 *
 * @return WordPoints_App The main WordPoints app.
 */
function wordpoints_apps() {

	if ( ! isset( WordPoints_App::$main ) ) {
		WordPoints_App::$main = new WordPoints_App( 'apps' );
	}

	return WordPoints_App::$main;
}

/**
 * Get the hooks app.
 *
 * @since 1.0.0
 *
 * @return WordPoints_Hooks The hooks app.
 */
function wordpoints_hooks() {

	if ( ! isset( WordPoints_App::$main ) ) {
		wordpoints_apps();
	}

	return WordPoints_App::$main->get_sub_app( 'hooks' );
}

/**
 * Get the entities app.
 *
 * @since 1.0.0
 *
 * @return WordPoints_App_Registry The hooks app.
 */
function wordpoints_entities() {

	if ( ! isset( WordPoints_App::$main ) ) {
		wordpoints_apps();
	}

	return WordPoints_App::$main->get_sub_app( 'entities' );
}

/**
 * Register sub apps when the apps app is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app-apps
 *
 * @param WordPoints_App $app The main apps app.
 */
function wordpoints_apps_init( $app ) {

	$apps = $app->sub_apps();

	$apps->register( 'hooks', 'WordPoints_Hooks' );
	$apps->register( 'entities', 'WordPoints_App_Registry' );
	$apps->register( 'data_types', 'WordPoints_Class_Registry' );
}

/**
 * Construct a class with a variable number of args.
 *
 * @since 1.0.0
 *
 * @param string $class_name The name of the class to construct.
 * @param array  $args       Up to 4 args to pass to the constructor.
 *
 * @return object|false The constructed object, or false if to many args were passed.
 */
function wordpoints_construct_class_with_args( $class_name, array $args ) {

	switch ( count( $args ) ) {
		case 0:
			return new $class_name();
		case 1:
			return new $class_name( $args[0] );
		case 2:
			return new $class_name( $args[0], $args[1] );
		case 3:
			return new $class_name( $args[0], $args[1], $args[2] );
		case 4:
			return new $class_name( $args[0], $args[1], $args[2], $args[3] );
		default:
			return false;
	}
}

/**
 * Parse a dynamic slug into the dynamic and generic components.
 *
 * In the hooks and entities APIs, we have a convention of using dynamic slugs when
 * certain elements are registered dynamically. Such slugs are of the following
 * format: <generic part>\<dynamic part>. In other words, the generic and dynamic
 * parts are separated by a backslash. This function provides a canonical method of
 * parsing a slug into its constituent parts.
 *
 * @since 1.0.0
 *
 * @param string $slug A slug (for an entity or hook event, for example).
 *
 * @return array The slug parsed into the 'generic' and 'dynamic' portions. If the
 *               slug is not dynamic, the value of each of those keys will be false.
 */
function wordpoints_parse_dynamic_slug( $slug ) {

	$parsed = array( 'dynamic' => false, 'generic' => false );

	$parts = explode( '\\', $slug, 2 );

	if ( isset( $parts[1] ) ) {
		$parsed['dynamic'] = $parts[1];
		$parsed['generic'] = $parts[0];
	}

	return $parsed;
}

/**
 * Get the GUID of the current entity context.
 *
 * Most entities exist only in the context of a specific site on the network (in
 * multisite—when not on multisite they are just global to the install). An
 * example of this would be a Post: a post on one site with the ID 5 is different
 * than a post with that same ID on another site. To get the ID of such an entity's
 * context, you would pass 'site' as the value of the `$slug` arg, and the IDs for
 * both the 'site' and 'network' contexts would be returned.
 *
 * Some entities exist in the context of the network itself, not any particular
 * site. You can get the ID for the context of such an entity by passing 'network'
 * as the value of `$slug`.
 *
 * Still other entities are global to the install, existing across all networks even
 * on a multi-network installation. An example of this would be a User: the user with
 * the ID 3 is the same on every site on the network, and every network in the
 * install.
 *
 * Some entities might exist in other contexts entirely.
 *
 * The context IDs are returned in ascending hierarchical order.
 *
 * @since 1.0.0
 *
 * @param string $slug The slug of the context you want to get the current GUID of.
 *
 * @return array|false The ID of the context you passed in and the IDs of its parent
 *                     contexts, indexed by context slug, or false if any of the
 *                     contexts isn't current.
 */
function wordpoints_entities_get_current_context_id( $slug ) {

	$current_context = array();

	/** @var WordPoints_Class_Registry $contexts */
	$contexts = wordpoints_entities()->get_sub_app( 'contexts' );

	while ( $slug ) {

		$context = $contexts->get( $slug );

		if ( ! $context instanceof WordPoints_Entity_Context ) {
			return false;
		}

		$id = $context->get_current_id();

		if ( false === $id ) {
			return false;
		}

		$current_context[ $slug ] = $id;

		$slug = $context->get_parent_slug();
	}

	return $current_context;
}

/**
 * Checks if we are in network context.
 *
 * There are times on multisite when we are in the context of the network as a whole,
 * and not in the context of any particular site. This includes the network admin
 * screens, and Ajax requests that originate from them.
 *
 * @since 1.0.0
 *
 * @return bool Whether we are in network context.
 */
function wordpoints_is_network_context() {

	if ( ! is_multisite() ) {
		return false;
	}

	if ( is_network_admin() ) {
		return true;
	}

	// See https://core.trac.wordpress.org/ticket/22589
	if (
		defined( 'DOING_AJAX' )
		&& DOING_AJAX
		&& isset( $_SERVER['HTTP_REFERER'] )
		&& preg_match(
			'#^' . preg_quote( network_admin_url(), '#' ) . '#i'
			, esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) )
		)
	) {
		return true;
	}

	/**
	 * Filter whether we are currently in network context.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $in_network_context Whether we are in network context.
	 */
	return apply_filters( 'wordpoints_is_network_context', false );
}

/**
 * Get the GUID of the primary arg of an event, serialized as JSON.
 *
 * If the event does not have a primary arg, an empty string will be returned.
 *
 * @since 1.0.0
 *
 * @param WordPoints_Hook_Event_Args $event_args The event args.
 *
 * @return string The primary arg's GUID, JSON encoded.
 */
function wordpoints_hooks_get_event_primary_arg_guid_json( WordPoints_Hook_Event_Args $event_args ) {

	$entity = $event_args->get_primary_arg();

	if ( ! $entity ) {
		return '';
	}

	$the_guid = $entity->get_the_guid();

	if ( ! $the_guid ) {
		return '';
	}

	return wp_json_encode( $the_guid );
}

/**
 * Register the global cache groups.
 *
 * @since 1.0.0
 *
 * @WordPress\action init 5 Earlier than the default so that the groups will be
 *                          registered before any other code runs.
 */
function wordpoints_hooks_api_add_global_cache_groups() {

	if ( function_exists( 'wp_cache_add_global_groups' ) ) {

		wp_cache_add_global_groups(
			array(
				'wordpoints_hook_periods',
				'wordpoints_hook_period_ids_by_reaction',
			)
		);
	}
}

/**
 * Escape a MySQL identifier.
 *
 * Quotes the identifier with backticks and escapes any backticks within it by
 * doubling them.
 *
 * @since 1.0.0
 *
 * @link https://dev.mysql.com/doc/refman/5.7/en/identifiers.html#idm139700789409120
 *
 * @param string $identifier The identifier (column, table, alias, etc.).
 *
 * @return string The escaped identifier. Already quoted, do not place within
 *                backticks.
 */
function wordpoints_escape_mysql_identifier( $identifier ) {
	return '`' . str_replace( '`', '``', $identifier ) . '`';
}


/**
 * Get an option or network option that must be an array.
 *
 * This is a wrapper for {@see wordpoints_get_maybe_network_option()} that will force
 * the return value to be an array.
 * 
 * @since 1.0.0
 *
 * @param string $option The name of the option to get.
 * @param bool   $network Whether to retrieve a network option, or a regular option.
 *                        By default a regular option will be retrieved, unless
 *                        WordPoints is network active, in which case a network
 *                        option will be retrieved.
 *
 * @return array The option value if it is an array, or an empty array if not.
 */
function wordpoints_get_maybe_network_array_option( $option, $network = null ) {

	$value = wordpoints_get_maybe_network_option( $option, $network );

	if ( ! is_array( $value ) ) {
		$value = array();
	}

	return $value;
}

/**
 * Get an option or network option from the database.
 *
 * @since 1.0.0
 *
 * @param string $option  The name of the option to get.
 * @param bool   $network Whether to retrieve a network option, or a regular option.
 *                        By default a regular option will be retrieved, unless
 *                        WordPoints is network active, in which case a network
 *                        option will be retrieved.
 * @param mixed  $default A default value to return if the option isn't found.
 *
 * @return mixed The option value if it exists, or $default (false by default).
 */
function wordpoints_get_maybe_network_option( $option, $network = null, $default = false ) {

	if ( $network || ( null === $network && is_wordpoints_network_active() ) ) {
		return get_site_option( $option, $default );
	} else {
		return get_option( $option, $default );
	}
}

/**
 * Add an option or network option.
 *
 * @since 1.0.0
 *
 * @param string $option   The name of the option to add.
 * @param mixed  $value    The value for the option.
 * @param bool   $network  Whether to add a network option, or a regular option. By
 *                         default a regular option will be added, unless WordPoints
 *                         is network active, in which case a network option will be
 *                         added.
 * @param string $autoload Whether to automatically load the option. 'yes' (default)
 *                         or 'no'. Does not apply if WordPoints is network active.
 *
 * @return bool Whether the option was added successfully.
 */
function wordpoints_add_maybe_network_option( $option, $value, $network = null, $autoload = 'yes' ) {

	if ( $network || ( null === $network && is_wordpoints_network_active() ) ) {
		return add_site_option( $option, $value );
	} else {
		return add_option( $option, $value, '', $autoload );
	}
}

/**
 * Update an option or network option.
 *
 * @since 1.0.0
 *
 * @param string $option  The name of the option to update.
 * @param mixed  $value   The new value for the option.
 * @param bool   $network Whether to update a network option, or a regular option.
 *                        By default a regular option will be updated, unless
 *                        WordPoints is network active, in which case a network
 *                        option will be updated.
 *
 * @return bool Whether the option was updated successfully.
 */
function wordpoints_update_maybe_network_option( $option, $value, $network = null ) {

	if ( $network || ( null === $network && is_wordpoints_network_active() ) ) {
		return update_site_option( $option, $value );
	} else {
		return update_option( $option, $value );
	}
}

/**
 * Delete an option or network option.
 *
 * @since 1.0.0
 *
 * @param string $option  The name of the option to delete.
 * @param bool   $network Whether to delete a network option, or a regular option.
 *                        By default a regular option will be deleted, unless
 *                        WordPoints is network active, in which case a network
 *                        option will be deleted.
 *
 * @return bool Whether the option was successfully deleted.
 */
function wordpoints_delete_maybe_network_option( $option, $network = null ) {

	if ( $network || ( null === $network && is_wordpoints_network_active() ) ) {
		return delete_site_option( $option );
	} else {
		return delete_option( $option );
	}
}

// EOF
