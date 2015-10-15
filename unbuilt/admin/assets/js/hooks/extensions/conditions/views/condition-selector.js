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
