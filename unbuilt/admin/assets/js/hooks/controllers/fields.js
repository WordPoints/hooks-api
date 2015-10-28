/**
 * wp.wordpoints.hooks.controller.Fields
 *
 * @since 1.0.0
 *
 * @class
 * @augments Backbone.Model
 *
 *
 */
var $ = Backbone.$,
	hooks = wp.wordpoints.hooks,
	l10n = wp.wordpoints.hooks.view.l10n,
	template = wp.wordpoints.hooks.template,
	Fields;

Fields = Backbone.Model.extend({

	defaults: {
		fields: {}
	},

	template: template( 'hook-reaction-field' ),
	templateHidden: template( 'hook-reaction-hidden-field' ),
	templateSelect: template( 'hook-reaction-select-field' ),

	emptyMessage: _.template( l10n.emptyField ),

	initialize: function () {

		this.listenTo( hooks, 'reaction:model:validate', this.validateReaction );
		this.listenTo( hooks, 'reaction:view:init', this.initReaction );

		this.attributes.fields.event = {
			type: 'hidden',
			required: true
		};
	},

	create: function ( reaction, name, value, data ) {

		data = _.extend(
			{ name: this.getFieldName( reaction, name ), value: value }
			, data
		);

		switch ( data.type ) {
			case 'select':
				return this.createSelect( data );

			case 'hidden':
				return this.templateHidden( data );
		}

		var DataType = DataTypes.get( data.type );

		if ( DataType ) {
			return DataType.createField( data );
		} else {
			return this.template( data );
		}
	},

	createSelect: function ( data ) {

		var $template = $( '<div></div>' ).html( this.templateSelect( data ) ),
			options = '';

		_.each( data.options, function ( option ) {

			options += $( '<option></option>' )
				.attr( 'value', option.value )
				.text( option.label ? option.label : option.value )
				.prop( 'outerHTML' );
		});

		$template.find( 'select' )
			.append( options )
			.val( data.value )
			.find( ':selected' )
				.attr( 'selected', true );

		return $template.html();
	},

	getPrefix: function ( reaction ) {
		// The reaction might not have been created yet, so we can't rely on the id.
		return 'wordpoints_hook_reaction_' + reaction.cid + '_';
	},

	getFieldName: function ( reaction, field ) {

		var name = this.getPrefix( reaction );

		if ( _.isArray( field ) ) {
			name += field.shift() + '[' + field.join( '][' ) + ']';
		} else {
			name += field;
		}

		return name;
	},

	getAttrSlug: function ( reaction, fieldName ) {

		var pregex = new RegExp( '^' + this.getPrefix( reaction ) );

		var name = fieldName.replace( pregex, '' );

		var nameParts = [],
			firstBracket = name.indexOf( '[' );

		// If this isn't an array-syntax name, we don't need to process it.
		if ( -1 === firstBracket ) {
			return name;
		}

		// Usually the bracket will be proceeded by something: `array[...]`.
		if ( 0 !== firstBracket ) {
			nameParts.push( name.substring( 0, firstBracket ) );
			name = name.substring( firstBracket );
		}

		nameParts = nameParts.concat( name.slice( 1, -1 ).split( '][' ) );

		// If the last element is empty, it is a non-associative array: `a[]`
		if ( nameParts[ nameParts.length - 1 ] === '' ) {
			nameParts.pop();
		}

		return nameParts;
	},

	// Get the data from a form as key => value pairs.
	getFormData: function ( reaction, $form, prefix ) {

		var formObj = {},
			pregex,
			inputs = $form.find( ':input' ).serializeArray();

		if ( typeof prefix === 'undefined' ) {
			prefix = '';
		}

		prefix = this.getPrefix( reaction ) + prefix;
		pregex = new RegExp( '^' + prefix );

		_.each( inputs, function ( input ) {

			if ( ! input.name.match( pregex ) ) {
				return;
			}

			formObj[ input.name.replace( pregex, '' ) ] = input.value;
		} );

		return this.arrayify( formObj );
	},

	arrayify: function ( formData ) {

		var arrayData = {};

		_.each( formData, function ( value, name ) {

			var nameParts = [],
				data = arrayData,
				isArray = false,
				firstBracket = name.indexOf( '[' );

			// If this isn't an array-syntax name, we don't need to process it.
			if ( -1 === firstBracket ) {
				data[ name ] = value;
				return;
			}

			// Usually the bracket will be proceeded by something: `array[...]`.
			if ( 0 !== firstBracket ) {
				nameParts.push( name.substring( 0, firstBracket ) );
				name = name.substring( firstBracket );
			}

			nameParts = nameParts.concat( name.slice( 1, -1 ).split( '][' ) );

			// If the last element is empty, it is a non-associative array: `a[]`
			if ( nameParts[ nameParts.length - 1 ] === '' ) {
				isArray = true;
				nameParts.pop();
			}

			var key = nameParts.pop();

			// Construct the hierarchical object.
			_.each( nameParts, function ( part ) {
				data = data[ part ] = ( data[ part ] || {} );
			});

			// Set the value.
			if ( isArray ) {

				if ( typeof data[ key ] === 'undefined' ) {
					data[ key ] = [];
				}

				data[ key ].push( value );

			} else {
				data[ key ] = value;
			}
		});

		return arrayData;
	},

	validate: function ( fields, attributes, errors ) {

		_.each( fields, function ( field, slug ) {
			if (
				field.required
				&& (
					typeof attributes[ slug ] === 'undefined'
					|| '' === $.trim( attributes[ slug ] )
				)
			) {
				errors.push( {
					field: slug,
					message: this.emptyMessage( field )
				} );
			}
		}, this );
	},

	initReaction: function ( reaction ) {

		this.listenTo( reaction, 'render:settings', this.renderReaction );
	},

	renderReaction: function ( reaction ) {

		var fieldsHTML = '';

		_.each( this.get( 'fields' ), function ( field, name ) {

			fieldsHTML += this.create(
				reaction.model,
				name,
				reaction.model.get( name ),
				field
			);

		}, this );

		reaction.$settings.html( fieldsHTML );
	},

	validateReaction: function ( reaction, attributes ) {

		var errors = [];

		this.validate( this.get( 'fields' ), attributes, errors );

		if ( ! _.isEmpty( errors ) ) {
			return errors;
		}
	}
});

var DataType = Backbone.Model.extend({

	idAttribute: 'slug',

	defaults: {
		inputType: 'text'
	},

	template: template( 'hook-reaction-field' ),

	createField: function ( data ) {

		return this.template(
			_.extend( {}, data, { type: this.get( 'inputType' ) } )
		);
	}
});

//var NumberType = Backbone.Model.extend({
//	defaults: {
//		inputType: 'number'
//	}
//});

var DataTypes = new Backbone.Collection();

DataTypes.add( new DataType( { slug: 'text' } ) );
DataTypes.add( new DataType( { slug: 'integer', inputType: 'number' } ) );

module.exports = Fields;
