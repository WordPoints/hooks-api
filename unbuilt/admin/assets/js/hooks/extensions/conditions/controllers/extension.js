/**
 * wp.wordpoints.hooks.extension.Conditions
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.controller.Extension
 *
 *
 */
var Extension = wp.wordpoints.hooks.controller.Extension,
	ConditionGroups = wp.wordpoints.hooks.model.ConditionGroups,
	ConditionsGroupsView = wp.wordpoints.hooks.view.ConditionGroups,
	getDeep = wp.wordpoints.hooks.util.getDeep,
	Conditions;

Conditions = Extension.extend({

	defaults: {
		slug: 'conditions'
	},

	initialize: function () {

		this.controllers = new Backbone.Collection( [], { comparator: 'slug' } );
		this.dataType = Backbone.Model.extend( { idAttribute: 'slug' } );
	},

	initReaction: function ( reaction ) {

		this.listenTo( reaction, 'validate', this.validateReactionConditions );

		reaction.conditions = {};
		reaction.model.conditions = {};

		var conditions = reaction.model.get( 'conditions' );

		if ( ! conditions ) {
			conditions = {};
		}

		_.each( reaction.Reactor.get( 'firers' ), function ( firerSlug ) {

			var conditionGroups = conditions[ firerSlug ];

			if ( ! conditionGroups ) {
				conditionGroups = [];
			}

			reaction.model.conditions[ firerSlug ] = new ConditionGroups(
				this.mapConditions( conditionGroups )
			);

			reaction.conditions[ firerSlug ] = new ConditionsGroupsView( {
				collection: reaction.model.conditions[ firerSlug ],
				reaction: reaction,
				hierarchy: [ firerSlug ]
			});

		}, this );

		this.listenTo( reaction, 'render:fields', function ( $el, currentFirerSlug ) {

			var conditions = reaction.conditions[ currentFirerSlug ];

			if ( ! conditions ) {
				return;
			}

			$el.append( conditions.render().$el );
		});
	},

	mapConditions: function ( conditions, hierarchy, preHierarchy ) {

		var conditionGroups = [];

		hierarchy = hierarchy || [];
		preHierarchy = preHierarchy || [];

		_.each( conditions, function ( arg, slug ) {

			if ( slug === '_conditions' ) {

				conditionGroups.push( {
					id: this.getIdFromHierarchy( hierarchy ),
					hierarchy: _.clone( hierarchy ),
					preHierarchy: preHierarchy,
					_conditions: arg
				} );

			} else {

				hierarchy.push( slug );

				conditionGroups = conditionGroups.concat(
					this.mapConditions( arg, hierarchy )
				);

				hierarchy.pop();
			}

		}, this );

		return conditionGroups;
	},

	// TODO move to condition groups?
	getIdFromHierarchy: function ( hierarchy ) {
		return hierarchy.join( '.' );
	},

	getDataTypeFromArg: function ( arg ) {

		var argType = arg.get( '_type' );

		switch ( argType ) {

			case 'attr':
				return arg.get( 'data_type' );

			case 'array':
				return 'entity_array';

			default:
				return argType;
		}
	},

	validateReactionConditions: function ( attributes, errors ) {

		// TODO
		this.model.validate(
			getDeep( attributes, this.fieldNamePrefix )
			, errors
		);
	},

	getType: function ( dataType, slug ) {

		if ( typeof this.data.conditions[ dataType ] === 'undefined' ) {
			return false;
		}

		if ( typeof this.data.conditions[ dataType ][ slug ] === 'undefined' ) {
			return false;
		}

		return this.data.conditions[ dataType ][ slug ];
	},

	// Get all conditions for a certain data type.
	getByDataType: function ( dataType ) {

		return this.data.conditions[ dataType ];
	},

	getController: function ( dataTypeSlug, slug ) {

		var dataType = this.controllers.get( dataTypeSlug ),
			controller;

		if ( dataType ) {
			controller = dataType.get( 'controllers' )[ slug ];
		}

		if ( ! controller ) {
			controller = Conditions.Condition;
		}

		var type = this.getType( dataTypeSlug, slug );

		if ( ! type ) {
			type = { slug: slug };
		}

		return new controller( type );
	},

	registerController: function ( dataTypeSlug, slug, controller ) {

		var dataType = this.controllers.get( dataTypeSlug );

		if ( ! dataType ) {
			dataType = new this.dataType({
				slug: dataTypeSlug,
				controllers: {}
			});

			this.controllers.add( dataType );
		}

		dataType.get( 'controllers' )[ slug ] = controller;
	}

} );

module.exports = Conditions;
