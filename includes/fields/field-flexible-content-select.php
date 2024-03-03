<?php

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('acfe_field_flexible_content_select')) {
	return;
}

class acfe_field_flexible_content_select {

	/**
	 * construct
	 */
	function __construct() {

		// Hooks
		add_filter('acfe/flexible/defaults_field', [$this, 'defaults_field'], 10);
		add_filter('acfe/flexible/defaults_layout', [$this, 'defaults_layout'], 10);

		add_action('acfe/flexible/render_field_settings', [$this, 'render_field_settings'], 10);
		add_action('acfe/flexible/render_layout_settings', [$this, 'render_layout_settings'], 10, 3);

		add_filter('acfe/flexible/wrapper_attributes', [$this, 'wrapper_attributes'], 10, 2);
		add_filter('acfe/flexible/layouts/label_atts', [$this, 'label_atts'], 10, 3);

	}


	/**
	 * defaults_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function defaults_field($field) {

		$field['acfe_flexible_modal'] = [
			'acfe_flexible_modal_enabled' => false,
			'acfe_flexible_modal_title' => false,
			'acfe_flexible_modal_size' => 'full',
			'acfe_flexible_modal_col' => '4',
			'acfe_flexible_modal_categories' => false,
		];

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

		$layout['acfe_flexible_category'] = false;

		return $layout;

	}


	/**
	 * render_field_settings
	 *
	 * @param $field
	 */
	function render_field_settings($field) {

		acf_render_field_setting($field, [
			'label' => __('Selection Modal', 'acfe'),
			'name' => 'acfe_flexible_modal',
			'key' => 'acfe_flexible_modal',
			'instructions' => __('Select layouts in a modal', 'acfe') . '. ' . '<a href="https://www.acf-extended.com/features/fields/flexible-content/modal-settings#selection-modal" target="_blank">' . __('See documentation', 'acfe') . '</a>',
			'type' => 'group',
			'layout' => 'block',
			'sub_fields' => [
				[
					'label' => '',
					'name' => 'acfe_flexible_modal_enabled',
					'key' => 'acfe_flexible_modal_enabled',
					'type' => 'true_false',
					'instructions' => '',
					'required' => false,
					'wrapper' => [
						'class' => 'acfe_width_auto',
						'id' => '',
					],
					'message' => '',
					'default_value' => false,
					'ui' => true,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'conditional_logic' => false,
				],
				[
					'label' => '',
					'name' => 'acfe_flexible_modal_title',
					'key' => 'acfe_flexible_modal_title',
					'type' => 'text',
					'prepend' => __('Title', 'acfe'),
					'placeholder' => 'Add Row',
					'instructions' => false,
					'required' => false,
					'wrapper' => [
						'width' => '25',
						'class' => '',
						'id' => '',
					],
					'conditional_logic' => [
						[
							[
								'field' => 'acfe_flexible_modal_enabled',
								'operator' => '==',
								'value' => '1',
							]
						]
					]
				],
				[
					'label' => '',
					'name' => 'acfe_flexible_modal_size',
					'key' => 'acfe_flexible_modal_size',
					'type' => 'select',
					'prepend' => '',
					'instructions' => false,
					'required' => false,
					'choices' => [
						'small' => 'Small',
						'medium' => 'Medium',
						'large' => 'Large',
						'xlarge' => 'Extra Large',
						'full' => 'Full',
					],
					'default_value' => 'full',
					'wrapper' => [
						'width' => '25',
						'class' => '',
						'id' => '',
						'data-acfe-prepend' => 'Size',
					],
					'conditional_logic' => [
						[
							[
								'field' => 'acfe_flexible_modal_enabled',
								'operator' => '==',
								'value' => '1',
							]
						]
					]
				],
				[
					'label' => '',
					'name' => 'acfe_flexible_modal_col',
					'key' => 'acfe_flexible_modal_col',
					'type' => 'select',
					'prepend' => '',
					'instructions' => false,
					'required' => false,
					'choices' => [
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					],
					'default_value' => '4',
					'wrapper' => [
						'width' => '15',
						'class' => '',
						'id' => '',
						'data-acfe-prepend' => 'Cols',
					],
					'conditional_logic' => [
						[
							[
								'field' => 'acfe_flexible_modal_enabled',
								'operator' => '==',
								'value' => '1',
							]
						]
					]
				],
				[
					'label' => '',
					'name' => 'acfe_flexible_modal_categories',
					'key' => 'acfe_flexible_modal_categories',
					'type' => 'true_false',
					'message' => __('Categories', 'acfe'),
					'instructions' => false,
					'required' => false,
					'wrapper' => [
						'width' => '15',
						'class' => '',
						'id' => '',
					],
					'conditional_logic' => [
						[
							[
								'field' => 'acfe_flexible_modal_enabled',
								'operator' => '==',
								'value' => '1',
							]
						]
					]
				],
			],
			'conditional_logic' => [
				[
					[
						'field' => 'acfe_flexible_advanced',
						'operator' => '==',
						'value' => '1',
					],
				]
			],
			'wrapper' => [
				'class' => 'acfe-field-setting-flex'
			]
		]);

	}


	/**
	 * render_layout_settings
	 *
	 * @param $field
	 * @param $layout
	 * @param $prefix
	 */
	function render_layout_settings($field, $layout, $prefix) {

		if (!$field['acfe_flexible_modal']['acfe_flexible_modal_categories']) {
			return;
		}

		echo '</li>';

		echo '<li>';
		acf_render_field_wrap([
			'prepend' => __('Category', 'acfe'),
			'name' => 'acfe_flexible_category',
			'type' => 'select',
			'ui' => 1,
			'multiple' => 1,
			'allow_custom' => 1,
			'class' => 'acf-fc-meta-name',
			'prefix' => $prefix,
			'value' => $layout['acfe_flexible_category'],
			'placeholder' => __('Enter value', 'acfe'),
			'wrapper' => [
				'data-acfe-prepend' => 'Categories',
			],
		], 'ul');

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

		if (!$field['acfe_flexible_modal']['acfe_flexible_modal_enabled']) {
			return $wrapper;
		}

		$wrapper['data-acfe-flexible-modal'] = 1;
		$wrapper['data-acfe-flexible-modal-col'] = $field['acfe_flexible_modal']['acfe_flexible_modal_col'];
		$wrapper['data-acfe-flexible-modal-size'] = $field['acfe_flexible_modal']['acfe_flexible_modal_size'];

		// Title
		if (!empty($field['acfe_flexible_modal']['acfe_flexible_modal_title'])) {
			$wrapper['data-acfe-flexible-modal-title'] = $field['acfe_flexible_modal']['acfe_flexible_modal_title'];
		}

		return $wrapper;

	}


	/**
	 * label_atts
	 *
	 * @param $atts
	 * @param $layout
	 * @param $field
	 *
	 * @return mixed
	 */
	function label_atts($atts, $layout, $field) {

		// Category
		if (!$field['acfe_flexible_modal']['acfe_flexible_modal_categories']) {
			return $atts;
		}

		$categories = $layout['acfe_flexible_category'];

		// Compatibility
		if (is_string($categories) && !empty($categories)) {
			$categories = explode('|', $categories);
			$categories = array_map('trim', $categories);
		}

		$atts['data-acfe-flexible-category'] = acf_get_array($categories);

		return $atts;

	}

}

acf_new_instance('acfe_field_flexible_content_select');