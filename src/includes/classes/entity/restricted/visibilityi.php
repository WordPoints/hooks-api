<?php

/**
 * Entity restricted visibility interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by entities whose visibility may be restricted for some users.
 *
 * @since 1.0.0
 */
interface WordPoints_Entity_Restricted_VisibilityI {

	/**
	 * Check whether a user has the caps to view this entity.
	 *
	 * Usually when you are implementing this method, you will want to return false
	 * if the entity doesn't exist, because it might not be possible to check if it
	 * was restricted in that case.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $user_id The user's ID.
	 * @param mixed $id      The entity's ID.
	 *
	 * @return bool Whether the user can view the entity.
	 */
	public function user_can_view( $user_id, $id );
}

// EOF
