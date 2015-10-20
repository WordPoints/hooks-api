(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
var hooks = wp.wordpoints.hooks;

// Models
hooks.model.Condition       = require( './conditions/models/condition.js' );
hooks.model.Conditions      = require( './conditions/models/conditions.js' );
hooks.model.ConditionGroup  = require( './conditions/models/condition-group.js' );
hooks.model.ConditionGroups = require( './conditions/models/condition-groups.js' );
hooks.model.ConditionType   = require( './conditions/models/condition-type.js' );
hooks.model.ConditionTypes  = require( './conditions/models/condition-types.js' );

// Views
hooks.view.Condition         = require( './conditions/views/condition.js' );
hooks.view.ConditionGroup    = require( './conditions/views/condition-group.js' );
hooks.view.ConditionSelector = require( './conditions/views/condition-selector.js' );
hooks.view.ConditionGroups   = require( './conditions/views/condition-groups.js' );

// Controllers.
hooks.extension.Conditions = require( './conditions/controllers/extension.js' );
hooks.extension.Conditions.Condition = require( './conditions/controllers/condition.js' );

var Conditions = new hooks.extension.Conditions();

// Conditions.
var Equals = require( './conditions/controllers/conditions/equals.js' );

Conditions.registerController( 'text', 'equals', Equals );
Conditions.registerController( 'entity_array', 'equals', Equals );
Conditions.registerController(
	'entity_array'
	, 'contains'
	, require( './conditions/controllers/conditions/entity-array-contains.js' )
);

// Register the extension.
hooks.Extensions.add( Conditions );

// EOF

},{"./conditions/controllers/condition.js":2,"./conditions/controllers/conditions/entity-array-contains.js":3,"./conditions/controllers/conditions/equals.js":4,"./conditions/controllers/extension.js":5,"./conditions/models/condition-group.js":6,"./conditions/models/condition-groups.js":7,"./conditions/models/condition-type.js":8,"./conditions/models/condition-types.js":9,"./conditions/models/condition.js":10,"./conditions/models/conditions.js":11,"./conditions/views/condition-group.js":12,"./conditions/views/condition-groups.js":13,"./conditions/views/condition-selector.js":14,"./conditions/views/condition.js":15}],2:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.extension.Conditions.condition.Condition
 *
 * @class
 * @augments Backbone.Model
 */

var Fields = wp.wordpoints.hooks.Fields,
	Condition;

Condition = Backbone.Model.extend({

	defaults: {
		slug: '',
		fields: []
	},

	idAttribute: 'slug',

	renderSettings: function ( condition, fieldNamePrefix ) {

		var fieldsHTML = '';

		_.each( this.get( 'fields' ), function ( setting, name ) {

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
	}
});

module.exports = Condition;

},{}],3:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.extension.Conditions.condition.EntityArrayContains
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.extension.Conditions.Condition
 */

var Condition = wp.wordpoints.hooks.extension.Conditions.Condition,
	ConditionGroups = wp.wordpoints.hooks.model.ConditionGroups,
	ConditionGroupsView = wp.wordpoints.hooks.view.ConditionGroups,
	ArgsCollection = wp.wordpoints.hooks.model.Args,
	Args = wp.wordpoints.hooks.Args,
	Fields = wp.wordpoints.hooks.Fields,
	Extensions = wp.wordpoints.hooks.Extensions,
	EntityArrayContains;

EntityArrayContains = Condition.extend({

	defaults: {
		slug: 'entity_array_contains'
	},

	renderSettings: function ( condition, fieldNamePrefix ) {

		var Conditions = Extensions.get( 'conditions' );

		// Render the main fields.
		var fields = this.__super__.constructor.renderSettings.apply(
			this
			, [ condition, this.get( 'fields' ), fieldNamePrefix ]
		);

		condition.$settings.append( fields );

		// Render view for sub-conditions.
		var preHierarchy = [ '_conditions', condition.model.id, 'settings', 'conditions' ];
		var conditionGroups = new ConditionGroups(
			Conditions.mapConditions(
				condition.model.get( 'settings' ).conditions
			)
		);

		var hierarchy = condition.model.get( '_hierarchy' ),
			arg;

		arg = Args.getEntity(
			Args.parseArgSlug( hierarchy[ hierarchy.length - 1 ] ).slug
		);

		var view = new ConditionGroupsView( {
			collection: conditionGroups,
			reaction: condition.reaction,
			args: new ArgsCollection( [ arg ] ),
			hierarchy: hierarchy.concat( preHierarchy )
		});

		condition.$settings.append( view.render().$el );

		return '';
	}
});

