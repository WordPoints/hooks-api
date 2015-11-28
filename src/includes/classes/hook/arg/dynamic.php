<?php

/**
 * Dynamic hook arg class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents a hook arg for dynamic entities.
 *
 * Some hook events are tied to entities which are "dynamic": for example, an entity
 * is registered for each post type with a slug of the format "post-{$slug}". The
 * $slug portion is dynamic, so we call these entities dynamic entities.
 *
 * Hook events have to be registered for specific entities, so events registered for
 * dynamic entities also need to be dynamic. However, hook actions don't have to be
 * tied to specific entities. They can supply generic args that are used by multiple
 * different entities (for example, "post"). This fact allows us to register a single
 * generic action for each group of dynamic entities, using this class when
 * registering the event args for the dynamic entities.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Arg_Dynamic extends WordPoints_Hook_Arg {

	/**
	 * The slug of the arg to retrieve from the action.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $arg_slug;

	/**
	 * @since 1.0.0
	 */
	public function __construct( $slug, WordPoints_Hook_ActionI $action = null ) {

		parent::__construct( $slug, $action );

		$parts = explode( '-', $this->slug, 2 );

		if ( isset( $parts[1] ) ) {
			$this->arg_slug = $parts[0];
		} else {
			$this->arg_slug = $this->slug;
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function get_value() {

		// We first check if there is an action arg with the full, dynamic name.
		$value = parent::get_value();

		// If not, then we check for an arg with the generic name.
		if ( null === $value && $this->action instanceof WordPoints_Hook_ActionI ) {
			$value = $this->action->get_arg_value( $this->arg_slug );
		}

		return $value;
	}
}

// EOF
