<?php

/**
 * Conditional DB table stored entity relationship interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by relationships stored in a db table with added query conditions.
 *
 * @since 1.0.0
 */
interface WordPoints_Entity_Relationship_Stored_DB_Table_ConditionsI
	extends WordPoints_Entity_Relationship_Stored_DB_TableI {

	/**
	 * Get additional conditions for retrieving entity relationships from the table.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Conditions on the table where these relationships are stored.
	 */
	public function get_conditions();
}

// EOF
