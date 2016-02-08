<?php

/**
 * Test case for WordPoints_Hook_Fire.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Fire.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Fire
 */
class WordPoints_Hook_Fire_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test constructing the fire.
	 *
	 * @since 1.0.0
	 */
	public function test_construct() {

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$this->assertEquals( $firer, $fire->firer );
		$this->assertEquals( $event_args, $fire->event_args );
		$this->assertEquals( $reaction, $fire->reaction );

		$this->assertInstanceOf( 'WordPoints_Hook_Hit_Logger', $fire->hit_logger );
	}

	/**
	 * Test marking the fire as a hit.
	 *
	 * @since 1.0.0
	 */
	public function test_hit() {

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$hit_id = $fire->hit();

		$this->assertInternalType( 'integer', $hit_id );
		$this->assertEquals( $fire->hit_id, $hit_id );

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );
	}

	/**
	 * Test marking the fire as a hit when it supersedes another hit.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding() {

		$this->mock_apps();

		// First we create a hit that can be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit of a different type that supersedes it.
		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged(
			array(
				'firer' => 'another_firer',
				'reaction_id' => $reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
		);
	}

	/**
	 * Test a prior hit will only be marked as superseded if a different type.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding_must_be_different_type() {

		$this->mock_apps();

		// First we create a hit that could be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit of the same type.
		$firer = new WordPoints_Hook_Firer( 'test_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ), 2 );

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
			, 0
		);
	}

	/**
	 * Test that the superseded hit must be for the same event.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding_must_be_same_event() {

		$this->mock_apps();

		// First we create a hit that could be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit from a different event.
		$reaction->update_event_slug( 'another_event' );

		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged(
			array(
				'firer' => 'another_firer',
				'event' => 'another_event',
				'reaction_id' => $reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
			, 0
		);
	}

	/**
	 * Test that the superseded hit must have the same arg GUID.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding_must_be_same_primary_arg_guid() {

		$this->mock_apps();

		$entity_slug = $this->factory->wordpoints->entity->create();

		// First we create a hit that could be superseded.
		$reaction   = $this->factory->wordpoints->hook_reaction->create();
		$arg        = new WordPoints_Hook_Arg( $entity_slug );
		$event_args = new WordPoints_Hook_Event_Args( array( $arg ) );
		$firer      = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$entity = $event_args->get_from_hierarchy( array( $entity_slug ) );
		$entity->set_the_value( 1 );

		$fire->hit();

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'primary_arg_guid' => array( $entity_slug => 1, 'test_context' => 1 ),
			)
		);

		// Then log another hit with a different primary arg GUID.
		$entity->set_the_value( 5 );

		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged(
			array(
				'firer' => 'another_firer',
				'primary_arg_guid' => array( $entity_slug => 5, 'test_context' => 1 ),
				'reaction_id' => $reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'primary_arg_guid' => array( $entity_slug => 1, 'test_context' => 1 ),
				'superseded_by' => $hit_id,
			)
			, 0
		);
	}

	/**
	 * Test that the superseded hit must be for the same reactor.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding_must_be_same_reactor() {

		$this->mock_apps();

		// First we create a hit that could be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit from a different reactor.
		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another_reactor' )
		);
		$other_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another_reactor' )
		);

		$this->assertEquals( $reaction->ID, $other_reaction->ID );

		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $other_reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged(
			array(
				'firer' => 'another_firer',
				'reactor' => 'another_reactor',
				'reaction_id' => $reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
			, 0
		);
	}

	/**
	 * Test that the superseded hit must be for the same reaction store.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding_must_be_same_reaction_store() {

		$this->mock_apps();

		// First we create a hit that could be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit from a different reaction store.
		$other_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reaction_store' => 'test_store' )
		);

		$this->assertEquals( 'test_store', $other_reaction->get_store_slug() );
		$this->assertEquals( $reaction->ID, $other_reaction->ID );
		$this->assertEquals(
			$reaction->get_context_id()
			, $other_reaction->get_context_id()
		);

		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $other_reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged(
			array(
				'firer' => 'another_firer',
				'reaction_store' => 'test_store',
				'reaction_id' => $reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
			, 0
		);
	}

	/**
	 * Test that the superseded hit must be for the same reaction context ID.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding_must_be_same_reaction_context_id() {

		$this->mock_apps();

		// First we create a hit that could be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit from a different reaction context ID.
		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction->context_id = array( 'test_context' => 5 );

		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged(
			array(
				'firer' => 'another_firer',
				'reaction_context_id' => $reaction->context_id,
				'reaction_id' => $reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
			, 0
		);
	}

	/**
	 * Test that the superseded hit must be for the same reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding_must_be_same_reaction() {

		$this->mock_apps();

		// First we create a hit that could be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit from a different reaction.
		$other_reaction = $this->factory->wordpoints->hook_reaction->create();

		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $other_reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged(
			array(
				'firer' => 'another_firer',
				'reaction_id' => $other_reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
			, 0
		);
	}

	/**
	 * Test that the superseded hit can be passed in on construction.
	 *
	 * @since 1.0.0
	 */
	public function test_construct_with_superseded_hit() {

		$this->mock_apps();

		// First we create two hits that could be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$fire->hit();

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ), 2 );

		// Then log another hit of a different type that supersedes one of those.
		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire(
			$firer
			, $event_args
			, $reaction
			, (object) array( 'id' => $hit_id )
		);

		$hit_id = $fire->hit();

		$this->assertHitsLogged(
			array(
				'firer' => 'another_firer',
				'reaction_id' => $reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
		);
	}
}

// EOF
