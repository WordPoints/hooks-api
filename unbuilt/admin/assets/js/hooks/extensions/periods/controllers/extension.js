/**
 * wp.wordpoints.hooks.extension.Periods
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.controller.Extension
 */
var Extension = wp.wordpoints.hooks.controller.Extension,
	Fields = wp.wordpoints.hooks.Fields,
	template = wp.wordpoints.hooks.template,
	$ = Backbone.$,
	Periods;

Periods = Extension.extend({

	defaults: {
		slug: 'periods',
		events: [ 'user_visit' ]
	},

	template: template( 'hook-periods' ),

	initReaction: function ( reaction ) {

		if ( ! this.showForReaction( reaction ) ) {
			return;
		}

		this.listenTo( reaction, 'render:fields', function () {

			var $periods = $( '<div></div>' ).html( this.template() );

			var name = [ 'periods', 0, 'length' ];

			$periods.find( '.periods' ).html(
				Fields.create(
					reaction.model
					, name
					, reaction.model.get( name )
					, {
						type: 'select',
						options: this.data.periods,
						label: this.data.l10n.label
					}
				)
			);

			reaction.$fields.append( $periods.html() );
		});
	},

	showForReaction: function ( reaction ) {
		return _.contains( this.get( 'events' ), reaction.model.get( 'event' ) );
	}

} );

module.exports = Periods;
