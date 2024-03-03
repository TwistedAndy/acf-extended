<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_field extends acf_field {

	/**
	 * construct
	 */
	function __construct() {

		// parent construct
		parent::__construct();

		// field
		$this->add_field_filter('acfe/field_wrapper_attributes', [$this, 'field_wrapper_attributes'], 10, 2);
		$this->add_field_filter('acfe/load_fields', [$this, 'load_fields'], 10, 2);

	}

}