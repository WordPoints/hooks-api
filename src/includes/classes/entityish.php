<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */


interface WordPoints_EntityishI {
	public function get_slug();
	public function get_title();

	public function user_can_view( $user_id, $id );

	public function get_the_value();
	public function set_the_value( $value );
}

abstract class WordPoints_Entityish implements WordPoints_EntityishI {

	protected $slug;

	protected $the_value;

	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	public function get_slug() {
		return $this->slug;
	}

	public function user_can_view( $user_id, $id ) {

		$can_view = true;

		if ( $this instanceof WordPoints_Entity_Check_CapsI ) {
			$can_view = $this->check_user_caps( $user_id, $id );
		}

		return $can_view; // TODO filter here
	}

	public function get_the_value() {
		return $this->the_value;
	}

	public function set_the_value( $value ) {
		$this->the_value = $value;
	}
}

interface WordPoints_Entity_Check_CapsI { // TODO change the name
	public function check_user_caps( $user_id, $id );
}

class WordPoints_Entity_Array {

	protected $entity_slug;
	protected $the_entities = array();
	protected $the_value;

	public function __construct( $entity_slug ) {
		$this->entity_slug = $entity_slug;
	}

	public function get_entity_slug() {
		return $this->entity_slug;
	}

	public function set_the_value( $values ) {

		$object = wordpoints_apps()->entities->get( $this->entity_slug );

		$this->the_entities = array();

		$this->the_value = $values;

		foreach ( $values as $value ) {

			/** @var WordPoints_Entity $entity */
			$entity = clone $object;
			$entity->set_the_value( $value );
			$this->the_entities[] = $entity;
		}
	}

	/**
	 *
	 *
	 * @since 1.
	 * @return WordPoints_Entity[]
	 */
	public function get_the_entities() {
		return $this->the_entities;
	}
}

// EOF
