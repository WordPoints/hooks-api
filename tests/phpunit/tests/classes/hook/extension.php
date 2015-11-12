<?php

/**
 * Test case for WordPoints_Hook_Extension.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Extension.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Extension
 */
class WordPoints_Hook_Extension_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the extension slug.
	 *
	 * @since 1.0.0
	 */
	public function test_get_slug() {

		$extension = new WordPoints_PHPUnit_Mock_Hook_Extension();

		$this->assertEquals( 'test_extension', $extension->get_slug() );
	}

	/**
	 * Test validating the extension's settings.
	 *
	 * @since 1.0.0
	 */
	public function test_validate_settings() {

		$extension = new WordPoints_PHPUnit_Mock_Hook_Extension();

		$settings = array(
			'test_extension' => array( 'key' => 'value' ),
			'other_settings' => 'here',
		);

		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$validated = $extension->validate_settings(
			$settings
			, $validator
			, $event_args
		);

		$this->assertEquals( $settings, $validated );

		$this->assertEquals(
			$settings['test_extension']
			, $extension->validations[0]['settings']
		);

		$this->assertEquals( $event_args, $extension->validations[0]['event_args'] );
		$this->assertEquals( $validator, $extension->validations[0]['validator'] );
		$this->assertEquals(
			array( 'test_extension' )
			, $extension->validations[0]['field_stack']
		);

		$this->assertEquals( array(), $validator->get_field_stack() );
	}

	/**
	 * Test validating the extension's settings when the key isn't set.
	 *
	 * @since 1.0.0
	 */
	public function test_validate_settings_not_set() {

		$extension = new WordPoints_PHPUnit_Mock_Hook_Extension();

		$settings = array(
			'other_settings' => 'here',
		);

		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$validated = $extension->validate_settings(
			$settings
			, $validator
			, $event_args
		);

		$this->assertEquals( $settings, $validated );
		$this->assertEquals( array(), $extension->validations );
		$this->assertEquals( array(), $validator->get_field_stack() );
	}

	/**
	 * Test updating the extension's settings.
	 *
	 * @since 1.0.0
	 */
	public function test_update_settings() {

		$this->mock_apps();

		$extension = new WordPoints_PHPUnit_Mock_Hook_Extension();

		$settings = array(
			'test_extension' => array( 'key' => 'value' ),
			'other_settings' => 'here',
		);

		/** @var WordPoints_Hook_ReactionI $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$extension->update_settings( $reaction, $settings );

		$this->assertEquals(
			$settings['test_extension']
			, $reaction->get_meta( 'test_extension' )
		);

		$this->assertNull( $reaction->get_meta( 'other_settings' ) );
	}

	/**
	 * Test updating the extension's settings when the key is not set causes existing
	 * setting to be deleted.
	 *
	 * @since 1.0.0
	 */
	public function test_update_settings_not_set() {

		$this->mock_apps();

		$extension = new WordPoints_PHPUnit_Mock_Hook_Extension();

		$settings = array(
			'other_settings' => 'here',
		);

		/** @var WordPoints_Hook_ReactionI $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'test_extension' => array( 'key' => 'value' ) )
		);

		$extension->update_settings( $reaction, $settings );

		$this->assertNull( $reaction->get_meta( 'test_extension' ) );
		$this->assertNull( $reaction->get_meta( 'other_settings' ) );
	}
}

// EOF
