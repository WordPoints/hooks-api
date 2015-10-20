/**
 * wp.wordpoints.hooks.view.ConditionGroups
 *
 * @class
 * @augments Backbone.View
 * @augments wp.wordpoints.hooks.view.Base
 */
var Base = wp.wordpoints.hooks.view.Base,
	ConditionGroupView = wp.wordpoints.hooks.view.ConditionGroup,
	Hook = wp.wordpoints.hooks.model.Reaction,
	ArgSelectors = wp.wordpoints.hooks.view.ArgSelectors,
	ConditionSelector = wp.wordpoints.hooks.view.ConditionSelector,
	Extensions = wp.wordpoints.hooks.Extensions,
	Args = wp.wordpoints.hooks.Args,
	template = wp.wordpoints.hooks.template,
	$cache = wp.wordpoints.$cache,
	ConditionGroups;

ConditionGroups = Base.extend({

	namespace: 'condition-groups',

	className: 'conditions',

	template: template( 'hook-condition-groups' ),

	hierarchy: [],

	events: {
		'click > .conditions-title .add-new':           'showArgSelector',
		'click > .add-condition-form .confirm-add-new': 'maybeAddNew',
		'click > .add-condition-form .cancel-add-new':  'cancelAddNew'
	},

	initialize: function ( options ) {

		if ( options.args ) {
			this.args = options.args;
		}

		if ( options.hierarchy ) {
			this.hierarchy = options.hierarchy;
		}

		this.listenTo( this.collection, 'add', this.addOne );
		this.listenTo( this.collection, 'reset', this.render );

		this.listenTo( this.reaction, 'cancel', this.cancelAddNew );

		this.collection.on( 'update', this.reaction.lockOpen, this.reaction );
		this.collection.on( 'reset', this.reaction.lockOpen, this.reaction );
	},

	render: function () {

		this.$el.html( this.template() );

		this.$c = $cache.call( this, this.$ );

		this.addAll();

		this.trigger( 'render', this );

		return this;
	},

	addAll: function () {
		this.collection.each( this.addOne, this )
	},

	addOne: function ( ConditionGroup ) {

		var view = new ConditionGroupView({
			model: ConditionGroup,
			reaction: this.reaction
		});

		this.$c( '> .condition-groups' ).append( view.render().$el );
	},

	showArgSelector: function () {

		this.$c( '> .conditions-title .add-new' ).attr( 'disabled', true );

		// TODO filter out unusable args.
		if ( ! this.ArgSelectors ) {

			var args = this.args;

			if ( ! args ) {
				args = Args.getEventArgs( this.reaction.model.get( 'event' ) );
			}

			this.ArgSelectors = new ArgSelectors({
				args: args,
				el: this.$( '.arg-selectors' )
			});

			this.listenTo( this.ArgSelectors, 'changing', this.maybeHideConditionSelector );
			this.listenTo( this.ArgSelectors, 'change', this.maybeShowConditionSelector );

			this.ArgSelectors.render();
		}

		this.$c( '> .add-condition-form' ).slideDown();
	},

	getArgType: function ( arg ) {

		var argType;

		if ( ! arg || ! arg.get ) {
			return;
		}

		switch ( arg.get( '_type' ) ) {

			case 'attr':
				argType = arg.get( 'type' );
				break;

			case 'array':
				argType = 'entity_array';
				break;

			case 'relationship':
				// We compress relationships to avoid redundancy.
				argType = this.getArgType( arg.getChild( arg.get( 'secondary' ) ) );
				break;

			default: return false;
		}

		return argType;
	},

	maybeShowConditionSelector: function ( argSelectors, arg ) {

		var argType = this.getArgType( arg );

		if ( ! argType ) {
			return;
		}

		var conditions = Extensions.get( 'conditions' ).getByDataType( argType );

		if ( ! this.ConditionSelector ) {

			this.ConditionSelector = new ConditionSelector({
				el: this.$( '.condition-selector' )
			});

			this.listenTo( this.ConditionSelector, 'change', this.conditionSelectionChange );

			this.$conditionSelector = this.ConditionSelector.$el;
		}

		this.ConditionSelector.collection.reset( _.toArray( conditions ) );

		this.$conditionSelector.show().find( 'select' ).focus();
	},

	maybeHideConditionSelector: function ( argSelectors, arg ) {

		if ( this.$conditionSelector && ! this.getArgType( arg ) ) {
			this.$conditionSelector.hide();
		}
	},

	cancelAddNew: function () {

		this.$c( '> .add-condition-form' ).slideUp();
		this.$c( '> .conditions-title .add-new' ).attr( 'disabled', false );
	},

	conditionSelectionChange: function ( selector, value ) {

		this.$c( '> .add-condition-form .confirm-add-new' )
			.attr( 'disabled', ! value );
	},

	maybeAddNew: function () {

		var selected = this.ConditionSelector.getSelected();

		if ( ! selected ) {
			return;
		}

		var hierarchy = this.ArgSelectors.getHierarchy(),
			id = Extensions.get( 'conditions' ).getIdFromHierarchy( hierarchy ),
			ConditionGroup = this.collection.get( id );

		if ( ! ConditionGroup ) {
			ConditionGroup = this.collection.add({
				id: id,
				hierarchy: hierarchy,
				preHierarchy: this.hierarchy
			});
		}

		ConditionGroup.add( { type: selected } );

		this.$c( '> .add-condition-form' ).hide();
		this.$c( '> .conditions-title .add-new' ).attr( 'disabled', false );

		// TODO highlight new condition?
	}
});

module.exports = ConditionGroups;
