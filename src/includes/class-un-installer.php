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
					reaction_id BIGINT(20) NOT NULL,
					signature CHAR(64) NOT NULL,
					hit_time DATETIME NOT NULL,
					PRIMARY KEY  (id),
					KEY period_signature (reaction_id,signature(8))',
			),
		),
	);
}

return 'WordPoints_Hooks_API_Un_Installer';

// EOF
