<?php

/**
 * Hook arg class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents a hook arg.
 *
 * When an action is fired, each event that is triggered by it needs to retrieve one
 * more values related to that event. These are called the event args. The values may
 * come from the action itself, or from elsewhere. This class provides a common
 * interface for retrieving those values and converting them into entities.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Arg {

	/**
	 * The slug of this arg.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The slug of the type of entity that this arg's value is.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $entity_slug;

	/**
	 * The action object.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_ActionI
	 */
	protected $action;

	/**
	 * Whether this arg is stateful or not.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $is_stateful = false;

	/**
	 * Construct the arg with a slug.
	 *
	 * Slugs are typically the slug of the entity itself, but this isn't always the
	 * case. Sometimes more than one value associated with an event will be of the
	 * same type of entity. To work around this, the arg slug can also be an entity
	 * alias. Entity aliases are just entity slugs that are prefixed with an
	 * arbitrary string ending in a semicolon. For example, 'current:user' is an
	 * alias of the User entity.
	 *
	 * @since 1.0.0
	 *
	 * @param string                  $slug   The arg slug.
	 * @param WordPoints_Hook_ActionI $action The calling action's object.
	 */
	public function __construct( $slug, WordPoints_Hook_ActionI $action = null ) {

		$this->slug = $slug;
		$this->action = $action;

		$parts = explode( ':', $slug, 2 );

		if ( isset( $parts[1] ) ) {
			$this->entity_slug = $parts[1];
		} else {
			$this->entity_slug = $slug;
		}
	}

	/**
	 * Get the slug of this arg.
	 *
	 * @since 1.0.0
	 *
	 * @return string The arg slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get the slug of the type of entity this arg is.
	 *
	 * @since 1.0.0
	 *
	 * @return string The entity slug.
	 */
	public function get_entity_slug() {
		return $this->entity_slug;
	}

	/**
	 * Get the entity object for this arg's value.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_Entity|false The entity, or false if not registered.
	 */
	public function get_entity() {

		$entity = wordpoints_entities()->get(
			$this->get_entity_slug()
		);

		if ( $entity instanceof WordPoints_Entity ) {
			$value = $this->get_value();

			if ( $value ) {
				$entity->set_the_value( $value );
			}
		}

		return $entity;
	}

	/**
	 * Retrieves the value for this arg.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed The arg value.
	 */
	public function get_value() {

		if ( $this->action instanceof WordPoints_Hook_ActionI ) {
			return $this->action->get_arg_value( $this->slug );
		} else {
			return null;
		}
	}

	/**
	 * Retrieves the human-readable title of this arg.
	 *
	 * @since 1.0.0
	 *
	 * @return string The arg title.
	 */
	public function get_title() {

		$entity = $this->get_entity();

		if ( ! $entity ) {
			return $this->slug;
		}

		return $entity->get_title();
	}

	/**
	 * Check whether the arg is stateful.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether this arg is stateful.
	 */
	public function is_stateful() {
		return $this->is_stateful;
	}
}

// EOF
