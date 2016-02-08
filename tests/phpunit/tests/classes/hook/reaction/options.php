<?php

/**
 * Test case for WordPoints_Hook_Reaction_Options.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Reaction_Options.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Reaction_Options
 */
class WordPoints_Hook_Reaction_Options_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * The reactor for use in the tests.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reactor
	 */
	protected $reactor;

	/**
	 * The reaction for use in the tests.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reaction_Options
	 */
	protected $reaction;

	/**
	 * @since 1.0.0
	 */
	public function setUp() {

		parent::setUp();

		$this->mock_apps();

		$this->reactor = $this->factory->wordpoints->hook_reactor->create_and_get(
			array(
				'stores' => array(
					'standard' => 'WordPoints_Hook_Reaction_Store_Options',
				),
			)
		);

		$this->reaction = $this->factory->wordpoints->hook_reaction->create();
	}

	/**
	 * Test getting the event slug.
	 *
	 * @since 1.0.0
	 */
	public function test_get_event_slug() {

		$this->assertEquals( 'test_event', $this->reaction->get_event_slug() );
	}

	/**
	 * Test updating the event slug.
	 *
	 * @since 1.0.0
	 */
	public function test_update_event_slug() {

		$this->assertTrue( $this->reaction->update_event_slug( 'another_event' ) );

		$this->assertEquals( 'another_event', $this->reaction->get_event_slug() );

		$this->assertEquals(
			array( $this->reaction )
			, $this->reactor->reactions->get_reactions_to_event( 'another_event' )
		);
	}

	/**
	 * Test getting a meta value.
	 *
	 * @since 1.0.0
	 */
	public function test_get_meta() {

		$this->assertEquals(
			array( 'test_entity' )
			, $this->reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test getting a nonexistent meta value.
	 *
	 * @since 1.0.0
	 */
	public function test_get_meta_nonexistent() {

		$this->assertFalse( $this->reaction->get_meta( 'key' ) );
	}

	/**
	 * Test adding a meta value.
	 *
	 * @since 1.0.0
	 */
	public function test_add_meta() {

		$this->assertTrue( $this->reaction->add_meta( 'key', 'value' ) );

		$this->assertEquals( 'value', $this->reaction->get_meta( 'key' ) );
	}

	/**
	 * Test adding a meta value when the key already exists.
	 *
	 * @since 1.0.0
	 */
	public function test_add_meta_exists() {

		$this->assertTrue( $this->reaction->add_meta( 'key', 'value' ) );

		$this->assertFalse( $this->reaction->add_meta( 'key', 'another' ) );

		$this->assertEquals( 'value', $this->reaction->get_meta( 'key' ) );
	}

	/**
	 * Test updating a meta value.
	 *
	 * @since 1.0.0
	 */
	public function test_update_meta() {

		$this->assertTrue( $this->reaction->update_meta( 'key', 'value' ) );

		$this->assertEquals( 'value', $this->reaction->get_meta( 'key' ) );
	}

	/**
	 * Test updating a meta value when the key already exists.
	 *
	 * @since 1.0.0
	 */
	public function test_update_meta_exists() {

		$this->assertTrue( $this->reaction->add_meta( 'key', 'value' ) );

		$this->assertTrue( $this->reaction->update_meta( 'key', 'another' ) );

		$this->assertEquals( 'another', $this->reaction->get_meta( 'key' ) );
	}

	/**
	 * Test deleting a meta value.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_meta() {

		$this->assertTrue( $this->reaction->delete_meta( 'target' ) );

		$this->assertFalse( $this->reaction->get_meta( 'target' ) );
	}

	/**
	 * Test deleting a meta value that doesn't exist.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_meta_nonexistent() {

		$this->assertFalse( $this->reaction->delete_meta( 'key' ) );
	}

	/**
	 * Test getting all meta.
	 *
	 * @since 1.0.0
	 */
	public function test_get_all_meta() {

		$all_meta = $this->reaction->get_all_meta();
		$this->assertArrayHasKey( 'target', $all_meta );
		$this->assertEquals( array( 'test_entity' ), $all_meta['target'] );
	}

	/**
	 * Test that regular options are used (not network-wide).
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPress multisite
	 */
	public function test_options() {

		$this->assertEquals( 1, $this->reaction->ID );

		$this->assertTrue( $this->reaction->add_meta( 'key', 'value' ) );

		// Create another site.
		$site_id = $this->factory->blog->create();

		switch_to_blog( $site_id );

		$reaction_2 = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 1, $reaction_2->ID );

		$this->assertFalse( $reaction_2->get_meta( 'key' ) );

		$this->assertTrue( $reaction_2->add_meta( 'key', 'another' ) );

		$this->assertTrue(
			$this->reactor->reactions->delete_reaction( $reaction_2->ID )
		);

		restore_current_blog();

		// The value should still be the same.
		$this->assertEquals( 'value', $this->reaction->get_meta( 'key' ) );
	}

	/**
	 * Test that network options are used.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPress multisite
	 */
	public function test_network_options() {

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get(
			array(
				'stores' => array(
					'standard' => 'WordPoints_Hook_Reaction_Store_Options_Network',
				),
			)
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 1, $reaction->ID );

		$this->assertTrue( $reaction->add_meta( 'key', 'value' ) );

		// Create another site.
		$site_id = $this->factory->blog->create();

		switch_to_blog( $site_id );

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals( 2, $reaction->ID );

		$reaction = $reactor->reactions->get_reaction( 1 );

		$this->assertEquals( 'value', $reaction->get_meta( 'key' ) );

		$this->assertTrue( $reaction->update_meta( 'key', 'another' ) );

		restore_current_blog();

		// The value should have been updated.
		$this->assertEquals( 'another', $reaction->get_meta( 'key' ) );
	}
}

// EOF
