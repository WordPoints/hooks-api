(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.Args
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	ArgsCollection = wp.wordpoints.hooks.model.Args,
	l10n = wp.wordpoints.hooks.view.l10n,
	Args;

Args = Backbone.Model.extend({

	defaults: {
		events: {},
		entities: {}
	},

	getEventArg: function ( eventSlug, slug ) {

		var event = this.get( 'events' )[ eventSlug ];

		if ( ! event || ! event.args || ! event.args[ slug ] ) {
			return false;
		}

		var entity = this.getEntity( slug );

		_.extend( entity.attributes, event.args[ slug ] );

		return entity;
	},

	getEventArgs: function ( eventSlug ) {

		var argsCollection = new ArgsCollection(),
			event = this.get( 'events' )[ eventSlug ];

		if ( typeof event === 'undefined' || typeof event.args === 'undefined' ) {
			return argsCollection;
		}

		_.each( event.args, function ( arg ) {

			var entity = this.getEntity( arg.slug );

			_.extend( entity.attributes, arg );

			argsCollection.add( entity );

		}, this );

		return argsCollection;
	},

	parseArgSlug: function ( slug ) {

		var isArray = false,
			isAlias = false;

		if ( '{}' === slug.slice( -2 ) ) {
			isArray = true;
			slug = slug.slice( 0, -2 );
		}

		var parts = slug.split( ':', 2 );

		if ( parts[1] ) {
			isAlias = parts[0];
			slug = parts[1];
		}

		return { slug: slug, isArray: isArray, isAlias: isAlias };
	},

	_getEntityData: function ( slug ) {

		var parsed = this.parseArgSlug( slug ),
			entity = this.get( 'entities' )[ parsed.slug ];

		if ( ! entity ) {
			return false;
		}

		//if ( parsed.isAlias ) {
			entity = _.extend( {}, entity, { slug: slug, _canonical: parsed.slug } );
		//}

		return entity
	},

	getEntity: function ( slug ) {

		if ( slug instanceof Entity ) {
			return slug;
		}

		var entity = this._getEntityData( slug );

		if ( ! entity ) {
			return false;
		}

		//if ( parsed.isArray ) {
		//	entity = new Array({ entity_slug: parsed.slug });
		//} else {
			entity = new Entity( entity );
		//}

		return entity;
	},

	getChildren: function ( slug ) {

		var entity = this._getEntityData( slug );

		if ( ! entity ) {
			return false;
		}

		var children = new ArgsCollection();

		_.each( entity.children, function ( child ) {

			var argType = Args.type[ child._type ];

			if ( ! argType ) {
				return;
			}

			children.add( new argType( child ) );

		}, this );

		return children;
	},

	getChild: function ( entitySlug, childSlug ) {

		var entity = this._getEntityData( entitySlug );

		if ( ! entity ) {
			return false;
		}

		var child = entity.children[ childSlug ];

		if ( ! child ) {
			return false;
		}

		var argType = Args.type[ child._type ];

		if ( ! argType ) {
			return;
		}

		return new argType( child );
	},

	/**
	 *
	 * @param hierarchy
	 * @param eventSlug Optional event for context.
	 * @returns {*}
	 */
	getArgsFromHierarchy: function ( hierarchy, eventSlug ) {

		var args = [], parent, arg, slug;

		for ( var i = 0; i < hierarchy.length; i++ ) {

			slug = hierarchy[ i ];

			if ( parent ) {
				if ( ! parent.getChild ) {
					return false;
				}

				arg = parent.getChild( slug );
			} else {
				if ( eventSlug && this.parseArgSlug( slug ).isAlias ) {
					arg = this.getEventArg( eventSlug, slug );
				} else {
					arg = this.getEntity( slug );
				}
			}

			if ( ! arg ) {
				return false;
			}

			parent = arg;

			args.push( arg );
		}

		return args;
	},

	getHierarchiesMatching: function ( options ) {

		var args = [], hierarchies = [], hierarchy = [];

		options = _.extend( {}, options );

		if ( options.event ) {
			options.top = this.getEventArgs( options.event ).models;
		}

		if ( options.top ) {
			args = _.isArray( options.top ) ? options.top : [ options.top ];
		} else {
			args = _.keys( this.get( 'entities' ) );
		}

		var matcher = this._hierarchyMatcher( options, hierarchy, hierarchies );

		if ( ! matcher ) {
			return hierarchies;
		}

		_.each( args, function ( slug ) {

			var arg = this.getEntity( slug );

			this._getHierarchiesMatching(
				arg
				, hierarchy
				, hierarchies
				, matcher
			);


		}, this );

		return hierarchies;
	},

	_hierarchyMatcher: function ( options, hierarchy, hierarchies ) {

		var filters = [], i;

		if ( options.end ) {
			filters.push( {
				method: _.isFunction( options.end ) ? 'filter' : 'where',
				arg: options.end
			});
		}

		if ( ! filters ) {
			return false;
		}

		return function ( subArgs, hierachy ) {

			var matching = [], matches;

			if ( subArgs instanceof Backbone.Collection ) {
				subArgs = subArgs.models;
			} else {
				subArgs = _.clone( subArgs );
			}

			_.each( subArgs, function ( match ) {
				match.hierachy = hierachy;
				matching.push( match );
			});

			matching = new ArgsCollection( matching );

			for ( i = 0; i < filters.length; i++ ) {

				matches = matching[ filters[ i ].method ]( filters[ i ].arg );

				if ( _.isEmpty( matches ) ) {
					return;
				}

				matching.reset( matches );
			}

			matching.each( function ( match ) {
				hierarchy.push( match );
				hierarchies.push( _.clone( hierarchy ) );
				hierarchy.pop();
			});
		};
	},

	_getHierarchiesMatching: function ( arg, hierarchy, hierarchies, addMatching ) {

		var subArgs;

		// Check the top-level args as well.
		if ( hierarchy.length === 0 ) {
			addMatching( [ arg ], hierarchy );
		}

		if ( arg instanceof Parent ) {
			subArgs = arg.getChildren();
		}

		if ( ! subArgs ) {
			return;
		}

		hierarchy.push( arg );

		addMatching( subArgs, hierarchy );

		subArgs.each( function ( subArg ) {

			this._getHierarchiesMatching(
				subArg
				, hierarchy
				, hierarchies
				, addMatching
			);

		}, this );

		hierarchy.pop();
	},

	buildHierarchyHumanId: function ( hierarchy ) {

		var humanId = '';

		_.each( hierarchy, function ( arg) {

			if ( ! arg || 'current' === arg.get( 'slug' ) ) {
				return;
			}

			var title = arg.get( 'title' );

			if ( ! title ) {
				return;
			}

			if ( '' !== humanId ) {
				// We compress relationships.
				if ( arg instanceof Entity ) {
					return;
				}

				humanId += l10n.separator;
			}

			humanId += title;
		});

		return humanId;
	}

}, { type: {} });

var Arg = Backbone.Model.extend({

	type: 'arg',

	idAttribute: 'slug',

	defaults: function () {
		return { _type: this.type }
	}
});

var Parent = Arg.extend({

	/**
	 * @abstract
	 */
	getChild: function ( slug ) {},

	/**
	 * @abstract
	 */
	getChildren: function () {}
});

var Entity = Parent.extend({
	type: 'entity',

	getChild: function ( slug ) {
		return wp.wordpoints.hooks.Args.getChild( this.get( 'slug' ), slug );
	},

	getChildren: function () {
		return wp.wordpoints.hooks.Args.getChildren( this.get( 'slug' ) );
	}
});

var Relationship = Parent.extend({
	type: 'relationship',

	parseArgSlug: function ( slug ) {

		var isArray = false;

		if ( '{}' === slug.slice( -2 ) ) {
			isArray = true;
			slug = slug.slice( 0, -2 );
		}

		return { isArray: isArray, slug: slug };
	},

	getChild: function ( slug ) {

		var child;

		if ( slug !== this.get( 'secondary' ) ) {
			return false
		}

		var parsed = this.parseArgSlug( slug );

		if ( parsed.isArray ) {
			child = new Array({ entity_slug: parsed.slug });
		} else {
			child = wp.wordpoints.hooks.Args.getEntity( parsed.slug );
		}

		return child;
	},

	getChildren: function () {
		return new ArgsCollection( [ this.getChild( this.get( 'secondary' ) ) ] );
	}
});

var Array = Arg.extend( {
	type: 'array',

	initialize: function () {
		this.set( 'slug', this.get( 'entity_slug' ) + '{}' );
	}
});

var Attr = Arg.extend( {
	type: 'attr'
});

Args.type.entity = Entity;
Args.type.relationship = Relationship;
Args.type.array = Array;
Args.type.attr = Attr;

module.exports = Args;

},{}],2:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.controller.Extension
 *
 * @since 1.0.0
 *
 * @class
 * @augments Backbone.Model
 *
 *
 */
