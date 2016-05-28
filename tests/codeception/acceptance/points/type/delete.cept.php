<?php

/**
 * Tests deleting a points type.
 *
 * @package WordPoints\Codeception
 * @since 1.0.0
 */

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Delete a points type' );
$I->hadCreatedAPointsType();
$I->amLoggedInAsAdminOnPage( 'wp-admin/admin.php?page=wordpoints_points_types' );
$I->see( 'Points Types' );
$I->see( 'Points', '.nav-tab-active' );
$I->see( 'Slug: points' );
$I->canSeeInFormFields(
	'#settings form'
	, array(
		'points-name' => 'Points',
		'points-prefix' => '',
		'points-suffix' => '',
	)
);
$I->click( 'Delete' );
$I->seeSuccessMessage();
$I->see( 'Points Types' );
$I->see( 'Add New', '.nav-tab-active' );

// EOF
