<?php

/**
 * DB table stored entity relationship interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by entity relationships that are stored in a database table.
 *
 * @since 1.0.0
 */
interface WordPoints_Entity_Relationship_Stored_DB_TableI {

	/**
	 * Get the name of the database table that this relationship is stored in.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the database table.
	 */
	public function get_table_name();

	/**
	 * Get the name of the table column that stores the ID of the primary entity.
	 *
	 * @since 1.0.0
	 *
	 * @return string|array The name of the field where the ID of the primary entity
	 *                      in the relationship is stored, or an array defining a
	 *                      join on a column in another table where the ID is stored.
	 */
	public function get_primary_id_field();

	/**
	 * Get the name of the table column that stores the ID of the related entity.
	 *
	 * @since 1.0.0
	 *
	 * @return string|array The name of the field where the ID of the related entity
	 *                      is stored, or an array defining a join on a column in
	 *                      another table where the ID is stored.
	 */
	public function get_related_id_field();
}

// EOF
