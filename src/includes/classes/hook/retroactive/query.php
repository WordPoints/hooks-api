<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */


interface WordPoints_Hook_Event_RetroactiveI {
	public function get_retroactive_description();
}


//public function modify_retroactive_query( WordPoints_Hook_Retroactive_QueryI $query ) {
//	$query->add_condition( array( 'field' => 'comment_approved', 'value' => '1' ) );
//}
//
//public function modify_retroactive_query( WordPoints_Hook_Retroactive_QueryI $query ) {
//	$query->add_condition( array( 'field' => 'post_status', 'value' => 'publish' ) );
//}



//public function modify_retroactive_query( WordPoints_Hook_Retroactive_QueryI $query ) {
//
//	$reaction = $query->get_reaction();
//
//	$this->validator = $query->get_validator();
//
//	$conditions = $reaction->get_meta( 'conditions' );
//
////		$conditions = $this->validate_conditions( $conditions );
//	var_dump( __FUNCTION__,$conditions );
//	$this->_modify_retroactive_query( $query, $conditions );
//}
//
//public function _modify_retroactive_query( WordPoints_Hook_Retroactive_QueryI $query, $args ) {
//
//	foreach ( $args as $arg_slug => $sub_args ) {
//
//		$query->arg_hierarchy_push( $arg_slug );
//
//		if ( isset( $sub_args['_conditions'] ) ) {
//
//			foreach ( $sub_args['_conditions'] as $condition ) {
//
//				$condition_obj = $this->conditions->get(
//					$condition['type']
//				);
//
//				if ( $condition_obj instanceof WordPoints_Hook_Retroactive_Query_ModifierI ) {
//
//					$condition_obj->modify_retroactive_query(
//						$query
//						, $condition['settings']
//					);
//
//				} else {
//					$query->add_condition( $condition['settings'] );
//				}
//			}
//
//			unset( $sub_args['_conditions'] );
//		}
//
//		$this->_modify_retroactive_query( $query, $sub_args );
//
//		$query->arg_hierarchy_pop( $arg_slug );
//	}
//}


interface WordPoints_Hook_Reactor_RetroactiveI extends WordPoints_Hook_Retroactive_Query_ModifierI {
	public function retroactive_hit( $target, array $records, WordPoints_Hook_Event_RetroactiveI $event, WordPoints_Hook_ReactionI $reaction );
}

abstract class WordPoints_Hook_Reactor_Retroactive implements WordPoints_Hook_Reactor_RetroactiveI {

	public function modify_retroactive_query( WordPoints_Hook_Retroactive_QueryI $query ) {

		$query->set_target( $query->get_reaction()->get_meta( 'target' ) );
	}

	public function reverse_hits() {
//		if ( ! $logs ) {
//			// Check if this reversal was part of a retroactive fire.
//			if ( $event instanceof WordPoints_Hook_Event_RetroactiveI ) {
//				$this->reverse_retroactive_hits( $args, $event );
//			}
//
//			return;
//		}
	}

	/**
	 * @since 1.0.0
	 */
	public function retroactive_hit( $target, array $records, WordPoints_Hook_Event_RetroactiveI $event, WordPoints_Hook_ReactionI $reaction ) {

		$count = count( $records );

		wordpoints_alter_points(
			$target
			, $reaction->get_meta( 'points' ) * $count
			, $reaction->get_meta( 'points_type' )
			, 'retroactive_' . $reaction->get_event_slug()
			, array( 'count' => $count )
			, $event->get_retroactive_description( $reaction )
		);
	}

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @param WordPoints_Hook_Event_Args $args
	 * @param WordPoints_Hook_Event_RetroactiveI $event
	 */
	protected function reverse_retroactive_hits(
		WordPoints_Hook_Event_Args $args,
		WordPoints_Hook_Event_RetroactiveI $event
	) {

		// TODO do we even need to do this?
		$query = new WordPoints_Points_Logs_Query(
			array(
				'log_type'   => "retroactive_{$event}",
				'meta_query' => array(
					array(
						'key'   => 'auto_reversed',
						'value' => $args->get_entity_id(),
					),
				),
			)
		);

		$logs = $query->get();

		if ( ! $logs ) {
			return;
		}

		foreach ( $logs as $log ) {

			wordpoints_alter_points(
				$log->user_id
				, - ( $log->points / wordpoints_get_points_log_meta( $log->id, 'count', true ) )
				, $log->points_type
				, "reverse_{$event}"
				, array( 'original_log_id' => $log->id )
				, $args->get_description()
			);

			wordpoints_add_points_log_meta( $log->id, 'auto_reversed', $args->get_entity_id() );
		}
	}
}

