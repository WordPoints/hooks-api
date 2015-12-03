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
class WordPoints_Hook_Event_Comment_Leave_Attachment_Test extends WordPoints_Hook_Event_Comment_Leave_Test {

	/**
	 * @since 1.0.0
	 */
	protected $dynamic_slug = 'attachment';
}

// EOF
