<?php

/**
 * The WordPoints apps class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * The main WordPoints app.
 *
 * @since 1.0.0
 *
 * @property-read WordPoints_Hooks    $hooks    The hooks app.
 * @property-read WordPoints_Entities $entities The entities app.
 */
class WordPoints_Apps extends WordPoints_App {

	/**
	 * The main app.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Apps
	 */
	public static $main_app;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		/**
		 * WordPoints apps initialized.
		 *
		 * @since 1.0.0
		 *
		 * @param WordPoints_Apps $apps The apps object.
		 */
		do_action( 'wordpoints_apps_init', $this->sub_apps );
	}
}

// EOF
