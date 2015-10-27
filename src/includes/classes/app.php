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
 * @property-read WordPoints_Class_Registry_Persistent $sub_apps Child apps registry.
 * @property-read object                               $*        Child app objects.
 */
class WordPoints_App {

	/**
	 * The main app.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_App
	 */
	public static $main;

	/**
	 * The slug of this app.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The full slug of this app, prefixed with the slug of the parent app.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $full_slug;

	/**
	 * A registry for child apps.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Class_Registry_Persistent
	 */
	protected $sub_apps;

	/**
	 * The parent of this app, if this is a sub-app.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_App
	 */
	protected $parent;

	/**
	 * Whether to skip calling an action when each registry is initialized.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $silent = false;

	/**
	 * Keeps track of which registries have been initialized.
	 *
	 * @since 1.0.0
	 *
	 * @var bool[]
	 */
	protected $did_init = array();

	/**
	 * @since 1.0.0
	 */
	public function __construct( $slug, $parent = null ) {

		$this->slug = $slug;
		$this->full_slug = $slug;

		if ( $parent instanceof WordPoints_App ) {
			$this->parent = $parent;

			if ( 'apps' !== $this->parent->full_slug ) {
				$this->full_slug = $this->parent->full_slug . '-' . $this->full_slug;
			}
		}

		$this->sub_apps = new WordPoints_Class_Registry_Persistent();

		$this->init();
	}

	/**
	 * @since 1.0.0
	 */
	public function __isset( $var ) {
		return $this->sub_apps->is_registered( $var );
	}

	/**
	 * @since 1.0.0
	 */
	public function __get( $var ) {

		if ( 'sub_apps' === $var ) {
			return $this->$var;
		}

		$sub = $this->sub_apps->get( $var, array( $this ) );

		if ( ! $sub ) {
			return null;
		}

		if (
			empty( $this->did_init[ $var ] )
			&& ! self::$main->silent
			&& $this->should_do_registry_init( $sub )
		) {

			/**
			 * Initialization of an app registry.
			 *
			 * The $var is the slug of the registry.
			 *
			 * @since 1.0.0
			 *
			 * @param WordPoints_Class_RegistryI|WordPoints_Class_Registry_ChildrenI
			 *        $registry The registry object.
			 */
			do_action( "wordpoints_init_app_registry-{$this->full_slug}-{$var}", $sub );

			$this->did_init[ $var ] = true;
		}

		return $sub;
	}

	/**
	 * @since 1.0.0
	 */
	public function __set( $var, $value ) {

		_doing_it_wrong(
			__METHOD__
			, 'Sub apps must be registered using $app->sub_apps->register().'
			, '1.0.0'
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function __unset( $var ) {

		_doing_it_wrong(
			__METHOD__
			, 'Sub apps must be deregistered using $app->sub_apps->deregister().'
			, '1.0.0'
		);
	}

	/**
	 * Check whether to call the init action for a registry sub-app.
	 *
	 * @since 1.0.0
	 *
	 * @param object $registry The sub-app object.
	 *
	 * @return bool Whether to call the init action or not.
	 */
	protected function should_do_registry_init( $registry ) {
		return (
		   $registry instanceof WordPoints_Class_RegistryI
		   || $registry instanceof WordPoints_Class_Registry_ChildrenI
		);
	}

	/**
	 * Initialize this app.
	 *
	 * @since 1.0.0
	 */
	protected function init() {

		/**
		 * WordPoints app initialized.
		 *
		 * The dynamic portion of the action is the slug of the app being
		 * initialized.
		 *
		 * @since 1.0.0
		 *
		 * @param WordPoints_App $app The app object.
		 */
		do_action( "wordpoints_init_app-{$this->full_slug}", $this );
	}
}

// EOF
