<?php

/**
 * Hook event factory class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Factory for hook events, for use in the unit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Factory_For_Hook_Event extends WP_UnitTest_Factory_For_Thing {

	/**
	 * @since 1.0.0
	 */
	function __construct( $factory = null ) {

		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'slug'           => 'test_event',
			'class'          => 'WordPoints_PHPUnit_Mock_Hook_Event',
		);
	}

	/**
	 * @since 1.0.0
	 */
	function create_object( $args ) {

		$hooks  = wordpoints_apps()->hooks;
		$events = $hooks->events;

		$slug = $args['slug'];
		$class = $args['class'];

		unset( $args['slug'], $args['class'] );

		if ( ! isset( $args['actions'] ) ) {
			$args['actions'] = array(
				'fire' => 'test_action',
				'reverse' => 'test_reverse_action',
			);
		}

		$actions = $hooks->actions;

		if ( ! $actions->is_registered( $args['actions']['fire'] ) ) {

			if ( 'test_action' === $args['actions']['fire'] ) {
				$this->factory->hook_action->create();
			} else {
				return false;
			}
		}

		if ( ! $actions->is_registered( $args['actions']['reverse'] ) ) {

			if ( 'test_reverse_action' === $args['actions']['reverse'] ) {
				$this->factory->hook_action->create(
					array( 'slug' => 'test_reverse_action' )
				);
			} else {
				return false;
			}
		}

		if ( ! isset( $args['args'] ) ) {
			$args['args'] = array( 'test_entity' => 'WordPoints_PHPUnit_Mock_Hook_Arg' );
		}

		foreach ( $args['args'] as $arg_slug => $class ) {
			$events->args->register( $slug, $arg_slug, $class );
		}

		$events->register( $slug, $class, $args );

		return $slug;
	}

	/**
	 * @since 1.0.0
	 */
	function update_object( $object, $fields ) {
		return $object;
	}

	/**
	 * @since 1.0.0
	 */
	function get_object_by_id( $object_id ) {
		return wordpoints_apps()->hooks->events->get( $object_id );
	}
}

// EOF
