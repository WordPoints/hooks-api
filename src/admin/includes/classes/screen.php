<?php

/**
 * Admin screen class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Bootstrap for displaying an administration screen.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Admin_Screen {

	/**
	 * The screen's ID.
	 *
	 * Defaults to the ID of the current screen.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $id;

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WP_Screen
	 */
	protected $wp_screen;

	protected $tabs;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->wp_screen = get_current_screen();
		$this->id = $this->wp_screen->id;

		$this->hooks();
	}

	/**
	 * Hook to actions and filters.
	 *
	 * @since 1.0.0
	 */
	function hooks() {

		/* Load the JavaScript needed for the settings screen. */
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( "admin_footer-{$this->id}", array( $this, 'footer_scripts' ) );
	}

	/**
	 * Enqueue needed scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {}

	/**
	 * Enqueue or print footer scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function footer_scripts() {}

	/**
	 * Perform actions while the screen is being loaded, before it is displayed.
	 *
	 * @since 1.0.0
	 */
	public function load() {}

	/**
	 * Display the screen.
	 *
	 * @since 1.0.0
	 */
	public function display() {

		?>

		<div class="wrap">

			<h1><?php echo esc_html( $this->get_title() ); ?></h1>

			<?php settings_errors(); ?>

			<?php wordpoints_admin_show_tabs( $this->tabs, false ); ?>

			<?php $this->display_content(); ?>

		</div><!-- .wrap -->

		<?php
	}

	/**
	 * Get the screen's title heading.
	 *
	 * @since 1.0.0
	 *
	 * @return string The screen title.
	 */
	abstract protected function get_title();

	/**
	 * Display the screen's contents.
	 *
	 * @since 1.0.0
	 */
	abstract protected function display_content();
}

// EOF
