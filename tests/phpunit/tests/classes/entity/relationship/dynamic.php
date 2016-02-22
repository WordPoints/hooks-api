<?php

/**
 * Test case for WordPoints_Entity_Relationship_Dynamic.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entity_Relationship_Dynamic.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity_Relationship_Dynamic
 */
class WordPoints_Entity_Relationship_Dynamic_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the arg value.
	 *
	 * @since        1.0.0
	 *
	 * @dataProvider data_provider_relationships
	 *
	 * @param string $related_slug      The slug of the related entity.
	 * @param string $relationship_slug The slug of the relationship.
	 * @param array  $primary_slug      The slug of the primary entity.
	 */
	public function test_get_value( $related_slug, $relationship_slug, $primary_slug ) {

		$this->mock_apps();

		if ( '{}' === substr( $related_slug, -2 ) ) {

			$relationship = new WordPoints_PHPUnit_Mock_Entity_Relationship_Dynamic_Array(
				$relationship_slug
			);

			$entity_slug = substr( $related_slug, 0, -2 );

		} else {

			$relationship = new WordPoints_PHPUnit_Mock_Entity_Relationship_Dynamic(
				$relationship_slug
			);

			$entity_slug = $related_slug;
		}

		$entities = wordpoints_entities();
		$entities->register( $entity_slug, 'WordPoints_PHPUnit_Mock_Entity' );

		$entity = $entities->get( $entity_slug );

		$this->assertEquals( $relationship_slug, $relationship->get_slug() );
		$this->assertEquals( $related_slug, $relationship->get_related_entity_slug() );
		$this->assertEquals( $primary_slug, $relationship->get_primary_entity_slug() );
		$this->assertEquals( $entity->get_title(), $relationship->get_title() );
	}

	/**
	 * Provides a list of sets hook arg configurations.
	 *
	 * @since 1.0.0
	 *
	 * @return array[]
	 */
	public function data_provider_relationships() {

		$return = $basic = array(
			'entity' => array( 'test_entity', 'relationship', 'primary_entity' ),
			'dynamic' => array( 'test_entity\a', 'relationship\a', 'primary_entity\a' ),
			'array' => array( 'test_entity{}', 'relationship', 'primary_entity' ),
			'array_dynamic' => array( 'test_entity\a{}', 'relationship\a', 'primary_entity\a' ),
			'double_dynamic' => array( 'test_entity\a\b', 'relationship\a\b', 'primary_entity\a\b' ),
			'array_double_dynamic' => array( 'test_entity\a\b{}', 'relationship\a\b', 'primary_entity\a\b' ),
		);

		return $return;
	}

	/**
	 * Test getting the title when the entity is not found.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title_unknown_entity() {

		$this->mock_apps();

		$relationship = new WordPoints_PHPUnit_Mock_Entity_Relationship_Dynamic(
			'relationship'
		);

		$this->assertEquals( 'test_entity', $relationship->get_title() );
	}
}

// EOF
