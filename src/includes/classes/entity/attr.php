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
	 * The data type of the values of this attribute.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $data_type;

	/**
	 * Get the value of this attribute from an entity.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Entity $entity The entity.
	 *
	 * @return mixed The attribute value.
	 */
	abstract protected function get_attr_value_from_entity( WordPoints_Entity $entity );

	/**
	 * Get the data type of this attribute's values.
	 *
	 * @since 1.0.0
	 *
	 * @return string The data type of this attribute's values.
	 */
	public function get_data_type() {
		return $this->data_type;
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

		$this->the_value = $this->get_attr_value_from_entity( $entity );

		return true;
	}
}

// EOF
