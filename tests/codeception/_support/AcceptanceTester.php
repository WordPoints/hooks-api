<?php

/**
 * Acceptance tester class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Tester for use in the acceptance tests.
 *
 * @since 1.0.0
 */
class AcceptanceTester extends \Codeception\Actor {

	use _generated\AcceptanceTesterActions;

	/**
	 * Logs the user in as the admin.
	 *
	 * A snapshot of the session is saved for later so that the same session can be
	 * reused.
	 */
	public function amLoggedInAsAdmin() {

		$I = $this;

		// If the snapshot already exists we just load it and don't need to log in.
		if ( $I->loadSessionSnapshot( 'admin' ) ) {
			return;
		}

		$I->loginAsAdmin();
		$I->saveSessionSnapshot( 'admin' );
	}

	/**
	 * Wait for a new reaction to be displayed on the screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $context The context in which the reaction should appear.
	 * @param int    $timeout The number of seconds to wait before timing out.
	 */
	public function waitForNewReaction( $context = '', $timeout = null ) {

		$I = $this;

		// Wait until the fields are actually interactive.
		// Attempting to set a field value immediately after creating the new
		// reaction  will result in an error: "Element is not currently interactable
		// and may not be manipulated."
		$I->waitForElementChange(
			"{$context} .wordpoints-hook-reaction.new [name=description]"
			, function ( \Facebook\WebDriver\WebDriverElement $element ) {

				try {

					// It should be OK that we clear this since this is a new
					// reaction and doesn't have a description yet.
					$element->clear();

				} catch ( Exception $e ) {

					codecept_debug(
						'Error while waiting for new reaction:' . $e->getMessage()
					);
				}

				return ! isset( $e );
			}
			, $timeout
		);
	}
}

// EOF
