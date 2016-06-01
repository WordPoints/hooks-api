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

		this.data = extensions[ this.id ];

		this.__child__.initialize.apply( this, arguments );
	},

	/**
	 * @since 1.0.0
	 * @abstract
	 */
	initReaction: emptyFunction( 'initReaction' )

}, { extend: extend } );

module.exports = Extension;
