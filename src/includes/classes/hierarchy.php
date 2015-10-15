<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

interface WordPoints_HierarchyI {
	public function get();
	public function push( $field, $data );
	public function pop();
	public function set_field( $field, $value );
	public function get_field( $field, $sub = null, $sub_2 = null );
	public function get_id();
	public function go_to( $id );
	public function push_to( $field, $value );
	public function ascend();
	public function descend( $to = null );
	public function is_main();
	public function next();
	public function reset();
}

abstract class WordPoints_Hierarchy_ implements  WordPoints_HierarchyI {

	protected $hierarchy = array();
	protected $current;
	protected $stack;
	protected $descendant_index;

	public function __construct() {
		$this->current =& $this->hierarchy;
	}

	public function get() {
		return $this->hierarchy;
	}

	public function push( $field, $data ) {

		if ( ! isset( $this->current['slug'] ) || $this->current['slug'] !== $field ) {

			if ( ! empty( $this->hierarchy ) ) {
				$this->current[ $this->descendant_index ][ $field ] = $data;
				$this->current =& $this->current[ $this->descendant_index ][ $field ];
			} else {
				$this->current = $this->stack[0] = $data;
			}

			$this->current['slug'] = $field;
		}

		$this->stack[] = $this->current;
	}

	public function pop() {

		unset( $this->current );

		$this->current = array_pop( $this->stack );
	}

	public function reset() {
		//	unset( $this->current_arg );
		$this->current =& $this->hierarchy;
		$this->stack = array();
	}

//	public function set_field( $field, $value ) {
//		$this->current[ $field ] = $value;
//	}
//
//	public function get_field( $field ) {
//		if ( ! isset( $this->current[ $field ] ) ) {
//			return null;
//		}
//
//		return $this->current[ $field ];
//	}

	public function push_to( $field, $value ) {

		if ( ! isset( $this->current[ $field ] ) ) {

		}
		//var_dump( $field, $value,$this->current[ $field ] );
		$this->current[ $field ][] = $value;
	}

	public function set_sub_field( $slug, $field, $value ) {
		$this->current[ $this->descendant_index ][ $slug ][ $field ] = $value;
	}

	public function get_sub_field( $slug, $field ) {
		return $this->current[ $this->descendant_index ][ $slug ][ $field ];
	}

	public function get_sub_data( $slug ) {
		return $this->current[ $this->descendant_index ][ $slug ];
	}

	public function ascend() {

		if ( ! $this->stack ) {
			return false;
		}

		$this->pop();

		return true;
	}

	public function descend( $to = null ) {

		if ( ! isset( $this->hierarchy[ $this->descendant_index ] ) ) {
			return false;
		}

		reset( $this->current[ $this->descendant_index ] );
		$field = key( $this->current[ $this->descendant_index ] );
		$this->current =& $this->current[ $this->descendant_index ][ $field ];

		$this->stack[] = $this->current;

		return true;
	}
}

class WordPoints_Hierarchy extends WordPoints_Hierarchy_ {

	/** @var int $current */
	protected $current = 0;
	protected $i = 0;

	public function __construct( $descendant_index ) {
		$this->descendant_index = $descendant_index;
	}

	public function get() {
	//	var_dump( $this->hierarchy );

		if ( empty( $this->hierarchy ) ) {
			return array();
		}

		return $this->build_hierarchy( $this->hierarchy[0] );
	}

	protected function build_hierarchy( $arg ) {

		if ( isset( $arg[ $this->descendant_index ] ) ) {

			foreach ( $arg[ $this->descendant_index ] as $sub_arg => $i ) {
				$arg[ $this->descendant_index ][ $sub_arg ] = $this->build_hierarchy( $this->hierarchy[ $i ] );
			}
		}

		if ( isset( $arg['_parent'] ) ) {
			unset( $arg['_parent'] );
		}

		unset( $arg['_i'] );

		return $arg;
	}

	public function push( $field, $data ) {

		if ( ! isset( $this->hierarchy[ $this->current ]['slug'] ) || $this->hierarchy[ $this->current ]['slug'] !== $field ) {

			$i = $this->i++;

			if ( ! empty( $this->hierarchy ) ) {
				$this->hierarchy[ $this->current ][ $this->descendant_index ][ $field ] = $i;
				$data['_parent'] = $this->current;
			}

			$this->hierarchy[ $i ] = $data;
			$this->hierarchy[ $i ]['slug'] = $field;
			$this->hierarchy[ $i ]['_i'] = $i;

			$this->current = $i;
		}
	}

