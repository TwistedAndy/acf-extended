<?php

if (!defined('ABSPATH')) {
	exit;
}

// check setting
if (!acf_get_setting('acfe/modules/forms')) {
	return;
}

if (class_exists('acfe_dynamic_forms_export')) {
	return;
}

class acfe_dynamic_forms_export extends acfe_dynamic_module_export {

	/**
	 * initialize
	 *
	 * @return void
	 */
	function initialize() {

		// vars
		$this->name = 'acfe_dynamic_forms_export';
		$this->title = __('Export Forms');
		$this->description = __('Export Forms');
		$this->select = __('Select Forms');
		$this->default_action = 'json';
		$this->allowed_actions = ['json'];
		$this->instance = acf_get_instance('acfe_dynamic_forms');
		$this->file = 'form';
		$this->files = 'forms';
		$this->messages = [
			'not_found' => __('No form available.'),
			'not_selected' => __('No forms selected'),
			'success_single' => '1 form exported',
			'success_multiple' => '%s forms exported',
		];

	}

}

acf_register_admin_tool('acfe_dynamic_forms_export');