<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

class WordPoints_Hook_Arg_Action extends WordPoints_Hook_Arg {

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hook_ActionI
	 */
	protected $action;

	public function __construct( $slug, WordPoints_Hook_ActionI $action = null ) {

		parent::__construct( $slug );

		$this->action = $action;
	}

	public function get_value() {
		if ( $this->action instanceof WordPoints_Hook_ActionI ) {
			return $this->action->get_arg_value( $this->entity_slug );
		} else {
			return null;
		}
	}

	public function get_title() {
		$this->get_entity()->get_title();
	}
}

class WordPoints_Hook_Arg_Current_Post extends WordPoints_Hook_Arg {

	public function get_title() {
		return __( 'Current Post', 'wordpoints' ); // TODO better title?
	}

	public function get_value() {

		if ( ! is_main_query() ) {
			return false;
		}

		$object = get_queried_object();

		if ( $object instanceof WP_Post ) {
			return $object;
		} else {
			return false;
		}
	}
}

// EOF