var hooks = wp.wordpoints.hooks,
	extensions = hooks.view.data.extensions,
	extend = hooks.util.extend,
	emptyFunction = hooks.util.emptyFunction,
	Extension;

Extension = Backbone.Model.extend({

	/**
	 * @since 1.0.0
	 */
	idAttribute: 'slug',

	/**
	 * @since 1.0.0
	 */
	initialize: function () {

		this.listenTo( hooks, 'reaction:view:init', this.initReaction );

		this.data = extensions[ this.id ]; // TODO

		this.__child__.initialize.apply( this, arguments );
	},

	/**
	 * @since 1.0.0
	 * @abstract
	 */
	initReaction: emptyFunction( 'initReaction' )

}, { extend: extend } );

module.exports = Extension;

},{}],3:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.controller.Extensions
 *
 * @class
 * @augments Backbone.Collection
 */
var Extension = wp.wordpoints.hooks.controller.Extension,
	Extensions;

Extensions = Backbone.Collection.extend({
	model: Extension
});

module.exports = Extensions;
},{}],4:[function(require,module,exports){
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
		}
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
					data[ key ] = []
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

},{}],5:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.controller.Reactor
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.controller.Extension
 *
 *
 */
var Extension = wp.wordpoints.hooks.controller.Extension,
	hooks = wp.wordpoints.hooks,
	emptyFunction = hooks.util.emptyFunction,
	Reactor;

