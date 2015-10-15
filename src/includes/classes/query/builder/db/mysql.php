<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */


interface WordPoints_Query_BuilderI {
	public function set_fields( $fields );
	public function add_field( $field );
	public function set_table( $table_name );
	public function get_table();
	public function enter_join( $join );
	public function exit_join();
	public function where( $condition );
	public function get_query();
}

abstract class WordPoints_Query_Builder_DB implements WordPoints_Query_BuilderI {

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hierarchy
	 */
	protected $query;

	protected $aliases = array();

	public function __construct() {
		$this->query = new WordPoints_Hierarchy( 'joins' );
		$this->query->push( '', array() );
	}

	public function set_fields( $fields ) {
		$this->query->set_field( 'fields', $fields );
	}

	public function add_field( $field ) {
		$this->query->push_to( 'fields', $field );
	}

	public function set_table( $table_name ) {
		$this->query->set_field( 'table_name',  $table_name );
	}

	public function get_table() {
		return $this->query->get_field( 'table_name' );
	}

	protected function get_alias( $data ) {

		if ( ! isset( $this->aliases[ $data['table_name'] ] ) ) {
			$this->aliases[ $data['table_name'] ] = 0;
		}

		return $data['table_name'] . '_' . ++$this->aliases[ $data['table_name'] ];
	}

	public function enter_join( $join ) {
		$alias = $this->get_alias( $join );
		$this->query->push( $alias, $join );
	}

	public function exit_join() {
		$this->query->pop();
	}

	public function where( $condition ) {

		$where = $this->query->get_field( 'where' );

		if ( ! is_array( $where ) ) {
			$where = array();
		}

		$where[] = $condition;

		$this->query->set_field( 'where', $where );
	}
}

class WordPoints_Query_Builder_Exception extends Exception {}

class WordPoints_Query_Builder_DB_MySQL extends WordPoints_Query_Builder_DB {

	protected $sql;

	protected $fields;

	protected $alias;

	public function get_query() {

		$this->sql = '';

		var_dump($this->query);
		try {
			$this->build_query( $this->query->get() );
		} catch ( WordPoints_Query_Builder_Exception $e ) {
			return new WP_Error( $e->getMessage(), $e->getTraceAsString() );
		}

		return $this->sql;
	}

	protected function build_query( $query ) {

		var_dump( __METHOD__, $query );

		$this->alias = $this->get_alias( $query );

		$this->build_fields( $query );
		$this->build_from( $query );
		$this->build_joins( $query );
		$this->build_where( $query );
		$this->build_select();
	}

	protected function build_select() {

		if ( empty( $this->fields ) ) {
			throw new WordPoints_Query_Builder_Exception( 'no fields' );
		}

		$this->sql = 'SELECT' . $this->fields . $this->sql;
	}

	protected function build_fields( $query ) {

		if ( empty( $query['fields'] ) ) {
			return;
		}

		foreach ( $query['fields'] as $field ) {

			if ( ! is_array( $field ) ) {
				$field = array( 'field' => $field );
			}

			if ( ! isset( $field['field'] ) ) {
				throw new WordPoints_Query_Builder_Exception( 'invalid field' );
			}

			if ( ! empty( $this->fields ) ) {
				$this->fields .= ',';
			}

			$this->fields .= ' ' . $this->build_field( $field['field'] );

			$as = $field['field'];
			if ( isset( $field['as'] ) ) {
				$as = $field['as'];
			}

			$this->fields .= ' AS `' . $this->escape_identifier( $as ) . '`';
		}
	}

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @param $field
	 *
	 * @throws WordPoints_Query_Builder_Exception
	 */
	protected function build_field( $field ) {

		$field_name = $this->get_field_name( $field );

		$sql = '`' . $this->escape_identifier( $this->alias ) . '`';
		$sql .= '.`' . $this->escape_identifier( $field_name ) . '`';

		return $sql;
	}

	protected function build_from( $query ) {

		if ( ! isset( $query['table_name'] ) ) {
			throw new WordPoints_Query_Builder_Exception( 'table_name' );
		}

		$this->sql .= "\n";
		$this->sql .= 'FROM `' . $this->escape_identifier( $query['table_name'] ) . '`';
		$this->sql .= ' AS `' . $this->escape_identifier( $this->alias ) . '`';
	}

	protected function build_joins( $query ) {

		if ( ! isset( $query['joins'] ) ) {
			return;
		}

		foreach ( $query['joins'] as $join ) {
			$this->build_join( $join );
		}
	}