module.exports = EntityArrayContains;

},{}],4:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.extension.Conditions.condition.Equals
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.extension.Conditions.Condition
 */

var Condition = wp.wordpoints.hooks.extension.Conditions.Condition,
	Args = wp.wordpoints.hooks.Args,
	Equals;

Equals = Condition.extend({

	defaults: {
		slug: 'equals'
	},

	renderSettings: function ( condition, fieldNamePrefix ) {

		var fields = this.get( 'fields' ),
			arg = condition.getArg();

		// We render the `value` field differently based on the type of argument.
		// TODO select?
		if ( arg && arg.get( '_type' ) === 'attr' ) {

			fields = _.extend( {}, fields );

			var values = arg.get( 'values' );

			if ( values ) {

				fields.value = _.extend(
					{}
					, fields.value
					, { type: 'select', options: values }
				);

			} else {
				fields.value = _.extend( {}, fields.value, { type: arg.get( 'type' ) } );
			}
		}

		return this.__super__.constructor.renderSettings.apply(
			this
			, [ condition, fieldNamePrefix ]
		);
	}
});

module.exports = Equals;

},{}],5:[function(require,module,exports){
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

},{}],6:[function(require,module,exports){
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

		var conditions = this.get( 'conditions' );

		this.setIds( models, 0 );

		return conditions.reset.apply( conditions, arguments );
	},

	add: function ( models, options ) {

		var conditions = this.get( 'conditions' );

		this.setIds( models, this.getNextId() );

		return conditions.add.apply( conditions, arguments );
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

			model._hierarchy = this.get( 'preHierarchy' ).concat(
				this.get( 'hierarchy' )
			);

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
},{}],7:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.model.ConditionGroups
 *
 * @class
 * @augments Backbone.Collection
 */
var ConditionGroup = wp.wordpoints.hooks.model.ConditionGroup,
	ConditionGroups;

ConditionGroups = Backbone.Collection.extend({

	model: ConditionGroup
});

module.exports = ConditionGroups;

},{}],8:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.model.ConditionType
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.model.Base
 */
var Base = wp.wordpoints.hooks.model.Base,
	ConditionType;

ConditionType = Base.extend({
	idAttribute: 'slug'
});

module.exports = ConditionType;

},{}],9:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.model.ConditionTypes
 *
 * @class
 * @augments Backbone.Collection
 */
var ConditionType = wp.wordpoints.hooks.model.ConditionType,
	ConditionTypes;

ConditionTypes = Backbone.Collection.extend({

	model: ConditionType

});

module.exports = ConditionTypes;

},{}],10:[function(require,module,exports){
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

},{}],11:[function(require,module,exports){
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
},{}],12:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.ConditionGroup
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	Condition = wp.wordpoints.hooks.view.Condition,
	Args = wp.wordpoints.hooks.Args,
	$ = Backbone.$,
	template = wp.wordpoints.hooks.template,
	ConditionGroup;

ConditionGroup = Base.extend({

	className: 'condition-group',

	template: template( 'hook-reaction-condition-group' ),

	initialize: function () {

		this.listenTo( this.model, 'add', this.addOne );
		this.listenTo( this.model, 'reset', this.render );
		this.listenTo( this.model, 'remove', this.maybeHide );

		this.model.on( 'add', this.reaction.lockOpen, this.reaction );
		this.model.on( 'remove', this.reaction.lockOpen, this.reaction );
		this.model.on( 'reset', this.reaction.lockOpen, this.reaction );
	},

	render: function () {

		this.$el.html( this.template() );

		this.maybeHide();

		this.$( '.condition-group-title' ).text(
			Args.buildHierarchyHumanId(
				Args.getArgsFromHierarchy( this.model.get( 'hierarchy' ) )
			)
		);

		this.addAll();

		return this;
	},

	addOne: function ( condition ) {

		condition.reaction = this.reaction.model;
		condition.hierarchy = this.model.hierarchy;

		var view = new Condition( {
			el: $( '<div class="condition"></div>' ),
			model: condition,
			reaction: this.reaction
		} );

		this.$el.append( view.render().$el ).show();

		this.listenTo( condition, 'destroy', function () {
			this.model.get( 'conditions' ).remove( condition.id );
		} );
	},

	addAll: function () {
		this.model.get( 'conditions' ).each( this.addOne, this );
	},

	// Hide the group when it is empty.
	maybeHide: function () {

		if ( 0 === this.model.get( 'conditions' ).length ) {
			this.$el.hide();
		}
	}
});

