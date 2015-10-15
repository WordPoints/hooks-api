<?php

/**
 * Base hook reactor class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Bootstrap for performing actions when an event is fired.
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
	 * The types of args that this reactor can relate target.
	 *
	 * @since 1.0.0
	 *
	 * @var string|string[]
	 */
	protected $arg_types;

	protected $settings;

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

	public function get_slug() {
		return $this->slug;
	}

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
		return $this->settings;
	}

	public function validate_settings(
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator,
		WordPoints_Hook_Event_Args $event_args
	) {

		if (
			! isset( $settings['target'] )
			|| ! $validator->validate_arg_hierarchy( $settings['target'], $this->arg_types )
		) {
			$validator->add_error( 'The target must be a %s.', 'target' ); // TODO
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
	 * @since    1.0.0
	 *
	 * @param WordPoints_Hook_Event_Args         $event_args
	 * @param WordPoints_Hook_Reaction_Validator $reaction
	 *
	 * @return
	 * @internal param WordPoints_Entity_HierarchyI $args
	 */
	abstract public function hit( WordPoints_Hook_Event_Args $event_args, WordPoints_Hook_Reaction_Validator $reaction );
}

interface WordPoints_Hook_Reactor_ReverseI {

	/**
	 * Reverse all hits from an event associated with a given argument.
	 *
	 * @since    1.0.0
	 *
	 * @param WordPoints_Hook_Event_Args    $event_args
	 * @param string|WordPoints_Hook_EventI $event The object for the event.
	 *
	 * @return
	 */
	public function reverse_hits( WordPoints_Hook_Event_Args $event_args, WordPoints_Hook_EventI $event );
}

interface WordPoints_Hook_Reactor_SpamI {

	/**
	 * Reverse all hits from an event associated with a given argument.
	 *
	 * @since    1.0.0
	 *
	 * @param WordPoints_Hook_Event_Args    $event_args
	 * @param string|WordPoints_Hook_EventI $event The object for the event.
	 *
	 * @return
	 */
	public function spam_hits( WordPoints_Hook_Event_Args $event_args, WordPoints_Hook_EventI $event );
}


// EOF
