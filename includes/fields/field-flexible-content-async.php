<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_field_flexible_content_async {

	/**
	 * construct
	 */
	function __construct() {

		// Hooks
		add_filter('acfe/flexible/defaults_field', [$this, 'defaults_field'], 5);
		add_action('acfe/flexible/render_field_settings', [$this, 'render_field_settings'], 5);
		add_filter('acfe/flexible/wrapper_attributes', [$this, 'wrapper_attributes'], 10, 2);
		add_filter('acfe/flexible/layouts/model', [$this, 'layout_model'], 10, 3);

		// Ajax
		add_action('wp_ajax_acfe/flexible/models', [$this, 'ajax_layout_model']);
		add_action('wp_ajax_nopriv_acfe/flexible/models', [$this, 'ajax_layout_model']);

	}


	/**
	 * defaults_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function defaults_field($field) {

		$field['acfe_flexible_async'] = [];
		return $field;

	}


	/**
	 * render_field_settings
	 *
	 * @param $field
	 */
	function render_field_settings($field) {

		acf_render_field_setting($field, [
			'label' => __('Asynchronous Settings', 'acfe'),
			'name' => 'acfe_flexible_async',
			'key' => 'acfe_flexible_async',
			'instructions' => '<a href="https://www.acf-extended.com/features/fields/flexible-content/advanced-settings#async-settings" target="_blank">' . __('See documentation', 'acfe') . '</a>',
			'type' => 'checkbox',
			'default_value' => '',
			'layout' => 'horizontal',
			'choices' => [
				'title' => __('Disable Title Ajax', 'acfe'),
				'layout' => __('Asynchronous Layout', 'acfe'),
			],
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
	 * wrapper_attributes
	 *
	 * @param $wrapper
	 * @param $field
	 *
	 * @return mixed
	 */
	function wrapper_attributes($wrapper, $field) {

		$async = $field['acfe_flexible_async'];

		// Ajax Layout
		if (in_array('layout', $async)) {
			$wrapper['data-acfe-flexible-ajax'] = 1;
		}

		// Remove ajax 'layout_title' call
		$disable = in_array('title', $async);
		$disable = apply_filters("acfe/flexible/remove_ajax_title", $disable, $field);
		$disable = apply_filters("acfe/flexible/remove_ajax_title/name={$field['_name']}", $disable, $field);
		$disable = apply_filters("acfe/flexible/remove_ajax_title/key={$field['key']}", $disable, $field);

		if ($disable) {
			$wrapper['data-acfe-flexible-remove-ajax-title'] = 1;
		}

		return $wrapper;

	}


	/**
	 * layout_model
	 *
	 * @param $return
	 * @param $field
	 * @param $layout
	 *
	 * @return bool|mixed
	 */
	function layout_model($return, $field, $layout) {

		if (!in_array('layout', $field['acfe_flexible_async'])) {
			return $return;
		}

		$i = 'acfcloneindex';
		$id = 'acfcloneindex';
		$value = [];
		$prefix = $field['name'] . '[' . $id . ']';

		$div = [
			'class' => 'layout acf-clone',
			'data-id' => 'acfcloneindex',
			'data-layout' => $layout['name']
		];

		$div = apply_filters("acfe/flexible/layouts/div", $div, $layout, $field, $i, $value, $prefix);

		echo '<div ' . acf_esc_atts($div) . '></div>';

		return true;

	}


	/**
	 * ajax_layout_model
	 */
	function ajax_layout_model() {

		// options
		$options = acf_parse_args($_POST, [
			'field_key' => '',
			'layout' => '',
		]);

		// get field
		$field = acf_get_field($options['field_key']);
		if (!$field) {
			exit();
		}

		// prepare field
		$field = acf_prepare_field($field);

		// loop available layouts
		foreach ($field['layouts'] as $k => $layout) {

			if ($layout['name'] === $options['layout']) {
				acf_get_instance('acfe_field_flexible_content')->render_layout($field, $layout, 'acfcloneindex', []); // render layout
				exit();
			}

		}

		exit();

	}

}

acf_new_instance('acfe_field_flexible_content_async');