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
	public function __construct( $reactor_slug ) {

		$this->reactor_slug = $reactor_slug;
		$this->hooks = wordpoints_apps()->hooks;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reaction( $id ) {

		return new $this->reaction_class(
			$id
			, $this->reactor_slug
			, $this->hooks->get_network_mode()
		);
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
	 * @since 1.0.0
	 */
	public function delete_reaction( $id ) {

		$reaction = $this->get_reaction( $id );

		if ( ! $reaction->exists() ) {
			return false;
		}

		return $reaction->delete();
	}

	/**
	 * Create or update a reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The settings for the reaction.
	 * @param int   $id       The ID of the reaction to update, if updating.
	 *
	 * @return WordPoints_Hook_ReactionI|false|WordPoints_Hook_Reaction_Validator True if the reaction was created/
	 *                                        updated successfully. False or a
	 *                                        validator instance if not.
	 */
	protected function create_or_update_reaction( array $settings, $id = null ) {

		$reaction = $this->get_reaction( $id );

		if ( isset( $id ) && ! $reaction->exists() ) {
			return false;
		}

		$settings['reactor'] = $this->reactor_slug;

		$validator = new WordPoints_Hook_Reaction_Validator( $settings );
		$settings = $validator->validate();

		if ( $validator->had_errors() ) {
			return $validator;
		}

		if ( ! $reaction->exists() ) {
			$result = $reaction->create( $settings['event'] );

			if ( ! $result ) {
				return false;
			}
		}

		$reaction->update_meta( 'event', $settings['event'] );

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = $this->hooks->reactors->get( $this->reactor_slug );

		$reactor->update_settings( $reaction, $settings );

		/** @var WordPoints_Hook_Extension $extension */
		foreach ( $this->hooks->extensions->get() as $extension ) {
			$extension->update_settings( $reaction, $settings );
		}

		/**
		 * A hook reaction is being saved.
		 *
		 * @param WordPoints_Hook_ReactionI $reaction The reaction object.
		 * @param array                     $settings The new settings for the reaction.
		 * @param bool                      $is_new   Whether the reaction was just now created.
		 */
		do_action( 'wordpoints_hook_reaction_save', $reaction, $settings, null === $id );

		return $reaction;
	}
}

// EOF