Reactor = Extension.extend({

	/**
	 * @since 1.0.0
	 */
	initialize: function () {

		this.listenTo( hooks, 'reactions:view:init', this.listenToDefaults );

		this.__child__.initialize.apply( this, arguments );
	},

	/**
	 * @since 1.0.0
	 */
	listenToDefaults: function ( reactionsView ) {

		this.listenTo(
			reactionsView
			, 'hook-reaction-defaults'
			, this.filterReactionDefaults
		);
	},

	/**
	 * @since 1.0.0
	 * @abstract
	 */
	filterReactionDefaults: emptyFunction( 'filterReactionDefaults' )
});

module.exports = Reactor;

},{}],6:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.controller.Reactors
 *
 * @class
 * @augments Backbone.Collection
 * @augments wp.wordpoints.hooks.controller.Extensions
 */
var Extensions = wp.wordpoints.hooks.controller.Extensions,
	Reactor = wp.wordpoints.hooks.controller.Reactor,
	Reactors;

Reactors = Extensions.extend({
	model: Reactor
});

module.exports = Reactors;
},{}],7:[function(require,module,exports){
var hooks = wp.wordpoints.hooks,
	$ = jQuery,
	data;

// Load the application once the DOM is ready.
$( function () {

	// Let all parts of the app know that we're about to start.
	hooks.trigger( 'init' );

	// We kick things off by creating the **Groups**.
	// Instead of generating new elements, bind to the existing skeletons of
	// the groups already present in the HTML.
	$( '.wordpoints-hook-reaction-group-container' ).each( function () {

		var $this = $( this ),
			event;

		event = $this
			.find( '.wordpoints-hook-reaction-group' )
				.data( 'wordpoints-hooks-hook-event' );

		console.log(event);
		console.log(data.reactions[ event ]);

		new hooks.view.Reactions( {
			el: $this,
			model: new hooks.model.Reactions( data.reactions[ event ] )
		} );
	} );
});

// Link any localized strings.
hooks.view.l10n = window.WordPointsHooksAdminL10n || {};

// Link any settings.
data = hooks.view.data = window.WordPointsHooksAdminData || {};

// Load the controllers.
hooks.controller.Fields     = require( './controllers/fields.js' );
hooks.controller.Extensions = require( './controllers/extensions.js' );
hooks.controller.Extension  = require( './controllers/extension.js' );
hooks.controller.Reactors   = require( './controllers/reactors.js' );
hooks.controller.Reactor    = require( './controllers/reactor.js' );
hooks.controller.Args       = require( './controllers/args.js' );

// Start them up here so that we can begin using them.
hooks.Fields     = new hooks.controller.Fields( { fields: data.fields } );
hooks.Reactors   = new hooks.controller.Reactors();
hooks.Extensions = new hooks.controller.Extensions();
hooks.Args       = new hooks.controller.Args({ events: data.events, entities: data.entities });

// Load the views.
hooks.view.Base              = require( './views/base.js' );
hooks.view.Reaction          = require( './views/reaction.js' );
hooks.view.Reactions         = require( './views/reactions.js' );
hooks.view.ArgOption         = require( './views/arg-option.js' );
hooks.view.ArgSelector       = require( './views/arg-selector.js' );
hooks.view.ArgSelectors      = require( './views/arg-selectors.js' );
hooks.view.ArgSelector2      = require( './views/arg-selector2.js' );

},{"./controllers/args.js":1,"./controllers/extension.js":2,"./controllers/extensions.js":3,"./controllers/fields.js":4,"./controllers/reactor.js":5,"./controllers/reactors.js":6,"./views/arg-option.js":8,"./views/arg-selector.js":9,"./views/arg-selector2.js":10,"./views/arg-selectors.js":11,"./views/base.js":12,"./views/reaction.js":13,"./views/reactions.js":14}],8:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.ArgOption
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	template = wp.wordpoints.hooks.template,
	ArgOption;

