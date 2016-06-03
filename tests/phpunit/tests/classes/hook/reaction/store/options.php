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

		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertTrue( $reaction_store->reaction_exists( $reaction->get_id() ) );
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

		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactions = $reaction_store->get_reactions();

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

		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactions = $reaction_store->get_reactions_to_event( 'test_event' );

		$this->assertEquals( array( $reaction ), $reactions );
	}

	/**
	 * Test getting all reactions to an event when there are none.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions_to_event_none() {

		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		$reactions = $reaction_store->get_reactions_to_event( 'test_event' );

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

		/** @var WordPoints_Hook_Reaction_Store_Options $reaction_store */
		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals(
			$reaction->get_event_slug()
			, $reaction_store->get_reaction_event_from_index( $reaction->get_id() )
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

		/** @var WordPoints_Hook_Reaction_Store_Options $reaction_store */
		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertTrue(
			$reaction_store->update_reaction_event_in_index( 1, 'another_event' )
		);

		$this->assertEquals(
			'another_event'
			, $reaction_store->get_reaction_event_from_index( $reaction->get_id() )
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

		/** @var WordPoints_Hook_Reaction_Store_Options $reaction_store */
		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertTrue( $reaction_store->delete_reaction( $reaction->get_id() ) );

		$this->assertFalse( $reaction_store->reaction_exists( $reaction->get_id() ) );
		$this->assertSame( array(), $reaction_store->get_reactions() );
		$this->assertSame(
			array()
			, $reaction_store->get_reactions_to_event( 'test_event' )
		);

		$this->assertFalse(
			$reaction_store->get_reaction_event_from_index( $reaction->get_id() )
		);
	}

	/**
	 * Test creating a reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction() {

		/** @var WordPoints_Hook_Reaction_Store_Options $reaction_store */
		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 1, $reaction->get_id() );

		$this->assertTrue( $reaction_store->reaction_exists( $reaction->get_id() ) );
		$this->assertEquals( array( $reaction ), $reaction_store->get_reactions() );
		$this->assertEquals(
			array( $reaction )
			, $reaction_store->get_reactions_to_event( 'test_event' )
		);

		$this->assertEquals(
			'test_event'
			, $reaction_store->get_reaction_event_from_index( $reaction->get_id() )
		);
	}

	/**
	 * Test creating a reaction increments the IDs.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction_increments_id() {

		/** @var WordPoints_Hook_Reaction_Store_Options $reaction_store */
		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$reactions = $this->factory->wordpoints->hook_reaction->create_many( 3 );

		$this->assertEquals( 1, $reactions[0]->get_id() );
		$this->assertEquals( 2, $reactions[1]->get_id() );
		$this->assertEquals( 3, $reactions[2]->get_id() );

		$this->assertTrue( $reaction_store->delete_reaction( 1 ) );

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 4, $reaction->get_id() );

		// When the newest reaction is deleted, the ID shouldn't be reused.
		$this->assertTrue( $reaction_store->delete_reaction( 4 ) );

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 5, $reaction->get_id() );
	}

	/**
	 * Test creating a reaction increments the IDs even when the index is off.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction_increments_id_index() {

		/** @var WordPoints_Hook_Reaction_Store_Options $reaction_store */
		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$reactions = $this->factory->wordpoints->hook_reaction->create_many( 3 );

		$this->assertEquals( 1, $reactions[0]->get_id() );
		$this->assertEquals( 2, $reactions[1]->get_id() );
		$this->assertEquals( 3, $reactions[2]->get_id() );

		$current_mode = wordpoints_hooks()->get_current_mode();
		$option_name = "wordpoints_hook_reaction_last_id-{$this->slug}-{$current_mode}";

		// When the index max is equal to the next ID as calculated from the option.
		$reaction_store->update_option( $option_name, 2 );

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 4, $reaction->get_id() );

		// When the index max is greater than the next ID as calculated from option.
		$reaction_store->update_option( $option_name, 1 );

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 5, $reaction->get_id() );
	}

	/**
	 * Test that regular options are used (not network-wide).
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPress multisite
	 */
	public function test_options() {

		$reaction_store = $this->factory->wordpoints->hook_reaction_store->create_and_get(
			array( 'class' => 'WordPoints_Hook_Reaction_Store_Options' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 1, $reaction->get_id() );

		// Create another site.
		$site_id = $this->factory->blog->create();

		switch_to_blog( $site_id );

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 1, $reaction->get_id() );

		$this->assertTrue( $reaction_store->delete_reaction( $reaction->get_id() ) );

		restore_current_blog();

		// The reaction on this site should still exist.
		$this->assertTrue( $reaction_store->reaction_exists( 1 ) );
	}
}

// EOF