	public function pop() {

		$this->current = isset( $this->hierarchy[ $this->current ]['_parent'] )
			?  $this->hierarchy[ $this->current ]['_parent']
			: 0;
	}

	public function reset() {
		$this->current = 0;
	}

	public function set_field( $field, $value ) {
		$this->hierarchy[ $this->current ][ $field ] = $value;
	}

	public function get_field( $field, $sub = null, $sub_2 = null ) {

		if ( ! isset( $this->hierarchy[ $this->current ][ $field ] ) ) {
			return null;
		}

		if ( isset( $sub ) ) {
			if ( ! isset( $this->hierarchy[ $this->current ][ $field ][ $sub ] ) ) {
				return null;
			}

			if ( isset( $sub_2 ) ) {
				if ( ! isset( $this->hierarchy[ $this->current ][ $field ][ $sub ][ $sub_2 ] ) ) {
					return null;
				} else {
					return $this->hierarchy[ $this->current ][ $field ][ $sub ][ $sub_2 ];
				}
			}

			return $this->hierarchy[ $this->current ][ $field ][ $sub ];
		}

		return $this->hierarchy[ $this->current ][ $field ];
	}

	public function push_to( $field, $value ) {

		if ( ! isset( $this->hierarchy[ $this->current ][ $field ] ) ) {

		}
		//var_dump( $field, $value,$this->current[ $field ] );
		$this->hierarchy[ $this->current ][ $field ][] = $value;
	}

	public function set_sub_field( $slug, $field, $value ) {
		$this->hierarchy[ $this->current ][ $this->descendant_index ][ $slug ][ $field ] = $value;
	}

	public function get_sub_field( $slug, $field ) {

		return $this->hierarchy[ $this->current ][ $this->descendant_index ][ $slug ][ $field ];
	}

	public function get_sub_data( $slug ) {
		return $this->hierarchy[ $this->current ][ $this->descendant_index ][ $slug ];
	}

	public function has_child( $slug ) {
		return isset( $this->hierarchy[ $this->current ][ $this->descendant_index ][ $slug ] );
	}

	public function get_children() {
		return isset( $this->hierarchy[ $this->current ][ $this->descendant_index ][ $slug ] );

	}

	public function ascend() {

		if ( ! isset( $this->hierarchy[ $this->current ]['_parent'] ) ) {
			return false;
		}

		$this->current = $this->hierarchy[ $this->current ]['_parent'];

		return true;
	}

	public function descend( $to = null ) {

		if ( ! isset( $this->hierarchy[ $this->current ][ $this->descendant_index ] ) ) {
			return false;
		}

		if ( isset( $to ) ) {

			if ( ! isset( $this->hierarchy[ $this->current ][ $this->descendant_index ][ $to ] ) ) {
				return false;
			}

			$this->current = $this->hierarchy[ $this->current ][ $this->descendant_index ][ $to ];

		} else {

			$this->current = reset(
				$this->hierarchy[ $this->current ][ $this->descendant_index ]
			);
		}

		return true;
	}

	public function next() {

		if ( ! isset( $this->hierarchy[ $this->current ]['_parent'] ) ) {
			return false;
		}

		$parent = $this->hierarchy[ $this->current ]['_parent'];

		if ( ! isset( $this->hierarchy[ $parent ][ $this->descendant_index ] ) ) {
			return false;
		}

		$sub_args = $this->hierarchy[ $parent ][ $this->descendant_index ];

		reset( $sub_args );

		while ( current( $sub_args ) !== $this->current ) {
			next( $sub_args );
		}

		if ( ! next( $sub_args ) ) {
			return false;
		}

		$this->current = current( $sub_args );

		return true;
	}

	public function get_id() {
		return $this->current;
	}

	public function go_to( $id ) {

		if ( false === wordpoints_posint( $id ) || $id < 0 ) {
			return;
		}

		$this->current = $id;
	}

	public function is_main() {
		return $this->current === 0;
	}
}

// EOF
