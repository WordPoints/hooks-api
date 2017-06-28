<?php

/**
 * Test case for the entity classes.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the entity classes.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity_Post_Type_Relationship
 * @covers WordPoints_Entity_Post_Type
 * @covers WordPoints_Entity_User_Role_Name
 */
class WordPoints_Hooks_All_Entities_Test
	extends WordPoints_PHPUnit_TestCase_Entities {

	/**
	 * Provides a list of entities.
	 *
	 * @since 1.0.0
	 *
	 * @return array The list of entities to test.
	 */
	public function data_provider_entities() {

		global $wpdb;

		$factory = $this->factory = new WP_UnitTest_Factory();
		$factory->wordpoints = WordPoints_PHPUnit_Factory::$factory;

		$entities = array(
			'post'    => array(
				array(
					'class'          => 'WordPoints_Entity_Post',
					'slug'           => 'post',
					'id_field'       => 'ID',
					'human_id_field' => 'post_title',
					'storage_info'   => array(
						'type' => 'db',
						'info' => array(
							'type'       => 'table',
							'table_name' => $wpdb->posts,
						),
					),
					'create_func'    => array( $this, 'create_post' ),
					'delete_func'    => array( $this, 'delete_post' ),
					'cant_view'      => $factory->post->create(
						array( 'post_status' => 'private' )
					),
					'children'       => array(
						'type' => array(
							'class'   => 'WordPoints_Entity_Post_Type_Relationship',
							'primary' => 'post',
							'related' => 'post_type',
							'storage_info' => array(
								'type' => 'db',
								'info' => array(
									'type'  => 'field',
									'field' => 'post_type',
								),
							),
						),
					),
				),
			),
			'post_type' => array(
				array(
					'class'          => 'WordPoints_Entity_Post_Type',
					'slug'           => 'post_type',
					'id_field'       => 'name',
					'human_id_field' => 'label',
					'storage_info'   => array(
						'type' => 'array',
						'info' => array( 'type' => 'method' ),
					),
					'create_func'    => array( $factory->wordpoints->post_type, 'create_and_get' ),
					'delete_func'    => '_unregister_post_type',
				),
			),
			'user_role' => array(
				array(
					'class'          => 'WordPoints_Entity_User_Role',
					'slug'           => 'user_role',
					'id_field'       => 'name',
					'human_id_field' => '_display_name',
					'storage_info'   => array(
						'type' => 'array',
						'info' => array( 'type' => 'method' ),
					),
					'create_func'    => array( $this, 'create_role' ),
					'delete_func'    => 'remove_role',
					'children'       => array(
						'name' => array(
							'class'     => 'WordPoints_Entity_User_Role_Name',
							'data_type' => 'slug',
							'storage_info' => array(
								'type' => 'array',
								'info' => array(
									'type'  => 'field',
									'field' => 'name',
								),
							),
						),
					),
				),
			),
		);

		if ( is_multisite() ) {
			$entities['site'] = array(
				array(
					'class'          => 'WordPoints_Entity_Site',
					'slug'           => 'site',
					'id_field'       => 'blog_id',
					'human_id_field' => 'blogname',
					'context'        => 'network',
					'storage_info'   => array(
						'type' => 'db',
						'info' => array(
							'type'       => 'table',
							'table_name' => $wpdb->blogs,
						),
					),
					'the_context'    => array( 'network' => 1 ),
					'create_func'    => array( $this, 'create_site' ),
					'delete_func'    => array( $this, 'delete_site' ),
				),
			);
		}

		return $entities;
	}

	/**
	 * Creates a site.
	 *
	 * @since 1.0.0
	 *
	 * @return object The site object.
	 */
	public function create_site() {
		// The factory doesn't return all of the details, but the blogname is needed
		// because it is the human_id.
		return get_blog_details( $this->factory->blog->create() );
	}

	/**
	 * Fully deletes a site.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The site ID.
	 */
	public function delete_site( $id ) {
		wpmu_delete_blog( $id, true );
	}
}

// EOF
