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
 */
function wordpoints_init_hooks() {

	$hooks = wordpoints_apps()->hooks;

	// Just accessing these causes them to be initialized. The rest of the API will
	// be lazy-loaded as it is needed.
	$hooks->actions;
	$hooks->events;
}
add_action( 'wordpoints_modules_loaded', 'wordpoints_init_hooks' );

/**
 * Register hook reactors when the reactors registry is initialized.
 *
 * @since 1.0.0
 *
 * @param WordPoints_Class_Registry_Persistent $reactors The reactors registry.
 */
function wordpoints_hook_reactors_init( $reactors ) {

	$reactors->register( 'points', 'WordPoints_Hook_Reactor_Points' );
}
add_action( 'wordpoints_hook_reactors_init', 'wordpoints_hook_reactors_init' );

/**
 * Register hook extension when the extension registry is initialized.
 *
 * @since 1.0.0
 *
 * @param WordPoints_Class_Registry_Persistent $extensions The extension registry.
 */
function wordpoints_hook_extension_init( $extensions ) {

	$extensions->register( 'conditions', 'WordPoints_Hook_Extension_Conditions' );
//	$extensions->register( 'periods', 'WordPoints_Hook_Extension_Periods' );
}
add_action( 'wordpoints_hook_extensions_init', 'wordpoints_hook_extension_init' );

/**
 * Register hook conditions when the conditions registry is initialized.
 *
 * @since 1.0.0
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
add_action( 'wordpoints_hook_conditions_init', 'wordpoints_hook_conditions_init' );

/**
 * Register hook actions when the action registry is initialized.
 *
 * @since 1.0.0
 *
 * @param WordPoints_Hook_Actions $actions The action registry.
 */
function wordpoints_hook_actions_init( $actions ) {

	$actions->register(
		'comment_approve'
		, 'WordPoints_Hook_Action'
		, array(
			'action' => 'transition_comment_status',
			'data'   => array(
				'arg_index'    => array( 'comment' => 2 ),
				'requirements' => array( 0 => 'approved' ),
			),
		)
	);

	$actions->register(
		'comment_new'
		, 'WordPoints_Hook_Action_Comment_New'
		, array(
			'action'     => 'wp_insert_comment',
			'arg_number' => 2,
		)
	);

	$actions->register(
		'comment_deapprove'
		, 'WordPoints_Hook_Action'
		, array(
			'action' => 'transition_comment_status',
			'data'   => array(
				'arg_index' => array( 'comment' => 2 ),
				'requirements' => array( 1 => 'approved' ),
			),
		)
	);

	$actions->register(
		'post_publish'
		, 'WordPoints_Hook_Action_Post_Publish'
		, array(
			'action' => 'transition_post_status',
			'data'   => array(
				'arg_index' => array( 'post' => 2 ),
				'requirements' => array( 0 => 'publish' ),
			),
		)
	);

	$actions->register(
		'post_delete'
		, 'WordPoints_Hook_Action'
		, array(
			'action' => 'post_delete',
			'data'   => array(
				'arg_index' => array( 'post' => 0 ),
			),
		)
	);

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
}
add_action( 'wordpoints_hook_actions_init', 'wordpoints_hook_actions_init' );

/**
 * Register hook events when the event registry is initialized.
 *
 * @since 1.0.0
 *
 * @param WordPoints_Hook_Events $events The event registry.
 */
