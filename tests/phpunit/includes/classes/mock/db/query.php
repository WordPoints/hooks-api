<?php

/**
 * Mock database query class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock database query for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_DB_Query extends WordPoints_DB_Query {

	/**
	 * @since 1.0.0
	 */
	public $columns = array(
		'id' => array( 'format' => '%d', 'unsigned' => true ),
		'int_col' => array( 'format' => '%d' ),
		'text_col' => array( 'format' => '%s' ),
		'date_col' => array( 'format' => '%s', 'is_date' => true ),
	);

	/**
	 * @since 1.0.0
	 */
	public $meta_type = 'wordpoints_db_query_test';

	/**
	 * @since 1.0.0
	 */
	public function __construct( $args = array() ) {

		global $wpdb;

		$this->table_name = $wpdb->prefix . 'wordpoints_db_query_test';

		$wpdb->wordpoints_db_query_testmeta = $wpdb->prefix . 'wordpoints_db_query_testmeta';

		parent::__construct( $args );
	}
}

// EOF
