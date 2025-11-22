<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_field_flexible_content_thumbnail {

	/**
	 * construct
	 */
	function __construct() {

		// Hooks
		add_filter('acfe/flexible/defaults_field', [$this, 'defaults_field'], 3);
		add_filter('acfe/flexible/defaults_layout', [$this, 'defaults_layout'], 3);

		add_action('acfe/flexible/render_field_settings', [$this, 'render_field_settings'], 3);
		add_action('acfe/flexible/render_layout_settings', [$this, 'render_layout_settings'], 25, 3);
		add_filter('acfe/flexible/validate_field', [$this, 'validate_thumbnail']);
		add_filter('acfe/flexible/wrapper_attributes', [$this, 'wrapper_attributes'], 10, 2);
		add_filter('acfe/flexible/layouts/select_atts', [$this, 'select_atts'], 10, 3);
		add_filter('acfe/flexible/layouts/select_label', [$this, 'select_label'], 20, 3);

	}


	/**
	 * defaults_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function defaults_field($field) {

		$field['acfe_flexible_layouts_thumbnails'] = false;

		return $field;

	}


	/**
	 * defaults_layout
	 *
	 * @param $layout
	 *
	 * @return mixed
	 */
	function defaults_layout($layout) {

		$layout['acfe_flexible_thumbnail'] = false;

		return $layout;

	}


	/**
	 * render_field_settings
	 *
	 * @param $field
	 */
	function render_field_settings($field) {

		acf_render_field_setting($field, [
			'label' => __('Layouts Thumbnails', 'acfe'),
			'name' => 'acfe_flexible_layouts_thumbnails',
			'key' => 'acfe_flexible_layouts_thumbnails',
			'instructions' => __('Set a thumbnail for each layouts', 'acfe') . '. ' . '<a href="https://www.acf-extended.com/features/fields/flexible-content/advanced-settings#layouts-thumbnails" target="_blank">' . __('See documentation', 'acfe') . '</a>',
			'type' => 'true_false',
			'message' => '',
			'default_value' => false,
			'ui' => true,
			'ui_on_text' => '',
			'ui_off_text' => '',
			'conditional_logic' => [
				[
					[
						'field' => 'acfe_flexible_advanced',
						'operator' => '==',
						'value' => '1',
					],
				]
			]
		]);

	}


	/**
	 * render_layout_settings
	 *
	 * @param $flexible
	 * @param $layout
	 * @param $prefix
	 */
	function render_layout_settings($field, $layout, $prefix) {

		if (!$field['acfe_flexible_layouts_thumbnails']) {
			return;
		}

		// Title
		echo '</li>';
		acf_render_field_wrap([
			'label' => __('Thumbnail', 'acfe'),
			'type' => 'hidden',
			'name' => 'acfe_flexible_thumbnail_label',
			'wrapper' => [
				'class' => 'acfe-flexible-field-setting',
			]
		], 'ul');
		echo '<li>';

		// Fields
		acf_render_field_wrap([
			'label' => false,
			'name' => 'acfe_flexible_thumbnail',
			'type' => 'image',
			'class' => '',
			'prefix' => $prefix,
			'value' => $layout['acfe_flexible_thumbnail'],
			'return_format' => 'array',
			'preview_size' => 'thumbnail',
			'library' => 'all',
		], 'ul');

	}


	/**
	 * validate_thumbnail
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function validate_thumbnail($field) {

		if (acfe_is_admin_screen()) {
			return $field;
		}

		foreach ($field['layouts'] as &$layout) {

			// Vars
			$thumbnail = $layout['acfe_flexible_thumbnail'];

			// Flexible Thumbnails
			$thumbnail = apply_filters("acfe/flexible/thumbnail", $thumbnail, $field, $layout);

			$layout['acfe_flexible_thumbnail'] = $thumbnail;

		}

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
		if (!$field['acfe_flexible_layouts_thumbnails']) {
			return $wrapper;
		}

		$wrapper['data-acfe-flexible-thumbnails'] = 1;

		return $wrapper;

	}


	/**
	 * select_atts
	 *
	 * @param $atts
	 * @param $layout
	 * @param $field
	 *
	 * @return mixed
	 */
	function select_atts($atts, $layout, $field) {

		// check setting
		if (!$field['acfe_flexible_layouts_thumbnails']) {
			return $atts;
		}

		// set thumbnail
		//$atts['data-thumbnail'] = 1;

		// return
		return $atts;

	}


	/**
	 * select_label
	 *
	 * @param $label
	 * @param $layout
	 * @param $field
	 *
	 * @return mixed|string
	 */
	function select_label($label, $layout, $field) {

		// check setting
		if (!$field['acfe_flexible_layouts_thumbnails']) {
			return $label;
		}

		// thumbnail
		$thumbnail = $this->get_thumbnail_url($layout);

		// prepend
		$prepend = [
			'class' => 'acfe-fc-layout-thumb',
		];

		// thumbnail not found
		if (!$thumbnail) {
			$prepend['class'] .= ' -not-found';
		}

		$prepend = '<div ' . acf_esc_atts($prepend) . '>';

		if ($thumbnail) {
			$prepend .= '<img src="' . esc_url($thumbnail) . '" />';
		}

		$prepend .= '</div>';

		return $prepend . $label;

	}


	/**
	 * get_thumbnail_url
	 *
	 * @param $layout
	 *
	 * @return false|mixed|string
	 */
	function get_thumbnail_url($layout) {

		// check thumbnail
		$thumbnail_url = $layout['acfe_flexible_thumbnail'];
		if (empty($thumbnail_url)) {
			return false;
		}

		// attachment id
		if (is_numeric($thumbnail_url)) {

			// get attachment url
			$thumbnail_url = wp_get_attachment_url($thumbnail_url);
			if (empty($thumbnail_url)) {
				return false;
			}

		}

		// return url
		return $thumbnail_url;

	}

}

acf_new_instance('acfe_field_flexible_content_thumbnail');