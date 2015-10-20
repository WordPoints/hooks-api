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
	Fields = wp.wordpoints.hooks.Fields,
	hooks = wp.wordpoints.hooks,
	$ = jQuery,
	Conditions;

Conditions = Extension.extend({

	defaults: {
		slug: 'conditions'
	},

	_byType: {},

	initialize: function () {

		this.listenTo( hooks, 'condition:model:validate', this.validateCondition );
		this.listenTo( hooks, 'condition:view:init', this.initCondition );

		this.controllers = new Backbone.Collection( [], { comparator: 'slug' } );
	},

	initReaction: function ( reaction ) {

		this.listenTo( reaction, 'validate', this.validateReactionConditions );

		reaction.model.conditions = new ConditionGroups(
			this.mapConditions( reaction.model.get( 'conditions' ) )
		);

		reaction.conditions = new ConditionsGroupsView( {
			collection: reaction.model.conditions,
			reaction: reaction
		});

		this.listenTo( reaction, 'render:fields', function () {
			reaction.$fields.append( reaction.conditions.render().$el );
		});
	},

	mapConditions: function ( conditions, hierarchy ) {

		var conditionGroups = [];

		hierarchy = hierarchy || [];

		_.each( conditions, function ( arg, slug ) {

			if ( slug === '_conditions' ) {

				conditionGroups.push( {
					id: this.getIdFromHierarchy( hierarchy ),
					hierarchy: _.clone( hierarchy ),
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

	getIdFromHierarchy: function ( hierarchy ) {
		return hierarchy.join( '.' );
	},

	initCondition: function ( condition ) {
		this.listenTo( condition, 'render:title', this.renderConditionTitle );
		this.listenTo( condition, 'render:settings', this.renderConditionSettings );
	},
	//
	//_getDataTypeFromCondition: function ( condition ) {
	//
	//},

	_getTypeFromCondition: function ( condition ) {

		var arg = condition.getArg(),
			dataType;

		if ( ! arg ) {
			return false;
		}

		switch ( arg.get( '_type' ) ) {

			case 'attr':
				dataType = arg.get( 'type' );
				break;

			case 'array':
				dataType = 'entity_array';
				break;
		}

		return this.getType( dataType, condition.get( 'type' ) );
	},

	renderConditionTitle: function ( condition ) {

		var conditionType = this._getTypeFromCondition( condition.model );

		if ( conditionType ) {
			condition.$title.text( conditionType.title );
		}
	},

	renderConditionSettings: function ( condition ) {

		// Build the fields based on the condition type.
		// Should this be a template (or maybe meta-template) supplied by the PHP?
		var conditionType = this._getTypeFromCondition( condition.model ),
			fields = '';

		var fieldNamePrefix = _.clone( condition.model.get( '_hierarchy' ) );
		fieldNamePrefix.unshift( 'conditions' );
		fieldNamePrefix.push(
			'_conditions'
			, condition.model.get( 'id' )
			, 'settings'
		);

		var fieldName = _.clone( fieldNamePrefix );

		fieldName.pop();
		fieldName.push( 'type' );

		fields += Fields.create(
			condition.model.reaction
			, fieldName
			, condition.model.get( 'type' )
			, { type: 'hidden' }
		);

		if ( conditionType ) {
			var controller = this.getController(
				conditionType.data_type
				, conditionType.slug
			);

			if ( controller ) {
				fields += controller.renderSettings( condition, fieldNamePrefix );
			}
		}

		condition.$settings.append( fields );
	},

	validateCondition: function ( condition, attributes, errors ) {

		var conditionType = this._getTypeFromCondition( condition );

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

	// Get all conditions for a certain attribute type.
	getByDataType: function ( dataType ) {

		return this.data.conditions[ dataType ];
	},

	getController: function ( dataType, slug ) {

		var controllers = this.controllers.get( dataType ),
			controller;

		if ( controllers ) {
			controller = controllers.get( slug );
		}

		if ( ! controller ) {
			controller = Conditions.Condition;
		}

		var type = this.getType( dataType, slug );

		if ( ! type ) {
			type = { slug: slug }
		}

		return new controller( type );
	},

	registerController: function ( dataType, slug, controller ) {

		var controllers = this.controllers.get( dataType );

		if ( ! controllers ) {
			return false;
		}

		controllers.add( controller );
	}

} );

module.exports = Conditions;
