<?php

/**
 * Hook event interface.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Defines the API for a hook event.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_EventI {

	/**
	 * Get the event slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string The event slug.
	 */
	public function get_slug();

	/**
	 * Get the human-readable title of this event.
	 *
	 * @since 1.0.0
	 *
	 * @return string The event title.
	 */
	public function get_title();

	/**
	 * Get the event description.
	 *
	 * @since 1.0.0
	 *
	 * @return string The event description.
	 */
	public function get_description();
}

// EOF
