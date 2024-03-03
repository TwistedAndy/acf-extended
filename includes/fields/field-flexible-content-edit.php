<?php

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('acfe_field_flexible_content_edit')) {
	return;
}

class acfe_field_flexible_content_edit {

	/**
	 * construct
	 */
	function __construct() {

		// Hooks
		add_filter('acfe/flexible/defaults_field', [$this, 'defaults_field'], 9);
		add_filter('acfe/flexible/defaults_layout', [$this, 'defaults_layout'], 9);

		add_action('acfe/flexible/render_field_settings', [$this, 'render_field_settings'], 9);
		add_action('acfe/flexible/render_layout_settings', [$this, 'render_layout_settings'], 19, 3);

		add_filter('acfe/flexible/validate_field', [$this, 'validate_edit']);
		add_filter('acfe/flexible/wrapper_attributes', [$this, 'wrapper_attributes'], 10, 2);
		add_action('acfe/flexible/pre_render_layout', [$this, 'pre_render_layout'], 5, 5);
		add_action('acfe/flexible/render_layout', [$this, 'render_layout'], 20, 5);
		add_filter('acfe/flexible/layouts/div', [$this, 'layout_div'], 10, 3);
		add_filter('acfe/flexible/layouts/icons', [$this, 'layout_icons'], 50, 3);
		add_filter('acfe/flexible/layouts/handle', [$this, 'layout_handle'], 10, 3);
		add_filter('acfe/flexible/layouts/placeholder', [$this, 'layout_handle'], 10, 3);

	}


	/**
	 * defaults_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function defaults_field($field) {

		$field['acfe_flexible_modal_edit'] = [
			'acfe_flexible_modal_edit_enabled' => false,
			'acfe_flexible_modal_edit_size' => 'large',
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

		$layout['acfe_flexible_modal_edit_size'] = false;

		return $layout;

	}


	/**
	 * render_field_settings
	 *
	 * @param $field
	 */
	function render_field_settings($field) {

		acf_render_field_setting($field, [
			'label' => __('Edit Modal', 'acfe'),
			'name' => 'acfe_flexible_modal_edit',
			'key' => 'acfe_flexible_modal_edit',
			'instructions' => __('Edit layout content in a modal', 'acfe') . '. ' . '<a href="https://www.acf-extended.com/features/fields/flexible-content/modal-settings#edit-modal" target="_blank">' . __('See documentation', 'acfe') . '</a>',
			'type' => 'group',
			'layout' => 'block',
			'sub_fields' => [
				[
					'label' => '',
					'name' => 'acfe_flexible_modal_edit_enabled',
					'key' => 'acfe_flexible_modal_edit_enabled',
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
					'name' => 'acfe_flexible_modal_edit_size',
					'key' => 'acfe_flexible_modal_edit_size',
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
					'default_value' => 'large',
					'wrapper' => [
						'width' => '25',
						'class' => '',
						'id' => '',
						'data-acfe-prepend' => 'Size',
					],
					'conditional_logic' => [
						[
							[
								'field' => 'acfe_flexible_modal_edit_enabled',
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

		if (!$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled']) {
			return;
		}

		// Title
		echo '</li>';
		acf_render_field_wrap([
			'label' => __('Modal settings', 'acfe'),
			'type' => 'hidden',
			'name' => 'acfe_flexible_modal_edit_label',
			'wrapper' => [
				'class' => 'acfe-flexible-field-setting',
			]
		], 'ul');
		echo '<li>';

		acf_render_field_wrap([
			'label' => '',
			'name' => 'acfe_flexible_modal_edit_size',
			'type' => 'select',
			'class' => '',
			'prefix' => $prefix,
			'value' => $layout['acfe_flexible_modal_edit_size'],
			'placeholder' => 'Default',
			'choices' => [
				'small' => 'Small',
				'medium' => 'Medium',
				'large' => 'Large',
				'xlarge' => 'Extra Large',
				'full' => 'Full',
			],
			'wrapper' => [
				'width' => '100',
				'data-acfe-prepend' => 'Modal size',
			],
			'default_value' => '',
			'allow_null' => 1,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 0,
		], 'ul');

	}


	/**
	 * validate_edit
	 *
	 * @param $field
	 *
	 * @return array|mixed
	 */
	function validate_edit($field) {

		if (!isset($field['acfe_flexible_modal_edition'])) {
			return $field;
		}

		$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled'] = $field['acfe_flexible_modal_edition'];

		unset($field['acfe_flexible_modal_edition']);

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
		if (!$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled']) {
			return $wrapper;
		}

		$wrapper['data-acfe-flexible-modal-edition'] = 1;

		return $wrapper;

	}


	/**
	 * pre_render_layout
	 *
	 * @param $layout
	 * @param $field
	 * @param $i
	 * @param $value
	 * @param $prefix
	 */
	function pre_render_layout($layout, $field, $i, $value, $prefix) {

		if (empty($layout['sub_fields']) || !$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled']) {
			return;
		}

		// modal
		$modal = [
			'class' => "acfe-modal -fields acfe-modal-edit-{$field['_name']} acfe-modal-edit-{$field['key']} acfe-modal-edit-{$layout['name']}",
			'data-size' => $field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_size'],
		];

		if (!empty($layout['acfe_flexible_modal_edit_size'])) {
			$modal['data-size'] = $layout['acfe_flexible_modal_edit_size'];
		}

		if (in_array('close', $field['acfe_flexible_add_actions'])) {
			$modal['data-footer'] = __('Close', 'acfe');
		}

		?>
		<div <?php echo acf_esc_atts($modal); ?>>
		<div class="acfe-modal-wrapper">
		<div class="acfe-modal-content">
		<?php

	}


	/**
	 * render_layout
	 *
	 * @param $layout
	 * @param $field
	 * @param $i
	 * @param $value
	 * @param $prefix
	 */
	function render_layout($layout, $field, $i, $value, $prefix) {

		if (empty($layout['sub_fields']) || !$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled']) {
			return;
		}

		?>
		</div>
		</div>
		</div>
		<?php

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

		if (!$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled']) {
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
	 * layout_icons
	 *
	 * @param $icons
	 * @param $layout
	 * @param $field
	 *
	 * @return mixed
	 */
	function layout_icons($icons, $layout, $field) {

		if (!$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled']) {
			return $icons;
		}

		acfe_unset($icons, 'collapse');

		return $icons;

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

		if (!$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled']) {
			return $handle;
		}

		$handle['data-action'] = 'acfe-flexible-modal-edit';

		return $handle;

	}

}

acf_new_instance('acfe_field_flexible_content_edit');