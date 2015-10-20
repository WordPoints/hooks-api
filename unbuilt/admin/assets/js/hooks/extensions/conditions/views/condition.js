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
