<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */


interface WordPoints_Entityish_QueryableI {
	public function get_storage_info();
}

//
//abstract class WordPoints_Entity
//	extends WordPoints_Entityish
//	implements WordPoints_Entity_ParentI {
//
//	protected $storage_type;
//
//	public function get_storage_info() {
//		return array(
//			'type' => $this->storage_type,
//			'meta' => array(
//				'id_field' => $this->id_field,
//			),
//		);
//	}
//}


abstract class WordPoints_Entity_Object_DB extends WordPoints_Entity_Object {

	protected $storage_type = 'db';

	/**
	 * The name of the table the objects are stored in.
	 *
	 * You must either define this or override the get_table_name() method.
	 *
	 * By default, the value will be used as the property name to access the table
	 * name on $wpdb.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $table_name;

	protected function get_table_name() {
		return $GLOBALS['wpdb']->{$this->table_name};
	}

	public function get_storage_info() {

		$info = parent::get_storage_info();
		$info['meta']['table_name'] = $this->get_table_name();

		return $info;
	}
}
//
//
//class WordPoints_Entity_Post
//	extends WordPoints_Entity_Object_DB
//	implements WordPoints_Entity_Check_CapsI {
//
//	protected $table_name = 'posts';
//
//}
//
//
//class WordPoints_Entity_Term extends WordPoints_Entity_Object_DB {
//
//	protected $table_name = 'terms';
//
//}

//
//class WordPoints_Entity_User extends WordPoints_Entity_Object_DB {
//
//	protected $table_name = 'users';
//}

//
//class WordPoints_Entity_Comment extends WordPoints_Entity_Object_DB {
//
//	protected $table_name = 'comments';
//}


//
//class WordPoints_Entity_Post_Type extends WordPoints_Entity_Object {
//
//	protected $storage_type = 'array';
//
//	public static function get_post_types() {
//		return get_post_types( array( 'public' => true ), 'labels' );
//	}
//
//	public function get_storage_info() {
//
//		$info = parent::get_storage_info();
//		$info['meta']['getter'] = array( $this, 'get_post_types' );
//
//		return $info;
//	}
//}

//
//class WordPoints_Entity_User_Role extends WordPoints_Entity_Object {
//
//	protected $storage_type = 'array';
//
//	public function get_user_roles() {
//		return wp_roles()->role_objects;
//	}
//
//	public function get_storage_info() {
//
//		$info = parent::get_storage_info();
//		$info['meta']['getter'] = array( $this, 'get_user_roles' );
//
//		return $info;
//	}
//}

//
//abstract class WordPoints_Entity_Attr
//	extends WordPoints_Entityish
//	implements WordPoints_Entity_ChildI, WordPoints_SpecedI {
//
//	protected $storage_type;
//
//	public function get_storage_info() {
//		return array(
//			'type' => $this->storage_type,
//			'meta' => array(
//				'field' => $this->get_field(),
//			),
//		);
//	}
//}

//
//class WordPoints_Entity_Post_Content extends WordPoints_Entity_Attr {
//
//	protected $storage_type = 'db';
//}

//
//class WordPoints_Entity_Post_Type_Name
//	extends WordPoints_Entity_Attr
//	implements WordPoints_Entity_Attr_Enumerable {
//
//	protected $storage_type = 'array';
//}

//class WordPoints_Entity_Term_Id extends WordPoints_Entity_Attr {
//
//	protected $storage_type = 'db';
//}

//
//class WordPoints_Entity_User_Role_Name
//	extends WordPoints_Entity_Attr
//	implements WordPoints_Entity_Attr_Enumerable {
//
//	protected $storage_type = 'array';
//
//}


//abstract class WordPoints_Entity_Relationship
//	extends WordPoints_Entityish
//	implements WordPoints_Entity_ParentI, WordPoints_Entity_ChildI {
//
//	protected $storage_type;
//
//	public function get_storage_info() {
//		return array(
//			'type' => $this->storage_type,
////			'meta' => array(
////				'field' => $this->id_field,
////			),
//		);
//	}
//}

//
//class WordPoints_Entity_Post_Author extends WordPoints_Entity_Relationship {
//
//	/**
//	 * @since 1.0.0
//	 */
//	public function get_storage_info() {
//
////		global $wpdb;
//		return array(
////			'post' => array(
////				'type' => 'db',
////				'meta' => array(
////					'table_name' => $wpdb->posts,
////					'primary_field' => 'ID',
////					'join_field' => 'post_author',
////				),
////			),
////			'user' => array(
//			'type' => 'db',
//			'meta' => array( 'field' => 'post_author' ),
////			),
//		);
//	}
//}


abstract class WordPoints_Entity_Relationship_DB extends WordPoints_Entity_Relationship {
	protected $primary_field;
	protected $secondary_field;

	public function get_primary_field() {
		return $this->primary_field;
	}

	public function get_secondary_field() {
		return $this->secondary_field;
	}
}


//
//class WordPoints_Entity_Post_Terms extends WordPoints_Entity_Relationship_OneToMany {
//
//	public function get_secondary_field() {
//		return array(
//			'table_name' => $GLOBALS['wpdb']->term_taxonomy,
//			'on' => array(
//				'primary_field' => 'term_taxonomy_id',
//				'join_field' => 'term_taxonomy_id',
//			),
//		);
//	}
//
//	public function get_storage_info() {
//		return array(
//			'type' => 'db',
//			'meta' => array(
//				'table_name' => $GLOBALS['wpdb']->term_relationships,
//				'join_field' => 'object_id',
//				'field' =>  $this->get_secondary_field(),
//			),
//		);
//	}
//}

//
//class WordPoints_Entity_Post_Type_Relationship extends WordPoints_Entity_Relationship {
//
//	/**
//	 * @since 1.0.0
//	 */
//	public function get_storage_info() {
//
//		return array(
//			'type' => 'db',
//			'meta' => array( 'field' => 'post_type' ),
//		);
//	}
//}

//
//class WordPoints_Entity_User_Roles extends WordPoints_Entity_Relationship_OneToMany {
//
//	protected $storage_type = 'db';
//
//	public function get_storage_info() {
//
//		global $wpdb;
//
//		$info = parent::get_storage_info();
//		$info['meta']['table_name'] = $wpdb->usermeta;
//		$info['meta']['join_field'] = 'user_id';
//		$info['meta']['field'] = array(
//			'name' => 'meta_value',
//			'type' => 'serialized_array',
//		);
//
//		$info['meta']['join_where'] = array(
//			array(
//				'field' => 'meta_key',
//				'value' => $wpdb->get_blog_prefix() . 'capabilities',
//			),
//		);
//
//		return $info;
//	}
//}


// EOF
