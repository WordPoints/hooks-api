/* jshint node:true */
module.exports = function( grunt ) {
	var path = require( 'path' ),
		SOURCE_DIR = 'src/',
		UNBUILT_DIR = 'unbuilt/',
		browserifyConfig = {},
		jsManifests = grunt.file.expand( { cwd: UNBUILT_DIR }, ['**/*.manifest.js'] );

	// Load tasks.
	require( 'matchdep' ).filterDev( 'grunt-*' ).forEach( grunt.loadNpmTasks );

	// Load legacy utils
	//grunt.util = require('grunt-legacy-util');

	jsManifests.forEach( function ( manifest ) {

		var build;

		build = manifest.substring( 0, manifest.indexOf( '.manifest.js' ) );

		browserifyConfig[ build ] = { files : {} };
		browserifyConfig[ build ].files[ SOURCE_DIR + build + '.js' ] = [ UNBUILT_DIR + manifest ];
	} );

	// Project configuration.
	grunt.initConfig({
		browserify: browserifyConfig,
		sass: {
			all: {
				expand: true,
				cwd: UNBUILT_DIR,
				dest: SOURCE_DIR,
				ext: '.css',
				src: ['**/*.scss'],
				options: {
					outputStyle: 'expanded'
				}
			}
		},
		_watch: {
			autoloader: {
				files: [
					SOURCE_DIR + 'includes/classes/**/*.php',
					'!' + SOURCE_DIR + 'includes/classes/index.php'
				],
				tasks: ['autoloader'],
				options: {
					event: [ 'added', 'deleted' ]
				}
			},
			browserify: {
				files: [ UNBUILT_DIR + '**/*.js' ],
				tasks: ['browserify'],
				spawn: false
			},
			// This triggers an automatic reload of the `watch` task.
			config: {
				files: 'Gruntfile.js'
			},
			css: {
				files: [ UNBUILT_DIR + '**/*.scss' ],
				tasks: ['sass:all']
			}
		}
	});

	// Register tasks.

	// We use browserify's watch mechanism instead of the normal watch task, because
	// it is much faster. See https://core.trac.wordpress.org/changeset/31629
	grunt.renameTask( 'watch', '_watch' );

	grunt.registerTask( 'watch', function() {
		if ( ! this.args.length || this.args.indexOf( 'browserify' ) > -1 ) {
			grunt.config( 'browserify.options', {
				browserifyOptions: {
					debug: true
				},
				watchifyOptions: {
					verbose: true
				},
				watch: true
			} );

			grunt.task.run( 'browserify' );
		}

		grunt.task.run( '_' + this.nameArgs );
	} );

	grunt.registerTask( 'autoloader', 'Generate the autoloader backup file.', function() {

		var before = 'require_once( dirname( __FILE__ ) . \'',
			after = '\' );\n',
			includes,
			contents,
			classes_dir = SOURCE_DIR + 'includes/classes/',
			file = classes_dir + 'index.php',
			class_files = grunt.file.expand(
				{ cwd: classes_dir },
				[ '**/*.php', '!index.php' ]
			);

		// Implode all of the class files.
		includes = before;
		includes += class_files.join( after + before );
		includes += after;

		contents = grunt.file.read( file );
		contents = contents.replace(
			/auto-generated \{[^}]*}/,
			'auto-generated {\n' + includes + '// }'
		);

		grunt.file.write( file, contents );
	});
};
