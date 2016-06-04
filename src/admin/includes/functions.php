<?php

/**
 * Administration side functions.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Register the admin apps when the main app is initialized.
 *
 * @since 1.0.0
 *
 * @WordPress\action wordpoints_init_app-apps
 *
 * @param WordPoints_App $app The main WordPoints app.
 */
function wordpoints_hooks_register_admin_apps( $app ) {

	$apps = $app->sub_apps();

	$apps->register( 'admin', 'WordPoints_App' );

	/** @var WordPoints_App $admin */
	$admin = $apps->get( 'admin' );

	$admin->sub_apps()->register( 'screen', 'WordPoints_Admin_Screens' );
}

/**
 * Register the admin screens.
 *
 * @since 1.0.0
 *
 * @WordPress\action admin_menu
 * @WordPress\action network_admin_menu
 */
function wordpoints_hooks_api_admin_menu() {

	$wordpoints_menu = wordpoints_get_main_admin_menu();

	/** @var WordPoints_Admin_Screens $admin_screens */
	$admin_screens = wordpoints_apps()->admin->screen;

	// Hooks page.
	$id = add_submenu_page(
		$wordpoints_menu
		, __( 'WordPoints — Points Types', 'wordpoints' )
		, __( 'Points Types', 'wordpoints' )
		, 'manage_options'
		, 'wordpoints_points_types'
		, array( $admin_screens, 'display' )
	);

	if ( $id ) {
		$admin_screens->register( $id, 'WordPoints_Admin_Screen_Points_Types' );
	}
}

/**
 * Register module admin scripts.
 *
 * @since 1.0.0
 *
 * @WordPress\action admin_init
 */
