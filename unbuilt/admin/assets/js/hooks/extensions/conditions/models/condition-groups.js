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
