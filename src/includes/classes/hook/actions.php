<?php

/**
 * Class for the hook actions.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Hook actions registry.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Actions extends WordPoints_Class_Registry {

	/**
	 * A hook router.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Router
	 */
	protected $router;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->router = wordpoints_apps()->hooks->router;

		$this->init();
	}

	/**
	 * Initialize the registry.
	 *
	 * @since 1.0.0
	 */
	protected function init() {

		/**
		 * Hook actions initialization.
		 *
		 * Hook to this to register any custom actions.
		 *
		 * @since 1.0.0
		 *
		 * @param WordPoints_Hook_Actions $actions The actions registry.
		 */
		do_action( 'wordpoints_hook_actions_init', $this );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $slug The slug of the action.
	 * @param array  $args The args to pass to the action.
	 *
	 * @return object|false The action object, or false if not found.
	 */
	public function get( $slug = null, array $action_args = array(), array $args = array() ) {

		if ( ! isset( $this->classes[ $slug ] ) ) {
			return false;
		}

		return new $this->classes[ $slug ]( $slug, $action_args, $args );
	}

	/**
	 * @since 1.0.0
	 */
	public function register( $slug, $class, array $args = array() ) {

		$result = parent::register( $slug, $class, $args );

		if ( ! $result ) {
			return false;
		}

		$this->router->add_action( $slug, $args );

		return true;
	}

	/**
	 * @since 1.0.0
	 */
	public function deregister( $slug ) {

		parent::deregister( $slug );

		$this->router->remove_action( $slug );
	}
}

// EOF
