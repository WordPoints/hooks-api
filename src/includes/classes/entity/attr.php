<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

abstract class WordPoints_Entity_Attr
	extends WordPoints_Entityish
	implements WordPoints_Entity_ChildI {

	protected $field;
	protected $type;

	protected function get_field() {
		return $this->field;
	}

	public function get_data_type() {
		return $this->type;
	}

	public function set_the_value_from_entity( WordPoints_Entity $entity ) {
		$this->the_value = $entity->get_the_attr_value( $this->get_field() );
	}
}

// EOF
