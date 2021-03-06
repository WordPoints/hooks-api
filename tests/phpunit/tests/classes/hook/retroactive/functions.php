<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

/**
 * Class Test
 *
 * @since 1.
 *
 * @coversNothing
 */
class WordPoints_Hook_Retroactive_Functions_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	public function test_post_publish_twice_blocked_reversals() {

		$this->create_points_type();

		$hooks = wordpoints_hooks();

		$points_target = $hooks->get_reaction_store( 'points' );
		$instance      = $points_target->create_reaction(
			array(
				'event'       => 'post_publish\post',
				'reactor'     => 'points',
				'points'      => 10,
				'points_type' => 'points',
				'target'      => array( 'post\post', 'author', 'user' ),
				'description' => 'lkjlkj',
				'log_text'    => 'lkjlkj',
				'blocker'     => array( 'toggle_off' => true ),
				'repeat_blocker' => array( 'toggle_on' => true ),
			)
		);

		$this->assertInstanceOf( 'WordPoints_Hook_ReactionI', $instance );

		$user_id = $this->factory->user->create();

		$this->assertEquals( 0, wordpoints_get_points( $user_id, 'points' ) );

		$post_id = $this->factory->post->create(
			array(
				'post_type' => 'post',
				'post_author' => $user_id,
			)
		);

		$this->assertEquals( 10, wordpoints_get_points( $user_id, 'points' ) );

		wp_update_post( array( 'ID' => $post_id, 'post_status' => 'draft' ) );

		$this->assertEquals( 10, wordpoints_get_points( $user_id, 'points' ) );

		wp_update_post( array( 'ID' => $post_id, 'post_status' => 'publish' ) );

		$this->assertEquals( 10, wordpoints_get_points( $user_id, 'points' ) );

		$this->factory->post->create(
			array(
				'post_type' => 'post',
				'post_author' => $user_id,
			)
		);

		$this->assertEquals( 20, wordpoints_get_points( $user_id, 'points' ) );

		wp_delete_post( $post_id, true );

