<?php

/**
 * Mock miss listener hook extension class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock miss listener hook extension class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Extension_Miss_Listener
	extends WordPoints_PHPUnit_Mock_Hook_Extension
	implements WordPoints_Hook_Extension_Miss_ListenerI {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'test_miss_listener_extension';
	
	/**
	 * The args passed to after_miss() each time it was called.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $misses = array();

	/**
	 * @since 1.0.0
	 */ 
	public function after_miss( WordPoints_Hook_Fire $fire ) {
		$this->misses[] = $fire;
	}
}

// EOF
