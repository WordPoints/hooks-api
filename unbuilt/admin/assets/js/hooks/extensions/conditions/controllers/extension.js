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

		this.handlers = new Backbone.Collection( [], { comparator: 'slug' } );

		_.each( Conditions.condition, function ( Condition ) {

			this.registerHandler(
				new Condition( this.getType( Condition.prototype.defaults.slug ) )
			);

		}, this )
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
		this.listenTo( condition, 'render:settings', this.renderConditionSettings );
	},

	renderConditionSettings: function ( condition ) {

		// Build the fields based on the condition type.
		// Should this be a template (or maybe meta-template) supplied by the PHP?
		var conditionType = this.getType( condition.model.get( 'type' ) ),
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
			, conditionType.slug
			, { type: 'hidden' }
		);

		var handler = this.getHandler( conditionType.slug );

		if ( handler ) {
			fields += handler.renderSettings( condition, fieldNamePrefix );
		} else {
			fields += this.renderConditionFields(
				condition
				, conditionType.fields
				, fieldNamePrefix
			);
		}

		condition.$settings.append( fields );
	},

	renderConditionFields: function ( condition, fields, fieldNamePrefix ) {

		var fieldsHTML = '';

		_.each( fields, function ( setting, name ) {

			var fieldName = _.clone( fieldNamePrefix );

			fieldName.push( name );

			fieldsHTML += Fields.create(
				condition.model.reaction
				, fieldName
				, condition.model.attributes.settings[ name ]
				, setting
			);

		}, this );

		return fieldsHTML;
	},

	validateCondition: function ( condition, attributes, errors ) {

		Fields.validate(
			Conditions.getSettingsFields( condition.get( 'type' ) )
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

	getType: function ( type ) {

		if ( typeof this.data.conditions[ type ] === 'undefined' ) {
			return false;
		}

		return this.data.conditions[ type ];
	},

	// Get the settings fields for a given type of condition.
	getSettingsFields: function ( conditionType ) {

		var condition = this.getType( conditionType );

		if ( ! condition ) {
			return false;
		}

		return condition.fields;
	},

	// Get all conditions for a certain attribute type.
	getByAttrType: function ( type ) {

		if ( ! this._byType[ type ] ) {
			this._byType[ type ] = _.filter( this.data.conditions, function ( condition ) {
				return condition.types[ type ];
			} );
		}

		return this._byType[ type ];
	},

	getHandler: function ( type ) {
		return this.handlers.get( type );
	},

	registerHandler: function ( handler ) {
		this.handlers.add( handler );
	}

}, { condition: {} } );

module.exports = Conditions;
