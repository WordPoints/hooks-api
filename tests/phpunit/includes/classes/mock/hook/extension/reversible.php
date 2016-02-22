<?php

/**
 * Mock reversible hook extension class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock reversible hook extension class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Extension_Reversible
	extends WordPoints_PHPUnit_Mock_Hook_Extension
	implements WordPoints_Hook_Extension_ReverseI {

	/**
	 * Whether the hit should be reversed.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $should_reverse = true;

	/**
	 * The args passed to should_reverse() each time it was called.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $reverse_checks = array();

	/**
	 * The args passed to after_reverse() each time it was called.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $after_reverse = array();


	/**
	 * @since 1.0.0
	 */
	public function should_reverse( WordPoints_Hook_Fire $fire ) {

		$this->reverse_checks[] = $fire;

		return $this->should_reverse;
	}

	/**
	 * @since 1.0.0
	 */
	public function after_reverse( WordPoints_Hook_Fire $fire ) {
		$this->after_reverse[] = $fire;
	}
}

// EOF
