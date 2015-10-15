<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

/*
 * First, we need to build a list of query args. This is necessary because the query
 * itself does not know what metadata for the reaction needs to be used in the query,
 * or how it should be used.
 *
 * Second, the query needs to loop through all of the args and build the query. Those
 * that it can't use will be added to a list and they'll be used to filter the
 * results.
 *
 * The query is run, and the results are filtered, if needed.
 *
 * How do we keep track of what should filter the query based on the args in the
 * list of args that couldn't be used?
 *
 * How do we figure out which query to use?
 *
 * These questions make me think two things:
 *
 * 1. I don't think we can decide what query to use until after we've collected the
 * list of args.
 * 2. We may actually need to use multiple different kinds of queries, and have them
 * work together somehow.
 *
 * Also, it seems that some things, like args, will be specific to a single query
 * type (like posts can only be accessed by queries that use the database), while
 * others, like conditions, will be able to apply to many different kinds of
 * queries.
 *
 * So maybe we would build multiple different kinds of queries simultaneously,
 * depending on the args involved. Then, once each is built, we would figure out how
 * to use them somehow. Maybe each query could calculate its complexity or something
 * like that, and we could compare that to see how to use them.
 *
 * Or, we could let each query class define a priority that would be used to
 * determine what query would be executed first.
 *
 * We'd still need to have each query keep a list of things that would have to be
 * used as filters instead. This is because a specific implementation of a given
 * query type may not handle a certain feature.
 */

interface WordPoints_Hook_Retroactive_QueryI {

	public function __construct( WordPoints_Hook_ReactionI $reaction );

	/**
	 *
	 *
	 * @since 1.
	 * @return WordPoints_Hook_ReactionI
	 */
	public function get_reaction();
	public function get_validator();

	public function arg_hierarchy_push( $slug );
	public function arg_hierarchy_pop();
	public function get_arg();

	public function set_target( $target_arg );
	public function select_value();
	public function add_condition( array $condition );

	public function get_results();
}

// EOF