ArgOption = Base.extend({

	namespace: 'arg-option',

	tagName: 'option',

	template: template( 'hook-arg-option' ),

	render: function () {

		this.$el = $( template( this.model ) );

		return this;
	}
});

module.exports = ArgOption;

},{}],9:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.ArgSelector
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	template = wp.wordpoints.hooks.template,
	ArgSelector;

ArgSelector = Base.extend({

	namespace: 'arg-selector',

	template: template( 'hook-arg-selector' ),

	optionTemplate: template( 'hook-arg-option' ),

	events: {
		'change select': 'triggerChange'
	},

	initialize: function ( options ) {

		this.label = options.label;
		this.number = options.number;

		this.listenTo( this.collection, 'update', this.render );
		this.listenTo( this.collection, 'reset', this.render );
	},

	render: function () {

		this.$el.html(
			this.template( { label: this.label, name: this.cid + '_' + this.number } )
		);

		this.$select = this.$( 'select' );

		this.collection.each( function ( arg ) {

			this.$select.append( this.optionTemplate( arg.attributes ) );

		}, this );

		this.trigger( 'render', this );

		return this;
	},

	triggerChange: function ( event ) {

		var value = this.$select.val();

		if ( '0' === value ) {
			value = false;
		}

		this.trigger( 'change', this, value, event );
	}
});

