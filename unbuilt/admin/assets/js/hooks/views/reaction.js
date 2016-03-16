/**
 * wp.wordpoints.hooks.view.Base
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	Fields = wp.wordpoints.hooks.Fields,
	Reactors = wp.wordpoints.hooks.Reactors,
	Args = wp.wordpoints.hooks.Args,
	$ = Backbone.$,
	l10n = wp.wordpoints.hooks.view.l10n,
	data = wp.wordpoints.hooks.view.data,
	Reaction;

// The DOM element for a reaction...
Reaction = Base.extend({

	namespace: 'reaction',

	className: 'wordpoints-hook-reaction',

	template: wp.wordpoints.hooks.template( 'hook-reaction' ),

	// The DOM events specific to an item.
	// TODO capture return key to submit the form.
	events: {
		'click .actions .delete':    'confirmDelete',
		'click .save':      'save',
		'click .cancel':    'cancel',
		'click .close':     'close',
		'click .edit':      'edit',
		'change .fields *': 'lockOpen',
		'keyup input':      'maybeLockOpen'
	},

	initialize: function () {

		this.listenTo( this.model, 'change:description', this.renderDescription );
		this.listenTo( this.model, 'change:reactor', this.setReactor );
		this.listenTo( this.model, 'change:reactor', this.renderTarget );
		this.listenTo( this.model, 'destroy', this.remove );
		this.listenTo( this.model, 'sync', this.showSuccess );
		this.listenTo( this.model, 'error', this.showError );
		this.listenTo( this.model, 'invalid', this.showError );

		this.on( 'render:settings', this.renderTarget );

		this.setReactor();
	},

	render: function () {

		this.$el.html( this.template() );

		this.$title    = this.$( '.title' );
		this.$fields   = this.$( '.fields' );
		this.$settings = this.$fields.find( '.settings' );
		this.$target   = this.$fields.find( '.target' );

		this.renderDescription();

		this.trigger( 'render', this );

		return this;
	},

	// Re-render the title of the hook.
	renderDescription: function () {

		this.$title.text( this.model.get( 'description' ) );

		this.trigger( 'render:title', this );
	},

	renderFields: function () {

		var currentActionType = this.getCurrentActionType();

		this.trigger( 'render:settings', this.$settings, currentActionType, this );
		this.trigger( 'render:fields', this.$fields, currentActionType, this );

		this.renderedFields = true;
	},

	renderTarget: function () {

		var argTypes = this.Reactor.get( 'arg_types' ),
			end;

		// If there is just one arg type, we can use the `_.where()`-like syntax.
		if ( argTypes.length === 1 ) {

			end = { _canonical: argTypes[0], _type: 'entity' };

		} else {

			// Otherwise, we'll be need our own function, for `_.filter()`.
			end = function ( arg ) {
				return (
					arg.get( '_type' ) === 'entity'
					&& _.contains( argTypes, arg.get( '_canonical' ) )
				);
			};
		}

		var hierarchies = Args.getHierarchiesMatching( {
			event: this.model.get( 'event' ),
			end: end
		} );

		var options = [];

		_.each( hierarchies, function ( hierarchy ) {
			options.push( {
				label: Args.buildHierarchyHumanId( hierarchy ),
				value: _.pluck( _.pluck( hierarchy, 'attributes' ), 'slug' ).join( ',' )
			} );
		});

		var value = this.model.get( 'target' );

		if ( _.isArray( value ) ) {
			value = value.join( ',' );
		}

		var label = this.Reactor.get( 'target_label' );

		if ( ! label ) {
			label = l10n.target_label;
		}

		var field = Fields.create(
			'target'
			, value
			, {
				type: 'select',
				options: options,
				label: label
			}
		);

		this.$target.html( field );
	},

	setReactor: function () {
		this.Reactor = Reactors.get( this.model.get( 'reactor' ) );
	},

	// Get the current action type that settings are being displayed for.
	// Right now we just default this to the first action type we find that
	// this reactor responds to with the current hit type.
	getCurrentActionType: function () {

		var eventActionTypes = data.action_types[ this.model.get( 'event' ) ];
		var reactorActionTypes = this.Reactor.get( 'action_types' );
		var currentHitType = this.getCurrentHitType();

		for ( var i = 0; i < eventActionTypes.length; i++ ) {
			if ( currentHitType === reactorActionTypes[ eventActionTypes[ i ] ] ) {
				return eventActionTypes[ i ];
			}
		}
	},

	// Get the current hit type that settings are being displayed for.
	// Right now we just default this to the main hit type for the reactor.
	getCurrentHitType: function () {
		return _.values( this.Reactor.get( 'hit_types' ) )[0];
	},

	// Toggle the visibility of the form.
	edit: function () {

		if ( ! this.renderedFields ) {
			this.renderFields();
		}

		// Then display the form.
		this.$fields.slideDown( 'fast' );
		this.$el.addClass( 'editing' );
	},

	// Close the form.
	close: function () {

		this.$fields.slideUp( 'fast' );
		this.$el.removeClass( 'editing' );
		this.$( '.success' ).hide();
	},

	// Maybe lock the form open when an input is altered.
	maybeLockOpen: function ( event ) {

		var $target = $( event.target );

		var attrSlug = Fields.getAttrSlug( this.model, $target.attr( 'name' ) );

		if ( $target.val() !== this.model.get( attrSlug ) + '' ) {
			this.lockOpen();
		}
	},

	// Lock the form open when the form values have been changed.
	lockOpen: function () {

		this.$el.addClass( 'changed' );
		this.$( '.save' ).prop( 'disabled', false );
		this.$( '.success' ).fadeOut();
	},

	// Cancel editing or adding a new reaction.
	cancel: function () {

		if ( this.$el.hasClass( 'new' ) ) {
			this.model.collection.trigger( 'cancel-add-new' );
			this.remove();
			return;
		}

		this.$el.removeClass( 'changed' );

		this.renderFields();

		this.trigger( 'cancel' );
	},

	// Save changes to the reaction.
	save: function () {

		this.wait();
		this.$( '.save' ).prop( 'disabled', true );

		var formData = Fields.getFormData( this.model, this.$fields );

		if ( formData.target ) {
			formData.target = formData.target.split( ',' );
		}

		this.model.save( formData, { wait: true } );
	},

	// Display a spinner while changes are being saved.
	wait: function () {

		this.$( '.spinner-overlay' ).show();
		this.$( '.err' ).slideUp();
	},

	// Confirm that a reaction is intended to be deleted before deleting it.
	confirmDelete: function () {

		var $dialog = $( '<div><p></p></div>' ),
			view = this;

		$dialog
			.attr( 'title', l10n.confirmTitle )
			.find( 'p' )
			.text( l10n.confirmDelete )
			.end()
			.dialog({
				dialogClass: 'wp-dialog wordpoints-delete-hook-reaction-dialog',
				resizable: false,
				draggable: false,
				height: 250,
				modal: true,
				buttons: [
					{
						text: l10n.deleteText,
						'class': 'button-primary',
						click: function() {
							$( this ).dialog( 'close' );
							view.destroy();
						}
					},
					{
						text: l10n.cancelText,
						'class': 'button-secondary',
						click: function() {
							$( this ).dialog( 'close' );
						}
					}
				]
			});
	},

	// Remove the item, destroy the model.
	destroy: function () {

		this.wait();

		this.model.destroy( { wait: true } );
	},

	// Display an error when there is an Ajax failure.
	showError: function ( event, response ) {

		var message, errors = [];

		this.$( '.spinner-overlay' ).hide();

		// Sometimes we get a list of errors.
		if ( response.errors ) {

			// When that happens, we loop over them and try to display each of
			// them next to their associated field.
			_.each( response.errors, function ( error ) {

				var $field, fieldName;

				// Sometimes some of the errors aren't for any particular field
				// though, so we collect them in an array an display them all
				// together a bit later.
				if ( ! error.field ) {
					errors.push( error.message );
					return;
				}

				fieldName = Fields.getFieldName( error.field );

				// When a field is specified, we try to locate it.
				$field = this.$(
					'[name="' + fieldName.replace( /[^a-z0-9-_\[\]\{}]/gi, '' ) + '"]'
				);

				if ( 0 === $field.length ) {

					// However, there are times when the error is for a field set
					// and not a single field. In that case, we try to find the
					// fields in that set.
					$field = this.$(
						'[name^="' + fieldName.replace( /[^a-z0-9-_\[\]\{}]/gi, '' ) + '"]'
					);

					// If that fails, we just add this to the general errors.
					if ( 0 === $field.length ) {
						errors.push( error.message );
						return;
					}

					$field = $field.first();
				}

				$field.before(
					$( '<div class="message err"></div>' )
						.text( error.message )
						//.show() // TODO we shouldn't need this
				);

			}, this );

			// If there were some general errors.
			if ( errors ) {

				var $errors = this.$( '.messages .err' );

				$errors.html( '' );

				_.each( errors, function ( error ) {
					$errors.append( $( '<p></p>' ).text( error ) );
				});

				$errors.fadeIn();
			}

		} else {

			// Sometimes we are given just one error message, or no message at
			// all, in which case we use the default.
			if ( response.message ) {
				message = response.message;
			} else {
				message = l10n.unexpectedError;
			}

			this.$( '.messages .err' ).text( message ).fadeIn();
		}
	},

	// Display a success message.
	showSuccess: function () {

		this.$( '.spinner-overlay' ).hide();

		this.$( '.success' )
			.text( l10n.changesSaved )
			.slideDown();

		this.$el.removeClass( 'new changed' );
	}
});

module.exports = Reaction;