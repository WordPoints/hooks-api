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
	protected $type = 'component';

	/**
	 * @since 1.0.0
	 */
	protected $schema = array(
		'global' => array(
			'tables' => array(
				'wordpoints_hook_periods' => '
					id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					reaction_id BIGINT(20) DEFAULT NULL,
					signature CHAR(64) DEFAULT NULL,
					hit_time BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
					meta LONGTEXT,
					PRIMARY KEY  (id),
					KEY reaction_id (reaction_id)',
			),
		),
	);
}

return 'WordPoints_Hooks_API_Un_Installer';

// EOF
