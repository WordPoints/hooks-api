<?php

/**
 * Test case for the admin menu functions.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the admin menu functions.
 *
 * @since 1.0.0
 */
class WordPoints_Admin_Menu_Functions_Test extends WordPoints_PHPUnit_TestCase_Admin {

	/**
	 * @since 1.0.0
	 */
	protected $backup_globals = array(
		'submenu',
		'menu',
		'_wp_real_parent_file',
		'_wp_submenu_nopriv',
		'_registered_pages',
		'_parent_pages'
	);

	/**
	 * Test the wordpoints_hooks_api_admin_menu() function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_hooks_api_admin_menu
	 */
	public function test_wordpoints_hooks_api_admin_menu() {

		$this->mock_apps();

		$this->give_current_user_caps( 'manage_options' );

		wordpoints_hooks_api_admin_menu();

		$this->assertAdminSubmenuRegistered( 'wordpoints_points_types' );

		/** @var WordPoints_Admin_Screens $app */
		$app = wordpoints_apps()->get_sub_app( 'admin' )->get_sub_app( 'screen' );

		$this->assertTrue(
			$app->is_registered(
				get_plugin_page_hookname(
					'wordpoints_points_types'
					, wordpoints_get_main_admin_menu()
				)
			)
		);
	}

	/**
	 * Assert that a submenu item has been registered for an admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug   The slug of the submenu item.
	 * @param string $parent The slug of the parent menu item.
	 */
	protected function assertAdminSubmenuRegistered( $slug, $parent = null ) {

		global $submenu;

		if ( null === $parent ) {
			$parent = wordpoints_get_main_admin_menu();
		}

		$this->assertArrayHasKey( $parent, $submenu );
		$this->assertContains( $slug, wp_list_pluck( $submenu[ $parent ], 2 ) );
	}
}

// EOF
