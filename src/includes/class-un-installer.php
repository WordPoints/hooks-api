<?php

/**
 * Class for un/installing the module.
 *
 * @package wordpoints-hook-api
 * @since 1.0.0
 */

/**
 * Un/installs the module.
 *
 * @since 1.0.0
 */
class WordPoints_Hooks_API_Un_Installer extends WordPoints_Un_Installer_Base {

	//
	// Protected Vars.
	//

	/**
	 * @since 1.0.0
	 */
	protected $type = 'module';

	/**
	 * @since 1.0.0
	 */
	protected $schema = array(
		'global' => array(
			'tables' => array(
				'wordpoints_hook_periods' => '
					id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					hit_id BIGINT(20) UNSIGNED NOT NULL,
					signature CHAR(64) NOT NULL,
					PRIMARY KEY  (id),
					KEY period_signature (hit_id,signature(8))',
				'wordpoints_hook_hits' => '
					id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					action_type VARCHAR(255) NOT NULL,
					primary_arg_guid TEXT NOT NULL,
					event VARCHAR(255) NOT NULL,
					reactor VARCHAR(255) NOT NULL,
					reaction_store VARCHAR(255) NOT NULL,
					reaction_context_id TEXT NOT NULL,
					reaction_id BIGINT(20) UNSIGNED NOT NULL,
					date DATETIME NOT NULL,
					PRIMARY KEY  (id)',
				'wordpoints_hook_hitmeta' => '
					meta_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					wordpoints_hook_hit_id BIGINT(20) UNSIGNED NOT NULL,
					meta_key VARCHAR(255) NOT NULL,
					meta_value LONGTEXT,
					PRIMARY KEY  (meta_id),
					KEY hit_id (wordpoints_hook_hit_id),
					KEY meta_key (meta_key(191))',
			),
		),
	);

	/**
	 * @since 1.0.0
	 */
	public function install( $network ) {

		// The autoloader won't automatically be initialized because it is usually
		// hooked to the modules loaded action, which will have already fired before
		// the module is loaded when it is being installed.
		WordPoints_Class_Autoloader::init();

		// Default to network mode off during the install, but save the current
		// mode so we can restore it afterward.
		$points_hooks_network_mode = WordPoints_Points_Hooks::get_network_mode();
		WordPoints_Points_Hooks::set_network_mode( false );

		$hooks = wordpoints_hooks();
		$hooks_mode = $hooks->get_current_mode();
		$hooks->set_current_mode( 'standard' );

		parent::install( $network );

		$hooks->set_current_mode( $hooks_mode );
		WordPoints_Points_Hooks::set_network_mode( $points_hooks_network_mode );
	}

	/**
	 * @since 1.0.0
	 */
	protected function install_network() {

		parent::install_network();

		WordPoints_Points_Hooks::set_network_mode( true );
		wordpoints_hooks()->set_current_mode( 'network' );

		$this->import_legacy_points_hooks();

		WordPoints_Points_Hooks::set_network_mode( false );
		wordpoints_hooks()->set_current_mode( 'standard' );
	}

	/**
	 * @since 1.0.0
	 */
	protected function install_site() {

		parent::install_site();

		$this->import_legacy_points_hooks();
	}

	/**
	 * @since 1.0.0
	 */
	protected function install_single() {

		parent::install_single();

		$this->import_legacy_points_hooks();
	}

	/**
	 * Import legacy points hooks to the new hooks API.
	 *
	 * @since 1.0.0
	 */
	protected function import_legacy_points_hooks() {

		$this->import_legacy_points_hook(
			'registration',
			'user_register',
			array( 'points' => true ),
			'register',
			array( 'user' )
		);

		$this->import_legacy_points_hook(
			'post',
			'post_publish\post',
			array(
				'points'       => true,
				'post_type'    => true,
				'auto_reverse' => true,
			),
			'post_publish',
			array( 'post\post', 'author', 'user' )
		);

		$this->import_legacy_points_hook(
			'comment',
			'comment_leave\post',
			array(
				'points'       => true,
				'post_type'    => true,
				'auto_reverse' => true,
			),
			'comment_approve',
			array( 'comment\post', 'author', 'user' )
		);

		$this->import_legacy_points_hook(
			'comment_received',
			'comment_leave\post',
			array(
				'points'       => true,
				'post_type'    => true,
				'auto_reverse' => true,
			),
			'comment_received',
			array( 'comment\post', 'post\post', 'post\post', 'author', 'user' )
		);

		$this->import_legacy_points_hook(
			'periodic',
			'user_visit',
			array( 'points' => true, 'period' => true, ),
			'periodic',
			array( 'current:user' )
		);
	}

	/**
	 * Import a legacy points hook.
	 *
	 * @since 1.0.0
	 *
	 * @param string $legacy_slug       The legacy hook slug.
	 * @param string $event_slug        The slug of the event to use when converting
	 *                                  the hook to a reaction.
	 * @param array  $expected_settings The expected settings for this hook.
	 * @param string $legacy_log_type   The legacy log type.
	 * @param array  $target            The target to use when converting the hook to
	 *                                  a reaction.
	 */
	protected function import_legacy_points_hook(
		$legacy_slug,
		$event_slug,
		$expected_settings,
		$legacy_log_type,
		$target
	) {

		$importer = new WordPoints_Legacy_Points_Hook_To_Reaction_Importer(
			"wordpoints_{$legacy_slug}_points_hook"
			, $event_slug
			, $expected_settings
			, $legacy_log_type
			, $target
		);

		$importer->import();
	}
}

return 'WordPoints_Hooks_API_Un_Installer';

// EOF
