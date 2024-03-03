<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_field_extend {

	var $name = '',
		$replace = [],
		$defaults = [],
		$instance = '';

	/**
	 * construct
	 */
	function __construct() {

		// initialize
		$this->initialize();

		// field instance
		$this->instance = $this->get_field_type();

		// defaults
		if ($this->defaults) {
			$this->instance->defaults = array_merge($this->instance->defaults, $this->defaults);
		}

		// field actions
		$actions = [

			// value
			['filter', 'acf/load_value', [$this, 'load_value'], 10, 3],
			['filter', 'acf/update_value', [$this, 'update_value'], 10, 3],
			['filter', 'acf/format_value', [$this, 'format_value'], 10, 3],
			['filter', 'acf/validate_value', [$this, 'validate_value'], 10, 4],
			['action', 'acf/delete_value', [$this, 'delete_value'], 10, 3],

			// field
			['filter', 'acf/validate_rest_value', [$this, 'validate_rest_value'], 10, 3],
			['filter', 'acf/validate_field', [$this, 'validate_field'], 10, 1],
			['filter', 'acf/load_field', [$this, 'load_field'], 10, 1],
			['filter', 'acf/update_field', [$this, 'update_field'], 10, 1],
			['filter', 'acf/duplicate_field', [$this, 'duplicate_field'], 10, 1],
			['action', 'acf/delete_field', [$this, 'delete_field'], 10, 1],
			['action', 'acf/render_field', [$this, 'render_field'], 9, 1],
			['action', 'acf/render_field_settings', [$this, 'render_field_settings'], 9, 1],
			['filter', 'acf/prepare_field', [$this, 'prepare_field'], 10, 1],
			['filter', 'acf/translate_field', [$this, 'translate_field'], 10, 1],
			['filter', 'acfe/field_wrapper_attributes', [$this, 'field_wrapper_attributes'], 10, 2],
			['filter', 'acfe/load_fields', [$this, 'load_fields'], 10, 2],
		];

		// loop
		foreach ($actions as $row) {

			// vars
			[$type, $hook, $function, $priority, $args] = $row;

			// get method
			$method = $type === 'filter' ? 'add_field_filter' : 'add_field_action';

			// use replace method
			if (in_array($function[1], $this->replace)) {
				$method = $type === 'filter' ? 'replace_field_filter' : 'replace_field_action';
			}

			// call method
			$this->{$method}($hook, $function, $priority, $args);

		}

		// input actions
		$this->add_action('acf/input/admin_enqueue_scripts', [$this, 'input_admin_enqueue_scripts'], 10, 0);
		$this->add_action('acf/input/admin_head', [$this, 'input_admin_head'], 10, 0);
		$this->add_action('acf/input/form_data', [$this, 'input_form_data'], 10, 1);
		$this->add_filter('acf/input/admin_l10n', [$this, 'input_admin_l10n'], 10, 1);
		$this->add_action('acf/input/admin_footer', [$this, 'input_admin_footer'], 10, 1);

		// field group actions
		$this->add_action('acf/field_group/admin_enqueue_scripts', [$this, 'field_group_admin_enqueue_scripts'], 10, 0);
		$this->add_action('acf/field_group/admin_head', [$this, 'field_group_admin_head'], 10, 0);
		$this->add_action('acf/field_group/admin_footer', [$this, 'field_group_admin_footer'], 10, 0);

	}


	/**
	 * initialize
	 */
	function initialize() {
		// ...
	}


	/**
	 * get_field_type
	 *
	 * @return mixed
	 */
	function get_field_type() {
		return acf_get_field_type($this->name);
	}

	/**
	 * add_filter
	 *
	 * @param $tag
	 * @param $function_to_add
	 * @param $priority
	 * @param $accepted_args
	 */
	function add_filter($tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1) {

		// bail early if no callable
		if (!is_callable($function_to_add)) {
			return;
		}

		// add
		add_filter($tag, $function_to_add, $priority, $accepted_args);

	}


	/**
	 * remove_filter
	 *
	 * @param $tag
	 * @param $function_to_remove
	 * @param $priority
	 */
	function remove_filter($tag = '', $function_to_remove = '', $priority = 10) {

		// bail early if no callable
		if (!is_callable($function_to_remove)) {
			return;
		}

		// remove
		remove_filter($tag, $function_to_remove, $priority);

	}


	/**
	 * replace_filter
	 *
	 * @param $tag
	 * @param $function_to_replace
	 * @param $priority
	 * @param $accepted_args
	 */
	function replace_filter($tag = '', $function_to_replace = '', $priority = 10, $accepted_args = 1) {

		// check instance
		if (!$this->instance) {
			$this->instance = $this->get_field_type();
		}

		// array
		if (is_array($function_to_replace)) {
			$function_to_remove = [$this->instance, $function_to_replace[1]];
			$function_to_add = $function_to_replace;

			// string
		} else {
			$function_to_remove = [$this->instance, $function_to_replace];
			$function_to_add = [$this, $function_to_replace];

		}

		// bail early if no callable
		if (!is_callable($function_to_add)) {
			return;
		}

		// replace
		$this->remove_filter($tag, $function_to_remove, $priority);
		$this->add_filter($tag, $function_to_add, $priority, $accepted_args);

	}


	/**
	 * add_field_filter
	 *
	 * @param $tag
	 * @param $function_to_add
	 * @param $priority
	 * @param $accepted_args
	 */
	function add_field_filter($tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1) {

		// append
		$tag .= '/type=' . $this->name;

		// add
		$this->add_filter($tag, $function_to_add, $priority, $accepted_args);

	}


	/**
	 * remove_field_filter
	 *
	 * @param $tag
	 * @param $function_to_remove
	 * @param $priority
	 */
	function remove_field_filter($tag = '', $function_to_remove = '', $priority = 10) {

		// append
		$tag .= '/type=' . $this->name;

		// remove
		$this->remove_filter($tag, $function_to_remove, $priority);

	}


	/**
	 * replace_field_filter
	 *
	 * @param string $tag
	 * @param string $function_to_replace
	 * @param int    $priority
	 * @param int    $accepted_args
	 */
	function replace_field_filter($tag = '', $function_to_replace = '', $priority = 10, $accepted_args = 1) {

		// append
		$tag .= '/type=' . $this->name;

		// replace
		$this->replace_filter($tag, $function_to_replace, $priority, $accepted_args);

	}


	/**
	 * add_action
	 *
	 * @param $tag
	 * @param $function_to_add
	 * @param $priority
	 * @param $accepted_args
	 */
	function add_action($tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1) {

		// bail early if no callable
		if (!is_callable($function_to_add)) {
			return;
		}

		// add
		add_action($tag, $function_to_add, $priority, $accepted_args);

	}


	/**
	 * remove_action
	 *
	 * @param $tag
	 * @param $function_to_remove
	 * @param $priority
	 */
	function remove_action($tag = '', $function_to_remove = '', $priority = 10) {

		// bail early if no callable
		if (!is_callable($function_to_remove)) {
			return;
		}

		// remove
		remove_action($tag, $function_to_remove, $priority);

	}


	/**
	 * replace_action
	 *
	 * @param $tag
	 * @param $function_to_replace
	 * @param $priority
	 * @param $accepted_args
	 */
	function replace_action($tag = '', $function_to_replace = '', $priority = 10, $accepted_args = 1) {

		// check instance
		if (!$this->instance) {
			$this->instance = $this->get_field_type();
		}

		// array
		if (is_array($function_to_replace)) {
			$function_to_remove = [$this->instance, $function_to_replace[1]];
			$function_to_add = $function_to_replace;

			// string
		} else {
			$function_to_remove = [$this->instance, $function_to_replace];
			$function_to_add = [$this, $function_to_replace];

		}

		// bail early if no callable
		if (!is_callable($function_to_add)) {
			return;
		}

		// replace
		$this->remove_action($tag, $function_to_remove, $priority);
		$this->add_action($tag, $function_to_add, $priority, $accepted_args);

	}

	/**
	 * add_field_action
	 *
	 * @param $tag
	 * @param $function_to_add
	 * @param $priority
	 * @param $accepted_args
	 */
	function add_field_action($tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1) {

		// append
		$tag .= '/type=' . $this->name;

		// add
		$this->add_action($tag, $function_to_add, $priority, $accepted_args);

	}


	/**
	 * remove_field_action
	 *
	 * @param $tag
	 * @param $function_to_remove
	 * @param $priority
	 */
	function remove_field_action($tag = '', $function_to_remove = '', $priority = 10) {

		// append
		$tag .= '/type=' . $this->name;

		// remove
		$this->remove_action($tag, $function_to_remove, $priority);

	}


	/**
	 * replace_field_action
	 *
	 * @param $tag
	 * @param $function_to_replace
	 * @param $priority
	 * @param $accepted_args
	 */
	function replace_field_action($tag = '', $function_to_replace = '', $priority = 10, $accepted_args = 1) {

		// append
		$tag .= '/type=' . $this->name;

		// replace
		$this->replace_action($tag, $function_to_replace, $priority, $accepted_args);

	}

}