module.exports = ArgSelector;

},{}],10:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.ArgSelectors
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	Args = wp.wordpoints.hooks.Args,
	template = wp.wordpoints.hooks.template,
	$ = Backbone.$,
	ArgSelector2;

ArgSelector2 = Base.extend({

	namespace: 'arg-selector2',

	tagName: 'div',

	template: template( 'hook-arg-selector' ),

	events: {
		'change select': 'triggerChange'
	},

	initialize: function ( options ) {
		if ( options.hierarchies ) {
			this.hierarchies = options.hierarchies;
		}
	},

	render: function () {
		
		this.$el.append(
			this.template( { label: this.label, name: this.cid } )
		);

		this.$select = this.$( 'select' );

		_.each( this.hierarchies, function ( hierarchy, index ) {

			var $option = $( '<option></option>' )
				.val( index )
				.text( Args.buildHierarchyHumanId( hierarchy ) );

			this.$select.append( $option );

		}, this );

		this.trigger( 'render', this );

		return this;
	},

	triggerChange: function ( event ) {

		var index = this.$select.val(),
			hierarchy, arg;

		// Don't do anything if the value hasn't really changed.
		if ( index === this.currentIndex ) {
			return;
		}

		this.currentIndex = index;

		if ( index !== false ) {
			hierarchy = this.hierarchies[ index ];

			if ( ! hierarchy ) {
				return;
			}

			arg = hierarchy[ hierarchy.length - 1 ];
		}

		this.trigger( 'change', this, arg, index, event );
	},

	getHierarchy: function () {

		var hierarchy = [];

		_.each( this.getHierarchyArgs(), function ( arg ) {
			hierarchy.push( arg.get( 'slug' ) );
		});

		return hierarchy;
	},

	getHierarchyArgs: function () {
		return this.hierarchies[ this.currentIndex ];
	}
});

module.exports = ArgSelector2;

},{}],11:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.ArgSelectors
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	ArgSelector = wp.wordpoints.hooks.view.ArgSelector,
	Args = wp.wordpoints.hooks.Args,
	$ = Backbone.$,
	ArgSelectors;

ArgSelectors = Base.extend({

	namespace: 'arg-selectors',

	tagName: 'div',

	initialize: function ( options ) {
		if ( options.args ) {
			this.args = options.args;
		}

		this.hierarchy = [];
	},

	render: function () {

		var args = this.args, arg;

		if ( args.length === 1 ) {
			arg = args.at( 0 );
			this.hierarchy.push( { arg: arg } );
			args = arg.getChildren();
		}

		this.addSelector( args );

		return this;
	},

	addSelector: function ( args ) {

		var selector = new ArgSelector({
			collection: args,
			number: this.hierarchy.length
		});

		selector.render();

		this.$el.append( selector.$el );

		selector.$( 'select' ).focus();

		this.hierarchy.push( { selector: selector } );

		this.listenTo( selector, 'change', this.update );
	},

	update: function ( selector, value ) {

		var id = selector.number,
			arg;

		// Don't do anything if the value hasn't really changed.
		if ( this.hierarchy[ id ].arg && value === this.hierarchy[ id ].arg.get( 'slug' ) ) {
			return;
		}

		if ( value ) {
			arg = selector.collection.get( value );

			if ( ! arg ) {
				return;
			}
		}

		this.trigger( 'changing', this, arg, value );

		if ( value ) {

			this.hierarchy[ id ].arg = arg;

			this.updateChildren( id );

		} else {

			// Nothing is selected, hide all child selectors.
			this.hideChildren( id );

			delete this.hierarchy[ id ].arg;
		}

		this.trigger( 'change', this, arg, value );
	},

	updateChildren: function ( id ) {

		var arg = this.hierarchy[ id ].arg, children;

		if ( arg.getChildren ) {

			children = arg.getChildren();

			// We compress relationships so we have just Post » Author instead of
			// Post » Author » User.
			if ( children.length && arg.get( '_type' ) === 'relationship' ) {
				var child = children.at( 0 );

				if ( ! child.getChildren ) {
					this.hideChildren( id );
					return;
				}

				children = child.getChildren();
			}

			// Hide any grandchild selectors.
			this.hideChildren( id + 1 );

			// Create the child selector if it does not exist.
			if ( ! this.hierarchy[ id + 1 ] ) {
				this.addSelector( children );
			} else {
				this.hierarchy[ id + 1 ].selector.collection.reset( children.models );
				this.hierarchy[ id + 1 ].selector.$el.show().find( 'select' ).focus();
			}

		} else {

			this.hideChildren( id );
		}
	},

	hideChildren: function ( id ) {
		_.each( this.hierarchy.slice( id + 1 ), function ( level ) {
			level.selector.$el.hide();
			delete level.arg;
		});
	},

	getHierarchy: function () {

		var hierarchy = [];

		_.each( this.hierarchy, function ( level ) {

			if ( ! level.arg ) {
				return;
			}

			hierarchy.push( level.arg.get( 'slug' ) );

			// Relationships are compressed, so we have to expand them here.
			if ( level.arg.get( '_type' ) === 'relationship' ) {
				hierarchy.push( level.arg.get( 'secondary' ) );
			}
		});

		return hierarchy;
	}
});

