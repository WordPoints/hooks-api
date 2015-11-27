<?php

/**
 * Ajax callbacks for hook reactions.
 *
 * @package WordPoints
 * @since 1.0.0
 */

/**
 * Respond to Ajax requests from the hooks admin screen.
 *
 * This code is part of a class mainly to keep it DRY by consolidating common code
 * into the private methods.
 *
 * @since 1.0.0
 */
class WordPoints_Admin_Ajax_Hooks {

	//
	// Private Vars.
	//

	/**
	 * The reactor that the reactions are being saved for.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reactor
	 */
	protected $reactor;

	/**
	 * The slug of the reactor that the reactions are being saved for.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $reactor_slug;

	//
	// Public Static Functions.
	//

	/**
	 * Prepare a hook reaction for return to the user.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_ReactionI $reaction The reaction object.
	 *
	 * @return array The hook reaction data extracted into an array.
	 */
	public static function prepare_hook_reaction( $reaction ) {

		$reactor = $reaction->get_reactor_slug();

		return array_merge(
			$reaction->get_all_meta()
			, array(
				'id' => $reaction->ID,
				'event' => $reaction->get_event_slug(),
				'reactor' => $reactor,
				'nonce' => wp_create_nonce(
					"wordpoints_update_hook_reaction|{$reactor}|{$reaction->ID}"
				),
				'delete_nonce' => wp_create_nonce(
					"wordpoints_delete_hook_reaction|{$reactor}|{$reaction->ID}"
				),
			)
		);
	}

	//
	// Public Methods.
	//

	/**
	 * Hook up the methods to the Ajax request actions when the class is constructed.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->hooks();
	}

	/**
	 * Hook the callback methods to the Ajax actions.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action(
			'wp_ajax_wordpoints_admin_create_hook_reaction'
			, array( $this, 'create_hook_reaction' )
		);

		add_action(
			'wp_ajax_wordpoints_admin_update_hook_reaction'
			, array( $this, 'update_hook_reaction' )
		);

		add_action(
			'wp_ajax_wordpoints_admin_delete_hook_reaction'
			, array( $this, 'delete_hook_reaction' )
		);
	}

	/**
	 * Handle an Ajax request to create a new hook reaction.
	 *
	 * @since 1.0.0
	 */
	public function create_hook_reaction() {

		$this->verify_user_can();

		$reactor = $this->get_reactor();

		$this->verify_request(
			"wordpoints_create_hook_reaction|{$this->reactor_slug}"
		);

		$reaction = $reactor->reactions->create_reaction( $this->get_data() );

		$this->send_json_result( $reaction, 'create' );
	}

	/**
	 * Handle an Ajax request to update a hook reaction.
	 *
	 * @since 1.0.0
	 */
	public function update_hook_reaction() {

		$this->verify_user_can();

		$reactor  = $this->get_reactor();
		$reaction = $this->get_reaction();

		$this->verify_request(
			"wordpoints_update_hook_reaction|{$this->reactor_slug}|{$reaction->ID}"
		);

		$reaction = $reactor->reactions->update_reaction(
			$reaction->ID
			, $this->get_data()
		);

		$this->send_json_result( $reaction, 'update' );
	}

	/**
	 * Handle Ajax requests to delete a hook reaction.
	 *
	 * @since 1.0.0
	 */
	public function delete_hook_reaction() {

		$this->verify_user_can();

		$reactor  = $this->get_reactor();
		$reaction = $this->get_reaction();

		$this->verify_request(
			"wordpoints_delete_hook_reaction|{$this->reactor_slug}|{$reaction->ID}"
		);

		$result = $reactor->reactions->delete_reaction( $reaction->ID );

		if ( ! $result ) {
			wp_send_json_error( array( 'message' => __( 'There was an error deleting the reaction. Please try again.', 'wordpoints' ) ) );
		}

		wp_send_json_success();
	}

	//
	// Private Methods.
	//