		$this->assertEquals( 20, wordpoints_get_points( $user_id, 'points' ) );
	}
	
	public function test_comment_leave_twice() {

		$this->create_points_type();

		$hooks = wordpoints_hooks();

		$points_target = $hooks->get_reaction_store( 'points' );
		$instance      = $points_target->create_reaction(
			array(
				'event'       => 'comment_leave\post',
				'reactor'     => 'points',
				'points'      => 10,
				'points_type' => 'points',
				'target'      => array( 'comment\post', 'post\post', 'post\post', 'author', 'user' ),
				'description' => 'lkjlkj',
				'log_text'    => 'lkjlkj',
			)
		);

		$this->assertIsReaction( $instance );

		$user_id = $this->factory->user->create();
		$post_id = $this->factory->post->create(
			array(
				'post_type' => 'post',
				'post_author' => $user_id,
			)
		);

		$comment_id = $this->factory->comment->create(
			array( 'comment_post_ID' => $post_id )
		);

		$this->assertEquals( 10, wordpoints_get_points( $user_id, 'points' ) );

		wp_update_comment( array( 'comment_ID' => $comment_id, 'comment_status' => 0 ) );

		$this->assertEquals( 10, wordpoints_get_points( $user_id, 'points' ) );

		wp_update_comment( array( 'comment_ID' => $comment_id, 'comment_status' => 'approved' ) );

		$this->assertEquals( 10, wordpoints_get_points( $user_id, 'points' ) );

		$this->factory->comment->create(
			array( 'comment_post_ID' => $post_id )
		);

		$this->assertEquals( 20, wordpoints_get_points( $user_id, 'points' ) );
	}

	public function test_publish_revision() {

		$this->listen_for_filter( 'post_publish' );
		$this->factory->post->create( array( 'post_type' => 'post' ) );
		$this->assertEquals( 0, $this->filter_was_called( 'post_publish' ) );
	}

	public function assert_points_awarded( $constraints, $user_id, $points ) {

		$hooks = wordpoints_hooks();

		/** @var WordPoints_Hook_Reactor_Points $points_target */
		$points_target = $hooks->get_sub_app( 'reactors' )->get( 'points' );
		$instance      = $points_target->reactions->create_reaction(
			array(
				'event'       => 'post_publish',
				'points'      => 10,
				'points_type' => 'points',
				'target'      => array( 'post', 'author', 'user' ),
				'constraints' => $constraints,
			)
		);

		$this->assertNotEquals( false, $instance );

		if ( $instance instanceof WordPoints_Hook_Reaction_Validator ) {
//			var_dump( $instance );
			$this->fail();
		}

		$query = new WordPoints_Hook_Retroactive_Query( $instance );

		$results = $query->get_results();

		if ( $results instanceof WordPoints_Hook_Reaction_Validator ) {
//			var_dump( $results );
			$this->fail();
		}

		$hook = $hooks->get_sub_app( 'events' )->get( 'post_publish' );

		foreach ( $results as $target => $result ) {
			$points_target->retroactive_hit( $target, $result, $hook, $instance );
		}

		$this->assertEquals( $points, wordpoints_get_points( $user_id, 'points' ) );
	}

	public function _test_post_content() {

		$user_id = $this->factory->user->create();

		$post_ids = $this->factory->post->create_many(
			3
			, array( 'post_author' => $user_id )
		);

		wp_update_post(
			array(
				'ID' => $post_ids[0],
				'post_content' => 'One day, I was in the park with Robert.',
			)
		);

		$this->assertStringMatchesFormat(
			'%aRobert%a'
			, get_post( $post_ids[0] )->post_content
		);

		$this->assert_points_awarded(
			array(
				'post' => array(
					'post_content' => array(
						'_constraints' => array(
							array(
								'type' => 'string_contains',
								'settings' => array( 'value' => 'Robert' ),
							),
						),
					),
				),
			)
			, $user_id
			, 10
		);
	}

	public function _test_post_type() {

		$user_id = $this->factory->user->create();

		$this->factory->post->create_many(
			2
			, array( 'post_author' => $user_id )
		);

		$this->factory->post->create(
			array( 'post_type' => 'page', 'post_author' => $user_id )
		);

		$this->assert_points_awarded(
			array(
				'post' => array(
					'post_type_rel' => array(
						'post_type' => array(
							'post_type_name' => array(
								'_constraints' => array(
									array(
										'type'     => 'equals',
										'settings' => array( 'value' => 'post' ),
									),
								),
							),
						),
					),
				),
			)
			, $user_id
			, 20
		);
	}

	public function _test_post_author() {

		$user_id = $this->factory->user->create();

		$post_ids = $this->factory->post->create_many(
			3
			, array( 'post_author' => $user_id )
		);

		$admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );

		$this->assertEquals( array( 'administrator' ), get_userdata( $admin_id )->roles );
		$this->assertEquals( array( 'subscriber' ), get_userdata( $user_id )->roles );
		wp_update_post( array( 'ID' => $post_ids[0], 'post_author' => $admin_id ) );

		$this->assertEquals( $admin_id, get_post( $post_ids[0] )->post_author );

		$this->assert_points_awarded(
			array(
				'post' => array(
					'post_author' => array(
						'user' => array(
							'user_roles' => array(
								'user_role{}' => array(
									'_constraints' => array(
										array(
											'type' => 'entity_array_contains',
											'settings' => array(
												'count' => array( 'min' => 1 ),
												'conditions' => array(
													'user_role' => array(
														'user_role_name' => array(
															'_constraints' => array(
																array(
																	'type' => 'equals',
																	'settings' => array(
																		'value' => 'administrator',
																	),
																),
															),
														),
													),
												),
											),
										),
									),
								),
							),
						),
					),
				),
			)
			, $admin_id
			, 10
		);
	}

	public function _test_post_terms() {

		$user_id = $this->factory->user->create();

		$post_ids = $this->factory->post->create_many(
			3
			, array( 'post_author' => $user_id )
		);

		$term_id = $this->factory->tag->create();

		wp_set_object_terms( $post_ids[0], $term_id, 'post_tag' );

		$cat_id = $this->factory->category->create();

		wp_set_object_terms( $post_ids[0], $cat_id, 'category', true );

		$this->assert_points_awarded(
			array(
				'post' => array(
					'post_terms' => array(
						'term{}' => array(
							'_constraints' => array(
								array(
									'type'     => 'entity_array_contains',
									'settings' => array(
										'count' => array( 'min' => 1 ),
										'conditions' => array(
											'term' => array(
												'term_id' => array(
													'_constraints' => array(
														array(
															'type' => 'equals',
															'settings' => array(
																'value' => $term_id,
															),
														),
													),
												),
											),
										),
									),
								),
//								array(
//									'type'     => 'entity_array_contains',
//									'settings' => array( 'value' => $cat_id ),
//								),
							),
						),
					),
				),
			)
			, $user_id
			, 10
		);
	}

	public function _test_works() {

		$user_id = $this->factory->user->create();

		$post_ids = $this->factory->post->create_many(
			3
			, array( 'post_author' => $user_id )
		);

		$page_id = $this->factory->post->create(
			array( 'post_type' => 'page', 'post_author' => $user_id )
		);

//		$term_id = $this->factory->tag->create();
//
//		wp_set_object_terms( $post_ids[0], $term_id, 'post_tag' );
//
//		$cat_id = $this->factory->category->create();
//
//		wp_set_object_terms( $post_ids[0], $cat_id, 'category', true );
//
		wp_update_post( array( 'ID' => $post_ids[0], 'post_content' => 'One day, I was in the park with Robert.' ) );

		$this->assertStringMatchesFormat( '%aRobert%a', get_post( $post_ids[0] )->post_content );

		$user_id_2 = $user_id;
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );

		$this->assertEquals( array( 'administrator' ), get_userdata( $user_id )->roles );
		$this->assertEquals( array( 'subscriber' ), get_userdata( $user_id_2 )->roles );
		wp_update_post( array( 'ID' => $post_ids[0], 'post_author' => $user_id ) );

		$this->assertEquals( $user_id, get_post( $post_ids[0] )->post_author );


		$points_target = WordPoints_Hooks::get_target( 'points' );
		$instance      = $points_target->instances->create_instance(
			array(
				'slug'        => 'post_publish',
				'points'      => 10,
				'points_type' => 'points',
				'target'      => array( 'post', 'post_author', 'user' ),
				'constraints' => array(
					'post' => array(
						'post_type_rel' => array(
							'post_type' => array(
								'post_type_name' => array(
									'_constraints' => array(
										array(
											'type'     => 'equals',
											'settings' => array( 'value' => 'post' ),
										),
									),
								),
							),
						),
						'post_author' => array(
							'user' => array(
								'user_roles' => array(
									'user_role{}' => array(
										'_constraints' => array(
											array(
												'type' => 'entity_array_contains',
												'settings' => array(
													'count' => array( 'min' => 1 ),
													'conditions' => array(
														'user_role' => array(
															'user_role_name' => array(
																'_constraints' => array(
																	array(
																		'type' => 'equals',
																		'settings'=> array(
																			'value' => 'administrator',
																		),
																	),
																),
															),
														),
													),
												),
											),
										),
									),
								),
							),
						),
//						'post_terms' => array(
//							'_constraints' => array(
//								array(
//									'type'     => 'array_contains',
//									'settings' => array( 'value' => $term_id ),
//								),
//								array(
//									'type'     => 'array_contains',
//									'settings' => array( 'value' => $cat_id ),
//								),
//							),
//						),
						'post_content' => array(
							'_constraints' => array(
								array(
									'type' => 'string_contains',
									'settings' => array( 'value' => 'Robert' ),
								),
							),
						),
					),
				),
			)
		);

		$this->assertNotEquals( false, $instance );

		if ( $instance instanceof WordPoints_Hook_Reaction_Validator ) {
			var_dump( $instance );
			$this->fail();
		}
		$this->assertEquals( 'post_publish', $instance->get_type() );
		$query = new WordPoints_Hook_Retroactive_Query( $instance );

		$results = $query->get_results();

		if ( $results instanceof WordPoints_Hook_Reaction_Validator ) {
			var_dump( $results );
			$this->fail();
		}