class WordPoints_Hook_Retroactive_Query implements WordPoints_Hook_Retroactive_QueryI {

	protected $reaction;

	protected $arg_hierarchy = array();

	protected $validator;

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hierarchy
	 */
	protected $query;

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hierarchy
	 */
	protected $queries;

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_EntityishI
	 */
	protected $parent_arg;

	protected $results;

	public function __construct( WordPoints_Hook_ReactionI $reaction ) {

		$this->reaction = $reaction;
		$this->arg_hierarchy = new WordPoints_Hierarchy( 'sub_args' );
		$this->hooks = wordpoints_hooks();
		$this->entities = wordpoints_entities();
		$this->validator = new WordPoints_Hook_Reaction_Validator( $reaction, true );
	}

	public function get_reaction() {
		return $this->reaction;
	}

	public function get_validator() {
		return $this->validator;
	}

	public function get_results() {

		if ( ! isset( $this->results ) ) {

			$this->execute();

			if ( $this->validator->had_errors() ) {
				$this->results = $this->validator;
			}
		}

		return $this->results;
	}

	protected function execute() {

		try {

			$this->prepare_query();
			$this->perform_query();
			$this->filter_results();
			$this->group_results();

		} catch ( WordPoints_Hook_Validator_Exception $e ) {
			unset( $e );
		}
	}

	protected function prepare_query() {

		$event_slug = $this->reaction->get_event_slug();

		$event = $this->hooks->events->get( $event_slug );

		if ( ! ( $event instanceof WordPoints_Hook_Event_RetroactiveI ) ) {
			$this->validator->add_error( 'invalid hook' );
		}

		foreach ( $this->hooks->events->args->get_children( $event_slug ) as $arg ) {
			$this->arg_hierarchy_push( // TODO
				$arg
			);
		}

		if ( $event instanceof WordPoints_Hook_Retroactive_Query_ModifierI ) {
			$event->modify_retroactive_query( $this );
			$this->reset();
		}

		$reactor = $this->hooks->reactors->get( $this->reaction->get_reactor_slug() );

		$reactor->modify_retroactive_query( $this );
		$this->reset();

		foreach ( $this->hooks->extensions->get_all() as $extension ) {

			if ( $extension instanceof WordPoints_Hook_Retroactive_Query_ModifierI ) {
				$extension->modify_retroactive_query( $this );
				$this->reset();
			}
		}
	}

	protected function perform_query() {

		$this->queries = new WordPoints_Hierarchy( 'sub_queries' );
		$this->consolidate_queries( array( $this->arg_hierarchy->get() ) );
		unset( $this->query );

		$this->queries->reset();

		// Find the tip of a query.
		$this->results = $this->execute_queries( $this->queries->get() );

		if ( is_wp_error( $this->results ) ) {
			$this->validator->add_error( $this->results );
		}
	}

	protected function consolidate_queries( $arg_hierarchy, $storage_type = null ) {

		foreach ( $arg_hierarchy as $data ) {
			$this->consolidate_query( $data );
		}
	}

	protected function consolidate_query( $data ) {

		if ( $data['storage_info']['type'] !== $this->queries->get_field( 'slug' ) ) {

			$parent_id = null;
			if ( isset( $this->query ) ) {
				$parent_id = $this->query->get_id();
			}

			$this->query = new WordPoints_Hierarchy( 'sub_args' );

			$this->queries->push(
				$data['storage_info']['type']
				, array( 'query' => $this->query, 'parent_id' => $parent_id )
			);

			$pushed_query = true;
		}

		if ( isset( $data['sub_args'] ) ) {
			$sub_args = $data['sub_args'];
			unset( $data['sub_args'] );
		}

		$this->query->push( $data['slug'], $data );

		if ( isset( $sub_args ) ) {
			$this->consolidate_queries( $sub_args, $data['storage_info']['type'] );
		}

		$this->query->pop();

		if ( ! empty( $pushed_query ) ) {
			$this->queries->pop();
			$this->query = $this->queries->get_field( 'query' );
		}
	}

