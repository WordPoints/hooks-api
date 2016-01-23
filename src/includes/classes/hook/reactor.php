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
 * @property-read WordPoints_Hook_Reaction_StorageI|null $reactions
 *                Object for accessing hook reactions for this reactor based on the
 *                current network mode. If a reactor doesn't support network
 *                reactions and network mode is on, this property is not available.
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
	 * @since 1.0.0
	 */
	public function __get( $var ) {

		if ( 'reactions' !== $var ) {
			return null;
		}

		$reaction_group = $this->get_reaction_group(
			wordpoints_hooks()->get_current_mode()
		);

		if ( ! $reaction_group ) {
			return null;
		}

		return $reaction_group;
	}

	/**
	 * Get a reaction group's storage object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug of the reaction group to get.
	 *
	 * @return WordPoints_Hook_Reaction_StorageI|false The reaction storage object.
	 */
	public function get_reaction_group( $slug ) {

		$reaction_group = wordpoints_hooks()->reaction_groups->get(
			$this->slug
			, $slug
			, array( $this )
		);

		if ( ! $reaction_group instanceof WordPoints_Hook_Reaction_StorageI ) {
			return false;
		}

		// Allowing access to groups out-of-context would lead to strange behavior.
		if ( false === $reaction_group->get_context_id() ) {
			return false;
		}

		return $reaction_group;
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
	 * Get all reactions to a particular event for this reactor.
	 *
	 * On multisite it will return all reactions for the current site, both standard
	 * ones and any network-wide ones (if this reactor offers a network storage
	 * class). Or, if network mode is on, it will return only the network-wide ones.
	 *
	 * Speaking more generally, it will return the reactions from all reaction groups
	 * that are available in the current context.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_slug The event slug.
	 *
	 * @return WordPoints_Hook_ReactionI[] All of the reaction objects.
	 */
	public function get_all_reactions_to_event( $event_slug ) {

		$reactions = array();

		$slugs = wordpoints_hooks()->reaction_groups->get_children_slugs(
			$this->slug
		);

		foreach ( $slugs as $slug ) {

			$storage = $this->get_reaction_group( $slug );

			if ( ! $storage ) {
				continue;
			}

			$reactions = array_merge(
				$reactions
				, $storage->get_reactions_to_event( $event_slug )
			);
		}

		return $reactions;
	}

	/**
	 * Get all reactions for this reactor.
	 *
	 * On multisite it will return all reactions for the current site, both standard
	 * ones and any network-wide ones (if this reactor offers a network storage
	 * class). Or, if network mode is on, it will return only the network-wide ones.
	 *
	 * Speaking more generally, it will return the reactions from all reaction groups
	 * that are available in the current context.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_Hook_ReactionI[] All of the reaction objects.
	 */
	public function get_all_reactions() {

		$reactions = array();

		$slugs = wordpoints_hooks()->reaction_groups->get_children_slugs(
			$this->slug
		);

		foreach ( $slugs as $slug ) {

			$storage = $this->get_reaction_group( $slug );

			if ( ! $storage ) {
				continue;
			}

			$reactions = array_merge( $reactions, $storage->get_reactions() );
		}

		return $reactions;
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
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 * @param WordPoints_Hook_ReactionI  $reaction   The reaction.
	 */
	abstract public function hit(
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_ReactionI $reaction
	);

	/**
	 * Reverses all hits matching this event and args.
	 *
	 * @since 1.0.0
	 *
	 * @param string                     $event_slug The event slug.
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 */
	abstract public function reverse_hits(
		$event_slug,
		WordPoints_Hook_Event_Args $event_args
	);
}

// EOF