module.exports = ConditionGroup;
},{}],13:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.ConditionGroups
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	ConditionGroupView = wp.wordpoints.hooks.view.ConditionGroup,
	Hook = wp.wordpoints.hooks.model.Reaction,
	ArgSelectors = wp.wordpoints.hooks.view.ArgSelectors,
	ConditionSelector = wp.wordpoints.hooks.view.ConditionSelector,
	Extensions = wp.wordpoints.hooks.Extensions,
	Args = wp.wordpoints.hooks.Args,
	template = wp.wordpoints.hooks.template,
	$cache = wp.wordpoints.$cache,
	ConditionGroups;

ConditionGroups = Base.extend({

	namespace: 'condition-groups',

	className: 'conditions',

	template: template( 'hook-condition-groups' ),

	hierarchy: [],

	events: {
		'click > .conditions-title .add-new':           'showArgSelector',
		'click > .add-condition-form .confirm-add-new': 'maybeAddNew',
		'click > .add-condition-form .cancel-add-new':  'cancelAddNew'
	},

	initialize: function ( options ) {

		if ( options.args ) {
			this.args = options.args;
		}

		if ( options.hierarchy ) {
			this.hierarchy = options.hierarchy;
		}

		this.listenTo( this.collection, 'add', this.addOne );
		this.listenTo( this.collection, 'reset', this.render );

		this.listenTo( this.reaction, 'cancel', this.cancelAddNew );

		this.collection.on( 'update', this.reaction.lockOpen, this.reaction );
		this.collection.on( 'reset', this.reaction.lockOpen, this.reaction );
	},

	render: function () {

		this.$el.html( this.template() );

		this.$c = $cache.call( this, this.$ );

		this.addAll();

		this.trigger( 'render', this );

		return this;
	},

	addAll: function () {
		this.collection.each( this.addOne, this )
	},

	addOne: function ( ConditionGroup ) {

		var view = new ConditionGroupView({
			model: ConditionGroup,
			reaction: this.reaction
		});

		this.$c( '> .condition-groups' ).append( view.render().$el );
	},

	showArgSelector: function () {

		this.$c( '> .conditions-title .add-new' ).attr( 'disabled', true );

		// TODO filter out unusable args.
		if ( ! this.ArgSelectors ) {

			var args = this.args;

			if ( ! args ) {
				args = Args.getEventArgs( this.reaction.model.get( 'event' ) );
			}

			this.ArgSelectors = new ArgSelectors({
				args: args,
				el: this.$( '.arg-selectors' )
			});

			this.listenTo( this.ArgSelectors, 'changing', this.maybeHideConditionSelector );
			this.listenTo( this.ArgSelectors, 'change', this.maybeShowConditionSelector );

			this.ArgSelectors.render();
		}

		this.$c( '> .add-condition-form' ).slideDown();
	},

	getArgType: function ( arg ) {

		var argType;

		if ( ! arg || ! arg.get ) {
			return;
		}

		switch ( arg.get( '_type' ) ) {

			case 'attr':
				argType = arg.get( 'type' );
				break;

			case 'array':
				argType = 'entity_array';
				break;

			case 'relationship':
				// We compress relationships to avoid redundancy.
				argType = this.getArgType( arg.getChild( arg.get( 'secondary' ) ) );
				break;

			default: return false;
		}

		return argType;
	},

	maybeShowConditionSelector: function ( argSelectors, arg ) {

		var argType = this.getArgType( arg );

		if ( ! argType ) {
			return;
		}

		var conditions = Extensions.get( 'conditions' ).getByDataType( argType );

		if ( ! this.ConditionSelector ) {

			this.ConditionSelector = new ConditionSelector({
				el: this.$( '.condition-selector' )
			});

			this.listenTo( this.ConditionSelector, 'change', this.conditionSelectionChange );

			this.$conditionSelector = this.ConditionSelector.$el;
		}

		this.ConditionSelector.collection.reset( _.toArray( conditions ) );

		this.$conditionSelector.show().find( 'select' ).focus();
	},

	maybeHideConditionSelector: function ( argSelectors, arg ) {

		if ( this.$conditionSelector && ! this.getArgType( arg ) ) {
			this.$conditionSelector.hide();
		}
	},

	cancelAddNew: function () {

		this.$c( '> .add-condition-form' ).slideUp();
		this.$c( '> .conditions-title .add-new' ).attr( 'disabled', false );
	},

	conditionSelectionChange: function ( selector, value ) {

		this.$c( '> .add-condition-form .confirm-add-new' )
			.attr( 'disabled', ! value );
	},

	maybeAddNew: function () {

		var selected = this.ConditionSelector.getSelected();

		if ( ! selected ) {
			return;
		}

		var hierarchy = this.ArgSelectors.getHierarchy(),
			id = Extensions.get( 'conditions' ).getIdFromHierarchy( hierarchy ),
			ConditionGroup = this.collection.get( id );

		if ( ! ConditionGroup ) {
			ConditionGroup = this.collection.add({
				id: id,
				hierarchy: hierarchy,
				preHierarchy: this.hierarchy
			});
		}

		ConditionGroup.add( { type: selected } );

		this.$c( '> .add-condition-form' ).hide();
		this.$c( '> .conditions-title .add-new' ).attr( 'disabled', false );

		// TODO highlight new condition?
	}
});