	protected function execute_queries( $queries ) {

		/** @var WordPoints_HierarchyI $query */
		$query = $queries['query'];

		if ( isset( $queries['sub_queries'] ) ) {
			foreach ( $queries['sub_queries'] as $query_data ) {

				$results = $this->execute_queries( $query_data );

				if ( is_wp_error( $results ) ) {
					return $results;
				}

				if ( empty( $results ) ) {
					return array();
				}

				/** @var WordPoints_HierarchyI $child_query */
				$child_query = $query_data['query'];
				$query->go_to( $query_data['parent_id'] );

				$condition = array(
//					'field' => $child_query->get_field( 'slug' ),
					'compare' => 'in',
					'value' => wp_list_pluck(
						$results
						, $child_query->get_field( 'storage_info', 'meta', 'id_field' )
					),
				);

				$query->push_to( 'conditions', $condition );
			}
		}

		$query->reset();
		$storage_type = $query->get_field( 'storage_info', 'type' );

		$executor = $this->hooks->retroactive_query_executors->get( $storage_type );

		if ( ! $executor ) {
			$this->validator->add_error(
				sprintf( 'unknown storage type "%s".', $storage_type )
			);
		}

		return $executor->execute( array( $query->get() ) );
	}

	protected function filter_results() {

		$reactor = $this->hooks->reactors->get( $this->reaction->get_reactor_slug() );

		if ( $reactor instanceof WordPoints_Hook_Retroactive_Query_FilterI ) {
			$reactor->filter_retroactive_query( $this );
		}

		foreach ( $this->hooks->extensions->get_all() as $extension ) {
			if ( $extension instanceof WordPoints_Hook_Retroactive_Query_FilterI ) {
				$extension->filter_retroactive_query( $this );
			}
		}
	}

	protected function group_results() {

		$grouped_results = array();

		foreach ( $this->results as $result ) {
			$grouped_results[ $result->target ][] = $result;
		}

		$this->results = $grouped_results;
	}

	public function add_condition( array $condition ) {

		$this->arg_hierarchy->push_to( 'conditions', $condition );
	}

	public function select_value( $data = array() ) {

		$this->arg_hierarchy->set_field( 'select', $data );
	}

	public function set_target( $target_arg ) {

		foreach ( $target_arg as $arg_slug ) {
			$this->arg_hierarchy_push( $arg_slug );
		}

//		$id = $this->arg_hierarchy->get_id();
//
//		// If this entity has a parent relationship, we
//		if ( $this->arg_hierarchy->ascend() ) {
//			$field = $this->arg_hierarchy->get_field( 'storage_info', 'meta', 'field' );
//			$this->arg_hierarchy->ascend();
//		} else {
			$field = $this->arg_hierarchy->get_field( 'storage_info', 'meta', 'id_field' );
//		}

		$this->select_value( array( 'field' => $field, 'as' => 'target' ) );

//		$this->arg_hierarchy->go_to( $id );
	}

	/**
	 *
	 *
	 * @since 1.
	 * @return WordPoints_EntityishI
	 */
	public function get_arg() {
		return $this->arg_hierarchy->get_field( 'arg' );
	}

	public function arg_hierarchy_push( $slug ) {

		$current_slug = $this->arg_hierarchy->get_field( 'slug' );

		if ( $current_slug === $slug && $this->arg_hierarchy->is_main() ) {
			return;
		}

//		if ( $current_slug !== $slug ) {

			if ( empty( $current_slug ) ) {
				$arg = $this->entities->get( $slug );
			} else {

				// If this child exists, don't overwrite it, just descend into it.
				if ( $this->arg_hierarchy->has_child( $slug ) ) {
					$this->arg_hierarchy->descend( $slug );
					return;
				}

				$parent_arg = $this->arg_hierarchy->get_field( 'arg' );

				if ( $parent_arg instanceof WordPoints_Entity_ParentI ) {
					$arg = $parent_arg->get_child( $slug );
				} else {
					return; // TODO
				}
			}

			if ( $arg instanceof WordPoints_Entity_Array ) {
				$arg = $this->entities->get( $arg->get_entity_slug() );
			}

			$data['arg'] = $arg;

			// TODO check if storage type is recognized?
			$data['storage_info'] = $arg->get_storage_info();

			$this->arg_hierarchy->push( $slug, $data );

//		} else {
//			$data = array();
//		}
	}

