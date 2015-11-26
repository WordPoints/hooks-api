<?php

/**
 * Test case for WordPoints_Data_Type_Integer.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Data_Type_Integer.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Data_Type_Integer
 */
class WordPoints_Data_Type_Integer_Test extends WP_UnitTestCase {

	/**
	 * Test validating the value.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider provider_valid_values
	 */
	public function test_validate_value( $value ) {

		$data_type = new WordPoints_Data_Type_Integer( 'test' );

		$this->assertEquals( $value, $data_type->validate_value( $value ) );
	}

	/**
	 * Provides valid values.
	 *
	 * @since 1.0.0
	 *
	 * @return array[]
	 */
	public function provider_valid_values() {
		return array(
			array( 15 ),
			array( 0 ),
			array( -53 ),
			array( '15' ),
			array( '0' ),
			array( '-53' ),
			array( 15.0 ),
		);
	}

	/**
	 * Test validating the value when it is invalid.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider provider_invalid_values
	 */
	public function test_validate_value_invalid( $value ) {

		$data_type = new WordPoints_Data_Type_Integer( 'test' );

		$this->assertWPError( $data_type->validate_value( $value ) );
	}

	/**
	 * Provides valid values.
	 *
	 * @since 1.0.0
	 *
	 * @return array[]
	 */
	public function provider_invalid_values() {
		return array(
			array( false ),
			array( true ),
			array( '10%' ),
			array( array( 2 ) ),
			array( array() ),
			array( new stdClass() ),
			array( 4.5 ),
		);
	}
}

// EOF