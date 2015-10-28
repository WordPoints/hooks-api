<?php

/**
 * Entityish class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Bootstrap for representing an entity-like object.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Entityish implements WordPoints_EntityishI {

	/**
	 * The slug of this entity/entity-child.
	 *
	 * You must either set this or override the get_slug() method.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The value of this entity/entity-child.
	 *
	 * @since 1.0.0
	 *
	 * @var mixed
	 */
	protected $the_value;

	/**
	 * Construct the entity/entity-child with a slug.
	 *
	 * @param string $slug The slug of the entity/entity-child.
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;
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
	public function user_can_view( $user_id, $id ) {

		$can_view = true;

		if ( $this instanceof WordPoints_Entity_Check_CapsI ) {
			$can_view = $this->check_user_caps( $user_id, $id );
		}

		return $can_view; // TODO filter here
	}

	/**
	 * @since 1.0.0
	 */
	public function get_the_value() {
		return $this->the_value;
	}

	/**
	 * @since 1.0.0
	 */
	public function set_the_value( $value ) {
		$this->the_value = $value;
	}
}

interface WordPoints_Entity_Check_CapsI { // TODO change the name

	/**
	 * Check whether a user has the caps to view this entity.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $user_id The user's ID.
	 * @param mixed $id      The entity's ID.
	 *
	 * @return bool Whether the user can view the entity.
	 */
	public function check_user_caps( $user_id, $id );
}

// EOF
