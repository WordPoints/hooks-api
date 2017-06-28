<?php

/**
 * Main file of the Hooks API module.
 *
 * ---------------------------------------------------------------------------------|
 * Copyright 2015  J.D. Grimes  (email : jdg@codesymphony.co)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or later, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * ---------------------------------------------------------------------------------|
 *
 * @package wordpoints-hooks-api
 * @version 1.0.0
 * @author  J.D. Grimes <jdg@codesymphony.co>
 * @license GPLv2+
 */

WordPoints_Modules::register(
	'
		Module Name: Hooks API
		Author:      J.D. Grimes
		Author URI:  http://codesymphony.co/
		Module URI:  https://github.com/WordPoints/hooks-api/
		Version:     1.0.0
		License:     GPLv2+
		Description: Provides a new API for hooks.
	'
	, __FILE__
);

/**
 * The module's functions.
 *
 * @since 1.0.0
 */
require_once( dirname( __FILE__ ) . '/includes/functions.php' );

/**
 * The module's actions and filters.
 *
 * @since 1.0.0
 */
require_once( dirname( __FILE__ ) . '/includes/actions.php' );

// Register the classes to autoload.
WordPoints_Class_Autoloader::register_dir(
	dirname( __FILE__ ) . '/includes/classes'
	, 'WordPoints_'
);

// EOF
