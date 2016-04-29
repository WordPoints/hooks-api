<?php

/**
 * Field entity attribute class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents an entity attribute which is stored as an entity field.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Entity_Attr_Field
	extends WordPoints_Entity_Attr
	implements WordPoints_Entityish_StoredI {

	/**
	 * The storage type for this entity attribute.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $storage_type;

	/**
	 * The field that this attribute is stored in on the entity.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $field;

	/**
	 * @since 1.0.0
	 */
	protected function get_attr_value_from_entity( WordPoints_Entity $entity ) {
		return $entity->get_the_attr_value( $this->field );
	}
	
	/**
	 * @since 1.0.0
	 */
	public function get_storage_info() {
		return array(
			'type' => $this->storage_type,
			'info' => array(
				'type'  => 'field',
				'field' => $this->field,
			),
		);
	}
}

// EOF
