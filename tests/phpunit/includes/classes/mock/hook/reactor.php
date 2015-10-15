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

	protected $reactions_class = 'WordPoints_PHPUnit_Mock_Hook_Reaction_Storage';

	public $validated_settings = array();
	public $validated;
	public $hits = array();

	/**
	 * @since 1.0.0
	 *
	 * @param array                              $settings
	 * @param WordPoints_Hook_Reaction_Validator $validator
	 * @param WordPoints_Hook_Event_Args         $event_args
	 *
	 * @return array
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
	 * @since    1.0.0
	 *
	 * @param WordPoints_Entity_HierarchyI|WordPoints_Hook_Event_Args      $event_args
	 * @param WordPoints_Hook_Reaction_Validator|WordPoints_Hook_ReactionI $reaction
	 */
	public function hit( WordPoints_Hook_Event_Args $event_args, WordPoints_Hook_Reaction_Validator $reaction ) {
		$this->hits[] = array( 'action' => $event_args, 'reaction' => $reaction );
	}
}

// EOF
