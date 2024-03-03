<?php

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('acfe_field_group_instruction_placement')) {
	return;
}

class acfe_field_group_instruction_placement {

	/**
	 * construct
	 */
	function __construct() {

		add_action('acf/field_group/admin_head', [$this, 'admin_head']);

	}


	/**
	 * admin_head
	 */
	function admin_head() {
		add_filter('acf/prepare_field/name=instruction_placement', [$this, 'prepare_instruction_placement']);
	}


	/**
	 * prepare_instruction_placement
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function prepare_instruction_placement($field) {

		$field['choices'] = array_merge($field['choices'], [
			'above_field' => __('Above fields', 'acfe'),
			'tooltip' => __('Tooltip', 'acfe')
		]);

		return $field;

	}

}

// initialize
new acfe_field_group_instruction_placement();