<?php

/**
 * Test case for the Comment Leave hook event.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the Comment Leave hook event.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Event_Comment_Leave
 */
class WordPoints_Hook_Event_Comment_Leave_Test extends WordPoints_PHPUnit_TestCase_Hook_Event {

	/**
	 * @since 1.0.0
	 */
	protected $event_class = 'WordPoints_Hook_Event_Comment_Leave';

	/**
	 * @since 1.0.0
	 */
	protected $event_slug = 'comment_leave';

	/**
	 * @since 1.0.0
	 */
	protected $expected_targets = array(
		array( 'comment', 'author', 'user' ),
		array( 'comment', 'post', 'post', 'author', 'user' ),
	);

	/**
	 * @since 1.0.0
	 */
	protected function fire_event( $arg, $reactor_slug ) {

		$comment_id = $this->factory->comment->create(
			array(
				'comment_approved' => 0,
				'user_id'          => $this->factory->user->create(),
				'comment_post_ID'  => $this->factory->post->create(
					array(
						'post_author' => $this->factory->user->create(),
					)
				),
			)
		);

		wp_update_comment(
			array( 'comment_ID' => $comment_id, 'comment_approved' => 1 )
		);

		return array(
			$this->factory->comment->create(
				array(
					'user_id'         => $this->factory->user->create(),
					'comment_post_ID' => $this->factory->post->create(
						array(
							'post_author' => $this->factory->user->create(),
						)
					),
				)
			),
			$comment_id,
		);
	}
}

// EOF
