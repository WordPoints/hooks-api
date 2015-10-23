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
			arg = condition.model.getArg();

		// We render the `value` field differently based on the type of argument.
		if ( arg && arg.get( '_type' ) === 'entity' ) {
			arg = arg.getChild( arg.get( 'id_field' ) );
		}

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

		this.set( 'fields', fields );

		return this.constructor.__super__.renderSettings.apply(
			this
			, [ condition, fieldNamePrefix ]
		);
	}
});

module.exports = Equals;