	public function arg_hierarchy_pop() {
		$this->arg_hierarchy->pop();
	}

	public function reset() {
		$this->arg_hierarchy->reset();
	}
}

interface WordPoints_Hook_Retroactive_Query_FilterI {
	public function filter_retroactive_query( WordPoints_Hook_Retroactive_QueryI $query );
}

interface WordPoints_Hook_Retroactive_Query_ExecutorI {
	public function execute( $query );
}

/*
 * 1. We need to know what element is supposed to process each piece.
 * 2. We need to let the target modify the query.
 * 3. All elements must add their conditions to the query, even when the query won't
 * be able to process them. Otherwise we'll be doing a lot of unnecessary filtering.
 *
 *
 *
 * We can only do a COUNT(*) and GROUP BY when no other fields beside the target
 * need to be returned. When their are other fields added for filters that will run
 * later, we would have to perform the COUNT(*) and GROUP BY after filtering.
 *
 * But What if the target needs multiple fields but also the GROUP BY and count?
 * Should we just let it do the processing for that itself in that case?
 */



class WordPoints_Hook_Retroactive_Query_Executor_Array
	implements WordPoints_Hook_Retroactive_Query_ExecutorI {

	protected $parent_data;

	protected $array;
	protected $results;

	public function execute( $query ) {

		foreach ( $query as $arg_data ) {

			$meta = $arg_data['storage_info']['meta'];

			if ( isset( $meta['getter'] ) ) {
				$this->array = $meta['getter']();
			}

			if ( isset( $arg_data['conditions'] ) ) {

				foreach ( $arg_data['conditions'] as $condition ) {

					if ( ! isset( $condition['field'] ) ) {
						if ( $arg_data['arg'] instanceof WordPoints_Entity_Attr ) {
							$condition['field'] = $meta['field'];
						} else {
							$condition['field'] = $meta['id_field'];
						}
					}

					if ( ! isset( $condition['condition'] ) ) {
						$condition['condition'] = '=';
					}

					$filter_args = array();

					switch ( $condition['condition'] ) {

						case '=':
							$filter_args[ $condition['field'] ] = $condition['value'];
							break;

						case 'in':
							// TODO

						default:
							return new WP_Error( 'invalid condition type' );
					}

					$this->array = wp_list_filter( $this->array, $filter_args );
				}
			}

//			if ( isset( $arg_data['select'] ) ) {
//				$builder->add_field( $arg_data['select'] );
//			}

			if ( isset( $arg_data['sub_args'] ) ) {
				$this->parent_data = $arg_data;
				$this->array = $this->execute( $arg_data['sub_args'] );
			}

			$this->results = $this->array;
		}

		return $this->results;
	}
}

wordpoints_hooks()->retroactive_query_executors->register( 'array', 'WordPoints_Hook_Retroactive_Query_Executor_Array' );