	/**
	 * Report an unexpected error.
	 *
	 * There is a common error message returned when certain hidden fields are
	 * absent. The user doesn't know these fields exist, so we give a generic error.
	 * In the real world, this should really never happen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $debug_context Context sent with the message (for debugging).
	 */
	private function unexpected_error( $debug_context ) {

		wp_send_json_error(
			array(
				'message' => __( 'There was an unexpected error. Try reloading the page.', 'wordpoints' ),
				'debug'   => $debug_context,
			)
		);
	}

	/**
	 * Verify that the current user can do this.
	 *
	 * This should be called before the request is processed. Then verify_request()
	 * may be called later, after data needed to verify the nonce is retrieved.
	 *
	 * @since 1.0.0
	 */
	private function verify_user_can() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to perform this action. Maybe you have been logged out?', 'wordpoints' ) ) );
		}
	}

	/**
	 * Verify the current request.
	 *
	 * Checks that the request is accompanied by a valid nonce for the action.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action The action the nonce should be for.
	 */
	private function verify_request( $action ) {

		if (
			empty( $_POST['nonce'] )
			|| ! wordpoints_verify_nonce( 'nonce', $action, null, 'post' )
		) {
			wp_send_json_error(
				array( 'message' => __( 'Your security token for this action has expired. Refresh the page and try again.', 'wordpoints' ) )
			);
		}
	}

	/**
	 * Get the hook reactor this request is made for.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_Hook_Reactor The object of the reactor.
	 */
	protected function get_reactor() {

		if ( ! isset( $_POST['reactor'] ) ) { // WPCS: CSRF OK.
			$this->unexpected_error( 'reactor' );
		}

		$reactor_slug = sanitize_key( $_POST['reactor'] ); // WPCS: CSRF OK.

		$reactor = wordpoints_hooks()->reactors->get( $reactor_slug );

		if ( ! $reactor instanceof WordPoints_Hook_Reactor ) {
			$this->unexpected_error( 'reactor_invalid' );
		}

		$this->reactor_slug = $reactor_slug;
		$this->reactor = $reactor;

		return $reactor;
	}

	/**
	 * Get the hook reaction this request is for.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_Hook_ReactionI The hook reaction that this request relates to.
	 */
	protected function get_reaction() {

		if ( ! isset( $_POST['id'] ) ) { // WPCS: CSRF OK.
			$this->unexpected_error( 'id' );
		}

		$reaction = $this->reactor->reactions->get_reaction(
			wordpoints_int( $_POST['id'] ) // WPCS: CSRF OK.
		);

		if ( ! $reaction ) {
			wp_send_json_error( array( 'message' => __( 'The reaction ID passed to the server is invalid. Perhaps it has been deleted. Try reloading the page.', 'wordpoints' ) ) );
		}

		return $reaction;
	}

	/**
	 * Get the hook reaction's settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array The hook reaction's settings.
	 */
	protected function get_data() {

		$data = wp_unslash( $_POST ); // WPCS: CSRF OK.

		unset( $data['id'], $data['action'], $data['nonce'], $data['reactor'] );

		return $data;
	}

	/**
	 * Send the hook reaction or an error back to the user based on the result.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $result The result of the action.
	 * @param string $action The action being performed: 'create' or 'update'.
	 */
	private function send_json_result( $result, $action ) {

		if ( ! $result ) {

			if ( 'create' === $action ) {
				$message = __( 'There was an error adding the reaction. Please try again.', 'wordpoints' );
			} else {
				$message = __( 'There was an error updating the reaction. Please try again.', 'wordpoints' );
			}

			wp_send_json_error( array( 'message' => $message ) );

		} elseif ( $result instanceof WordPoints_Hook_Reaction_Validator ) {

			wp_send_json_error( array( 'errors' => $result->get_errors() ) );
		}

		$data = null;

		if ( 'create' === $action ) {
			$data = self::prepare_hook_reaction( $result );
		}

		wp_send_json_success( $data );
	}
}

// EOF