function wordpoints_hooks_admin_register_scripts() {

	$assets_url = wordpoints_modules_url( '/assets', dirname( __FILE__ ) );

	// CSS

	wp_register_style(
		'wordpoints-hooks-admin'
		, $assets_url . '/css/hooks.css'
		, array( 'dashicons', 'wp-jquery-ui-dialog' )
		, WORDPOINTS_VERSION
	);

	// JS

	wp_register_script(
		'wordpoints-admin-points-types'
		, $assets_url . '/js/points-types.js'
		, array( 'backbone', 'jquery-ui-dialog', 'wp-util' )
		, WORDPOINTS_VERSION
	);

	wp_register_script(
		'wordpoints-hooks-models'
		, $assets_url . '/js/hooks/models.js'
		, array( 'backbone', 'jquery-ui-dialog', 'wp-util' )
		, WORDPOINTS_VERSION
	);

	wp_register_script(
		'wordpoints-hooks-views'
		, $assets_url . '/js/hooks/views.js'
		, array( 'wordpoints-hooks-models' )
		, WORDPOINTS_VERSION
	);

	wp_localize_script(
		'wordpoints-admin-points-types'
		, 'WordPointsPointsTypesL10n'
		, array(
			'confirmDelete' => esc_html__( 'Are you sure that you want to delete this points type? This will delete all related logs and event hooks.', 'wordpoints' )
			                   . ' ' . esc_html__( 'Once a points type has been deleted, you cannot bring it back.', 'wordpoints' ),
			'confirmTitle'  => esc_html__( 'Are you sure?', 'wordpoints' ),
			'deleteText'    => esc_html__( 'Delete', 'wordpoints' ),
			'cancelText'    => esc_html__( 'Cancel', 'wordpoints' ),
		)
	);

	wp_localize_script(
		'wordpoints-hooks-views'
		, 'WordPointsHooksAdminL10n'
		, array(
			'unexpectedError' => __( 'There was an unexpected error. Try reloading the page.', 'wordpoints' ),
			'changesSaved'    => __( 'Your changes have been saved.', 'wordpoints' ),
			/* translators: the name of the field that cannot be empty */
			'emptyField'      => sprintf( __( '%s cannot be empty.', 'wordpoints' ), '{{ data.label }}' ),
			'confirmDelete'   => __( 'Are you sure that you want to delete this reaction? This action cannot be undone.', 'wordpoints' ),
			'confirmTitle'    => __( 'Are you sure?', 'wordpoints' ),
			'deleteText'      => __( 'Delete', 'wordpoints' ),
			'cancelText'      => __( 'Cancel', 'wordpoints' ),
			'separator'       => __( ' » ', 'wordpoints' ),
			'target_label'    => __( 'Target', 'wordpoints' ),
			// translators: form field
			'cannotBeChanged' => __( '(cannot be changed)', 'wordpoints' ),
			'fieldsInvalid'   => __( 'Error: the values of some fields are invalid. Please correct these and then try again.', 'wordpoints' ),
		)
	);

	wp_script_add_data(
		'wordpoints-hooks-views'
		, 'wordpoints-templates'
		, '
		<script type="text/template" id="tmpl-wordpoints-hook-reaction">
			<div class="view">
				<div class="title"></div>
				<button type="button" class="edit button-secondary">
					' . esc_html__( 'Edit', 'wordpoints' ) . '
				</button>
				<button type="button" class="close button-secondary">
					' . esc_html__( 'Close', 'wordpoints' ) . '
				</button>
			</div>
			<div class="form">
				<form>
					<div class="fields">
						<div class="settings"></div>
						<div class="target"></div>
					</div>
					<div class="messages">
						<div class="success"></div>
						<div class="err"></div>
					</div>
					<div class="actions">
						<div class="spinner-overlay">
							<span class="spinner is-active"></span>
						</div>
						<div class="action-buttons">
							<button type="button" class="save button-primary" disabled>
								' . esc_html__( 'Save', 'wordpoints' ) . '
							</button>
							<button type="button" class="cancel button-secondary">
								' . esc_html__( 'Cancel', 'wordpoints' ) . '
							</button>
							<button type="button" class="close button-secondary">
								' . esc_html__( 'Close', 'wordpoints' ) . '
							</button>
							<button type="button" class="delete button-secondary">
								' . esc_html__( 'Delete', 'wordpoints' ) . '
							</button>
						</div>
					</div>
				</form>
			</div>
		</script>

		<script type="text/template" id="tmpl-wordpoints-hook-arg-selector">
			<div class="arg-selector">
				<label>
					{{ data.label }}
					<select name="{{ data.name }}"></select>
				</label>
			</div>
		</script>

		<script type="text/template" id="tmpl-wordpoints-hook-arg-option">
			<option value="{{ data.slug }}">{{ data.title }}</option>
		</script>

		<script type="text/template" id="tmpl-wordpoints-hook-reaction-field">
			<p class="description description-thin">
				<label>
					{{ data.label }}
					<input type="{{ data.type }}" class="widefat" name="{{ data.name }}"
					       value="{{ data.value }}"/>
				</label>
			</p>
		</script>

		<script type="text/template" id="tmpl-wordpoints-hook-reaction-select-field">
			<p class="description description-thin">
				<label>
					{{ data.label }}
					<select name="{{ data.name }}" class="widefat"></select>
				</label>
			</p>
		</script>

		<script type="text/template" id="tmpl-wordpoints-hook-reaction-hidden-field">
			<input type="hidden" name="{{ data.name }}" value="{{ data.value }}"/>
		</script>
		'
	);

	wp_register_script(
		'wordpoints-hooks-reactor-points'
		, $assets_url . '/js/hooks/reactors/points.js'
		, array( 'wordpoints-hooks-views' )
		, WORDPOINTS_VERSION
	);

	wp_register_script(
		'wordpoints-hooks-extension-conditions'
		, $assets_url . '/js/hooks/extensions/conditions.js'
		, array( 'wordpoints-hooks-views' )
		, WORDPOINTS_VERSION
	);

	wp_script_add_data(
		'wordpoints-hooks-extension-conditions'
		, 'wordpoints-templates'
		, '
			<script type="text/template" id="tmpl-wordpoints-hook-condition-groups">
				<div class="conditions-title section-title">
					<h4>' . esc_html__( 'Conditions', 'wordpoints' ) . '</h4>
					<button type="button" class="add-new button-secondary button-link">
						<span class="screen-reader-text">' . esc_html__( 'Add New Condition', 'wordpoints' ) . '</span>
						<span class="dashicons dashicons-plus"></span>
					</button>
				</div>
				<div class="add-condition-form hidden">
					<div class="no-conditions hidden">
						' . esc_html__( 'No conditions available.', 'wordpoints' ) . '
					</div>
					<div class="condition-selectors">
						<div class="arg-selectors"></div>
						<div class="condition-selector"></div>
					</div>
					<button type="button" class="confirm-add-new button-secondary" disabled aria-label="' . esc_attr__( 'Add Condition', 'wordpoints' ) . '">
						' . esc_html_x( 'Add', 'reaction condition', 'wordpoints' ) . '
					</button>
					<button type="button" class="cancel-add-new button-secondary" aria-label="' . esc_attr__( 'Cancel Adding New Condition', 'wordpoints' ) . '">
						' . esc_html_x( 'Cancel', 'reaction condition', 'wordpoints' ) . '
					</button>
				</div>
				<div class="condition-groups section-content"></div>
			</script>

			<script type="text/template" id="tmpl-wordpoints-hook-reaction-condition-group">
				<div class="condition-group-title"></div>
			</script>

			<script type="text/template" id="tmpl-wordpoints-hook-reaction-condition">
				<div class="condition-controls">
					<div class="condition-title"></div>
					<button type="button" class="delete button-secondary button-link">
						<span class="screen-reader-text">' . esc_html__( 'Remove Condition', 'wordpoints' ) . '</span>
						<span class="dashicons dashicons-no"></span>
					</button>
				</div>
				<div class="condition-settings"></div>
			</script>

			<script type="text/template" id="tmpl-wordpoints-hook-condition-selector">
				<label>
					{{ data.label }}
					<select name="{{ data.name }}"></select>
				</label>
			</script>
		'
	);

	wp_register_script(
		'wordpoints-hooks-extension-periods'
		, $assets_url . '/js/hooks/extensions/periods.js'
		, array( 'wordpoints-hooks-views' )
		, WORDPOINTS_VERSION
	);

	wp_script_add_data(
		'wordpoints-hooks-extension-periods'
		, 'wordpoints-templates'
		, '
			<script type="text/template" id="tmpl-wordpoints-hook-periods">
				<div class="periods-title section-title">
					<h4>' . esc_html__( 'Rate Limit', 'wordpoints' ) . '</h4>
				</div>
				<div class="periods section-content"></div>
			</script>
		'
	);
}