	protected function build_join( $join ) {

		if ( ! isset( $join['table_name'] ) ) {
			throw new WordPoints_Query_Builder_Exception( 'join table_name' );
		}

		if ( ! isset( $join['on'] ) ) {
			throw new WordPoints_Query_Builder_Exception( 'join on' );
		}

		if ( ! isset( $join['on']['join_field'] ) ) {
			throw new WordPoints_Query_Builder_Exception( 'join on join_field' );
		}

		if ( ! isset( $join['on']['primary_field'] ) ) {
			throw new WordPoints_Query_Builder_Exception( 'join on primary_field' );
		}

		$alias = $this->get_alias( $join );

		$this->sql .= "\n";
		$this->sql .= 'INNER JOIN `' . $this->escape_identifier( $join['table_name'] ) . '`';
		$this->sql .= ' AS `' . $this->escape_identifier( $alias ) . '`';
		$this->sql .= "\n\t" . 'ON ' . $this->build_field( $join['on']['primary_field'] );

		$old_alias = $this->alias;
		$this->alias = $alias;

		$this->sql .= ' = ' . $this->build_field( $join['on']['join_field'] );

		$this->build_fields( $join );

		if ( isset( $join['where'] ) ) {
			foreach ( $join['where'] as $condition ) {
				$this->build_condition( $condition );
			}
		}

		$this->build_joins( $join );

		$this->alias = $old_alias;
	}

	protected function build_where( $query ) {

		if ( empty( $query['where'] ) ) {
			return;
		}

		$this->sql .= "\n";
		$this->sql .= 'WHERE 1=1';

		foreach ( $query['where'] as $condition ) {
			$this->build_condition( $condition );
		}
	}

	protected function build_condition( $condition ) {

		global $wpdb;

		if ( ! isset( $condition['field'], $condition['value'] ) ) {
			var_dump($condition);
			throw new WordPoints_Query_Builder_Exception( 'bad condition' );
		}

		if ( ! isset( $condition['compare'] ) ) {
			$condition['compare'] = '=';
		}

		$this->sql .= "\n\t";

		$this->build_condition_type( $condition );

		$field_type = $this->get_field_type( $condition['field'] );
		if ( 'serialized_array' === $field_type ) {
			if ( is_array( $condition['value'] ) ) {
				if ( isset( $condition['compare'] ) && 'in' === $condition['compare'] ) {
					$this->sql .= '( 1=0 ';
					foreach ( $condition['value'] as $index => $value ) {
						$this->build_condition(
							array(
								'type' => 'or',
								'value' => "%{$value}%",
								'field' => $condition['field'],
								'compare' => 'like',
							)
						);
					}
					$this->sql .= ')';
					return;
				}
			} else {
				$condition['value'] = $this->serialize_value( $condition['value'] );
			}
		}

		$this->sql .= $this->build_field( $condition['field'] );

		switch ( $condition['compare'] ) {

			case '=':
				$this->sql .= ' = ';
				break;

			case 'like':
				$this->sql .= ' LIKE ';
				break;

			case 'in':
				$this->sql .= ' IN (' . wordpoints_prepare__in( $condition['value'] ) . ')';
				return;

			default:
				throw new WordPoints_Query_Builder_Exception( 'bad condition comparison' );
		}

		if ( ! is_scalar( $condition['value'] ) ) {
			throw new WordPoints_Query_Builder_Exception( 'bad condition value' );
		}

		$this->sql .= $wpdb->prepare( '%s', $condition['value'] );
	}

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @param $condition
	 *
	 * @throws WordPoints_Query_Builder_Exception
	 */
	protected function build_condition_type( $condition ) {

		if ( ! isset( $condition['type'] ) ) {
			$condition['type'] = 'and';
		}

		switch ( $condition['type'] ) {

			case 'and':
				$this->sql .= 'AND ';
				break;

			case 'or':
				$this->sql .= 'OR ';
				break;

			default:
				throw new WordPoints_Query_Builder_Exception( 'bad condition type' );
		}
	}

	protected function escape_identifier( $identifier ) {

		return str_replace( '`', '``', $identifier );
	}

	protected function get_field_name( $field ) {

		if ( is_array( $field ) ) {

			if ( ! isset( $field['name'] ) ) {
				throw new WordPoints_Query_Builder_Exception( 'field name' );
			}

			$field = $field['name'];
		}

		return $field;
	}

	protected function get_field_type( $field ) {

		if ( is_array( $field ) && isset( $field['type'] ) ) {
			return $field['type'];
		}

		return 'string';
	}

	protected function serialize_value( $value ) {

		if ( is_string( $value ) ) {
			$value = '"' . $value . '"';
		}

		return '%:' . $value . ';%';
	}
}

// EOF