module.exports = ArgSelectors;

},{}],12:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.Base
 *
 * @class
 * @augments Backbone.View
 */
var hooks = wp.wordpoints.hooks,
	extend = hooks.util.extend,
	Base;

// Add a base view so we can have a standardized view bootstrap for this app.
Base = Backbone.View.extend( {

	// First, we let each view specify its own namespace, so we can use it as
	// a prefix for any standard events we want to fire.
	namespace: '_base',

	// We have an initialization bootstrap. Below we'll set things up so that
	// this gets called even when an extending view specifies an `initialize`
	// function.
	initialize: function ( options ) {

		// The first thing we do is to allow for a namespace to be passed in
		// as an option when the view is constructed, instead of forcing it
		// to be part of the prototype only.
		if ( typeof options.namespace !== 'undefined' ) {
			this.namespace = options.namespace;
		}

		if ( typeof options.reaction !== 'undefined' ) {
			this.reaction = options.reaction;
		}

		// Once things are set up, we call the extending view's `initialize`
		// function. It is mapped to `_initialize` on the current object.
		this.__child__.initialize.apply( this, arguments );

		// Finally, we trigger an action to let the whole app know we just
		// created this view.
		hooks.trigger( this.namespace + ':view:init', this );
	}

}, { extend: extend } );