/**
 * Export the data for the scripts needed to make the hooks UI work.
 *
 * @since 1.0.0
 */
function wordpoints_hooks_ui_setup_script_data() {

	$hooks = wordpoints_hooks();

	$extensions_data = wordpoints_hooks_ui_get_script_data_from_objects(
		$hooks->get_sub_app( 'extensions' )->get_all()
		, 'extension'
	);

	$reactor_data = wordpoints_hooks_ui_get_script_data_from_objects(
		$hooks->get_sub_app( 'reactors' )->get_all()
		, 'reactor'
	);

	$event_action_types = wordpoints_hooks_ui_get_script_data_event_action_types();
	$entities_data = wordpoints_hooks_ui_get_script_data_entities();

	$data = array(
		'fields'     => (object) array(),
		'reactions'  => (object) array(),
		'events'     => (object) array(),
		'extensions' => $extensions_data,
		'entities'   => $entities_data,
		'reactors'   => $reactor_data,
		'event_action_types' => $event_action_types,
	);

	/**
	 * Filter the hooks data used to provide the UI.
	 *
	 * This is currently exported as JSON to the Backbone.js powered UI. But
	 * that could change in the future. The important thing is that the data is
	 * bing exported and will be used by something somehow.
	 *
	 * @param array $data The data.
	 */
	$data = apply_filters( 'wordpoints_hooks_ui_data', $data );

	wp_localize_script(
		'wordpoints-hooks-models'
		, 'WordPointsHooksAdminData'
		, $data
	);
}

/**
 * Get the UI script data from a bunch of objects.
 *
 * @since 1.0.0
 *
 * @param object[] $objects Objects that might provide script UI data.
 * @param string   $type    The type of objects. Used to automatically enqueue
 *                          scripts for the objects.
 *
 * @return array The data extracted from the objects.
 */
function wordpoints_hooks_ui_get_script_data_from_objects( $objects, $type ) {

	$data = array();

	foreach ( $objects as $slug => $object ) {

		if ( $object instanceof WordPoints_Hook_UI_Script_Data_ProviderI ) {
			$data[ $slug ] = $object->get_ui_script_data();
		}

		if ( wp_script_is( "wordpoints-hooks-{$type}-{$slug}", 'registered' ) ) {
			wp_enqueue_script( "wordpoints-hooks-{$type}-{$slug}" );
		}
	}

	return $data;
}

/**
 * Get the entities data for use in the hooks UI.
 *
 * @since 1.0.0
 *
 * @return array The entities data for use in the hooks UI.
 */
