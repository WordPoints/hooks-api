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

	$apps = $app->sub_apps;

	$apps->register( 'admin', 'WordPoints_App' );

	/** @var WordPoints_App $admin */
	$admin = $apps->get( 'admin' );

	$admin->sub_apps->register( 'screen', 'WordPoints_Admin_Screens' );
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

	$extensions_data = array();

	foreach ( $hooks->extensions->get_all() as $slug => $extension ) {

		if ( $extension instanceof WordPoints_Hook_Extension ) {
			$extensions_data[ $slug ] = $extension->get_ui_script_data();
		}

		if ( wp_script_is( "wordpoints-hooks-extension-{$slug}", 'registered' ) ) {
			wp_enqueue_script( "wordpoints-hooks-extension-{$slug}" );
		}
	}

	$reactor_data = array();

	foreach ( $hooks->reactors->get_all() as $slug => $reactor ) {

		if ( $reactor instanceof WordPoints_Hook_Reactor ) {
			$reactor_data[ $slug ] = array(
				'slug'         => $reactor->get_slug(),
				'fields'       => $reactor->get_settings_fields(),
				'arg_types'    => $reactor->get_arg_types(),
				'target_label' => __( 'Award To', 'wordpoints' ),
			);
		}

		if ( wp_script_is( "wordpoints-hooks-reactor-{$slug}", 'registered' ) ) {
			wp_enqueue_script( "wordpoints-hooks-reactor-{$slug}" );
		}
	}

	$entities = wordpoints_entities();

	$entities_data = array();

	/** @var WordPoints_Class_Registry_Children $entity_children */
	$entity_children = $entities->children;

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

				$child_data[ $child_slug ]['_type'] = 'attr';
				$child_data[ $child_slug ]['data_type']  = $child->get_data_type();

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
			$child_data[ $child_slug ] = apply_filters( 'wordpoints_hooks_ui_data_entity_child', $child_data[ $child_slug ], $child );
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
		$entities_data[ $slug ] = apply_filters( 'wordpoints_hooks_ui_data_entity', $entities_data[ $slug ], $entity );
	}

	$data = array(
		'fields'     => (object) array(),
		'reactions'  => (object) array(),
		'events'     => (object) array(),
		'extensions' => $extensions_data,
		'entities'   => $entities_data,
		'reactors'   => $reactor_data,
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
