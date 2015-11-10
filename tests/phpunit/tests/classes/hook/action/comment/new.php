<?php

/**
 * Test case for WordPoints_Hook_Action_Comment_New.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Action_Comment_New.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Action_Comment_New
 */
class WordPoints_Hook_Action_Comment_New_Test
	extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test checking if an action should fire.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_requirements_met() {

		$comment = $this->factory->comment->create_and_get(
			array( 'comment_approved' => '1' )
		);

		$action = new WordPoints_Hook_Action_Comment_New(
			'test'
			, array( 'a', $comment )
		);

		$this->assertTrue( $action->should_fire() );
	}

	/**
	 * Test checking if an action should fire when the requirements aren't met.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_requirements_not_met() {

		$comment = $this->factory->comment->create_and_get(
			array( 'comment_approved' => '0' )
		);

		$action = new WordPoints_Hook_Action_Comment_New(
			'test'
			, array( 'a', $comment )
		);

		$this->assertFalse( $action->should_fire() );
	}

	/**
	 * Test checking if the action should fire when there is no comment.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_no_comment() {

		$action = new WordPoints_Hook_Action_Comment_New(
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

		$comment = $this->factory->comment->create_and_get(
			array( 'comment_approved' => '1' )
		);

		$action = new WordPoints_Hook_Action_Comment_New(
			'test'
			, array( 'a', $comment )
			, array( 'requirements' => array( 0 => 'a' ) )
		);

		$this->assertTrue( $action->should_fire() );
	}

	/**
	 * Test checking if an action should fire when the requirements aren't met.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_other_requirements_not_met() {

		$comment = $this->factory->comment->create_and_get(
			array( 'comment_approved' => '1' )
		);

		$action = new WordPoints_Hook_Action_Comment_New(
			'test'
			, array( 'a', $comment )
			, array( 'requirements' => array( 0 => 'b' ) )
		);

		$this->assertFalse( $action->should_fire() );
	}
}

// EOF
