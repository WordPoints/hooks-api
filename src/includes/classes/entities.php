<?php

/**
 * Class of the entities app.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Entities app.
 *
 * @since 1.0.0
 *
 * @property-read WordPoints_Class_Registry_Children $children The entity children registry.
 */
class WordPoints_Entities extends WordPoints_Class_Registry {

	/**
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->app = new WordPoints_App();

		$this->init();
	}

	/**
	 * @since 1.0.0
	 */
	public function __get( $var ) {

		return $this->app->__get( $var );
	}

	/**
	 * Register the sub apps when the app is initialized.
	 *
	 * @since 1.0.0
	 */
	protected function init() {

		$this->sub_apps->register( 'children', 'WordPoints_Class_Registry_Children' );

		/**
		 * Entities initialization.
		 *
		 * Hook to this to register any custom sub-apps.
		 *
		 * @since 1.0.0
		 *
		 * @param WordPoints_Entities $entities The entities app.
		 */
		do_action( 'wordpoints_entities_init', $this );
	}
}

// EOF
