<?php

/**
 * Hook hit query class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Runs queries on the hook hits database table.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Hit_Query extends WordPoints_DB_Query {

	/**
	 * @since 1.0.0
	 */
	protected $columns = array(
		'id' => array( 'format' => '%d', 'unsigned' => true ),
		'firer' => array( 'format' => '%s' ),
		'primary_arg_guid' => array( 'format' => '%s' ),
		'event' => array( 'format' => '%s' ),
		'reactor' => array( 'format' => '%s' ),
		'reaction_store' => array( 'format' => '%s' ),
		'reaction_context_id' => array( 'format' => '%s' ),
		'reaction_id' => array( 'format' => '%d', 'unsigned' => true ),
		'date' => array( 'format' => '%s', 'is_date' => true ),
	);

	/**
	 * @since 1.0.0
	 */
	protected $meta_type = 'wordpoints_hook_hit';

	//
	// Public Methods.
	//

	/**
	 * Construct the class.
	 *
	 * All of the arguments are expected *not* to be SQL escaped.
	 *
	 * @since 1.0.0
	 *
	 * @see WordPoints_DB_Query::$columns For a fuller explanation of the {column},
	 *                                    {column}__in, {column}__not_in, and
	 *                                    {column}__compare args.
	 * @see WP_Meta_Query for the proper arguments for 'meta_query', 'meta_key',
	 *                    'meta_value', 'meta_compare', and 'meta_type'.
	 * @see WP_Date_Query for the proper arguments for 'date_query'.
	 *
	 * @param array $args {
	 *        The arguments for the query.
	 *
	 *        @type string|array $fields                       Fields to include in the results.
	 *        @type int          $id                           The ID of the hit to retrieve.
	 *        @type string       $id__compare                  The comparison operator to use with the above value.
	 *        @type int[]        $id__in                       A list of IDs to query for.
	 *        @type int[]        $id__not_in                   A list of IDs to exclude.
	 *        @type string       $firer                        The slug of the firer to query for.
	 *        @type string       $firer__compare               The comparison operator to use with the above value.
	 *        @type string[]     $firer__in                    A list of firers to query for.
	 *        @type string[]     $firer__not_in                A list of firers to exclude.
	 *        @type string       $primary_arg_guid             The JSON encoded primary arg GUID to query for.
	 *        @type string       $primary_arg_guid__compare    The comparison operator to use with the above value.
	 *        @type string[]     $primary_arg_guid__in         A list of primary arg GUIDs to query for.
	 *        @type string[]     $primary_arg_guid__not_in     A list of primary arg GUIDs to exclude.
	 *        @type string       $event                        The slug of the event to query for.
	 *        @type string       $event__compare               The comparison operator to use with the above value.
	 *        @type string[]     $event__in                    A list of events to query for.
	 *        @type string[]     $event__not_in                A list of events to exclude.
	 *        @type string       $reactor                      The slug of the reactor to query for.
	 *        @type string       $reactor__compare             The comparison operator to use with the above value.
	 *        @type string[]     $reactor__in                  A list of reactors to query for.
	 *        @type string[]     $reactor__not_in              A list of reactors to exclude.
	 *        @type string       $reaction_store               The slug of the reaction store to query for.
	 *        @type string       $reaction_store__compare      The comparison operator to use with the above value.
	 *        @type string[]     $reaction_store__in           A list of reaction stores to query for.
	 *        @type string[]     $reaction_store__not_in       A list of reaction stores to exclude.
	 *        @type string       $reaction_context_id          The JSON encoded reaction context ID to query for.
	 *        @type string       $reaction_context_id__compare The comparison operator to use with the above value.
	 *        @type string[]     $reaction_context_id__in      A list of reaction context IDs to query for.
	 *        @type string[]     $reaction_context_id__not_in  A list of reaction context IDs to exclude.
	 *        @type int          $reaction_id                  The ID of the reaction to retrieve.
	 *        @type string       $reaction_id__compare         The comparison operator to use with the above value.
	 *        @type int[]        $reaction_id__in              A list of reaction IDs to query for.
	 *        @type int[]        $reaction_id__not_in          A list of reaction IDs to exclude.
	 *        @type array        $date_query                   See WP_Date_Query
	 *        @type int          $limit                        The maximum number of results to return. Default is null
	 *                                                         (no limit).
	 *        @type int          $start                        The start for the LIMIT clause. Default: 0.
	 *        @type string       $order_by                     The field to use to order the results. Default: 'date'.
	 *                                                         Supports 'meta_value'.
	 *        @type string       $order                        The order for the query: ASC or DESC (default).
	 *        @type string       $meta_key                     See WP_Meta_Query.
	 *        @type mixed        $meta_value                   See WP_Meta_Query.
	 *        @type string       $meta_compare                 See WP_Meta_Query.
	 *        @type string       $meta_type                    See WP_Meta_Query.
	 *        @type array        $meta_query                   See WP_Meta_Query.
	 * }
	 */
	public function __construct( $args = array() ) {

		global $wpdb;

		$this->table_name = $wpdb->wordpoints_hook_hits;

		$this->defaults['order_by'] = 'date';

		parent::__construct( $args );
	}
}

// EOF
