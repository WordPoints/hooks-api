<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

//
//class WordPoints_Hook_Condition_String_Contains
//	extends WordPoints_Hook_Condition
//	implements WordPoints_Hook_Retroactive_Query_ModifierI {
//
//	public function modify_retroactive_query(
//		WordPoints_Hook_Retroactive_QueryI $query,
//		array $condition = null
//	) {
//
//		global $wpdb;
//
//		$condition['compare'] = 'like';
//		$condition['value'] = '%' . $wpdb->esc_like( $condition['value'] ) . '%';
//		$query->add_condition( $condition );
//	}
//}

//
//class WordPoints_Hook_Condition_Entity_Array_Contains
//	extends WordPoints_Hook_Condition
//	implements WordPoints_Hook_Retroactive_Query_ModifierI {
//
//	public function modify_retroactive_query(
//		WordPoints_Hook_Retroactive_QueryI $query,
//		array $condition = null
//	) {
//
//		$this->retroactive_query_add_count( $query, $condition );
//
//		$this->conditions_extension->_modify_retroactive_query(
//			$query
//			, reset( $condition['conditions'] )
//		);
//	}
//
//	/**
//	 *
//	 *
//	 * @since 1.
//	 *
//	 * @param WordPoints_Hook_Retroactive_QueryI $query
//	 * @param array                              $settings
//	 *
//	 * @return array
//	 */
//	private function retroactive_query_add_count(
//		WordPoints_Hook_Retroactive_QueryI $query,
//		array $settings
//	) {
//
//		$count_condition = array( 'type' => 'count' );
//
//		if ( isset( $settings['max'] ) ) {
//			$count_condition['value']   = $settings['max'];
//			$count_condition['compare'] = '<=';
//		}
//
//		if ( isset( $settings['min'] ) ) {
//
//			if ( isset( $count_condition['value'] ) ) {
//				$query->add_condition( $count_condition );
//			} elseif ( 1 === $settings['min'] ) {
//				return;
//			} else {
//				$count_condition['value']   = $settings['min'];
//				$count_condition['compare'] = '>=';
//			}
//		}
//
//		$query->add_condition( $count_condition );
//	}
//}

// EOF
