<?php

/**
 * Mock hook extension class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hook extension class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Extension extends WordPoints_Hook_Extension {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'test_extension';

	/**
	 * Whether the event should hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $should_hit = true;

	/**
	 * The args passed to should_hit() each time it was called.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $hit_checks = array();

	/**
	 * The settings passed to the validator each time it was called.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $validations = array();

	/**
	 * @since 1.0.0
	 */
	public function should_hit(
		WordPoints_Hook_Reaction_Validator $reaction,
		WordPoints_Hook_Event_Args $event_args
	) {

		$this->hit_checks[] = array(
			'reaction'   => $reaction,
			'event_args' => $event_args,
		);

		return $this->should_hit;
	}

	/**
	 * Validates the extensions settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The extension's settings.
	 *
	 * @return array The validated settings.
	 */
	protected function validate_test_extension( array $settings ) {

		$this->validations[] = array(
			'settings' => $settings,
			'validator' => $this->validator,
			'event_args' => $this->event_args,
			'field_stack' => $this->validator->get_field_stack(),
		);

		if ( ! empty( $settings['fail'] ) ) {
			$this->validator->add_error( $settings['fail'], 'fail' );
			$settings = array();
		}

		return $settings;
	}
}

// EOF
