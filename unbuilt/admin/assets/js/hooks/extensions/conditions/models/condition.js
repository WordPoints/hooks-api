/**
 * wp.wordpoints.hooks.model.Condition
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.model.Base
 */
var Base = wp.wordpoints.hooks.model.Base,
	Args = wp.wordpoints.hooks.Args,
	Extensions = wp.wordpoints.hooks.Extensions,
	Fields = wp.wordpoints.hooks.Fields,
	getDeep = wp.wordpoints.hooks.util.getDeep,
	Condition;

Condition = Base.extend({

	defaults: {
		type: '',
		settings: []
	},

	initialize: function ( attributes, options ) {
		if ( options.group ) {
			this.group = options.group;
		}
	},

	validate: function ( attributes, errors ) {

		var conditionType = this.getType();

		if ( ! conditionType ) {
			return;
		}

		var fields = conditionType.fields;

		Fields.validate(
			fields
			, attributes
			, errors
		);
	},

	getType: function () {

		var arg = this.getArg();

		if ( ! arg ) {
			return false;
		}

		var Conditions = Extensions.get( 'conditions' );

		return Conditions.getType(
			Conditions.getDataTypeFromArg( arg )
			, this.get( 'type' )
		);
	},

	getArg: function () {

		if ( ! this.arg ) {

			var args = Args.getArgsFromHierarchy(
				this.getHierarchy()
				, this.reaction.get( 'event' )
			);

			if ( args ) {
				this.arg = args[ args.length - 1 ];
			}
		}

		return this.arg;
	},

	getHierarchy: function () {
		return this.group.get( 'hierarchy' );
	},

	getFullHierarchy: function () {

		return this.group.get( 'preHierarchy' ).concat(
			this.group.get( 'hierarchy' )
		);
	},

	sync: function ( method, model, options ) {

		if ( ! model.reaction ) {
			return;
		}

		var conditions = getDeep(
			model.reaction.attributes.conditions
			, model.getFullHierarchy().concat( [ '_conditions' ] )
		);

		switch ( method ) {

			case 'create':
				if ( typeof conditions[ model.id ] !== 'undefined' ) {
					options.error( { message: todo } ); // TODO error messages
					return;
				}

				conditions[ model.id ] = _.omit( model.attributes, [ 'id' ] );

				options.success();
				break;

			case 'read':
				if ( typeof conditions[ model.id ] === 'undefined' ) {
					options.error( { message: todo } );
					return;
				}

				options.success( conditions[ model.id ] );
				break;

			case 'update':
				if ( typeof conditions[ model.id ] === 'undefined' ) {
					options.error( { message: todo } );
					return;
				}

				conditions[ model.id ] = _.omit( model.attributes, [ 'id' ] );

				options.success();
				break;

			case 'delete':
				if ( ! conditions || typeof conditions[ model.id ] === 'undefined' ) {
					//options.error( { message: todo } );
					return;
				}

				delete conditions[ model.id ];
				break;
		}
	}
});

module.exports = Condition;
