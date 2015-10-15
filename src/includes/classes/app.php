<?php

/**
 * Class for WordPoints apps.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * An app for WordPoints.
 *
 * Apps are self-contained APIs that can include sub-apps.
 *
 * The sub-apps are not required to be instances of WordPoints_App themselves, they
 * can be any sort of object.
 *
 * @since 1.0.0
 *
 * @property-read WordPoints_Class_Registry_Persistent $sub_apps
 */
class WordPoints_App {

	/**
	 * A registry for child apps.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Class_Registry_Persistent
	 */
	protected $sub_apps;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->sub_apps = new WordPoints_Class_Registry_Persistent();
	}

	/**
	 * @since 1.0.0
	 */
	public function __get( $var ) {

		if ( 'sub_apps' === $var ) {
			return $this->sub_apps;
		}

		if ( $this->sub_apps->is_registered( $var ) ) {
			return $this->sub_apps->get( $var );
		}

		return null;
	}
}

// EOF
