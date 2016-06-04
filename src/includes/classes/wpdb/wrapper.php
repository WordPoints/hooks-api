<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

class WordPoints_WPDB_Wrapper {

	protected $wpdb;

	public function __construct( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function __get( $name ) {
		return $this->wpdb->$name;
	}

	public function __set( $name, $value ) {
		$this->wpdb->$name = $value;
	}

	public function __isset( $name ) {
		return isset( $this->wpdb->$name );
	}

	public function __unset( $name ) {
		unset( $this->wpdb->$name );
	}

	public function __call( $name, $arguments ) {

		$wrappers = wordpoints_apps()->get_sub_app( 'wpdb_wrappers' );
		$slugs = $wrappers->get_child_slugs( $name );
		$should_listen = $query = false;


		switch ( $name ) {

			case 'insert':
//			case 'replace':
				$query = new WordPoints_WPDB_Query_Data_Array( $this->args[0] );
			break;

			case 'update':
				$query =  new WordPoints_WPDB_Query_Data_Array( $this->args[0], $this->args[2] );
			break;

			case 'delete':
				$query =  new WordPoints_WPDB_Query_Data_Array( $this->args[0], $this->args[1] );
			break;

			case 'query':
				$query =  new WordPoints_WPDB_Query_Data_SQL( $this->args[0] );
			break;
		}

		if ( $query ) {
			/** @var WordPoints_WPDB_Query_WrapperI $wrapper */
			$wrapper = $wrappers->get( $name, $query );

			$result = $wrapper->execute();
		} else {
			$result = call_user_func_array( array( $this->wpdb, $name ), $arguments );
		}

		return $result;
	}
}


interface WordPoints_WPDB_Query_Data {
	public function get_table_name();
	public function get_where_clause();
}

interface WordPoints_MySQL_Query_Parser_SimpleI {
	public function get_table_name();
	public function get_where_clause();
}

class WordPoints_MySQL_Query_Parser_Simple_Insert implements WordPoints_MySQL_Query_Parser_SimpleI {

	public function __construct( $sql ) {
		$this->sql = $sql;
	}

	public function get_table_name() {
		// TODO: Implement get_table_name() method.
	}

	public function get_where_clause() {

		// we'd have to get the keys from the database table, to know what the where
		// was aexactly. but we can just get the complete where, even though some
		// columns will not be needed. then the other code can decide what to do.
		// although I guess sense we have the tablel name we could reun the query
		// ourselves if we need/want to.
		if ( stripos( $this->sql, 'ON DUPLICATE KEY UPDATE' ) ) {

		}

		return '';
	}
}

class WordPoints_WPDB_Query_Data_SQL implements WordPoints_WPDB_Query_Data {

	protected $sql;
	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_MySQL_Query_Parser_SimpleI
	 */
	protected $parser;

	public function __construct( $sql ) {
		$this->sql = $sql;
		$this->parser = $this->get_parser();
	}

	protected function get_parser() {
		// may need to determine parser based on query type, however, it may be that
		// we actually need to parse the query somewhat just to determine what kind
		// of query it is.
		// maybe the parser will actually be passed in, and the query type deterimed
		// by teh method wrapper.
		$parser = new WordPoints_MySQL_Query_Parser_Simple_Insert( $this->sql );
		// maybe we don't even need to parse.
		return $parser;
	}

	public function get_table_name() {
		return $this->parser->get_table_name();
	}

	public function get_where_clause() {
		$this->parser->get_where_clause();
	}
}

class WordPoints_WPDB_Query_Data_Array implements WordPoints_WPDB_Query_Data {

	protected $table_name;
	protected $where_data;

	public function __construct( $table_name, $where_data = array() ) {
		$this->table_name = $table_name;
		$this->where_data = $where_data;
	}

	public function get_table_name() {
		return $this->table_name;
	}

	public function get_where_clause() {
		// build the WHERE clause from the data.
	}
}

interface WordPoints_WPDB_Query_WrapperI {
	public function __construct( $slug, $args, $wpdb );
	public function execute();

}

abstract class WordPoints_WPDB_Query_Wrapper implements WordPoints_WPDB_Query_WrapperI {

	protected $slug;
	protected $args;
	protected $wpdb;
	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_WPDB_Query_Data|false
	 */
	protected $data;
	protected $result;
	protected $backed_up_wpdb_vars;

	public function __construct( $slug, $args, $wpdb ) {
		$this->slug = $slug;
		$this->args = $args;
		$this->wpdb = $wpdb;
		$this->data = $this->get_query_data();
	}

	protected function get_query_data() {

		switch ( $this->slug ) {

			case 'insert':
				//			case 'replace':
				return new WordPoints_WPDB_Query_Data_Array( $this->args[0] );

			case 'update':
				return new WordPoints_WPDB_Query_Data_Array( $this->args[0], $this->args[2] );

			case 'delete':
				return new WordPoints_WPDB_Query_Data_Array( $this->args[0], $this->args[1] );

			case 'query':
				return new WordPoints_WPDB_Query_Data_SQL( $this->args[0] );
		}

		return false;
	}

