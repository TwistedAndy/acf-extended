<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_field_flexible_content_state {

	/**
	 * construct
	 */
	function __construct() {

		// Hooks
		add_filter('acfe/flexible/defaults_field', [$this, 'defaults_field'], 8);
		add_action('acfe/flexible/render_field_settings', [$this, 'render_field_settings'], 8);

		add_filter('acfe/flexible/validate_field', [$this, 'validate_state']);
		add_filter('acfe/flexible/wrapper_attributes', [$this, 'wrapper_attributes'], 10, 2);
		add_filter('acfe/flexible/layouts/div', [$this, 'layout_div'], 10, 3);
		add_filter('acfe/flexible/layouts/placeholder', [$this, 'layout_placeholder'], 10, 3);
		add_filter('acfe/flexible/layouts/handle', [$this, 'layout_handle'], 10, 3);
		add_filter('acfe/flexible/layouts/icons', [$this, 'layout_icons'], 50, 3);

	}


	/**
	 * defaults_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function defaults_field($field) {

		$field['acfe_flexible_layouts_state'] = false;

		return $field;

	}


	/**
	 * render_field_settings
	 *
	 * @param $field
	 */
	function render_field_settings($field) {

		// Layouts: Force State
		acf_render_field_setting($field, [
			'label' => __('Default Layouts State', 'acfe'),
			'name' => 'acfe_flexible_layouts_state',
			'key' => 'acfe_flexible_layouts_state',
			'instructions' => __('Force layouts to be collapsed or opened', 'acfe'),
			'type' => 'radio',
			'layout' => 'horizontal',
			'default_value' => 'user',
			'placeholder' => __('Default (User preference)', 'acfe'),
			'choices' => [
				'user' => 'User preference',
				'collapse' => 'Collapsed',
				'open' => 'Opened',
				'force_open' => 'Always opened',
			],
			'conditional_logic' => [
				[
					[
						'field' => 'acfe_flexible_advanced',
						'operator' => '==',
						'value' => '1',
					],
					[
						'field' => 'acfe_flexible_modal_edit_enabled',
						'operator' => '!=',
						'value' => '1',
					]
				]
			]
		]);

	}


	/**
	 * validate_state
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function validate_state($field) {

		if (!acf_maybe_get($field, 'acfe_flexible_layouts_remove_collapse')) {
			return $field;
		}

		$field['acfe_flexible_layouts_state'] = 'force_open';

		return $field;

	}


	/**
	 * wrapper_attributes
	 *
	 * @param $wrapper
	 * @param $field
	 *
	 * @return mixed
	 */
	function wrapper_attributes($wrapper, $field) {

		// check setting
		if (($field['acfe_flexible_layouts_state'] !== 'open' && $field['acfe_flexible_layouts_state'] !== 'force_open') || $field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled']) {
			return $wrapper;
		}

		$wrapper['data-acfe-flexible-open'] = 1;

		return $wrapper;

	}


	/**
	 * layout_div
	 *
	 * @param $div
	 * @param $layout
	 * @param $field
	 *
	 * @return mixed
	 */
	function layout_div($div, $layout, $field) {

		if ($field['acfe_flexible_layouts_state'] !== 'collapse') {
			return $div;
		}

		// Already in class
		if (in_array('-collapsed', explode(' ', $div['class']))) {
			return $div;
		}

		$div['class'] .= ' -collapsed';

		return $div;

	}


	/**
	 * layout_placeholder
	 *
	 * @param $placeholder
	 * @param $layout
	 * @param $field
	 *
	 * @return mixed
	 */
	function layout_placeholder($placeholder, $layout, $field) {

		if ($field['acfe_flexible_layouts_state'] === 'collapse' || $field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled']) {
			return $placeholder;
		}

		// Already in class
		if (in_array('acf-hidden', explode(' ', $placeholder['class']))) {
			return $placeholder;
		}

		$placeholder['class'] .= ' acf-hidden';

		return $placeholder;

	}


	/**
	 * layout_handle
	 *
	 * @param $handle
	 * @param $layout
	 * @param $field
	 *
	 * @return mixed
	 */
	function layout_handle($handle, $layout, $field) {

		if ($field['acfe_flexible_layouts_state'] !== 'force_open') {
			return $handle;
		}

		// remove [data-name="collapse-layout"] so it doesn't trigger js click event
		acfe_unset($handle, 'data-name');

		return $handle;

	}


	/**
	 * layout_icons
	 *
	 * @param $icons
	 * @param $layout
	 * @param $field
	 *
	 * @return mixed
	 */
	function layout_icons($icons, $layout, $field) {

		if ($field['acfe_flexible_layouts_state'] !== 'force_open') {
			return $icons;
		}

		acfe_unset($icons, 'collapse');

		return $icons;

	}

}

acf_new_instance('acfe_field_flexible_content_state');