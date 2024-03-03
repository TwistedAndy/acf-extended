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
		add_filter('acfe/flexible/layouts/label_prepend', [$this, 'label_prepend'], 10, 3);

		add_filter('acf/fields/flexible_content/layout_title', [$this, 'layout_title'], 0, 4);

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
	function render_layout_settings($flexible, $layout, $prefix) {

		if (!acf_maybe_get($flexible, 'acfe_flexible_layouts_thumbnails')) {
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
		if (!acf_maybe_get($field, 'acfe_flexible_layouts_thumbnails')) {
			return $wrapper;
		}

		$wrapper['data-acfe-flexible-thumbnails'] = 1;

		return $wrapper;

	}


	/**
	 * label_prepend
	 *
	 * @param $prepend
	 * @param $layout
	 * @param $field
	 *
	 * @return mixed|string
	 */
	function label_prepend($prepend, $layout, $field) {

		if (!acf_maybe_get($field, 'acfe_flexible_layouts_thumbnails')) {
			return $prepend;
		}

		$prepend = [
			'class' => 'acfe-flexible-layout-thumbnail',
		];

		// Modal disabled
		if (!$field['acfe_flexible_modal']['acfe_flexible_modal_enabled']) {
			$prepend['class'] .= ' acfe-flexible-layout-thumbnail-no-modal';
		}

		// Thumbnail
		$thumbnail = $layout['acfe_flexible_thumbnail'];
		$has_thumbnail = false;

		if (!empty($thumbnail)) {

			$has_thumbnail = true;
			$prepend['style'] = "background-image:url({$thumbnail});";

			// Attachment ID
			if (is_numeric($thumbnail)) {

				$has_thumbnail = false;

				if ($thumbnail_src = wp_get_attachment_url($thumbnail)) {
					$has_thumbnail = true;
					$prepend['style'] = "background-image:url({$thumbnail_src});";
				}

			}

		}

		// Thumbnail not found
		if (!$has_thumbnail) {
			$prepend['class'] .= ' acfe-flexible-layout-thumbnail-not-found';
		}

		return '<div ' . acf_esc_atts($prepend) . '></div>';

	}


	/**
	 * layout_title
	 *
	 * @param $title
	 * @param $field
	 * @param $layout
	 * @param $i
	 *
	 * @return array|string|string[]|null
	 */
	function layout_title($title, $field, $layout, $i) {

		return preg_replace('#<div class="acfe-flexible-layout-thumbnail(.*?)</div>#', '', $title);

	}

}

acf_new_instance('acfe_field_flexible_content_thumbnail');