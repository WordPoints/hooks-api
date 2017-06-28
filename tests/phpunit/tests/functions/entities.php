<?php

/**
 * Test case for the entities functions.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the entities functions.
 *
 * @since 1.0.0
 */
class WordPoints_Entities_Functions_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test the entity registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_taxonomy_entities_init
	 */
	public function test_taxonomy_entities() {

		$this->mock_apps();

		$entities = wordpoints_entities();

		$filter_2 = 'wordpoints_register_entities_for_taxonomies';
		$this->listen_for_filter( $filter_2 );

		wordpoints_taxonomy_entities_init( $entities );

		$this->assertEquals( 1, $this->filter_was_called( $filter_2 ) );

		$children = $entities->get_sub_app( 'children' );
	
		$this->assertTrue( $entities->is_registered( 'term\post_tag' ) );
		$this->assertTrue( $children->is_registered( 'term\post_tag', 'id' ) );

		$this->assertTrue( $entities->is_registered( 'term\category' ) );
		$this->assertTrue( $children->is_registered( 'term\category', 'id' ) );

		$this->assertTrue( $entities->is_registered( 'term\post_format' ) );
		$this->assertTrue( $children->is_registered( 'term\post_format', 'id' ) );
	}
	
	/**
	 * Test the entity registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_register_post_type_taxonomy_entities
	 */
	public function test_post_type_taxonomy_entities() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		
		wordpoints_entities_init( $entities );
		
		$children = $entities->get_sub_app( 'children' );

		$this->assertTrue( $children->is_registered( 'post\post', 'terms\post_tag' ) );
		$this->assertTrue( $children->is_registered( 'post\post', 'terms\category' ) );
		$this->assertTrue( $children->is_registered( 'post\post', 'terms\post_format' ) );
	}

	/**
	 * Test that it registers the expected entities.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_register_taxonomy_entities
	 */
	public function test_register_taxonomy_entities() {

		$this->mock_apps();

		$filter = 'wordpoints_register_taxonomy_entities';
		$mock = $this->listen_for_filter( $filter );

		wordpoints_register_taxonomy_entities( 'post_tag' );

		$this->assertEquals( 1, $mock->call_count );
		$this->assertEquals( array( 'post_tag' ), $mock->calls[0] );

		$entities = wordpoints_entities();
		$children = $entities->get_sub_app( 'children' );

		$this->assertTrue( $entities->is_registered( 'term\post_tag' ) );
		$this->assertTrue( $children->is_registered( 'term\post_tag', 'id' ) );
	}
}

// EOF
