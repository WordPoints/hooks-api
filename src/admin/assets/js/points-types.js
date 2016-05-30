/**
 * Generic code for the points types administration screen.
 *
 * @package WordPoints\Points\Administration
 * @since 1.0.0
 */

/* global WordPointsPointsTypesL10n, jQuery */

jQuery( document ).ready( function ( $ ) {

	var $currentDelete;

	// Require confirmation for points type delete.
	$( '#settings .delete' ).click( function( event ) {

		if ( ! $currentDelete ) {

			$currentDelete = $( this );

			event.preventDefault();

			$( '<div></div>' )
				.attr( 'title', WordPointsPointsTypesL10n.confirmTitle )
				.html( $( '<p></p>' ).text( WordPointsPointsTypesL10n.confirmDelete ) )
				.dialog({
					dialogClass: 'wp-dialog wordpoints-delete-type-dialog',
					resizable: false,
					draggable: false,
					height: 250,
					modal: true,
					buttons: [
						{
							text: WordPointsPointsTypesL10n.deleteText,
							'class': 'button-primary',
							click: function() {
								$( this ).dialog( 'close' );
								$currentDelete.click();
								$currentDelete = false;
							}
						},
						{
							text: WordPointsPointsTypesL10n.cancelText,
							'class': 'button-secondary',
							click: function() {
								$( this ).dialog( 'close' );
								$currentDelete = false;
							}
						}
					]
				});
		}
	});

});

// EOF
