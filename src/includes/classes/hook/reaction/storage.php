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
	 * The slug of the reactor which these reactions belong to.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $reactor_slug;

	/**
	 * Whether this object is storing network-wide reactions.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $network_wide;

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
	 * The hooks app.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hooks
	 */
	protected $hooks;

	/**
	 * @since 1.0.0
	 */
	public function __construct( $reactor_slug, $network_wide ) {

		$this->reactor_slug = $reactor_slug;
		$this->network_wide = $network_wide;
		$this->hooks = wordpoints_hooks();
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactor_slug() {
		return $this->reactor_slug;
	}

	/**
	 * @since 1.0.0
	 */
	public function is_network_wide() {
		return $this->network_wide;
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

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = $this->hooks->reactors->get( $this->reactor_slug );

		$validator = new WordPoints_Hook_Reaction_Validator( $settings, $reactor );
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

		$reactor->update_settings( $reaction, $settings );

		/** @var WordPoints_Hook_Extension $extension */
		foreach ( $this->hooks->extensions->get_all() as $extension ) {
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
