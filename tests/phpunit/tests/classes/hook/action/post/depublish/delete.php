<?php

/**
 * Test case for WordPoints_Hook_Action_Post_Depublish_Delete.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Action_Post_Depublish_Delete.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Action_Post_Depublish_Delete
 */
class WordPoints_Hook_Action_Post_Depublish_Delete_Test
	extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test checking if an action should fire.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_requirements_met() {

		$post = $this->factory->post->create_and_get(
			array( 'post_status' => 'publish' )
		);

		$action = new WordPoints_Hook_Action_Post_Depublish_Delete(
			'test'
			, array( $post )
			, array( 'arg_index' => array( 'post' => 0 ) )
		);

		$this->assertTrue( $action->should_fire() );
	}

	/**
	 * Test checking if an action should fire when the requirements aren't met.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_requirements_not_met() {

		$post = $this->factory->post->create_and_get(
			array( 'post_status' => 'draft' )
		);

		$action = new WordPoints_Hook_Action_Post_Depublish_Delete(
			'test'
			, array( $post )
			, array( 'arg_index' => array( 'post' => 0 ) )
		);

		$this->assertFalse( $action->should_fire() );
	}

	/**
	 * Test checking if the action should fire when there is no post.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_no_post() {

		$action = new WordPoints_Hook_Action_Post_Depublish_Delete(
			'test'
			, array( 'a' )
		);

		$this->assertFalse( $action->should_fire() );
	}

	/**
	 * Test checking if an action should fire when the requirements are met.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_other_requirements_met() {

		$post = $this->factory->post->create_and_get(
			array( 'post_status' => 'publish' )
		);

		$action = new WordPoints_Hook_Action_Post_Depublish_Delete(
			'test'
			, array( $post, 'a' )
			, array(
				'arg_index' => array( 'post' => 0 ),
				'requirements' => array( 1 => 'a' ),
			)
		);

		$this->assertTrue( $action->should_fire() );
	}

	/**
	 * Test checking if an action should fire when the requirements aren't met.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_other_requirements_not_met() {

		$post = $this->factory->post->create_and_get(
			array( 'post_status' => 'publish' )
		);

		$action = new WordPoints_Hook_Action_Post_Depublish_Delete(
			'test'
			, array( $post, 'b' )
			, array(
				'arg_index' => array( 'post' => 0 ),
				'requirements' => array( 1 => 'a' ),
			)
		);

		$this->assertFalse( $action->should_fire() );
	}
}

// EOF