var_dump( $results );
		//	$this->assertEquals( array( $user_id => $post_ids + array( $page_id ) ), $results );

		$hook = WordPoints_Hooks::get_hook( 'post_publish' );

		foreach ( $results as $target => $result ) {
			$points_target->retroactive_hit( $target, $result, $hook, $instance );
		}

		$this->assertEquals( 10, wordpoints_get_points( $user_id, 'points' ) );
	}
}

/**
 * Class Testttt
 *
 * @since 1.
 *
 * @covers WordPoints_Hierarchy
 */
class Testttt{
	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hierarchy
	 */
	protected $hierarchy;

	public function setUp() {
		parent::setUp();

		$this->hierarchy = new WordPoints_Hierarchy( 'sub' );
	}

	/**
	 * Test that it returns an empty array when the hierarchy is empty.
	 *
	 * @since 1.0.0
	 */
	public function test_get_empty() {

		$this->assertEquals( array(), $this->hierarchy->get() );
	}

	/**
	 * Test that it returns the hierarchy.
	 *
	 * @since 1.0.0
	 */
	public function test_get() {

		$this->hierarchy->push( 'a', array( 'something' => 'test' ) );
		$this->hierarchy->push( 'b', array( 'something' => 'test2' ) );
		$this->hierarchy->pop();
		$this->hierarchy->push( 'c', array( 'something' => 'test3' ) );

		$this->assertEquals(
			array(
				'slug' => 'a',
				'something' => 'test',
				'sub' => array(
					'b' => array(
						'slug' => 'b',
						'something' => 'test2',
					),
					'c' => array(
						'slug' => 'c',
						'something' => 'test3',
					),
				),
			)
			, $this->hierarchy->get()
		);
	}

	/**
	 * Test calling pop when there is no parent.
	 *
	 * @since 1.0.0
	 */
	public function test_pop_no_parent() {

		$this->hierarchy->push( 'a', array( 'something' => 'test' ) );
		$this->hierarchy->pop();
		$this->hierarchy->push( 'b', array( 'something' => 'test2' ) );
		$this->hierarchy->push( 'c', array( 'something' => 'test3' ) );

		$this->assertEquals(
			array(
				'slug' => 'a',
				'something' => 'test',
				'sub' => array(
					'b' => array(
						'slug' => 'b',
						'something' => 'test2',
						'sub' => array(
							'c' => array(
								'slug' => 'c',
								'something' => 'test3',
							),
						),
					),
				),
			)
			, $this->hierarchy->get()
		);
	}

}

// EOF
