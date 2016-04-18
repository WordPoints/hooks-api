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
 * @covers WordPoints_Entity_User
 * @covers WordPoints_Entity_User_Roles
 * @covers WordPoints_Entity_Post
 * @covers WordPoints_Entity_Post_Author
 * @covers WordPoints_Entity_Post_Content
 * @covers WordPoints_Entity_Post_Type_Relationship
 * @covers WordPoints_Entity_Comment
 * @covers WordPoints_Entity_Comment_Author
 * @covers WordPoints_Entity_Comment_Post
 * @covers WordPoints_Entity_Post_Type
 * @covers WordPoints_Entity_User_Role
 * @covers WordPoints_Entity_User_Role_Name
 */
class WordPoints_All_Entities_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test an entity
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_entities
	 *
	 * @param array $data The data for this test.
	 */
	public function test_entity( $data ) {

		$class = $data['class'];

		/** @var WordPoints_Entity $entity */
		$entity = new $class( $data['slug'] );

		$the_entity = call_user_func( $data['create_func'] );

		$the_id = $the_entity->{$data['id_field']};
		$the_human_id = $the_entity->{$data['human_id_field']};

		$this->assertNotEmpty( $entity->get_title() );

		if ( isset( $data['context'] ) ) {
			$this->assertEquals( $data['context'], $entity->get_context() );
		} else {
			$this->assertEquals( 'site', $entity->get_context() );
		}

		$this->assertEquals(
			$the_human_id
			, $entity->get_human_id( $the_id )
		);

		$this->assertTrue( $entity->exists( $the_id ) );

		$this->assertTrue( $entity->set_the_value( $the_entity ) );
		$this->assertEquals( $the_id, $entity->get_the_value() );
		$this->assertEquals( $the_id, $entity->get_the_id() );
		$this->assertEquals(
			$the_human_id
			, $entity->get_the_attr_value( $data['human_id_field'] )
		);

		if ( isset( $data['context'] ) ) {

			$this->assertSame( $data['the_context'], $entity->get_the_context() );

		} else {

			$the_context = array(
				$data['slug'] => $the_id,
				'site' => 1,
				'network' => 1,
			);

			$this->assertSame( $the_context, $entity->get_the_guid() );

			unset( $the_context[ $data['slug'] ] );

			$this->assertSame( $the_context, $entity->get_the_context() );
		}

		$this->assertTrue( $entity->set_the_value( $the_id ) );
		$this->assertEquals( $the_id, $entity->get_the_value() );
		$this->assertEquals( $the_id, $entity->get_the_id() );
		$this->assertEquals(
			$the_human_id
			, $entity->get_the_attr_value( $data['human_id_field'] )
		);

		if ( $entity instanceof WordPoints_Entity_Restricted_VisibilityI ) {

			$can_view = ( isset( $data['can_view'] ) ) ? $data['can_view'] : $the_id;

			$user_id = $this->factory->user->create();

			$this->assertTrue( $entity->user_can_view( $user_id, $can_view ) );
			$this->assertFalse( $entity->user_can_view( $user_id, $data['cant_view'] ) );
		}

		if ( isset( $data['children'] ) ) {
			foreach ( $data['children'] as $slug => $child_data ) {

				$child = new $child_data['class']( $slug );

				$this->assertNotEmpty( $child->get_title() );

				if ( $child instanceof WordPoints_Entity_Attr ) {

					$this->assertEquals(
						$child_data['data_type']
						, $child->get_data_type()
					);

					$child->set_the_value_from_entity( $entity );
					
					if ( $child instanceof WordPoints_Entity_Attr_FieldI ) {
						
						$this->assertEquals(
							$entity->get_the_attr_value( $child_data['field'] )
							, $child->get_the_value()
						);
						
						$this->assertEquals(
							$child_data['field']
							, $child->get_field()
						);
					}

				} elseif ( $child instanceof WordPoints_Entity_Relationship ) {

					$this->assertEquals(
						$child_data['primary']
						, $child->get_primary_entity_slug()
					);

					$this->assertEquals(
						$child_data['related']
						, $child->get_related_entity_slug()
					);

					$child->set_the_value_from_entity( $entity );

					$this->assertEquals(
						$entity->get_the_attr_value( $child_data['value'] )
						, $child->get_the_value()
					);
				}
			}
		}

		if ( $entity instanceof WordPoints_Entity_EnumerableI ) {
			$this->assertInternalType( 'array', $entity->get_enumerated_values() );
		}

		call_user_func( $data['delete_func'], $the_id );

		if ( isset( $data['children'] ) ) {

			foreach ( $data['children'] as $slug => $child_data ) {

				$child = new $child_data['class']( $slug );

				// We're just checking that there are no errors here. Whether the
				// value will be set depends on the child and whether its value is
				// stored as field on the parent object or not.
				$child->set_the_value_from_entity( $entity );
			}

			$entity->set_the_value( null );

			foreach ( $data['children'] as $slug => $child_data ) {

				$child = new $child_data['class']( $slug );

				$child->set_the_value_from_entity( $entity );

				$this->assertNull( $child->get_the_value() );
			}
		}

		$this->assertFalse( $entity->exists( $the_id ) );
		$this->assertFalse( $entity->set_the_value( $the_id ) );
		$this->assertFalse( $entity->get_human_id( $the_id ) );
	}

	/**
	 * Provides a list of entities.
	 *
	 * @since 1.0.0
	 *
	 * @return array The list of entities to test.
	 */
	public function data_provider_entities() {

		$factory = $this->factory = new WP_UnitTest_Factory();
		$factory->wordpoints = WordPoints_PHPUnit_Factory::$factory;

		$entities = array(
			'user'    => array(
				array(
					'class'          => 'WordPoints_Entity_User',
					'slug'           => 'user',
					'id_field'       => 'ID',
					'human_id_field' => 'display_name',
					'context'        => '',
					'the_context'    => array(),
					'create_func'    => array( $factory->user, 'create_and_get' ),
					'delete_func'    => array( $this, 'delete_user' ),
					'children'       => array(
						'roles' => array(
							'class'   => 'WordPoints_Entity_User_Roles',
							'primary' => 'user',
							'related' => 'user_role{}',
							'value'   => 'roles',
						),
					),
				),
			),
			'post'    => array(
				array(
					'class'          => 'WordPoints_Entity_Post',
					'slug'           => 'post',
					'id_field'       => 'ID',
					'human_id_field' => 'post_title',
					'create_func'    => array( $this, 'create_post' ),
					'delete_func'    => array( $this, 'delete_post' ),
					'cant_view'      => $factory->post->create(
						array( 'post_status' => 'private' )
					),
					'children'       => array(
						'author' => array(
							'class'   => 'WordPoints_Entity_Post_Author',
							'primary' => 'post',
							'related' => 'user',
							'value'   => 'post_author',
						),
						'content' => array(
							'class'     => 'WordPoints_Entity_Post_Content',
							'data_type' => 'text',
							'field'     => 'post_content',
						),
						'type' => array(
							'class'   => 'WordPoints_Entity_Post_Type_Relationship',
							'primary' => 'post',
							'related' => 'post_type',
							'value'   => 'post_type',
						),
					),
				),
			),
			'comment' => array(
				array(
					'class'          => 'WordPoints_Entity_Comment',
					'slug'           => 'post',
					'id_field'       => 'comment_ID',
					'human_id_field' => 'comment_content',
					'create_func'    => array( $this, 'create_comment' ),
					'delete_func'    => array( $this, 'delete_comment' ),
					'cant_view'      => $factory->comment->create(
						array(
							'comment_post_ID' => $factory->post->create(
								array(
									'post_status' => 'private',
									'post_author' => $factory->user->create(
										// We have to do this because prior to WP 4.4
										// the generators used by the test factories
										// don't use a static incrementer.
										array(
											'user_login' => 'WP 4.3- 1',
											'user_email' => 'WP+4.3-+1@l.com',
										)
									),
								)
							),
						)
					),
					'children'       => array(
						'author' => array(
							'class'   => 'WordPoints_Entity_Comment_Author',
							'primary' => 'comment',
							'related' => 'user',
							'value'   => 'user_id',
						),
						'post' => array(
							'class'   => 'WordPoints_Entity_Comment_Post',
							'primary' => 'comment',
							'related' => 'post',
							'value'   => 'comment_post_ID',
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
					'create_func'    => array( $this, 'create_role' ),
					'delete_func'    => 'remove_role',
					'children'       => array(
						'name' => array(
							'class'     => 'WordPoints_Entity_User_Role_Name',
							'data_type' => 'slug',
							'field'     => 'name',
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
					'the_context'    => array( 'network' => 1 ),
					'create_func'    => array( $this, 'create_site' ),
					'delete_func'    => array( $this, 'delete_site' ),
				),
			);
		}

		return $entities;
	}

	/**
	 * Fully deletes a user.
	 *
	 * This is back-compat for WP pre-4.3, when this method was added to the main
	 * test case class.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The user ID.
	 *
	 * @return bool Whether the user was deleted successfully.
	 */
	public static function delete_user( $id ) {
		if ( is_multisite() ) {
			return wpmu_delete_user( $id );
		} else {
			return wp_delete_user( $id );
		}
	}

	/**
	 * Creates a post.
	 *
	 * @since 1.0.0
	 *
	 * @return object The post object.
	 */
	public function create_post() {

		return $this->factory->post->create_and_get(
			array( 'post_author' => $this->factory->user->create() )
		);
	}

	/**
	 * Fully deletes a post.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The post ID.
	 */
	public function delete_post( $id ) {
		wp_delete_post( $id, true );
	}

	/**
	 * Creates a comment.
	 *
	 * @since 1.0.0
	 *
	 * @return object The comment object.
	 */
	public function create_comment() {

		return $this->factory->comment->create_and_get(
			array(
				'user_id'         => $this->factory->user->create(),
				'comment_post_ID' => $this->factory->post->create(),
			)
		);
	}

	/**
	 * Fully deletes a comment.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The comment ID.
	 */
	public function delete_comment( $id ) {
		wp_delete_comment( $id, true );
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

	/**
	 * Creates a role.
	 *
	 * @since 1.0.0
	 *
	 * @return object The role object.
	 */
	public function create_role() {

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$role = $this->factory->wordpoints->user_role->create_and_get();

		$names = $wp_roles->get_names();

		// See https://core.trac.wordpress.org/ticket/34608
		$role->_display_name = $names[ $role->name ];

		return $role;
	}
}

// EOF
