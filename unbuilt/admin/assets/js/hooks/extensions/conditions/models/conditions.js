/**
 * wp.wordpoints.hooks.model.Conditions
 *
 * @class
 * @augments Backbone.Collection
 */
var Condition = wp.wordpoints.hooks.model.Condition,
	getDeep = wp.wordpoints.hooks.util.getDeep,
	setDeep = wp.wordpoints.hooks.util.setDeep,
	Conditions;

Conditions = Backbone.Collection.extend({

	// Reference to this collection's model.
	model: Condition,

	comparator: 'id',

	sync: function ( method, collection, options ) {
		// TODO Hook should be passed in as an option.

		var conditions = getDeep(
			collection.reaction.attributes.conditions
			, collection.hierarchy.concat( [ '_conditions' ] )
		);

		switch ( method ) {

			case 'create':
				if ( typeof conditions !== 'undefined' ) {
					options.error( { message: todo } ); // TODO
					return;
				}

				setDeep(
					collection.reaction.attributes.conditions
					, collection.hierarchy.concat( [ '_conditions' ] )
					, collection.models
				);

				options.success();
				break;

			case 'read':
				if ( typeof conditions === 'undefined' ) {
					options.error( { message: todo } ); // TODO
					return;
				}

				options.success( conditions );
				break;

			default:
				options.error( { message: 'Conditions can only be read.' } );
		}
	}
});

module.exports = Conditions;