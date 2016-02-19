<?php

/**
 * Mock hook firer class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hook firer for the PHPUnit tests.
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
	public $meta_object_id_column = 'wordpoints_db_query_test_id';

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
		$this->meta_table_name = $wpdb->prefix . 'wordpoints_db_query_testmeta';

		$wpdb->wordpoints_db_query_testmeta = $this->meta_table_name;

		parent::__construct( $args );
	}
}

// EOF
