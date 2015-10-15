<?php

/**
 * Mock hook arg class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

class WordPoints_PHPUnit_Mock_Hook_Arg extends WordPoints_Hook_Arg {

	public function get_value() {
		return true;
	}

	public function get_title() {
		return $this->get_entity()->get_title();
	}
}

// EOF
