<?php

/**
 * Entity attribute class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents an Entity attribute.
 *
 * Using a Post as an example type of entity, an example of an attribute would be the
 * title.
 *
 * Each attribute is represented by a child of this class.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Entity_Attr
	extends WordPoints_Entityish
	implements WordPoints_Entity_ChildI {

	/**
	 * The field that this attribute is stored in on the entity.
	 *
	 * You must either define this property in your child class or else override the
	 * get_field() method.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $field;

	/**
	 * The data type of the values of this attribute.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Get the field that this attribute's value is stored in.
	 *
	 * @since 1.0.0
	 *
	 * @return string The field this attribute's value is stored in.
	 */
	protected function get_field() {
		return $this->field;
	}

	/**
	 * Get the data type of this attribute's values.
	 *
	 * @since 1.0.0
	 *
	 * @return string The data type of this attribute's values.
	 */
	public function get_data_type() {
		return $this->type;
	}

	/**
	 * Set the value of this attribute from an entity.
	 *
	 * This class can represent either a type of attribute generically (e.g., Post
	 * title) or a specific value of that attribute (the title of a specific Post).
	 * This method is used to set a specific value for the attribute this object
	 * should represent.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Entity $entity An entity object.
	 *
	 * @return bool Whether the value was set correctly.
	 */
	public function set_the_value_from_entity( WordPoints_Entity $entity ) {

		$this->the_value = $entity->get_the_attr_value( $this->get_field() );

		return true;
	}
}

// EOF
