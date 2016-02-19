<?php

/**
 * Test case for database functions.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the database functions.
 *
 * @since 1.0.0
 */
class WordPoints_DB_Function_Test extends WordPoints_PHPUnit_TestCase {

	/**
	 * Test wordpoints_escape_mysql_identifier().
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_escape_mysql_identifier
	 *
	 * @dataProvider data_provider_wordpoints_escape_mysql_identifier
	 *
	 * @param string $value   The identifier.
	 * @param string $escaped The expected escaped value.
	 */
	public function test_wordpoints_escape_mysql_identifier( $value, $escaped ) {

		$this->assertEquals( $escaped, wordpoints_escape_mysql_identifier( $value ) );
	}

	/**
	 * Provides sets of data for the wordpoints_escape_mysql_identifier() tests.
	 *
	 * @since 1.0.0
	 *
	 * @return string[][] Sets of MySQL identifiers.
	 */
	public function data_provider_wordpoints_escape_mysql_identifier() {
		return array(
			'plain' => array( 'column', '`column`' ),
			'space' => array( 'column 1', '`column 1`' ),
			'backtick' => array( 'back`tick', '`back``tick`' ),
			'double_backtick' => array( 'back``tick', '`back````tick`' ),
		);
	}
}

// EOF
