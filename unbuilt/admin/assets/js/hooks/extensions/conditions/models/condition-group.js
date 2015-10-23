/**
 * wp.wordpoints.hooks.model.ConditionGroup
 *
 * @class
 * @augments Backbone.Collection
 */
var Conditions = wp.wordpoints.hooks.model.Conditions,
	getDeep = wp.wordpoints.hooks.util.getDeep,
	setDeep = wp.wordpoints.hooks.util.setDeep,
	ConditionGroup;

ConditionGroup = Backbone.Model.extend({

	defaults: function () {
		return {
			id: '',
			hierarchy: [],
			preHierarchy: [],
			conditions: new Conditions,
			reaction: null
		};
	},

	initialize: function ( attributes ) {

		// Set up even proxying.
		//_.each( [ 'add', 'reset', 'remove', 'update' ], function ( event ) {

			this.listenTo( this.attributes.conditions, 'all', this.trigger );

		//}, this );

		// Add the conditions to the collection.
		if ( attributes._conditions ) {
			this.reset( attributes._conditions );
		}
	},

	// Make sure that the model ids are properly set. Conditions are identified
	// by the index of the array in which they are stored. We copy the keys to
	// the id attributes of the models.
	reset: function ( models, options ) {

		options = options || {};
		options.group = this;

		var conditions = this.get( 'conditions' );

		this.setIds( models, 0 );

		return conditions.reset.call( conditions, models, options );
	},

	add: function ( models, options ) {

		options = options || {};
		options.group = this;

		var conditions = this.get( 'conditions' );

		this.setIds( models, this.getNextId() );

		return conditions.add.call( conditions, models, options );
	},

	getNextId: function() {

		var conditions = this.get( 'conditions' );

		if ( !conditions.length ) {
			return 0;
		}

		return parseInt( conditions.sort().last().get( 'id' ), 10 ) + 1;
	},

	setIds: function ( models, startId ) {

		if ( ! models ) {
			return;
		}

		_.each( _.isArray( models ) ? models : [ models ], function ( model, id ) {

			if ( startId !== 0 ) {
				model.id = startId++;
			} else {
				model.id = id;
			}

			// This will be set when an object is converted to a model, but if it is
			// a model already, we need to set it here.
			if ( model instanceof Backbone.Model ) {
				model.group = this;
			}

		}, this );
	},

	sync: function ( method, collection, options ) {

		// TODO sync back to collection instead, and let the collection sync with reaction.
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
				options.error( { message: 'Condition groups can only be read.' } );
		}
	}
});

module.exports = ConditionGroup;