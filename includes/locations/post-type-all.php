<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_location_post_type_all {

	/**
	 * construct
	 */
	function __construct() {

		add_filter('acf/location/rule_values/post_type', [$this, 'rule_values']);
		add_filter('acf/location/rule_match/post_type', [$this, 'rule_match'], 10, 3);

	}


	/**
	 * rule_values
	 *
	 * @param $choices
	 *
	 * @return string[]|void[]
	 */
	function rule_values($choices) {

		return array_merge(['all' => __('All', 'acf')], $choices);

	}


	/**
	 * rule_match
	 *
	 * @param $match
	 * @param $rule
	 * @param $options
	 *
	 * @return bool|mixed
	 */
	function rule_match($match, $rule, $options) {

		if ($rule['value'] !== 'all') {
			return $match;
		}

		if (!acf_maybe_get($options, 'post_type')) {
			return $match;
		}

		$post_types = acf_get_post_types();

		$match = in_array($options['post_type'], $post_types);

		if ($rule['operator'] === '!=') {
			$match = !$match;
		}

		return $match;

	}

}

new acfe_location_post_type_all();