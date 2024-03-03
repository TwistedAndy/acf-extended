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

		add_filter('acfe/flexible/validate_field', [$this, 'validate_async']);
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

		/**
		 * old settings:
		 *
		 * acfe_flexible_disable_ajax_title
		 * acfe_flexible_layouts_ajax
		 */

		acf_render_field_setting($field, [
			'label' => __('Asynchronous Settings', 'acfe'),
			'name' => 'acfe_flexible_async',
			'key' => 'acfe_flexible_async',
			'instructions' => '<a href="https://www.acf-extended.com/features/fields/flexible-content/advanced-settings#async-settings" target="_blank">' . __('See documentation', 'acfe') . '</a>',
			'type' => 'checkbox',
			'default_value' => '',
			'layout' => 'horizontal',
			'choices' => [
				'title' => 'Disable Title Ajax',
				'layout' => 'Asynchronous Layout',
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
	 * validate_async
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function validate_async($field) {

		$async = acf_get_array($field['acfe_flexible_async']);

		// acfe_flexible_disable_ajax_title
		if (acf_maybe_get($field, 'acfe_flexible_disable_ajax_title')) {

			if (!in_array('title', $async)) $async[] = 'title';
			acfe_unset($field, 'acfe_flexible_disable_ajax_title');

		}

		// acfe_flexible_layouts_ajax
		if (acf_maybe_get($field, 'acfe_flexible_layouts_ajax')) {

			if (!in_array('layout', $async)) $async[] = 'layout';
			acfe_unset($field, 'acfe_flexible_layouts_ajax');

		}

		$field['acfe_flexible_async'] = $async;

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

		$field = acf_get_field($options['field_key']);

		if (!is_array($field) or empty($field['type'])) {
			exit();
		}

		$acfe_instance = acf_get_instance('acfe_field_flexible_content');
		$field = acf_prepare_field($field);

		foreach ($field['layouts'] as $layout) {

			if ($layout['name'] !== $options['layout']) {
				continue;
			}

			$acfe_instance->render_layout($field, $layout, 'acfcloneindex', []);
			exit();

		}

		exit();

	}

}

acf_new_instance('acfe_field_flexible_content_async');