function wordpoints_hooks_ui_get_script_data_entities() {

	$entities = wordpoints_entities();

	$entities_data = array();

	/** @var WordPoints_Class_Registry_Children $entity_children */
	$entity_children = $entities->get_sub_app( 'children' );

	/** @var WordPoints_Entity $entity */
	foreach ( $entities->get_all() as $slug => $entity ) {

		$child_data = array();

		/** @var WordPoints_EntityishI $child */
		foreach ( $entity_children->get_children( $slug ) as $child_slug => $child ) {

			$child_data[ $child_slug ] = array(
				'slug'  => $child_slug,
				'title' => $child->get_title(),
			);

			if ( $child instanceof WordPoints_Entity_Attr ) {

				$child_data[ $child_slug ]['_type']     = 'attr';
				$child_data[ $child_slug ]['data_type'] = $child->get_data_type();

			} elseif ( $child instanceof WordPoints_Entity_Relationship ) {

				$child_data[ $child_slug ]['_type']     = 'relationship';
				$child_data[ $child_slug ]['primary']   = $child->get_primary_entity_slug();
				$child_data[ $child_slug ]['secondary'] = $child->get_related_entity_slug();
			}

			/**
			 * Filter the data for an entity child.
			 *
			 * Entity children include attributes and relationships.
			 *
			 * @param array                $data  The data for the entity child.
			 * @param WordPoints_Entityish $child The child's object.
			 */
			$child_data[ $child_slug ] = apply_filters(
				'wordpoints_hooks_ui_data_entity_child'
				, $child_data[ $child_slug ]
				, $child
			);
		}

		$entities_data[ $slug ] = array(
			'slug'     => $slug,
			'title'    => $entity->get_title(),
			'children' => $child_data,
			'id_field' => $entity->get_id_field(),
			'_type'    => 'entity',
		);

		if ( $entity instanceof WordPoints_Entity_EnumerableI ) {

			$values = array();

			foreach ( $entity->get_enumerated_values() as $value ) {
				if ( $entity->set_the_value( $value ) ) {
					$values[] = array(
						'value' => $entity->get_the_id(),
						'label' => $entity->get_the_human_id(),
					);
				}
			}

			$entities_data[ $slug ]['values'] = $values;
		}

		/**
		 * Filter the data for an entity.
		 *
		 * @param array             $data   The data for the entity.
		 * @param WordPoints_Entity $entity The entity object.
		 */
		$entities_data[ $slug ] = apply_filters(
			'wordpoints_hooks_ui_data_entity'
			, $entities_data[ $slug ]
			, $entity
		);
	}

	return $entities_data;
}

/**
 * Get a list of action types for each event for the hooks UI script data.
 *
 * @since 1.0.0
 *
 * @return array The event action types.
 */
function wordpoints_hooks_ui_get_script_data_event_action_types() {

	// We want a list of the action types for each event. We can start with this list
	// but it is indexed by action slug and then action type and then event slug, so
	// we ned to do some processing.
	$event_index = wordpoints_hooks()->get_sub_app( 'router' )->get_event_index();

	// We don't care about the action slugs, so first we get rid of that bottom level
	// of the array.
	$event_index = call_user_func_array( 'array_merge_recursive', $event_index );

	$event_action_types = array();

	// This leaves us the event indexed by action type. But we actually need to flip
	// this, so that we have the action types indexed by event slug.
	foreach ( $event_index as $action_type => $events ) {
		foreach ( $events as $event => $unused ) {
			$event_action_types[ $event ][ $action_type ] = true;
		}
	}

	return $event_action_types;
}

/**
 * Append templates registered in wordpoints-templates script data to scripts.
 *
 * One day templates will probably be stored in separate files instead.
 *
 * @link https://core.trac.wordpress.org/ticket/31281
 *
 * @since 1.0.0
 *
 * @WordPress\filter script_loader_tag
 *
 * @param string $html   The HTML for the script.
 * @param string $handle The handle of the script.
 *
 * @return string The HTML with templates appended.
 */
function wordpoints_script_templates_filter( $html, $handle ) {

	global $wp_scripts;

	$templates = $wp_scripts->get_data( $handle, 'wordpoints-templates' );

	if ( $templates ) {
		$html .= $templates;
	}

	return $html;
}

/**
 * Initialize the Ajax actions.
 *
 * @since 1.0.0
 *
 * @WordPress\action admin_init
 */
function wordpoints_hooks_admin_ajax() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		new WordPoints_Admin_Ajax_Hooks;
	}
}

// EOF
