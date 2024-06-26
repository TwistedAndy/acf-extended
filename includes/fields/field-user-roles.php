<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_field_user_roles extends acf_field {

	/**
	 * initialize
	 */
	function initialize() {

		$this->name = 'acfe_user_roles';
		$this->label = __('User Roles', 'acfe');
		$this->category = 'WordPress';
		$this->defaults = [
			'user_role' => [],
			'field_type' => 'checkbox',
			'multiple' => 0,
			'allow_null' => 0,
			'choices' => [],
			'default_value' => '',
			'ui' => 0,
			'ajax' => 0,
			'placeholder' => '',
			'search_placeholder' => '',
			'layout' => '',
			'toggle' => 0,
			'allow_custom' => 0,
		];

	}


	/**
	 * render_field_settings
	 *
	 * @param $field
	 */
	function render_field_settings($field) {

		if (isset($field['default_value'])) {
			$field['default_value'] = acf_encode_choices($field['default_value'], false);
		}

		// Allow User Role
		acf_render_field_setting($field, [
			'label' => __('Allow User Role', 'acf'),
			'instructions' => '',
			'type' => 'select',
			'name' => 'user_role',
			'choices' => acfe_get_roles(),
			'multiple' => 1,
			'ui' => 1,
			'allow_null' => 1,
			'placeholder' => __("All user roles", 'acf'),
		]);

		// field_type
		acf_render_field_setting($field, [
			'label' => __('Appearance', 'acf'),
			'instructions' => __('Select the appearance of this field', 'acf'),
			'type' => 'select',
			'name' => 'field_type',
			'optgroup' => true,
			'choices' => [
				'checkbox' => __('Checkbox', 'acf'),
				'radio' => __('Radio Buttons', 'acf'),
				'select' => _x('Select', 'noun', 'acf')
			]
		]);

		// default_value
		acf_render_field_setting($field, [
			'label' => __('Default Value', 'acf'),
			'instructions' => __('Enter each default value on a new line', 'acf'),
			'name' => 'default_value',
			'type' => 'textarea',
		]);

		// Select + Radio: allow_null
		acf_render_field_setting($field, [
			'label' => __('Allow Null?', 'acf'),
			'instructions' => '',
			'name' => 'allow_null',
			'type' => 'true_false',
			'ui' => 1,
			'conditions' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'select',
					],
				],
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'radio',
					],
				],
			]
		]);

		// Select: multiple
		acf_render_field_setting($field, [
			'label' => __('Select multiple values?', 'acf'),
			'instructions' => '',
			'name' => 'multiple',
			'type' => 'true_false',
			'ui' => 1,
			'conditions' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'select',
					],
				],
			]
		]);

		// Select: ui
		acf_render_field_setting($field, [
			'label' => __('Stylised UI', 'acf'),
			'instructions' => '',
			'name' => 'ui',
			'type' => 'true_false',
			'ui' => 1,
			'conditions' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'select',
					],
				],
			]
		]);


		// Select: ajax
		acf_render_field_setting($field, [
			'label' => __('Use AJAX to lazy load choices?', 'acf'),
			'instructions' => '',
			'name' => 'ajax',
			'type' => 'true_false',
			'ui' => 1,
			'conditions' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'select',
					],
					[
						'field' => 'ui',
						'operator' => '==',
						'value' => 1,
					],
				],
			]
		]);

		// Select: Placeholder
		acf_render_field_setting($field, [
			'label' => __('Placeholder', 'acf'),
			'instructions' => __('Appears within the input', 'acf'),
			'type' => 'text',
			'name' => 'placeholder',
			'placeholder' => _x('Select', 'verb', 'acf'),
			'conditional_logic' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'select',
					],
					[
						'field' => 'ui',
						'operator' => '==',
						'value' => '0',
					],
					[
						'field' => 'allow_null',
						'operator' => '==',
						'value' => '1',
					],
					[
						'field' => 'multiple',
						'operator' => '==',
						'value' => '0',
					],
				],
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'select',
					],
					[
						'field' => 'ui',
						'operator' => '==',
						'value' => '1',
					],
					[
						'field' => 'allow_null',
						'operator' => '==',
						'value' => '1',
					],
				],
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'select',
					],
					[
						'field' => 'ui',
						'operator' => '==',
						'value' => '1',
					],
					[
						'field' => 'multiple',
						'operator' => '==',
						'value' => '1',
					],
				],
			]
		]);

		// Select: Search Placeholder
		acf_render_field_setting($field, [
			'label' => __('Search Input Placeholder', 'acf'),
			'instructions' => __('Appears within the search input', 'acf'),
			'type' => 'text',
			'name' => 'search_placeholder',
			'placeholder' => '',
			'conditional_logic' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'select',
					],
					[
						'field' => 'ui',
						'operator' => '==',
						'value' => '1',
					],
					[
						'field' => 'multiple',
						'operator' => '==',
						'value' => '0',
					],
				],
			]
		]);

		// Radio: other_choice
		acf_render_field_setting($field, [
			'label' => __('Other', 'acf'),
			'instructions' => '',
			'name' => 'other_choice',
			'type' => 'true_false',
			'ui' => 1,
			'message' => __("Add 'other' choice to allow for custom values", 'acf'),
			'conditions' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'radio',
					],
				],
			]
		]);

		// Checkbox: layout
		acf_render_field_setting($field, [
			'label' => __('Layout', 'acf'),
			'instructions' => '',
			'type' => 'radio',
			'name' => 'layout',
			'layout' => 'horizontal',
			'choices' => [
				'vertical' => __("Vertical", 'acf'),
				'horizontal' => __("Horizontal", 'acf')
			],
			'conditions' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'checkbox',
					],
				],
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'radio',
					],
				],
			]
		]);

		// Checkbox: toggle
		acf_render_field_setting($field, [
			'label' => __('Toggle', 'acf'),
			'instructions' => __('Prepend an extra checkbox to toggle all choices', 'acf'),
			'name' => 'toggle',
			'type' => 'true_false',
			'ui' => 1,
			'conditions' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'checkbox',
					],
				],
			]
		]);

		// Checkbox: other_choice
		acf_render_field_setting($field, [
			'label' => __('Allow Custom', 'acf'),
			'instructions' => '',
			'name' => 'allow_custom',
			'type' => 'true_false',
			'ui' => 1,
			'message' => __("Allow 'custom' values to be added", 'acf'),
			'conditions' => [
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'checkbox',
					],
				],
				[
					[
						'field' => 'field_type',
						'operator' => '==',
						'value' => 'select',
					],
					[
						'field' => 'ui',
						'operator' => '==',
						'value' => '1',
					],
				]
			]
		]);

	}


	/**
	 * update_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function update_field($field) {

		$field['default_value'] = acf_decode_choices($field['default_value'], true);

		if ($field['field_type'] === 'radio') {
			$field['default_value'] = acfe_unarray($field['default_value']);
		}

		return $field;

	}


	/**
	 * prepare_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function prepare_field($field) {

		// field type
		$type = $field['type'];
		$field_type = $field['field_type'];

		$field['type'] = $field_type;
		$field['wrapper']['data-ftype'] = $type;

		// choices
		$field['choices'] = acfe_get_roles($field['user_role']);

		// allow custom
		if ($field['allow_custom']) {

			$value = acf_maybe_get($field, 'value');
			$value = acf_get_array($value);

			foreach ($value as $v) {

				// append custom value to choices
				if (!isset($field['choices'][$v])) {
					$field['choices'][$v] = $v;
					$field['custom_choices'][$v] = $v;
				}
			}

		}

		return $field;

	}


	/**
	 * translate_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function translate_field($field) {

		$field['placeholder'] = acf_translate($field['placeholder']);
		$field['search_placeholder'] = acf_translate($field['search_placeholder']);

		return $field;

	}

}

// initialize
acf_register_field_type('acfe_field_user_roles');