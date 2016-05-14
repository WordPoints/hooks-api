<?php

/**
 * Repeat blocker hook extension class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Blocks identical fires from hitting twice for a single reaction.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Extension_Repeat_Blocker extends WordPoints_Hook_Extension {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'repeat_blocker';

	/**
	 * @since 1.0.0
	 */
	protected function validate_action_type_settings( $settings ) {
		return (bool) $settings;
	}

	/**
	 * @since 1.0.0
	 */
	public function should_hit( WordPoints_Hook_Fire $fire ) {

		$block_repeats = (bool) $this->get_settings_from_fire( $fire );

		if ( ! $block_repeats ) {
			return true;
		}

		if ( $fire->get_matching_hits_query()->count() > 0 ) {
			return false;
		}

		return true;
	}
}

// EOF
