<?php

/**
 * Base hook reaction storage class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Bootstrap for hook reaction storage methods.
 *
 * This class provides a common bootstrap for creating, updated, and deleting
 * reactions. It also provides a bootstrap for retrieving a single hook reaction.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Hook_Reaction_Storage implements WordPoints_Hook_Reaction_StorageI {

	/**
	 * The slug of this storage group.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The reactor that these reactions belong to.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reactor
	 */
	protected $reactor;

	/**
	 * The slug of the contexts in which the reactions are stored.
	 *
	 * @since 1.0.0
	 *
	 * @see wordpoints_entities_get_current_context_id()
	 *
	 * @var string[]
	 */
	protected $context = 'site';

	/**
	 * The name of the class to use for reaction objects.
	 *
	 * The class must implement the WordPoints_Hook_ReactionI interface.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $reaction_class;

	/**
	 * @since 1.0.0
	 */
	public function __construct( $slug, WordPoints_Hook_Reactor $reactor ) {

		$this->slug = $slug;
		$this->reactor = $reactor;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactor_slug() {
		return $this->reactor->get_slug();
	}

	/**
	 * @since 1.0.0
	 */
	public function get_context_id() {
		return wordpoints_entities_get_current_context_id( $this->context );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reaction( $id ) {

		if ( ! $this->reaction_exists( $id ) ) {
			return false;
		}

		return new $this->reaction_class( $id, $this );
	}

	/**
	 * @since 1.0.0
	 */
	public function create_reaction( array $settings ) {
		return $this->create_or_update_reaction( $settings );
	}

	/**
	 * @since 1.0.0
	 */
	public function update_reaction( $id, array $settings ) {
		return $this->create_or_update_reaction( $settings, $id );
	}

	/**
	 * Create or update a reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The settings for the reaction.
	 * @param int   $id       The ID of the reaction to update, if updating.
	 *
	 * @return WordPoints_Hook_ReactionI|false|WordPoints_Hook_Reaction_Validator
	 *         The reaction object if created/updated successfully. False or a
	 *         validator instance if not.
	 */
	protected function create_or_update_reaction( array $settings, $id = null ) {

		$is_new = ! isset( $id );

		if ( ! $is_new && ! $this->reaction_exists( $id ) ) {
			return false;
		}

		$validator = new WordPoints_Hook_Reaction_Validator( $settings, $this->reactor );
		$settings = $validator->validate();

		if ( $validator->had_errors() ) {
			return $validator;
		}

		if ( $is_new ) {

			$id = $this->_create_reaction( $settings['event'] );

			if ( ! $id ) {
				return false;
			}
		}

		$reaction = $this->get_reaction( $id );

		$reaction->update_event_slug( $settings['event'] );

		unset( $settings['event'] );

		$this->reactor->update_settings( $reaction, $settings );

		/** @var WordPoints_Hook_Extension $extension */
		foreach ( wordpoints_hooks()->extensions->get_all() as $extension ) {
			$extension->update_settings( $reaction, $settings );
		}

		/**
		 * A hook reaction is being saved.
		 *
		 * @param WordPoints_Hook_ReactionI $reaction The reaction object.
		 * @param array                     $settings The new settings for the reaction.
		 * @param bool                      $is_new   Whether the reaction was just now created.
		 */
		do_action( 'wordpoints_hook_reaction_save', $reaction, $settings, $is_new );

		return $reaction;
	}

	/**
	 * Create a reaction.
	 *
	 * The event slug is provided in case it is needed (for some storage methods it
	 * is).
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_slug The slug of the event this reaction is for.
	 *
	 * @return int|false The reaction ID, or false if not created.
	 */
	abstract protected function _create_reaction( $event_slug );
}

// EOF
