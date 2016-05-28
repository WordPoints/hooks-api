<?php

/**
 * Tests creating a points reaction.
 *
 * @package WordPoints\Codeception
 * @since 1.0.0
 */

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Create a points reaction' );
$I->hadCreatedAPointsType();
$I->amLoggedInAsAdmin();
$I->amOnPage( 'wp-admin/admin.php?page=wordpoints_points_types' );
$I->see( 'Points Types' );
$I->see( 'Points', '.nav-tab-active' );
$I->see( 'Events' );
$I->see( 'Register', '#points-user_register' );
$I->click( 'Add New', '#points-user_register' );
$I->waitForNewReaction();
$I->seeElement( '#points-user_register .add-reaction[disabled]' );
$I->fillField( 'description', 'Registering.' );
$I->fillField( 'log_text', 'Registration.' );
$I->fillField( 'points', '10' );
$I->click( 'Save', '#points-user_register' );
$I->waitForJqueryAjax();
$I->see( 'Your changes have been saved.', '#points-user_register .messages' );
$I->cantSeeElement( '#points-user_register .add-reaction[disabled]' );

// EOF
