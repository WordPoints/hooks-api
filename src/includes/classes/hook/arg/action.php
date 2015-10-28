<?php

/**
 * Action hook arg class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents a hook arg whose value is retrieved from the action.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Arg_Action extends WordPoints_Hook_Arg {

	/**
	 * The action object.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_ActionI
	 */
	protected $action;

	/**
	 * @param string                       $slug   The arg slug.
	 * @param WordPoints_Hook_ActionI|null $action The calling action's object.
	 */
	public function __construct( $slug, WordPoints_Hook_ActionI $action = null ) {

		parent::__construct( $slug );

		$this->action = $action;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_value() {

		if ( $this->action instanceof WordPoints_Hook_ActionI ) {
			return $this->action->get_arg_value( $this->entity_slug );
		} else {
			return null;
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		$this->get_entity()->get_title();
	}
}

// EOF
