<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_field_dynamic_render extends acf_field {

	/**
	 * initialize
	 */
	function initialize() {

		$this->name = 'acfe_dynamic_render';
		$this->label = __('Dynamic Render', 'acfe');
		$this->category = 'layout';
		$this->defaults = [
			'render' => ''
		];

	}


	/**
	 * render_field
	 *
	 * @param $field
	 */
	function render_field($field) {

		// check callback
		// check isset() for backward compatibility with the old acfe_dynamic_message field type
		if (isset($field['render']) && is_callable($field['render'])) {
			call_user_func_array($field['render'], [$field]);
		}

	}

}

// initialize
acf_register_field_type('acfe_field_dynamic_render');