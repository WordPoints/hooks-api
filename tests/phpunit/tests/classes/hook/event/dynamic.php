<?php

/**
 * Test case for WordPoints_Hook_Event_Dynamic.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Event_Dynamic.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Event_Dynamic
 */
class WordPoints_Hook_Event_Dynamic_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the entity title.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entity_title() {

		$entity = $this->factory->wordpoints->entity->create_and_get(
			array( 'slug' => 'generic' )
		);

		$event = new WordPoints_PHPUnit_Mock_Hook_Event_Dynamic( 'test_event' );

		$this->assertEquals( $entity->get_title(), $event->get_entity_title() );
	}

	/**
	 * Test getting the title when the entity is dynamic.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entity_title_dynamic() {

		$entity = $this->factory->wordpoints->entity->create_and_get(
			array( 'slug' => 'generic\dynamic' )
		);

		$event = new WordPoints_PHPUnit_Mock_Hook_Event_Dynamic( 'event\dynamic' );

		$this->assertEquals( $entity->get_title(), $event->get_entity_title() );
	}

	/**
	 * Test getting the title when the entity is unknown.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entity_title_nonexistent() {

		$event = new WordPoints_PHPUnit_Mock_Hook_Event_Dynamic( 'event\dynamic' );

		$this->assertEquals( $event->get_slug(), $event->get_entity_title() );
	}
}

// EOF
