<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

interface WordPoints_Hook_Reaction_SettingI {
	public function __construct( $slug );
	public function get_type();
	public function get_label();
	public function is_required();
	public function get( WordPoints_Hook_ReactionI $reaction );
	public function validate( $value, WordPoints_Hook_Reaction_Validator $validator );
	public function save( $value, WordPoints_Hook_ReactionI $reaction, $is_new );
}

class WordPoints_Hook_Reaction_Setting implements WordPoints_Hook_Reaction_SettingI {

	protected $slug;
	protected $type = 'hidden';
	protected $required = true;

	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_label() {
		return '';
	}

	public function is_required() {
		return $this->required;
	}

	public function get( WordPoints_Hook_ReactionI $reaction ) {
		return $reaction->get_meta( $this->slug );
	}

	public function validate( $value, WordPoints_Hook_Reaction_Validator $validator ) {

		if ( $this->is_required() && '' === trim( $value ) ) {

			$validator->add_error(
				sprintf(
					__( '%s cannot be empty.', 'wordpoints' )
					, $this->get_label()
				)
				, $this->slug
			);
		}

		return $value;
	}

	public function save( $value, WordPoints_Hook_ReactionI $reaction, $is_new ) {
		$reaction->update_meta( $this->slug, $value );
	}
}
//
//class WordPoints_Hook_Reaction_Setting_Description
//	extends WordPoints_Hook_Reaction_Setting {
//
//	protected $type = 'text';
//
//	public function get_label() {
//		return __( 'Description', 'wordpoints' );
//	}
//}

class WordPoints_Hook_Settings extends WordPoints_Class_Registry {

	protected $settings = array();

	public function get_settings_fields() {

		$fields = array();

		foreach ( $this->settings as $slug => $class ) {

			/** @var WordPoints_Hook_Reaction_SettingI $setting */
			$setting = new $class( $slug );

			$fields[ $slug ] = array(
				'type' => $setting->get_type(),
				'label' => $setting->get_label(),
				'required' => $setting->is_required(),
			);
		}

		return $fields;
	}

	public function save_settings( $reaction, $settings, $is_new ) {

		foreach ( $this->settings as $slug => $class ) {

			/** @var WordPoints_Hook_Reaction_SettingI $setting */
			$setting = new $class( $slug );

			$value = isset( $settings[ $slug ] ) ? $settings[ $slug ] : null;

			$setting->save( $value, $reaction, $is_new );
		}
	}

	public function validate_settings( $settings, $validator ) {

		foreach ( $this->settings as $slug => $class ) {

			/** @var WordPoints_Hook_Reaction_SettingI $setting */
			$setting = new $class( $slug );

			$value = isset( $settings[ $slug ] ) ? $settings[ $slug ] : null;

			$settings[ $slug ] = $setting->validate( $value, $validator );
		}

		return $settings;
	}
}

/**
 *
 *
 * @since 1.
 *
 * @param WordPoints_Hooks $hooks
 */
function wordpoints_hook_settings_app( $hooks ) {
	$hooks->sub_apps->register( 'settings', 'WordPoints_Hook_Settings' );
}
//add_action( 'wordpoints_hooks_init', 'wordpoints_hook_settings_app' );

/**
 *
 *
 * @since 1.
 *
 * @param WordPoints_Hook_Settings $settings
 */
function wordpoints_register_hook_settings( $settings ) {

	add_action( 'wordpoints_hook_reaction_save', array( $settings, 'save_settings' ), 10, 3 );
	add_filter( 'wordpoints_hook_reaction_validate', array( $settings, 'validate_settings' ), 10, 2 );

	$settings->register( 'description', 'WordPoints_Hook_Reaction_Setting_Description' );
}
//add_action( 'wordpoints_hook_settings_init', 'wordpoints_register_hook_settings' );

// EOF
