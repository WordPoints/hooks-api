<?php

/**
 * Mock hook reactor class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hook reactor for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Reactor extends WordPoints_Hook_Reactor {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'test_reactor';

	/**
	 * @since 1.0.0
	 */
	protected $arg_types = 'test_entity';

	/**
	 * @since 1.0.0
	 */
	protected $reactions_class = 'WordPoints_PHPUnit_Mock_Hook_Reaction_Storage';

	/**
	 * A list of settings that were passed in for each call of validate_settings().
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $validated_settings = array();

	/**
	 * If set, will be returned by validate_settings().
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $validated;

	/**
	 * A list of hits this reactor has received.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $hits = array();

	/**
	 * @since 1.0.0
	 */
	public function __construct( $slug = null ) {
		if ( isset( $slug ) ) {
			$this->slug = $slug;
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function validate_settings( array $settings, WordPoints_Hook_Reaction_Validator $validator, WordPoints_Hook_Event_Args $event_args ) {

		$this->validated_settings[] = $settings;

		if ( isset( $this->validated ) ) {
			return $this->validated;
		}

		$settings = parent::validate_settings( $settings, $validator, $event_args );

		return $settings;
	}

	/**
	 * @since 1.0.0
	 */
	public function hit( WordPoints_Hook_Event_Args $event_args, WordPoints_Hook_Reaction_Validator $reaction ) {
		$this->hits[] = array( 'action' => $event_args, 'reaction' => $reaction );
	}
}

// EOF
