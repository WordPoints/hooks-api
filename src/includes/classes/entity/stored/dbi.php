<?php

/**
 * Database stored entity interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by entities that are stored in the database.
 * 
 * @since 1.0.0
 */
interface WordPoints_Entity_Stored_DBI {
	
	/**
	 * Get the name of the table the objects are stored in.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the database table where this entity is stored.
	 */
	public function get_table_name();
}

// EOF
