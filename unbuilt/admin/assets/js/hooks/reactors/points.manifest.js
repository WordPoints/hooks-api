var hooks = wp.wordpoints.hooks,
	data = wp.wordpoints.hooks.view.data.reactors.points;

hooks.on( 'init', function () {

	hooks.Reactors.add( new hooks.reactor.Points( data ) );
});

hooks.reactor.Points = require( './points.js' );
