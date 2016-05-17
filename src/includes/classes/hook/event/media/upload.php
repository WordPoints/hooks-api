<?php

/**
 * Media upload hook event class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a hook event that occurs when a media file is uploaded.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Event_Media_Upload
	extends WordPoints_Hook_Event
	implements WordPoints_Hook_Event_ReversingI {

	/**
	 * @since 1.0.0
	 */
	public function get_title() {

		return __( 'Upload Media', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_description() {

		return __( 'When a file is uploaded to the Media Library.', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reversal_text() {
		return __( 'Media file deleted.', 'wordpoints' );
	}
}

// EOF
