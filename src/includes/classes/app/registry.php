<?php

/**
 * Class for apps that are also class registries.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * An app that is also a class registry.
 *
 * @since 1.0.0
 */
class WordPoints_App_Registry
	extends WordPoints_App
	implements WordPoints_Class_RegistryI {

	/**
	 * The class registry object.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Class_Registry
	 */
	protected $registry;

	/**
	 * @since 1.0.0
	 */
	public function __construct( $slug ) {

		$this->registry = new WordPoints_Class_Registry();

		parent::__construct( $slug );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_all( array $args = array() ) {
		return $this->registry->get_all( $args );
	}

	/**
	 * @since 1.0.0
	 */
	public function get( $slug, array $args = array() ) {
		return $this->registry->get( $slug, $args );
	}

	/**
	 * @since 1.0.0
	 */
	public function register( $slug, $class, array $args = array() ) {
		return $this->registry->register( $slug, $class, $args );
	}

	/**
	 * @since 1.0.0
	 */
	public function deregister( $slug ) {
		$this->registry->deregister( $slug );
	}

	/**
	 * @since 1.0.0
	 */
	public function is_registered( $slug ) {
		return $this->registry->is_registered( $slug );
	}
}

// EOF
