<?php

/**
 * Blocker hook extension class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Blocks a fire from hitting.
 *
 * Useful when you want to block fires of a specific firer for a reaction.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Extension_Blocker extends WordPoints_Hook_Extension {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'blocker';

	/**
	 * @since 1.0.0
	 */
	public function validate_firer_settings( $settings ) {
		return (bool) $settings;
	}

	/**
	 * @since 1.0.0
	 */
	public function should_hit( WordPoints_Hook_Fire $fire ) {
		return ! $this->get_settings_from_fire( $fire );
	}
}

// EOF
