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
					hit_id BIGINT(20) UNSIGNED NOT NULL,
					signature CHAR(64) NOT NULL,
					PRIMARY KEY  (id),
					KEY period_signature (hit_id,signature(8))',
				'wordpoints_hook_hits' => '
					id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					firer VARCHAR(255) NOT NULL,
					primary_arg_guid TEXT NOT NULL,
					event VARCHAR(255) NOT NULL,
					reactor VARCHAR(255) NOT NULL,
					reaction_store VARCHAR(255) NOT NULL,
					reaction_context_id TEXT NOT NULL,
					reaction_id BIGINT(20) UNSIGNED NOT NULL,
					date DATETIME NOT NULL,
					superseded_by BIGINT(20) UNSIGNED DEFAULT NULL,
					PRIMARY KEY  (id),
					KEY unsuperseded (event(191),firer(191),primary_arg_guid(191),superseded_by)',
				'wordpoints_hook_hitmeta' => '
					meta_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					hit_id BIGINT(20) UNSIGNED NOT NULL,
					meta_key VARCHAR(255) NOT NULL,
					meta_value LONGTEXT,
					PRIMARY KEY  (meta_id),
					KEY hit_id (hit_id),
					KEY meta_key (meta_key(191))',
			),
		),
	);
}

return 'WordPoints_Hooks_API_Un_Installer';

// EOF
