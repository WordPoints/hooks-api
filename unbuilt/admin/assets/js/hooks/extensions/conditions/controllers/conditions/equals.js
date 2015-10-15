/**
 * wp.wordpoints.hooks.extension.Conditions.condition.Equals
 *
 * @class
 * @augments Backbone.Model
 * @augments wp.wordpoints.hooks.extension.Conditions.Condition
 */

var Condition = wp.wordpoints.hooks.extension.Conditions.Condition,
	Args = wp.wordpoints.hooks.Args,
	Extensions = wp.wordpoints.hooks.Extensions,
	Equals;

Equals = Condition.extend({

	defaults: {
		slug: 'equals'
	},

	renderSettings: function ( condition, fieldNamePrefix ) {

		var fields = this.get( 'fields' ),
			hierarchy = _.clone( condition.model.get( '_hierarchy' ) ),
			arg;

		arg = Args.getChild(
			hierarchy[ hierarchy.length - 2 ]
			, hierarchy[ hierarchy.length - 1 ]
		);

		// We render the `value` field differently based on the type of argument.
		if ( arg.get( '_type' ) === 'attr' ) {

			fields = _.extend( {}, fields );

			var values = arg.get( 'values' );

			if ( values ) {

				fields.value = _.extend(
					{}
					, fields.value
					, { type: 'select', options: values }
				);

			} else if ( arg.get( 'type' ) === 'int' ) {
				fields.value = _.extend( {}, fields.value, { type: 'number' } );
			}
		}

		return Extensions.get( 'conditions' ).renderConditionFields(
			condition
			, fields
			, fieldNamePrefix
		);
	}
});

module.exports = Equals;
