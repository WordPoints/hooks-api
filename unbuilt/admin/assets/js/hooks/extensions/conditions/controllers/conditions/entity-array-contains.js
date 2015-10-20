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
