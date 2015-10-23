<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Admin_Screen_Points_Types extends WordPoints_Admin_Screen {

	protected $current_points_type;

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hooks
	 */
	protected $hooks;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		$this->hooks = wordpoints_apps()->hooks;
		$this->entities = wordpoints_apps()->entities;
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_title() {
		return _x( 'Points Types', 'page title', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function hooks() {

		parent::hooks();

		add_action( 'add_meta_boxes', array( $this, 'add_points_settings_meta_box' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_event_meta_boxes' ) );
	}

	/**
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_style( 'wordpoints-hooks-admin' );

		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'wordpoints-hooks-reactor-points' );

		$extensions_data = array();

		foreach ( $this->hooks->extensions->get() as $slug => $class ) {

			/** @var WordPoints_Hook_Extension $extension */
			$extension = new $class();

			$extensions_data[ $slug ] = $extension->get_data();

			if ( wp_script_is( "wordpoints-hooks-extension-{$slug}", 'registered' ) ) {
				wp_enqueue_script( "wordpoints-hooks-extension-{$slug}" );
			}
		}

		$entities_data = array();

		$entity_children = $this->entities->children;

		/** @var WordPoints_Entity $entity */
		foreach ( $this->entities->get() as $slug => $entity ) {

			$child_data = array();

			/** @var WordPoints_EntityishI $child */
			foreach ( $entity_children->get( $slug ) as $child_slug => $child ) {

				$child_data[ $child_slug ] = array(
					'slug'  => $child_slug,
					'title' => $child->get_title(),
				);

				if ( $child instanceof WordPoints_Entity_Attr ) {

					$child_data[ $child_slug ]['_type'] = 'attr';
					$child_data[ $child_slug ]['type']  = $child->get_data_type();
//					$child_data[ $child_slug ]['specs'] = $child->get_data_type_specs();

					if ( $child instanceof WordPoints_Entity_Attr_Enumerable ) {
						$child_data[ $child_slug ]['values'] = $child->get_enumerated_values();
					}

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

			/**
			 * Filter the data for an entity.
			 *
			 * @param array             $data   The data for the entity.
			 * @param WordPoints_Entity $entity The entity object.
			 */
			$entities_data[ $slug ] = apply_filters( 'wordpoints_hooks_ui_data_entity', $entities_data[ $slug ], $entity );
		}

		/** @var WordPoints_Hook_Reactor_Points $points_reactor */
		$points_reactor = $this->hooks->reactors->get( 'points' );

		$data = array(
			'fields'     => (object) array(),
			'reactions'  => (object) array(),
			'events'     => (object) array(),
			'extensions' => $extensions_data,
			'entities'   => $entities_data,
			'reactors'   => array(
				'points' => array(
					'slug'         => $points_reactor->get_slug(),
					'fields'       => $points_reactor->get_settings_fields(),
					'arg_types'    => $points_reactor->get_arg_types(),
					'target_label' => __( 'Award To', 'wordpoints' ),
				),
			),
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
	 * @since 0.1.0
	 */
	function footer_scripts() {

		?>

		<script type="text/javascript">
			jQuery( document ).ready( function ( $ ) {

				$( '.if-js-closed' )
					.removeClass( 'if-js-closed' )
					.addClass( 'closed' );

				postboxes.add_postbox_toggles(
					<?php echo wp_json_encode( $this->id ); ?>
				);
			} );
		</script>

		<?php
	}

	public function add_points_settings_meta_box() {

		if ( ! current_user_can( 'manage_wordpoints_points_types' ) ) {
			return;
		}

		add_meta_box(
			'settings'
			, __( 'Settings', 'wordpoints' )
			, array( $this, 'display_points_settings_meta_box' )
			, $this->id
			, 'side'
			, 'default'
		);
	}

	public function display_points_settings_meta_box() {

		if ( ! current_user_can( 'manage_wordpoints_points_types' ) ) {
			return;
		}

		$slug = $this->current_points_type;

		$add_new = 0;

		$points_type = wordpoints_get_points_type( $slug );

		if ( ! $points_type ) {

			$points_type = array();
			$add_new     = wp_create_nonce( 'wordpoints_add_new_points_type' );
		}

		$points_type = array_merge(
			array(
				'name'   => '',
				'prefix' => '',
				'suffix' => '',
			)
			, $points_type
		);

		?>

		<form method="post">
			<?php if ( $slug ) : ?>
				<p>
					<span class="wordpoints-points-slug">
						<em>
							<?php echo esc_html( sprintf( __( 'Slug: %s', 'wordpoints' ), $slug ) ); ?>
						</em>
					</span>
				</p>
				<?php wp_nonce_field( "wordpoints_update_points_type-$slug", 'update_points_type' ); ?>
			<?php endif; ?>

			<?php

			/**
			 * At the top of the points type settings form.
			 *
			 * Called before the default inputs are displayed.
			 *
			 * @since 1.0.0
			 *
			 * @param string $points_type The slug of the points type.
			 */
			do_action( 'wordpoints_points_type_form_top', $slug );

			if ( $add_new ) {

				// Mark the prefix and suffix optional on the add new form.
				$prefix = _x( 'Prefix (optional):', 'points type', 'wordpoints' );
				$suffix = _x( 'Suffix (optional):', 'points type', 'wordpoints' );

			} else {

				$prefix = _x( 'Prefix:', 'points type', 'wordpoints' );
				$suffix = _x( 'Suffix:', 'points type', 'wordpoints' );
			}

			?>

			<p>
				<label
					for="points-name-<?php echo esc_attr( $slug ); ?>"><?php echo esc_html_x( 'Name:', 'points type', 'wordpoints' ); ?></label>
				<input class="widefat" type="text"
				       id="points-name-<?php echo esc_attr( $slug ); ?>"
				       name="points-name"
				       value="<?php echo esc_attr( $points_type['name'] ); ?>"/>
			</p>
			<p>
				<label
					for="points-prefix-<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $prefix ); ?></label>
				<input class="widefat" type="text"
				       id="points-prefix-<?php echo esc_attr( $slug ); ?>"
				       name="points-prefix"
				       value="<?php echo esc_attr( $points_type['prefix'] ); ?>"/>
			</p>
			<p>
				<label
					for="points-suffix-<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $suffix ); ?></label>
				<input class="widefat" type="text"
				       id="points-suffix-<?php echo esc_attr( $slug ); ?>"
				       name="points-suffix"
				       value="<?php echo esc_attr( $points_type['suffix'] ); ?>"/>
			</p>

			<?php

			/**
			 * At the bottom of the points type settings form.
			 *
			 * Called below the default inputs, but above the submit buttons.
			 *
			 * @since 1.0.0
			 *
			 * @param string $points_type The slug of the points type.
			 */
			do_action( 'wordpoints_points_type_form_bottom', $slug );

			?>

			<input type="hidden" name="points-slug"
			       value="<?php echo esc_attr( $slug ); ?>"/>
			<input type="hidden" name="add_new" class="add_new"
			       value="<?php echo esc_attr( $add_new ); ?>"/>

			<div class="hook-control-actions">
				<div class="alignleft">
					<?php

					if ( ! $add_new ) {
						wp_nonce_field( "wordpoints_delete_points_type-{$slug}", 'delete-points-type-nonce' );
						submit_button( _x( 'Delete', 'points type', 'wordpoints' ), 'delete', 'delete-points-type', false, array( 'id' => "delete_points_type-{$slug}" ) );
					}

					?>
				</div>
				<div class="alignright">
					<?php submit_button( _x( 'Save', 'points type', 'wordpoints' ), 'button-primary hook-control-save right', 'save-points-type', false, array( 'id' => "points-{$slug}-save" ) ); ?>
					<span class="spinner"></span>
				</div>
				<br class="clear"/>
			</div>
		</form>

		<?php
	}

	public function add_event_meta_boxes() {

		if ( ! $this->current_points_type ) {
			return;
		}

		/** @var WordPoints_Hook_EventI $event */
		foreach ( $this->hooks->events->get() as $slug => $event ) {

			add_meta_box(
				"{$this->current_points_type}-{$slug}"
				, $event->get_title()
				, array( $this, 'display_event_meta_box' )
				, $this->id
				, 'events'
				, 'default'
				, array(
					'points_type' => $this->current_points_type,
					'slug'        => $slug,
				)
			);
		}
	}

	public function display_event_meta_box( $points_type, $meta_box ) {

		$event_slug = $meta_box['args']['slug'];

		/** @var WordPoints_Hook_Reactor $points_reactor */
		$points_reactor = $this->hooks->reactors->get( 'points' );

		$data = array();

		foreach ( $points_reactor->reactions->get_reactions_to_event( $event_slug ) as $id => $reaction ) {
			if ( $reaction->get_meta( 'points_type' ) === $this->current_points_type ) {
				$data[] = WordPoints_Admin_Ajax_Hooks::prepare_hook_reaction(
					$reaction
				);
			}
		}

		$args = $this->hooks->events->args->get( $event_slug );

		$event_data = array( 'args' => array() );

		foreach ( $args as $slug => $arg ) {

			$event_data['args'][ $slug ] = array(
				'slug' => $slug,
			);

			if (
				$arg instanceof WordPoints_Hook_Arg
				&& ! ( $arg instanceof WordPoints_Hook_Arg_Action )
			) {
				$event_data['args'][ $slug ]['title'] = $arg->get_title();
			}
		}

		?>

		<script>
			WordPointsHooksAdminData.events.<?php echo sanitize_key( $event_slug ); ?> = <?php echo wp_json_encode( $event_data ); ?>;
			WordPointsHooksAdminData.reactions.<?php echo sanitize_key( $event_slug ); ?> = <?php echo wp_json_encode( $data ); ?>;
		</script>

		<div class="wordpoints-hook-reaction-group-container">
			<div class="wordpoints-hook-reaction-group"
				data-wordpoints-hooks-hook-event="<?php echo esc_attr( $event_slug ); ?>"
				data-wordpoints-hooks-points-type="<?php echo esc_attr( $this->current_points_type ); ?>"
				data-wordpoints-hooks-create-nonce="<?php echo esc_attr( wp_create_nonce( 'wordpoints_create_hook_reaction|points' ) ); ?>"
				data-wordpoints-hooks-reactor="points">
			</div>

			<div class="spinner-overlay" style="display: block;">
				<span class="spinner is-active"></span>
			</div>

			<div class="error hidden">
				<p></p>
			</div>

			<div class="controls">
				<button type="button" class="button-primary add-reaction">
					<?php esc_html_e( 'Add New', 'wordpoints' ); ?>
				</button>
			</div>
		</div>

		<?php
	}

	public function load() {

		$this->save_points_type();

		$points_types = wordpoints_get_points_types();

		// Show a tab for each points type.
		$tabs = array();

		foreach ( $points_types as $slug => $settings ) {
			$tabs[ $slug ] = $settings['name'];
		}

		$tabs['add-new'] = __( 'Add New', 'wordpoints' );

		$tab = wordpoints_admin_get_current_tab( $tabs );

		if ( 'add-new' !== $tab ) {
			$this->current_points_type = $tab;
		}

		do_action( 'add_meta_boxes', $this->id );

		$this->tabs = $tabs;
	}

	public function save_points_type() {

		if ( ! current_user_can( 'manage_wordpoints_points_types' ) ) {
			return;
		}

		if (
			isset(
				$_POST['save-points-type']
				, $_POST['points-name']
				, $_POST['points-prefix']
				, $_POST['points-suffix']
			)
		) {

			$settings = array();

			$settings['name']   = trim( sanitize_text_field( wp_unslash( $_POST['points-name'] ) ) );
			$settings['prefix'] = ltrim( sanitize_text_field( wp_unslash( $_POST['points-prefix'] ) ) );
			$settings['suffix'] = rtrim( sanitize_text_field( wp_unslash( $_POST['points-suffix'] ) ) );

			if (
				isset( $_POST['points-slug'] )
				&& wordpoints_verify_nonce( 'update_points_type', 'wordpoints_update_points_type-%s', array( 'points-slug' ), 'post' )
			) {

				// - We are updating an existing points type.

				$points_type = sanitize_key( $_POST['points-slug'] );

				$old_settings = wordpoints_get_points_type( $points_type );

				if ( false === $old_settings ) {

					add_settings_error(
						''
						, 'wordpoints_points_type_update'
						, __( 'Error: failed updating points type.', 'wordpoints' )
						, 'updated'
					);

					return;
				}

				if ( is_array( $old_settings ) ) {
					$settings = array_merge( $old_settings, $settings );
				}

				if ( ! wordpoints_update_points_type( $points_type, $settings ) ) {

					add_settings_error(
						''
						, 'wordpoints_points_type_update'
						, __( 'Error: failed updating points type.', 'wordpoints' )
						, 'updated'
					);

				} else {

					add_settings_error(
						''
						, 'wordpoints_points_type_update'
						, __( 'Points type updated.', 'wordpoints' )
						, 'updated'
					);
				}

			} elseif ( wordpoints_verify_nonce( 'add_new', 'wordpoints_add_new_points_type', null, 'post' ) ) {

				// - We are creating a new points type.

				$slug = wordpoints_add_points_type( $settings );

				if ( ! $slug ) {

					add_settings_error(
						''
						, 'wordpoints_points_type_create'
						, __( 'Please choose a unique name for this points type.', 'wordpoints' )
					);

				} else {

					$_GET['tab'] = $slug;

					add_settings_error(
						''
						, 'wordpoints_points_type_create'
						, __( 'Points type created.', 'wordpoints' )
						, 'updated'
					);
				}
			}

		} elseif (
			! empty( $_POST['delete-points-type'] )
			&& isset( $_POST['points-slug'] )
			&& wordpoints_verify_nonce( 'delete-points-type-nonce', 'wordpoints_delete_points_type-%s', array( 'points-slug' ), 'post' )
		) {

			// - We are deleting a points type.

			if ( wordpoints_delete_points_type( sanitize_key( $_POST['points-slug'] ) ) ) {

				add_settings_error(
					''
					, 'wordpoints_points_type_delete'
					, __( 'Points type deleted.', 'wordpoints' )
					, 'updated'
				);

			} else {

				add_settings_error(
					''
					, 'wordpoints_points_type_delete'
					, __( 'Error while deleting.', 'wordpoints' )
				);
			}
		}
	}

	public function display_content() {

		/**
		 * Top of points hooks admin screen.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wordpoints_admin_points_events_head' );

		if ( is_network_admin() ) {
			$title = __( 'Network Events', 'wordpoints' );
			$description = __( 'Award points when various events happen on this network.', 'wordpoints' );
		} else {
			$title = __( 'Events', 'wordpoints' );
			$description = __( 'Award points when various events happen on this site.', 'wordpoints' );
		}

		$points_type = wordpoints_get_points_type( $this->current_points_type );

		?>

		<div class="wordpoints-points-type-meta-box-wrap">

				<form>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
				</form>

				<div id="poststuff">

					<div id="post-body" class="metabox-holder columns-<?php echo 1 === (int) get_current_screen()->get_columns() ? '1' : '2'; ?>">

						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes( $this->id, 'side', $points_type ); ?>
						</div>

						<?php if ( isset( $this->current_points_type ) ) : ?>
							<div class="wordpoints-hook-events-heading">
								<h2><?php echo esc_html( $title ); ?></h2>
								<p class="description"><?php echo esc_html( $description ); ?></p>
							</div>

							<div id="postbox-container-2" class="postbox-container">
								<?php do_meta_boxes( $this->id, 'events', $points_type ); ?>
							</div>
						<?php endif; ?>

					</div>

					<br class="clear">

				</div>

		</div>

		<?php

		/**
		 * Bottom of points hooks admin screen.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wordpoints_admin_points_events_foot' );
	}
}

// EOF
