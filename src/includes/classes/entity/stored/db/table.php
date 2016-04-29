<?php

/**
 * Database table stored entity class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents an entity stored in a database table.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Entity_Stored_DB_Table
	extends WordPoints_Entity
	implements WordPoints_Entityish_StoredI {

	/**
	 * The name of the $wpdb property that holds the name of this table.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $wpdb_table_name;

	/**
	 * @since 1.0.0
	 */
	public function get_storage_info() {
		return array(
			'type' => 'db',
			'info' => array(
				'type'       => 'table',
				'table_name' => $GLOBALS['wpdb']->{$this->wpdb_table_name},
			),
		);
	}
}

// EOF
