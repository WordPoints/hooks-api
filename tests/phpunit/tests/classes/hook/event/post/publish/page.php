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
class WordPoints_Hook_Event_Post_Publish_Page_Test extends WordPoints_Hook_Event_Post_Publish_Test {

	/**
	 * @since 1.0.0
	 */
	protected $dynamic_slug = 'page';
}

// EOF