class WordPoints_Hook_Retroactive_Query_Executor_MySQL
	implements WordPoints_Hook_Retroactive_Query_ExecutorI {

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Query_BuilderI
	 */
	protected $builder;
	protected $arg_data;
	protected $parent_data;
	protected $grandparent_data;

	protected $entered_join;

	public function execute( $query ) {
		// WE should be getting a hierarchy object here.
		// This will let us loop over things easier.
		// We'll also need to be marking the non-usable parts of the query.
		// We'll need to test that as well.

		global $wpdb;

		$this->builder = new WordPoints_Query_Builder_DB_MySQL();

		$this->build_query( $query );

		$sql = $this->builder->get_query();

		if ( is_wp_error( $sql ) ) {
			return $sql;
		}

		return $wpdb->get_results( $sql );
	}

	protected function build_query( $args ) {

		foreach ( $args as $arg_data ) {

			$this->arg_data = $arg_data;

			if ( isset( $arg_data['storage_info']['meta']['table_name'] ) ) {
				$this->build_table_schema( $arg_data['storage_info']['meta'] );
			}

			if ( isset( $arg_data['conditions'] ) ) {
				$this->build_conditions( $arg_data['conditions'] );
			}

			if ( isset( $arg_data['select'] ) ) {
				$this->builder->add_field( $arg_data['select'] );
			}

			if ( isset( $arg_data['sub_args'] ) ) {

				$this->grandparent_data = $this->parent_data;
				$this->parent_data = $arg_data;

				$this->build_query( $arg_data['sub_args'] );

				$this->parent_data = $this->grandparent_data;
			}

			if ( isset( $this->entered_join ) ) {
				$this->builder->exit_join();
				$this->entered_join = null;
			}
		}
	}

	protected function build_table_schema( $db ) {

		if ( $this->builder->get_table() ) {

			if ( $this->parent_data['arg'] instanceof WordPoints_Entity_Relationship ) {
				$primary_field = $this->parent_data['storage_info']['meta']['field'];
				$join_field = $db['id_field'];
				//$primary_field = $this->arg_data['arg']->get_secondary_field();
			} elseif ( $this->arg_data['arg'] instanceof WordPoints_Entity_Relationship ) {
				$join_field = $db['join_field'];
				$primary_field = $this->parent_data['storage_info']['meta']['id_field'];
			} else {
				throw new WordPoints_Query_Builder_Exception( 'Houston, we have a problem.' );
			}

			if ( is_array( $primary_field ) && isset( $primary_field['table_name'] ) ) {
				$this->builder->enter_join( $primary_field );
				$primary_field = $primary_field['on']['join_field'];
			}

			$join = array(
				'table_name' => $db['table_name'],
				'on' => array(
					'join_field' => $join_field,
					'primary_field' => $primary_field,
				),
			);

			if ( isset( $db['join_where'] ) ) {
				$join['where'] = $db['join_where'];
			}

			$this->builder->enter_join( $join );

			$this->entered_join = true;

		} else {
			$this->builder->set_table( $db['table_name'] );
			$this->builder->add_field( $db['id_field'] );
		}
	}

	protected function build_conditions( $conditions ) {

		// Join conditions should be pushed to the end.
		$join_conditions = array();

		foreach ( $conditions as $condition ) {

			// THis needs to be the identifier field for the type of arg that this
			// field represents.
			// FOr an entity, that would be the id_field.
			// However, for a relationship it could be something else.
			// Ultimately, this will have to be left up to the arg object to
			// decide.
			if ( ! isset( $condition['field'] ) ) {
				if ( $this->arg_data['arg'] instanceof WordPoints_Entity_Relationship ) {
//					$condition['field'] = $this->arg_data['arg']->get_secondary_field();
					$condition['field'] = $this->arg_data['storage_info']['meta']['field'];

					if ( is_array( $condition['field'] && isset( $condition['field']['table_name'] ) ) ) {
						$join_conditions[] = $condition;
						continue;
					}
				} elseif ( $this->arg_data['arg'] instanceof WordPoints_Entity_Attr ) {
					$condition['field'] = $this->arg_data['arg']->get_field();
				} else {
					var_dump($this->arg_data['arg']);exit;
				}
			}

			$this->builder->where( $condition );
		}

		$join_count = count( $join_conditions );
		$i = 0;

		foreach ( $join_conditions as $condition ) {

			$i++;

			$this->builder->enter_join( $condition['field'] );

			$condition['field'] = $condition['field']['on']['primary_field'];

			$this->builder->where( $condition );

			$this->builder->exit_join();

			// If there are more join conditions, we need to leave a fresh, trailing
			// join for the next one to join to.
			if ( $i < $join_count ) {

				// There is a current join, but it is already being used, so we
				// need to exit it.
				$this->builder->exit_join();

				// Then we just build the schema again like before.
				$this->build_table_schema(
					$this->arg_data['storage_info']['meta']
				);
			}
		}
	}
}


wordpoints_hooks()->retroactive_query_executors->register( 'db', 'WordPoints_Hook_Retroactive_Query_Executor_MySQL' );


// EOF
