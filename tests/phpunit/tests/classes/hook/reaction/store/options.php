<?php

/**
 * Test case for WordPoints_Hook_Reaction_Store_Options.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Reaction_Store_Options.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Reaction_Store_Options
 */
class WordPoints_Hook_Reaction_Store_Options_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test checking if a nonexistent reaction exists.
	 *
	 * @since 1.0.0
	 */
	public function test_nonexistent_reaction_exists() {

		$store = new WordPoints_Hook_Reaction_Store_Options(
			'test_store'
			, new WordPoints_PHPUnit_Mock_Hook_Reactor( 'test_reactor' )
		);

		$this->assertFalse( $store->reaction_exists( 1 ) );
	}

	/**
	 * Test checking if a reaction exists.
	 *
	 * @since 1.0.0
	 */
	public function test_reaction_exists() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertTrue( $reactor->reactions->reaction_exists( $reaction->ID ) );
	}

	/**
	 * Test getting all reactions when there are none.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions_none() {

		$store = new WordPoints_Hook_Reaction_Store_Options(
			'test_store'
			, new WordPoints_PHPUnit_Mock_Hook_Reactor( 'test_reactor' )
		);

		$this->assertSame( array(), $store->get_reactions() );
	}

	/**
	 * Test getting all reactions.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactions = $reactor->reactions->get_reactions();

		$this->assertEquals( array( $reaction ), $reactions );
	}

	/**
	 * Test getting all reactions to an event when there are no reactions.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions_to_event_no_reactions() {

		$store = new WordPoints_Hook_Reaction_Store_Options(
			'test_store'
			, new WordPoints_PHPUnit_Mock_Hook_Reactor( 'test_reactor' )
		);

		$this->assertSame(
			array()
			, $store->get_reactions_to_event( 'test_event' )
		);
	}

	/**
	 * Test getting all reactions to an event.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions_to_event() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactions = $reactor->reactions->get_reactions_to_event( 'test_event' );

		$this->assertEquals( array( $reaction ), $reactions );
	}

	/**
	 * Test getting all reactions to an event when there are none.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions_to_event_none() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		$reactions = $reactor->reactions->get_reactions_to_event( 'test_event' );

		$this->assertSame( array(), $reactions );
	}

	/**
	 * Test getting the event for a nonexistent reaction from the index.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_event_from_index_nonexistent() {

		$store = new WordPoints_Hook_Reaction_Store_Options(
			'test_store'
			, new WordPoints_PHPUnit_Mock_Hook_Reactor( 'test_reactor' )
		);

		$this->assertFalse( $store->get_reaction_event_from_index( 1 ) );
	}

	/**
	 * Test getting the event for a reaction from the index.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_event_from_index() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals(
			$reaction->get_event_slug()
			, $reactor->reactions->get_reaction_event_from_index( $reaction->ID )
		);
	}

	/**
	 * Test updating the event for a nonexistent reaction in the index.
	 *
	 * @since 1.0.0
	 */
	public function test_update_reaction_event_from_index_nonexistent() {

		$store = new WordPoints_Hook_Reaction_Store_Options(
			'test_store'
			, new WordPoints_PHPUnit_Mock_Hook_Reactor( 'test_reactor' )
		);

		$this->assertFalse(
			$store->update_reaction_event_in_index( 1, 'test_event' )
		);
	}

	/**
	 * Test getting the event for a reaction in the index.
	 *
	 * @since 1.0.0
	 */
	public function test_update_reaction_event_in_index() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertTrue(
			$reactor->reactions->update_reaction_event_in_index( 1, 'another_event' )
		);

		$this->assertEquals(
			'another_event'
			, $reactor->reactions->get_reaction_event_from_index( $reaction->ID )
		);
	}

	/**
	 * Test deleting a reaction that doesn't exist.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_reaction_nonexistent() {

		$store = new WordPoints_Hook_Reaction_Store_Options(
			'test_store'
			, new WordPoints_PHPUnit_Mock_Hook_Reactor( 'test_reactor' )
		);

		$this->assertFalse( $store->delete_reaction( 1 ) );
	}

	/**
	 * Test deleting a reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_reaction() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertTrue( $reactor->reactions->delete_reaction( $reaction->ID ) );

		$this->assertFalse( $reactor->reactions->reaction_exists( $reaction->ID ) );
		$this->assertSame( array(), $reactor->reactions->get_reactions() );
		$this->assertSame(
			array()
			, $reactor->reactions->get_reactions_to_event( 'test_event' )
		);

		$this->assertFalse(
			$reactor->reactions->get_reaction_event_from_index( $reaction->ID )
		);
	}

	/**
	 * Test creating a reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 1, $reaction->ID );

		$this->assertTrue( $reactor->reactions->reaction_exists( $reaction->ID ) );
		$this->assertEquals( array( $reaction ), $reactor->reactions->get_reactions() );
		$this->assertEquals(
			array( $reaction )
			, $reactor->reactions->get_reactions_to_event( 'test_event' )
		);

		$this->assertEquals(
			'test_event'
			, $reactor->reactions->get_reaction_event_from_index( $reaction->ID )
		);
	}

	/**
	 * Test creating a reaction increments the IDs.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction_increments_id() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$reactions = $this->factory->wordpoints->hook_reaction->create_many( 3 );

		$this->assertEquals( 1, $reactions[0]->ID );
		$this->assertEquals( 2, $reactions[1]->ID );
		$this->assertEquals( 3, $reactions[2]->ID );

		$this->assertTrue( $reactor->reactions->delete_reaction( 1 ) );

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 4, $reaction->ID );

		// When the newest reaction is deleted, the ID is reused.
		$this->assertTrue( $reactor->reactions->delete_reaction( 4 ) );

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 4, $reaction->ID );
	}

	/**
	 * Test that regular options are used (not network-wide).
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPress multisite
	 */
	public function test_options() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor->standard_reactions_class = 'WordPoints_Hook_Reaction_Store_Options';

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 1, $reaction->ID );

		// Create another site.
		$site_id = $this->factory->blog->create();

		switch_to_blog( $site_id );

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 1, $reaction->ID );

		$this->assertTrue( $reactor->reactions->delete_reaction( $reaction->ID ) );

		restore_current_blog();

		// The reaction on this site should still exist.
		$this->assertTrue( $reactor->reactions->reaction_exists( 1 ) );
	}
}

// EOF
