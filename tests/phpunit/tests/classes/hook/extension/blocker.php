<?php

/**
 * Test case for WordPoints_Hook_Extension_Blocker.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Extension_Blocker.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Extension_Blocker
 */
class WordPoints_Hook_Extension_Blocker_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test validating the settings.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_settings
	 *
	 * @param array $settings An array of valid settings.
	 */
	public function test_validate_settings( array $settings ) {

		$this->mock_apps();

		$this->factory->wordpoints->hook_firer->create(
			array( 'slug' => 'test_firer' )
		);

		$extension = new WordPoints_Hook_Extension_Blocker();
		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->set_validator( $validator );

		$result = $extension->validate_settings( $settings, $validator, $event_args );

		$this->assertFalse( $validator->had_errors() );
		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertEquals( $settings, $result );
	}

	/**
	 * Provides sets of valid settings for this extension.
	 *
	 * @since 1.0.0
	 *
	 * @return array The sets of valid settings.
	 */
	public function data_provider_valid_settings() {
		return array(
			'empty' => array( array() ),
			'one' => array( array( 'test_firer' => '1' ) ),
			'zero' => array( array( 'test_firer' => '0' ) ),
			'true' => array( array( 'test_firer' => true ) ),
			'false' => array( array( 'test_firer' => false ) ),
		);
	}

	/**
	 * Test checking whether an event should hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_settings_should_hit
	 *
	 * @param array $settings Settings for the extension.
	 */
	public function test_should_hit( array $settings ) {

		$this->mock_apps();

		$extension = new WordPoints_Hook_Extension_Blocker();

		$this->factory->wordpoints->hook_firer->create(
			array( 'slug' => 'test_firer' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta( 'blocker', $settings );

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$fire = new WordPoints_Hook_Fire(
			new WordPoints_Hook_Firer( 'test_firer' )
			, $event_args
			, $reaction
		);

		$this->assertTrue( $extension->should_hit( $fire ) );

		$this->assertNull( $event_args->get_current() );
	}

	/**
	 * Provides sets of settings that should cause this extension to hit.
	 *
	 * @since 1.0.0
	 *
	 * @return array The sets of settings.
	 */
	public function data_provider_valid_settings_should_hit() {
		return array(
			'empty' => array( array() ),
			'false' => array( array( 'test_firer' => false ) ),
		);
	}

	/**
	 * Test checking whether an event should hit the target when it should not.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_settings_should_not_hit
	 *
	 * @param array $settings Settings for the extension.
	 */
	public function test_should_not_hit( array $settings ) {

		$this->mock_apps();

		$extension = new WordPoints_Hook_Extension_Blocker();

		$this->factory->wordpoints->hook_firer->create(
			array( 'slug' => 'test_firer' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta( 'blocker', $settings );

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$fire = new WordPoints_Hook_Fire(
			new WordPoints_Hook_Firer( 'test_firer' )
			, $event_args
			, $reaction
		);

		$this->assertFalse( $extension->should_hit( $fire ) );

		$this->assertNull( $event_args->get_current() );
	}

	/**
	 * Provides sets of settings that should cause this extension to not hit.
	 *
	 * @since 1.0.0
	 *
	 * @return array The sets of settings.
	 */
	public function data_provider_valid_settings_should_not_hit() {
		return array(
			'true' => array( array( 'test_firer' => true ) ),
		);
	}
}

// EOF
