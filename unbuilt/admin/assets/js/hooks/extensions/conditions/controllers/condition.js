/**
 * wp.wordpoints.hooks.extension.Conditions.condition.Condition
 *
 * @class
 * @augments Backbone.Model
 */

var Condition = Backbone.Model.extend({

	defaults: {
		slug: '',
		fields: []
	},

	idAttribute: 'slug',

	renderSettings: function ( condition, fieldNamePrefix ) {}
});

module.exports = Condition;
