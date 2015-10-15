<?php

/**
 * Administration screens class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Handles the display of administration screens.
 *
 * @since 1.0.0
 */
class WordPoints_Admin_Screens extends WordPoints_Class_Registry {

	/**
	 * The object for the current administration screen.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Admin_Screen
	 */
	protected $current_screen;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'current_screen', array( $this, 'set_current_screen' ) );
	}

	/**
	 * Set the current screen.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Screen $current_screen The WP_Screen object for the current screen.
	 */
	public function set_current_screen( $current_screen ) {

		if ( ! $this->is_registered( $current_screen->id ) ) {
			return;
		}

		$this->current_screen = $this->get( $current_screen->id );

		add_action(
			"load-{$current_screen->id}"
			, array( $this->current_screen, 'load' )
		);
	}

	/**
	 * Display the current screen.
	 *
	 * @since 1.0.0
	 */
	public function display() {

		$this->current_screen->display();
	}
}

// EOF