module.exports = ConditionGroups;

},{}],14:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.ConditionSelector
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	ConditionTypes = wp.wordpoints.hooks.model.ConditionTypes,
	template = wp.wordpoints.hooks.template,
	ConditionSelector;

ConditionSelector = Base.extend({

	namespace: 'condition-selector',

	template: template( 'hook-condition-selector' ),

	optionTemplate: template( 'hook-arg-option' ),

	events: {
		'change select': 'triggerChange'
	},

	initialize: function ( options ) {

		this.label = options.label;

		if ( ! this.collection ) {
			this.collection = new ConditionTypes({ comparator: 'title' });
		}

		this.listenTo( this.collection, 'update', this.render );
		this.listenTo( this.collection, 'reset', this.render );
	},

	render: function () {

		this.$el.html(
			this.template(
				{ label: this.label, name: this.cid + '_condition_selector' }
			)
		);

		this.$select = this.$( 'select' );

		this.collection.each( function ( condition ) {

			this.$select.append( this.optionTemplate( condition.attributes ) );

		}, this );

		this.trigger( 'render', this );

		return this;
	},

	triggerChange: function ( event ) {

		this.trigger( 'change', this, this.getSelected(), event );
	},

	getSelected: function () {

		var value = this.$select.val();

		if ( '0' === value ) {
			value = false;
		}

		return value;
	}
});

module.exports = ConditionSelector;

},{}],15:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.Condition
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	template = wp.wordpoints.hooks.template,
	Extensions = wp.wordpoints.hooks.Extensions,
	Condition;

Condition = Base.extend({

	namespace: 'condition',

	className: 'wordpoints-hook-condition',

	template: template( 'hook-reaction-condition' ),

	events: {
		'click .delete': 'destroy'
	},

	initialize: function () {

		this.listenTo( this.model, 'change', this.render );
		this.listenTo( this.model, 'destroy', this.remove );

		this.listenTo( this.model, 'invalid', this.model.reaction.showError );

		this.extension = Extensions.get( 'conditions' );
	},

	// Display the condition settings form.
	render: function () {

		this.$el.html( this.template() );

		this.$title = this.$( '.condition-title' );
		this.$settings = this.$( '.condition-settings' );

		this.trigger( 'render:title', this );
		this.trigger( 'render:settings', this );
		this.trigger( 'render', this );

		return this;
	},

	// Remove the item, destroy the model.
	destroy: function () {

		this.model.destroy();
	}
});

module.exports = Condition;

},{}]},{},[1]);
