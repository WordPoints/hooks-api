<?php

/**
 * Test case for the Media Upload hook event.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the Media Upload hook event.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Event_Media_Upload
 */
class WordPoints_Hook_Event_Media_Upload_Test extends WordPoints_PHPUnit_TestCase_Hook_Event {

	/**
	 * @since 1.0.0
	 */
	protected $event_class = 'WordPoints_Hook_Event_Media_Upload';

	/**
	 * @since 1.0.0
	 */
	protected $event_slug = 'media_upload';

	/**
	 * @since 1.0.0
	 */
	protected $expected_targets = array(
		array( 'post\\attachment', 'author', 'user' ),
	);

	/**
	 * @since 1.0.0
	 */
	protected function fire_event( $arg, $reactor_slug ) {

		return $this->factory->post->create(
			array(
				'post_author' => $this->factory->user->create(),
				'post_type'   => 'attachment',
			)
		);
	}
}

// EOF
