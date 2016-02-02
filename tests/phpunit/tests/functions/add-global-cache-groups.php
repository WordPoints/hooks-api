<?php

/**
 * Test case for wordpoints_hooks_api_add_global_cache_groups().
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests wordpoints_hooks_api_add_global_cache_groups().
 *
 * @since 1.0.0
 *
 * @covers ::wordpoints_hooks_api_add_global_cache_groups
 */
class WordPoints_Add_Global_Cache_Groups_Functions_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test that the global cache groups are registered.
	 *
	 * @since 1.0.0
	 */
	public function test_groups_added() {

		global $wp_object_cache;

		$global_groups = $wp_object_cache->global_groups;

		$this->assertArrayHasKey( 'wordpoints_hook_periods', $global_groups );
		$this->assertArrayHasKey( 'wordpoints_hook_period_ids_by_reaction', $global_groups );

		unset(
			$global_groups['wordpoints_hook_periods']
			, $global_groups['wordpoints_hook_period_ids_by_reaction']
		);

		$wp_object_cache->global_groups = $global_groups;

		wordpoints_hooks_api_add_global_cache_groups();

		$global_groups = $wp_object_cache->global_groups;

		$this->assertArrayHasKey( 'wordpoints_hook_periods', $global_groups );
		$this->assertArrayHasKey( 'wordpoints_hook_period_ids_by_reaction', $global_groups );
	}
}

// EOF
