<?php

/**
 * Mock reversible hook reactor class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock reversible hook reactor for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible
	extends WordPoints_PHPUnit_Mock_Hook_Reactor
	implements WordPoints_Hook_Reactor_ReverseI {

	/**
	 * A list of reverse hits this reactor has received.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $reverse_hits = array();

	/**
	 * @since 1.0.0
	 */
	public function reverse_hit( WordPoints_Hook_Fire $fire ) {
		$this->reverse_hits[] = $fire;
	}
}

// EOF
