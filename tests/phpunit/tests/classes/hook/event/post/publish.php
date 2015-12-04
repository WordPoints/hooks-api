<?php

/**
 * Test case for the Post Publish hook event.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the Post Publish hook event.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Event_Post_Publish
 */
class WordPoints_Hook_Event_Post_Publish_Test extends WordPoints_PHPUnit_TestCase_Hook_Event_Dynamic {

	/**
	 * @since 1.0.0
	 */
	protected $event_class = 'WordPoints_Hook_Event_Post_Publish';

	/**
	 * @since 1.0.0
	 */
	protected $event_slug = 'post_publish\\';

	/**
	 * @since 1.0.0
	 */
	protected $expected_targets = array(
		array( 'post\\', 'author', 'user' ),
	);

	/**
	 * @since 1.0.0
	 */
	protected $dynamic_slug = 'post';

	/**
	 * @since 1.0.0
	 */
	protected function fire_event( $arg, $reactor_slug ) {

		$post_id = $this->factory->post->create(
			array(
				'post_author' => $this->factory->user->create(),
				'post_type'   => $this->dynamic_slug,
				'post_status' => 'draft',
			)
		);

		$this->factory->post->update_object(
			$post_id
			, array( 'post_status' => 'publish' )
		);

		return array(
			$post_id,
			$this->factory->post->create(
				array(
					'post_author' => $this->factory->user->create(),
					'post_type'   => $this->dynamic_slug,
				)
			),
		);
	}
}

// EOF
