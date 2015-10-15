<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

abstract class WordPoints_Hook_Extension implements WordPoints_Hook_SettingsI {

	/**
	 * The unique slug for identifying this extension.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hook_Reaction_Validator
	 */
	protected $validator;

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hook_Event_Args
	 */
	protected $event_args;

	/**
	 * @since 1.0.0
	 */
	public function validate_settings(
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator,
		WordPoints_Hook_Event_Args $event_args
	) {

		if ( ! isset( $settings[ $this->slug ] ) ) {
			return $settings;
		}

		$this->validator = $validator;
		$this->event_args = $event_args;

		$this->validator->push_field( $this->slug );
		$settings[ $this->slug ] = $this->{"validate_{$this->slug}"}( $settings[ $this->slug ] );
		$this->validator->pop_field();

		return $settings;
	}

	/**
	 * @since 1.0.0
	 */
	public function update_settings( WordPoints_Hook_ReactionI $reaction, array $settings ) {

		if ( isset( $settings[ $this->slug ] ) ) {
			$reaction->update_meta( $this->slug, $settings[ $this->slug ] );
		} else {
			$reaction->delete_meta( $this->slug );
		}
	}

	abstract public function should_hit(
		WordPoints_Hook_Reaction_Validator $reaction
		, WordPoints_Hook_Event_Args $event_args
	);
}

interface WordPoints_Hook_Extension_SpamI {

	public function after_spam(
		WordPoints_Hook_EventI $event,
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_Reactor_SpamI $reactor
	);
}


interface WordPoints_Hook_Extension_ReverseI {

	public function after_reverse(
		WordPoints_Hook_EventI $event,
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_Reactor_ReverseI $reactor
	);
}

// EOF
