<?php

/**
 * Hook reaction factory class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Factory for hook reactions, for use in the unit tests.
 *
 * @since 1.0.0
 *
 * @method WordPoints_Hook_ReactionI create( $args = array(), $generation_definitions = null )
 * @method WordPoints_Hook_ReactionI create_and_get( $args = array(), $generation_definitions = null )
 * @method WordPoints_Hook_ReactionI[] create_many( $count, $args = array(), $generation_definitions = null )
 */
class WordPoints_PHPUnit_Factory_For_Hook_Reaction extends WP_UnitTest_Factory_For_Thing {

	/**
	 * @since 1.0.0
	 */
	function __construct( $factory = null ) {

		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'event'       => 'test_event',
			'reactor'     => 'test_reactor',
			'description' => new WP_UnitTest_Generator_Sequence(
				'Hook reaction description %s'
			),
		);
	}

	/**
	 * @since 1.0.0
	 */
	function create_object( $args ) {

		$hooks    = wordpoints_hooks();
		$reactors = $hooks->reactors;

		if ( ! $reactors->is_registered( $args['reactor'] ) ) {
			$this->factory->hook_reactor->create(
				array( 'slug' => $args['reactor'] )
			);
		}

		if ( ! $hooks->events->is_registered( $args['event'] ) ) {

			if ( 'test_event' === $args['event'] || 'another' === $args['event'] ) {
				$this->factory->hook_event->create( array( 'slug' => $args['event'] ) );
			} else {
				return false;
			}
		}

		if ( ! isset( $args['target'] ) ) {
			$args['target'] = array( 'test_entity' );
		}

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = $reactors->get( $args['reactor'] );

		if ( isset( $args['reaction_store'] ) ) {

			$reaction_stores = $hooks->reaction_stores;

			if ( ! $reaction_stores->is_registered( $args['reactor'], $args['reaction_store'] ) ) {
				$reaction_stores->register(
					'test_reactor'
					, 'test_store'
					, 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store'
				);
			}

			$reactions = $reactor->get_reaction_store( $args['reaction_store'] );

		} else {

			$reactions = $reactor->reactions;
		}

		unset( $args['reactor'], $args['reaction_store'] );

		$reaction = $reactions->create_reaction( $args );

		if ( ! $reaction ) {
			return $reaction;
		}

		if ( $reaction instanceof WordPoints_Hook_Reaction_Validator ) {
			return new WP_Error(
				'wordpoints_hook_reaction_factor_create'
				, ''
				, $reaction
			);
		}

		return $reaction;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_ReactionI $object The reaction object.
	 * @param array                     $fields The new fields.
	 *
	 * @return WordPoints_Hook_ReactionI The reaction object.
	 */
	function update_object( $object, $fields ) {

		$hooks = wordpoints_hooks();

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = $hooks->reactors->get( $object->get_reactor_slug() );

		$fields = array_merge( $object->get_all_meta(), $fields );

		$reaction = $reactor->reactions->update_reaction( $object->ID, $fields );

		if ( ! $reaction ) {
			return $reaction;
		}

		if ( $reaction instanceof WordPoints_Hook_Reaction_Validator ) {
			return new WP_Error(
				'wordpoints_hook_reaction_factor_create'
				, ''
				, $reaction
			);
		}

		return $reaction;
	}

	/**
	 * @since 1.0.0
	 */
	function get_object_by_id( $object_id ) {
		return $object_id;
	}
}

// EOF
