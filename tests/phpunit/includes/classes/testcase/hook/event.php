<?php

/**
 * Hook event test case class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Parent test case for testing a hook event.
 *
 * @since 1.0.0
 */
abstract class WordPoints_PHPUnit_TestCase_Hook_Event extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * The class of the event being tested.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $event_class;

	/**
	 * The slug of the event being tested.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $event_slug;

	/**
	 * A list of targets which are expected to be tested.
	 *
	 * This helps us make sure that the tests are actually testing the most common use-
	 * cases.
	 *
	 * @since 1.0.0
	 *
	 * @var string[][]
	 */
	protected $expected_targets = array();

	/**
	 * An instance of the event being tested.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_EventI
	 */
	protected $event;

	/**
	 * Shortcut to the hooks app.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hooks
	 */
	protected $hooks;

	/**
	 * A list of targets which are expected to be tested.
	 *
	 * @since 1.0.0
	 *
	 * @var string[][]
	 */
	protected static $_expected_targets = array();

	/**
	 * The targets that have been tested.
	 *
	 * @since 1.0.0
	 *
	 * @var string[][]
	 */
	protected static $tested_targets = array();

	/**
	 * @since 1.0.0
	 */
	public static function tearDownAfterClass() {

		parent::tearDownAfterClass();

		foreach ( self::$_expected_targets as $expected_target ) {
			if ( ! in_array( $expected_target, self::$tested_targets, true ) ) {
				self::fail(
					'Expected target not tested: '
					. self::target_implode( $expected_target ) . PHP_EOL . PHP_EOL
					. 'Tested targets:' . PHP_EOL
					. implode(
						PHP_EOL
						, array_map(
							array( __CLASS__, 'target_implode' )
							, self::$tested_targets
						)
					)
				);
			}
		}
	}

	/**
	 * Create a human-readable string from a target hierarchy.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $target A target hierarchy.
	 *
	 * @return string The target in human-readable format.
	 */
	protected static function target_implode( $target ) {
		return implode( ' » ', $target );
	}

	/**
	 * @since 1.0.0
	 */
	public function setUp() {

		parent::setUp();

		$this->event = new $this->event_class( $this->event_slug );
		$this->hooks = wordpoints_hooks();

		if ( ! isset( $this->factory->wordpoints ) ) {
			$this->factory->wordpoints = WordPoints_PHPUnit_Factory::$factory;
		}
	}

	/**
	 * Test getting the title.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title() {
		$this->assertNotEmpty( $this->event->get_title() );
	}

	/**
	 * Test getting the description.
	 *
	 * @since 1.0.0
	 */
	public function test_get_description() {

		$this->assertNotEmpty( $this->event->get_description() );

		if ( $this->event instanceof WordPoints_Hook_Event_RetroactiveI ) {
			$this->assertNotEmpty( $this->event->get_retroactive_description() );
		}
	}

	/**
	 * Test that the event fires.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_targets
	 */
	public function test_fires( $target, $reactor_slug ) {

		self::$tested_targets[] = $target;

		$reactor = $this->hooks->reactors->get( $reactor_slug );

		switch ( $reactor_slug ) {

			case 'points':
				$this->create_points_type();

				$settings = array(
					'event'       => $this->event_slug,
					'description' => 'Test Description',
					'log_text'    => 'Test Log Text',
					'points'      => 10,
					'points_type' => 'points',
				);

				$assertion = 'assert_user_has_points';
				break;

			default:
				$this->fail( 'Unknown reactor: ' . $reactor_slug );
				return;
		}

		$settings['target'] = $target;

		$reaction = $reactor->reactions->create_reaction( $settings );

		$this->assertIsReaction( $reaction );

		$arg = wordpoints_entities()->get( $target[0] );

		$base_entity_id = $this->fire_event( $arg, $reactor_slug );

		$this->assertTrue( $arg->set_the_value( $base_entity_id ) );

		$hierarchy = new WordPoints_Entity_Hierarchy( $arg );

		$entity = $hierarchy->get_from_hierarchy( $target );

		$this->assertInstanceOf( 'WordPoints_Entity', $entity );

		$target_id = $entity->get_the_value();

		call_user_func( array( $this, $assertion ), $target_id );
	}

	/**
	 * Provides sets of targets that reactors should be able to hit.
	 *
	 * @since 1.0.0
	 *
	 * @return array[]
	 */
	public function data_provider_targets() {

		self::$_expected_targets = $this->expected_targets;

		$this->hooks = wordpoints_hooks();

		$reactors = $this->hooks->reactors->get_all();

		$arg_types_index = array();

		/** @var WordPoints_Hook_Reactor $reactor */
		foreach ( $reactors as $slug => $reactor ) {
			$arg_types = $reactor->get_arg_types();

			foreach ( $arg_types as $arg_type ) {
				$arg_types_index[ $arg_type ][] = $slug;
			}
		}

		$args = $this->hooks->events->args->get_children( $this->event_slug );

		return $this->get_targets_from_args( $args, $arg_types_index );
	}

	/**
	 * Assembles a list of possible targets given a list of args and a list of
	 * reactors that support them.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Arg[]|WordPoints_EntityishI[] $args            The args.
	 * @param string[][]                                    $arg_types_index A list of reactor slugs indexed by arg slug.
	 * @param array[][]                                     $targets         The targets data.
	 * @param string[]                                      $target_stack    The target stack.
	 *
	 * @return array[][] The target data.
	 */
	protected function get_targets_from_args( $args, $arg_types_index, array $targets = array(), array $target_stack = array() ) {

		foreach ( $args as $slug => $arg ) {

			if ( $arg instanceof WordPoints_Hook_Arg ) {
				$arg = $arg->get_entity();
			} elseif ( $arg instanceof WordPoints_Entity_Relationship ) {
				$child_slug = $arg->get_related_entity_slug();
				$target_stack[] = $slug;
				$arg = $arg->get_child( $child_slug );
				$slug = $arg->get_slug();
			} else {
				continue;
			}

			$target_stack[] = $slug;

			if ( isset( $arg_types_index[ $slug ] ) ) {
				foreach ( $arg_types_index[ $slug ] as $reactor_slug ) {
					$targets[] = array( $target_stack, $reactor_slug );
				}
			}

			$children = wordpoints_entities()->children->get_children( $slug );

			$targets = $this->get_targets_from_args(
				$children
				, $arg_types_index
				, $targets
				, $target_stack
			);

			if ( isset( $child_slug ) ) {
				array_pop( $target_stack );
			}

			array_pop( $target_stack );
		}

		return $targets;
	}

	/**
	 * Assert that a user has points.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The ID of the user.
	 */
	protected function assert_user_has_points( $user_id ) {
		$this->assertEquals( 10, wordpoints_get_points( $user_id, 'points' ) );
	}

	/**
	 * Fire the event.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Entity $arg          The object for the main event arg.
	 * @param string            $reactor_slug The reactor slug.
	 *
	 * @return mixed The ID of the the $arg in the event.
	 */
	abstract protected function fire_event( $arg, $reactor_slug );
}

// EOF
