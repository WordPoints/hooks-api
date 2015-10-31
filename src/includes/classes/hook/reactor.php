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
 *
 * @property-read WordPoints_Hook_Reaction_StorageI $reactions
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
	 * The settings fields used by this reactor.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $settings_fields;

	/**
	 * The reaction storage class this reactor uses.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $reactions_class = 'WordPoints_Hook_Reaction_Storage_Options';

	/**
	 * Object for accessing hook reactions for this reactor.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reaction_StorageI
	 */
	protected $reactions;

	/**
	 * @since 1.0.0
	 */
	public function __get( $var ) {

		if ( 'reactions' === $var ) {
			if ( ! isset( $this->reactions ) ) {
				$this->reactions = new $this->reactions_class( $this->slug );
			}

			return $this->reactions;
		}

		return null;
	}

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
	 * @param WordPoints_Hook_Event_Args         $event_args The event args.
	 * @param WordPoints_Hook_Reaction_Validator $reaction   The reaction.
	 */
	abstract public function hit( WordPoints_Hook_Event_Args $event_args, WordPoints_Hook_Reaction_Validator $reaction );
}

// EOF
