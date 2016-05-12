<?php

/**
 * Hook reactor class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Bootstrap for performing pre-scripted reactions when an event is fired.
 *
 * When a hook event fires, it is the job of the reactor to perform the action
 * specified for each reaction object. For most reactors this means that they must
 * "hit" a "target". For example, it might award points to a particular user.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Hook_Reactor implements WordPoints_Hook_SettingsI {

	/**
	 * The unique slug identifying this hook reactor.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The types of args that this reactor can target.
	 *
	 * @since 1.0.0
	 *
	 * @var string|string[]
	 */
	protected $arg_types;

	/**
	 * The slugs of the action types that this reactor listens for.
	 *
	 * @since 1.0.0
	 *
	 * @var string|string[]
	 */
	protected $action_types;

	/**
	 * The settings fields used by this reactor.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $settings_fields;

	/**
	 * Get the slug of this reactor.
	 *
	 * @since 1.0.0
	 *
	 * @return string The reactor's slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get a list of the slugs of each type of arg that this reactor supports.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] The slugs of the arg types this reactor supports.
	 */
	public function get_arg_types() {
		return (array) $this->arg_types;
	}

	/**
	 * Get a list of the slugs of the action types that this reactor listens for.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] The slugs of the action types this reactor listens for.
	 */
	public function get_action_types() {
		return (array) $this->action_types;
	}

	/**
	 * Get the settings fields used by the reactor.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] The meta keys used to store this reactor's settings.
	 */
	public function get_settings_fields() {
		return $this->settings_fields;
	}

	/**
	 * Get the data the scripts need for the UI.
	 *
	 * @since 1.0.0
	 *
	 * @return array Any data that needs to be present for the scripts in the UI.
	 */
	public function get_ui_script_data() {

		return array(
			'slug'         => $this->get_slug(),
			'fields'       => $this->get_settings_fields(),
			'arg_types'    => $this->get_arg_types(),
			'action_types' => $this->get_action_types(),
		);
	}

	/**
	 * Check what context this reactor exists in.
	 *
	 * When a reactor is not network-wide, network reactions are not supported. For
	 * example, the points reactor is not network-wide when WordPoints isn't network-
	 * active, because the points types are created per-site. We default all reactors
	 * to being network wide only when WordPoints is network-active, but some may
	 * need to override this.
	 *
	 * @since 1.0.0
	 *
	 * @return string The slug of the context in which this reactor exists.
	 */
	public function get_context() {

		return is_wordpoints_network_active() ? 'network' : 'site';
	}

	/**
	 * @since 1.0.0
	 */
	public function validate_settings(
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator,
		WordPoints_Hook_Event_Args $event_args
	) {

		if (
			empty( $settings['target'] )
			|| ! is_array( $settings['target'] )
		) {

			$validator->add_error( __( 'Invalid target.', 'wordpoints' ), 'target' );

		} else {

			$target = $event_args->get_from_hierarchy( $settings['target'] );

			if (
				! $target instanceof WordPoints_Entity
				|| ! in_array( $target->get_slug(), (array) $this->arg_types )
			) {
				$validator->add_error( __( 'Invalid target.', 'wordpoints' ), 'target' );
			}
		}

		return $settings;
	}

	/**
	 * @since 1.0.0
	 */
	public function update_settings( WordPoints_Hook_ReactionI $reaction, array $settings ) {
		$reaction->update_meta( 'target', $settings['target'] );
	}

	/**
	 * Perform an action when the reactor is hit by an event being fired.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The hook fire object.
	 */
	abstract public function hit( WordPoints_Hook_Fire $fire );
}

// EOF
