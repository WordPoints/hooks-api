<?php

/**
 * Hook action object.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents an action.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Action implements WordPoints_Hook_ActionI {

	/**
	 * The slug of this action.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The args the action was called with.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $args;

	/**
	 * The indexes of the named args in the array of action args.
	 *
	 * You must either define this in your subclass or override the get_arg_value()
	 * method.
	 *
	 * The indexes of the array should match the indexes in the args array. The
	 * values are the slugs of the args whose value is that that index. The slugs
	 * will usually be entity slugs or entity aliases and the entity value/ID will
	 * be the value of that action arg.
	 *
	 * Keep in mind that the args array (and therefore this) are 0-indexed.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	protected $arg_index;

	/**
	 * Requirements for the args that must be met for the action to fire.
	 *
	 * The requirements are expected arg values indexed by the index of the arg
	 * in the args array.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $requirements = array();

	/**
	 * @since 1.0.0
	 */
	public function __construct( $slug, array $action_args, array $args = array() ) {

		$this->slug = $slug;
		$this->args = $action_args;

		if ( isset( $args['requirements'] ) ) {
			$this->requirements = $args['requirements'];
		}

		if ( isset( $args['arg_index'] ) ) {
			$this->arg_index = $args['arg_index'];
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * @since 1.0.0
	 */
	public function should_fire() {

		foreach ( $this->requirements as $arg => $value ) {
			if ( ! isset( $this->args[ $arg ] ) || $this->args[ $arg ] !== $value ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_arg_value( $arg_slug ) {

		if ( ! isset( $this->arg_index[ $arg_slug ] ) ) {
			return null;
		}

		$index = $this->arg_index[ $arg_slug ];

		if ( ! isset( $this->args[ $index ] ) ) {
			return null;
		}

		return $this->args[ $index ];
	}
}

// EOF
