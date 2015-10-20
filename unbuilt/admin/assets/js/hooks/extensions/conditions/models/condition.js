/**
 * wp.wordpoints.hooks.model.Condition
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.model.Base
 */
var Base = wp.wordpoints.hooks.model.Base,
	Args = wp.wordpoints.hooks.Args,
	getDeep = wp.wordpoints.hooks.util.getDeep,
	Condition;

Condition = Base.extend({

	defaults: {
		type: '',
		settings: []
	},

	getArg: function () {

		var hierarchy = this.get( '_hierarchy' );

		// TODO aliases
		var arg = Args.getChild(
			hierarchy[ hierarchy.length - 2 ]
			, hierarchy[ hierarchy.length - 1 ]
		);

		return arg;
	},

	sync: function ( method, model, options ) {

		if ( ! model.reaction ) {
			return;
		}

		var conditions = getDeep(
			model.reaction.attributes.conditions
			, model.get( '_hierarchy' ).concat( [ '_conditions' ] )
		);

		switch ( method ) {

			case 'create':
				if ( typeof conditions[ model.id ] !== 'undefined' ) {
					options.error( { message: todo } ); // TODO error messages
					return;
				}

				conditions[ model.id ] = _.omit( model.attributes, [ 'id', '_hierarchy' ] );

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

				conditions[ model.id ] = _.omit(
					model.attributes
					, [ 'id', '_hierarchy'  ]
				);

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