module.exports = Base;

},{}],13:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.Base
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	Fields = wp.wordpoints.hooks.Fields,
	fields = wp.wordpoints.hooks.view.data.fields,
	Reactors = wp.wordpoints.hooks.Reactors,
	Args = wp.wordpoints.hooks.Args,
	$ = Backbone.$,
	l10n = wp.wordpoints.hooks.view.l10n,
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

		this.trigger( 'render:settings', this );
		this.trigger( 'render:fields', this );

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
			}
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

		var field = Fields.create(
			this.model
			, 'target'
			, value
			, {
				type: 'select',
				options: options,
				label: this.Reactor.get( 'target_label' )
			}
		);

		this.$target.html( field );
	},

	setReactor: function () {
		this.Reactor = Reactors.get( this.model.get( 'reactor' ) );
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

		if ( $target.val() != this.model.get( attrSlug ) ) {
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
	showError: function ( event, response, options ) {

		var message, $error, errors = [];

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

				fieldName = Fields.getFieldName( this.model, error.field );

				// When a field is specified, we try to locate it.
				$field = this.$(
					'[name="' + fieldName.replace( /[^a-z0-9-_\[\]\{\}]/gi, '' ) + '"]'
				);

				if ( 0 === $field.length ) {

					// However, there are times when the error is for a field set
					// and not a single field. In that case, we try to find the
					// fields in that set.
					$field = this.$(
						'[name^="' + fieldName.replace( /[^a-z0-9-_\[\]\{\}]/gi, '' ) + '"]'
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
					$errors.append( $( '<p></p>' ).text( error ) )
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
},{}],14:[function(require,module,exports){
/**
 * wp.wordpoints.hooks.view.Hooks
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	ReactionView = wp.wordpoints.hooks.view.Reaction,
	Reaction = wp.wordpoints.hooks.model.Reaction,
	Reactions;

Reactions = Base.extend({

	namespace: 'reactions',

	// Delegated events for creating new reactions.
	events: {
		'click .add-reaction': 'initAddReaction'
	},

	// At initialization we bind to the relevant events on the `Reactions`
	// collection, when items are added or changed. Kick things off by
	// loading any preexisting hooks from *the database*.
	initialize: function() {

		this.$reactionGroup = this.$( '.wordpoints-hook-reaction-group' );
		this.$addReaction   = this.$( '.add-reaction' );
		this.$events = this.$( '.wordpoints-hook-events' );

		if ( this.$events.length !== 0 ) {
			// Check how many different events this group supports. If it is only
			// one, we can hide the event selector.
			if ( 2 === this.$events.children( 'option' ).length ) {
				this.$events.prop( 'selectedIndex', 1 ).hide();
			}
		}

		// Make sure that the add reaction button isn't disabled, because sometimes
		// the browser will automatically disable it, e.g., if it was disabled
		// and the page was refreshed.
		this.$addReaction.prop( 'disabled', false );

		this.listenTo( this.model, 'add', this.addOne );
		this.listenTo( this.model, 'reset', this.addAll );
		this.listenTo( this.model, 'cancel-add-new', this.cancelAddReaction );

		this.addAll();
	},

	// Add a single reaction to the group by creating a view for it, and appending
	// its element to the group. If this is a new reaction we enter edit mode from
	// and lock the view open until it is saved.
	addOne: function( reaction ) {

		var view = new ReactionView( { model: reaction } ),
			element = view.render().el;

		if ( '' === reaction.get( 'description' ) ) {
			view.edit();
			view.lockOpen();
			view.$el.addClass( 'new' );
		}

		// Append the element to the group.
		this.$reactionGroup.append( element );
	},

	// Add all items in the **Reactions** collection at once.
	addAll: function() {
		this.model.each( this.addOne, this );

		this.$( '.spinner-overlay' ).fadeOut();
	},

	getReactionDefaults: function () {

		var defaults = {};

		if ( this.$events.length !== 0 ) {

			// First, be sure that an event was selected.
			var event = this.$events.val();

			if ( '0' === event ) {
				// Show an error.
			}

			defaults.event = event;
			defaults.nonce = this.$events
				.find(
				'option[value="' + event.replace( /[^a-z0-9-_]/gi, '' ) + '"]'
			)
				.data( 'nonce' );

		} else {

			defaults.event = this.$reactionGroup.data( 'wordpoints-hooks-hook-event' );
			defaults.nonce = this.$reactionGroup.data( 'wordpoints-hooks-create-nonce' );
		}

		defaults.reactor = this.$reactionGroup.data( 'wordpoints-hooks-reactor' );

		this.trigger( 'hook-reaction-defaults', defaults, this );

		return defaults;
	},

	// Show the form for a new reaction.
	initAddReaction: function () {

		data = this.getReactionDefaults();

		this.$addReaction.prop( 'disabled', true );

		this.model.add( [ new Reaction( data ) ] );
	},

	// When a new reaction is removed, re-enable the add reaction button.
	cancelAddReaction: function () {
		this.$addReaction.prop( 'disabled', false );
	}
});

module.exports = Reactions;

},{}]},{},[7]);
