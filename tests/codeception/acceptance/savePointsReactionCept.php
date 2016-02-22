<?php

/**
 * Tests saving a points reaction.
 *
 * @package WordPoints\Codeception
 * @since 1.0.0
 */

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Save a points reaction' );
$I->amLoggedInAsAdmin();
$I->amOnPage( 'wp-admin/admin.php?page=wordpoints_points_types' );
$I->see( 'Points Types' );
$I->see( 'Add New', '.nav-tab-active' );
$I->dontSee( 'Events' );
$I->see( 'Settings' );
$I->fillField( 'points-name', 'Points' );
$I->fillField( 'points-prefix', '$' );
$I->fillField( 'points-suffix', 'pts.' );
$I->click( 'Save' );
$I->see( 'Points Types' );
$I->see( 'Points', '.nav-tab-active' );
$I->see( 'Events' );
$I->see( 'Settings' );
$I->canSeeInFormFields(
	'#settings form'
	, array(
		'points-name' => 'Points',
		'points-prefix' => '$',
		'points-suffix' => 'pts.',
	)
);
$I->see( 'Register', '#points-user_register' );
$I->click( 'Add New', '#points-user_register' );
$I->waitForNewReaction();
$I->fillField( 'description', 'Registering.' );
$I->fillField( 'log_text', 'Registration.' );
$I->fillField( 'points', '10' );
$I->click( 'Save', '#points-user_register' );
$I->waitForJqueryAjax();
$I->see( 'Your changes have been saved.', '#points-user_register .messages' );