	public function execute() {

		$should_listen = $this->should_listen();

		if ( $should_listen ) {
			$this->before_query();
		}

		$this->result = call_user_func_array( array( $this->wpdb, $this->slug ), $this->args );

		if ( $should_listen && $this->was_successful_query() ) {
			$this->backup_wpdb_vars();
			$this->after_query();
			$this->restore_wpdb_vars();
		}

		return $this->result;
	}

	protected function backup_wpdb_vars() {
		$to_backup = array(
			// public
			'last_error',
			'num_rows',
			'insert_id',
			'func_call',
			
			// marked private but still public
			'rows_affected',
			'last_query',
			'last_result',
			
			// protected but magic getter
			'col_info',
			'result',
		);

		// we could check for the availableility of eahc valu here in case of dropins
		// or they become not available in the future.
		foreach ( $to_backup as $property ) {
			$this->backed_up_wpdb_vars[ $property ] = $this->wpdb->$property;
		}
	}

	protected function restore_wpdb_vars() {

		// make sure that the last result is freed, since we're going to overwrite it
		// with the backed up result.
		$this->wpdb->flush();

		foreach ( $this->backed_up_wpdb_vars as $property => $value ) {
			$this->wpdb->$property = $value;
		}
	}

	abstract public function should_listen();

	abstract protected function before_query();

	abstract protected function was_successful_query();

	abstract protected function after_query();
}

abstract class WordPoints_WPDB_Query_Wrapper_Basic extends WordPoints_WPDB_Query_Wrapper {

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Entity
	 */
	protected $entity;
	protected $table_name;

	/**
	 * check if this is a table that we want to listen for.
	 * see if the table name matches an entity name.
	 * if not, try removing the base prefix, then the site prefix, etc. until
	 * we find a similar entity. then we can doulbe-check that we have the right
	 * entity by then getting the table name form the entity object and checking
	 * that it matches. If all else fails we can loop through the entities, or
	 * else maybe have a cache of the table name index that we can use as a
	 * look-up
	 */
	protected function is_entity_table() {

		$this->entity = 'new entity object' . $this->data->get_table_name();

		return true;
	}

	public function should_listen() {

		if ( ! $this->data ) {
			return false;
		}

		if ( $this->is_entity_table() ) {
			return false;
		}

		return true;
	}

	protected function was_successful_query() {
		return (bool) wordpoints_posint( $not_by_ref = $this->wpdb->num_rows );
	}
}

class WordPoints_WPDB_Query_Wrapper_Query implements WordPoints_WPDB_Query_WrapperI {

	protected $slug;
	protected $args;
	protected $wpdb;

	public function __construct( $slug, $args, $wpdb ) {
		$this->slug = $slug;
		$this->args = $args;
		$this->wpdb = $wpdb;
	}

	public function execute() {

		// check if this is a query that we want to listen to.
		// if so, call execute on a particular child.
		// otherwise, just call execute.
		if ( $this->get_query_type() ) {

		} else {
			$result = call_user_func_array( array( $this->wpdb, $this->slug ), $this->args );
		}

		return $result;
	}
}

class WordPoints_WPDB_Query_Wrapper_Update extends WordPoints_WPDB_Query_Wrapper_Basic {
//	 get a "before" snapshot of the entities.
//	 run a query based on the $where args.
//	 acutally, not needed. just grab the where args. that tells us what was
//	 being changed. the only thing that we'd need was the ids of each of the
//	 entities taht actually was changed. however, I guess maybe we can't know
//	 which entities actually changed, and how much they changed (which atts),
//	 unless we actually query pull out the "before" snapshots for each one.
//	 I suppose in some simple cases when the ID is supplied in where, there
//	 is only one entity to be updated. but we'd still need the before snapshot
//	 unless there was only one attribute being updated, in which case it would
//	 be safe to assumte that hte attribute was updated wihtou needing a before
//	 snapshot, just by checkin gthe nubmer of affected rows. However,  that
//	 might not be worht it.
	protected function before_query() {

		$ids = $this->get_entity_ids();

		$this->entities = new WordPoints_Entity_Array( $this->entity->get_slug() );
		$this->entities->set_the_value( $ids );
	}

	protected function get_entity_ids() {
		$where = $this->data->get_where_clause();
		$id_field = wordpoints_escape_mysql_identifier( $this->entity->get_id_field() );
		$table_name = wordpoints_escape_mysql_identifier( $this->data->get_table_name() );
		return $this->wpdb->get_col( "SELECT {$id_field} FROM {$table_name} {$where}" );
	}

	protected function after_query() {

		// I guess we invoke the api to run the action now?
		$this->trigger_entity_update_actions();
	}
}

class WordPoints_WPDB_Query_Wrapper_Insert extends WordPoints_WPDB_Query_Wrapper_Basic {

	
	protected function before_query() {

		// in case of on duplicate key update.
		$where_clause = $this->data->get_where_clause();

		// get a before snapshot, if any matching are found.
		if ( $where_clause ) {

		}
	}

	protected function after_query() {
		$this->entity->set_the_value( $this->wpdb->insert_id );
		// I guess we invoke the api to run the action now?
		$this->trigger_entity_create_actions();
	}
}


class WordPoints_WPDB_Query_Wrapper_Delete extends WordPoints_WPDB_Query_Wrapper_Update {

	protected function after_query() {
		// I guess we invoke the api to run the action now?
		$this->trigger_entity_delete_actions( $this->entities );
	}
}



// EOF