function wordpoints_hook_events_init( $events ) {

//	$events->register(
//		'comment_leave'
//		, 'WordPoints_Hook_Event_Comment_Leave'
//		, array(
//			'actions' => array(
//				'fire' => array( 'comment_approve', 'comment_new' ),
//				'reverse' => 'comment_deapprove',
//				'spam' => 'comment_spam',
//			),
//		)
//	);

	$events->register(
		'post_publish'
		, 'WordPoints_Hook_Event_Post_Publish'
		, array(
			'actions' => array(
				'fire' => 'post_publish',
				'reverse' => 'post_delete', // TODO this should be hooked to post unpublish instead
			),
			'args' => array(
				'post' => 'WordPoints_Hook_Arg_Action',
			),
		)
	);

	$events->register(
		'user_register'
		, 'WordPoints_Hook_Event_User_Register'
		, array(
			'actions' => array(
				'fire' => 'user_register',
				'reverse' => 'user_delete',
			),
			'args' => array(
				'user' => 'WordPoints_Hook_Arg_Action',
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

	if ( is_multisite() ) {

		// TODO network hooks
//		$children->register(
//			'current'
//			, 'site'
//			, 'WordPoints_Hook_Arg_Current_Site'
//		);
	}
}
add_action( 'wordpoints_hook_events_init', 'wordpoints_hook_events_init' );

/**
 * Register hook events when the firer registry is initialized.
 *
 * @since 1.0.0
 *
 * @param WordPoints_Class_Registry_Persistent $firers The firer registry.
 */
function wordpoints_hook_firers_init( $firers ) {

	$firers->register( 'fire', 'WordPoints_Hook_Firer' );
	$firers->register( 'reverse', 'WordPoints_Hook_Firer_Reverse' );
	$firers->register( 'spam', 'WordPoints_Hook_Firer_Spam' );
}
add_action( 'wordpoints_hook_firers_init', 'wordpoints_hook_firers_init' );

/**
 * Register entities when the entities app is initialized.
 *
 * @since 1.0.0
 *
 * @param WordPoints_Entities $entities The entities app.
 */
function wordpoints_entities_init( $entities ) {

	//
	// Entities.
	//

	$entities->register( 'post', 'WordPoints_Entity_Post' );
	$entities->register( 'post_type', 'WordPoints_Entity_Post_Type' );
//	$entities->register( 'comment', 'WordPoints_Entity_Comment' );
	$entities->register( 'user', 'WordPoints_Entity_User' );
	$entities->register( 'user_role', 'WordPoints_Entity_User_Role' );
	$entities->register( 'term', 'WordPoints_Entity_Term' );

	//
	// Attributes.
	//

	$atts = $entities->children;

	$atts->register( 'post', 'content', 'WordPoints_Entity_Post_Content' );
	$atts->register( 'post_type', 'name', 'WordPoints_Entity_Post_Type_Name' );
	$atts->register( 'term', 'id', 'WordPoints_Entity_Term_Id' );
	$atts->register( 'user_role', 'name', 'WordPoints_Entity_User_Role_Name' );

	//
	// Relationships.
	//

	$children = $entities->children;

	$children->register( 'post', 'author', 'WordPoints_Entity_Post_Author' );
	$children->register( 'post', 'type', 'WordPoints_Entity_Post_Type_Relationship' );
	$children->register( 'post', 'terms', 'WordPoints_Entity_Post_Terms' );
	$children->register( 'user', 'roles', 'WordPoints_Entity_User_Roles' );

//	$children->register(
//		'comment'
//		, 'post'
//		, 'WordPoints_Entity_Comment_Post'
//	);
//
//	$children->register(
//		'comment'
//		, 'author'
//		, 'WordPoints_Entity_Comment_Author'
//	);

	foreach ( get_post_types( array( 'public' => true ), false ) as $slug => $post_type ) {

	}
}
add_action( 'wordpoints_entities_init', 'wordpoints_entities_init' );

/**
 *
 *
 * @since 1.
 *
 * @param WordPoints_Class_RegistryI $data_types
 */
function wordpoints_data_types_init( $data_types ) {

	$data_types->register( 'integer', 'WordPoints_Data_Type_Integer' );
	$data_types->register( 'text', 'WordPoints_Data_Type_Text' );

//	/** @var WordPoints_Class_Registry_ChildrenI $formats */
//	$formats = $data_types->formats;
//
//	$formats->register( 'string', 'text', 'WordPoints_Data_Type_Format' );
//
//	/** @var WordPoints_Class_Registry_ChildrenI $specs */
//	$specs = $data_types->specs = new WordPoints_Class_Registry_Children;
//
//	$specs->register( 'integer', 'max', 'WordPoints_Spec_Integer_Max' );
//	$specs->register( 'text', 'max_length', 'WordPoints_Spec_Text_Length_Max' );
}

function wordpoints_hooks_user_can_view_points_log( $can_view, $log ) {

	if ( ! $can_view ) {
		return $can_view;
	}

	$user_id = get_current_user_id();

	$event_slug = $log->log_type;
//
//	if ( 'reverse_' === substr( $event_slug, 0, 8 ) ) {
//		$event_slug = substr( $event_slug, 8 );
//	}

	$wordpoints_apps = wordpoints_apps();
	$entities = $wordpoints_apps->entities;
	$hooks = $wordpoints_apps->hooks;

	/** @var WordPoints_Hook_Arg $arg */
	foreach ( $hooks->events->args->get( $event_slug ) as $slug => $arg ) {

		$value = wordpoints_get_points_log_meta( $log->id, $slug, true );

		if ( ! $value ) {
			return $can_view;
		}

		$entity = $entities->get( $arg->get_entity_slug() );

		if (
			! ( $entity instanceof WordPoints_Entity )
			|| ! $entity->user_can_view( $user_id, $value )
		) {
			return false;
		}
	}

	return $can_view;
}

/**
 * Get the main WordPoints app.
 *
 * @since 1.0.0
 *
 * @return WordPoints_Apps The main WordPoints app.
 */
function wordpoints_apps() {

	if ( ! isset( WordPoints_Apps::$main_app ) ) {
		WordPoints_Apps::$main_app = new WordPoints_Apps();
	}

	return WordPoints_Apps::$main_app;
}

/**
 *
 *
 * @since 1.0.0
 *
 * @param $apps WordPoints_Class_Registry_Persistent
 */
function wordpoints_hooks_app_init( $apps ) {

	$apps->register( 'hooks', 'WordPoints_Hooks' );
	$apps->register( 'entities', 'WordPoints_Entities' );
	$apps->register( 'data_types', 'WordPoints_Class_Registry' );
}
add_action( 'wordpoints_apps_init', 'wordpoints_hooks_app_init' );

// EOF
