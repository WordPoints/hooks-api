/**
 * wp.wordpoints.hooks.reactor.Points
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.controller.Reactor
 */
var Reactor = wp.wordpoints.hooks.controller.Reactor,
	Fields = wp.wordpoints.hooks.Fields,
	Points;

Points = Reactor.extend({

	defaults: {
		slug: 'points',
		fields: {}
	},

	initReaction: function ( reaction ) {

		this.listenTo( reaction, 'render:settings', this.render );
		this.listenTo( reaction.model, 'validate', this.validate );
	},

	render: function ( $el, currentFirerSlug, reaction ) {

		var fields = '';

		_.forEach( this.get( 'fields' ), function ( field, name ) {

			fields += Fields.create(
				name,
				reaction.model.get( name ),
				field
			);
		});

		reaction.$settings.append( fields );
	},

	validate: function ( attributes, errors ) {

		Fields.validate(
			this.get( 'fields' )
			, attributes
			, errors
		);
	},

	filterReactionDefaults: function ( defaults, view ) {
		defaults.points_type = view.$reactionGroup.data( 'wordpoints-hooks-points-type' );
	}
});

module.exports = Points;
