<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

abstract class WordPoints_Hook_Arg {

	protected $slug;

	protected $entity_slug;

	public function __construct( $slug ) {

		$this->slug = $slug;

		$parts = explode( ':', $slug, 2 );

		if ( isset( $parts[1] ) ) {
			$this->entity_slug = $parts[1];
		} else {
			$this->entity_slug = $slug;
		}
	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_entity_slug() {
		return $this->entity_slug;
	}

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @return WordPoints_Entity
	 */
	public function get_entity() {

		$entity = wordpoints_apps()->entities->get(
			$this->get_entity_slug()
		);

		if ( $entity instanceof WordPoints_Entity ) {
			$entity->set_the_value( $this->get_value() );
		}

		return $entity;
	}

	abstract public function get_value();
	abstract public function get_title();
}

// EOF
