<?php

/**
 * Hook UI script data provider interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by objects that want to provide data for the hooks UI scripts.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_UI_Script_Data_ProviderI {

	/**
	 * Get the data the scripts need for the UI.
	 *
	 * @since 1.0.0
	 *
	 * @return array Any data that needs to be present for the scripts in the UI.
	 */
	public function get_ui_script_data();
}

// EOF
