<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class ACFE_Admin_Options_List extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct([
			'singular' => __('Option', 'acfe'),
			'plural' => __('Options', 'acfe'),
			'ajax' => false
		]);

	}

	/**
	 * Retrieve data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_options($per_page = 100, $page_number = 1, $search = '') {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->options}";

		if (!empty($search)) {

			$search = '%' . $wpdb->esc_like($search) . '%';
			$sql .= $wpdb->prepare(" WHERE option_name LIKE %s", $search);

		}

		if (empty($_REQUEST['orderby'])) {

			$sql .= ' ORDER BY option_id ASC';

		} else {

			$orderby = esc_sql($_REQUEST['orderby']);
			$order = !empty($_REQUEST['order']) ? esc_sql($_REQUEST['order']) : 'ASC';

			$orderby_order = sanitize_sql_orderby($orderby . ' ' . $order);

			if ($orderby_order) {
				$sql .= ' ORDER BY ' . $orderby_order;
			}

		}

		if (empty($search)) {

			$sql .= " LIMIT $per_page";
			$sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

		}

		return $wpdb->get_results($sql, 'ARRAY_A');

	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count($search = '') {

		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->options}";

		if (!empty($search)) {

			$search = '%' . $wpdb->esc_like($search) . '%';
			$sql .= $wpdb->prepare(" WHERE option_name LIKE %s", $search);

		}

		return $wpdb->get_var($sql);

	}


	/** Text displayed when no data is available */
	public function no_items() {

		_e('No options avaliable.', 'acfe');

	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array  $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default($item, $column_name) {

		if ($column_name === 'option_id') {

			return $item['option_id'];

		} elseif ($column_name === 'option_value') {

			// raw
			$raw = map_deep($item['option_value'], '_wp_specialchars');

			// serialized
			if (is_serialized($item['option_value'])) {

				$value = maybe_unserialize($item['option_value']);

				if (is_object($value) && is_a($value, '__PHP_Incomplete_Class')) {
					// do nothing
				} else {
					$value = map_deep($value, '_wp_specialchars');
				}

				return '<pre style="max-height:200px; overflow:auto; white-space: pre;">' . print_r($value, true) . '</pre><pre style="max-height:200px; overflow:auto; white-space: pre; margin-top:10px;">' . print_r($raw, true) . '</pre>';

				// html
			} elseif ($item['option_value'] != strip_tags($item['option_value'])) {

				return '<pre style="max-height:200px; overflow:auto; white-space: pre;">' . print_r($raw, true) . '</pre>';

				// json
			} elseif (acfe_is_json($item['option_value'])) {

				$value = json_decode($item['option_value']);
				$value = @map_deep($value, '_wp_specialchars');

				return '<pre style="max-height:200px; overflow:auto; white-space: pre;">' . print_r($value, true) . '</pre><pre style="max-height:200px; overflow:auto; white-space: pre; margin-top:10px;">' . print_r($raw, true) . '</pre>';

				// default
			} else {

				return $raw;

			}

		} elseif ($column_name === 'autoload') {

			return $item['autoload'];

		} else {

			return print_r($item, true);

		}

	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_cb($item) {

		return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['option_id']);

	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	public function column_option_name($item) {

		$delete_nonce = wp_create_nonce('acfe_options_delete_option');

		$title = '<strong>' . $item['option_name'] . '</strong>';

		$actions = [
			'edit' => sprintf('<a href="?page=%s&action=edit&option=%s">' . __('Edit') . '</a>', esc_attr($_REQUEST['page']), absint($item['option_id'])),
			'delete' => sprintf('<a href="?page=%s&action=delete&option=%s&_wpnonce=%s">' . __('Delete') . '</a>', esc_attr($_REQUEST['page']), absint($item['option_id']), $delete_nonce),
		];

		return $title . $this->row_actions($actions);

	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = [
			'cb' => '<input type="checkbox" />',
			'option_id' => __('ID', 'acfe'),
			'option_name' => __('Name', 'acfe'),
			'option_value' => __('Value', 'acfe'),
			'autoload' => __('Autoload', 'acfe'),
		];

		return $columns;

	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {

		$sortable_columns = [
			'option_id' => ['option_id', true],
			'option_name' => ['option_name', true],
			'option_value' => ['option_value', true],
			'autoload' => ['autoload', true],
		];

		return $sortable_columns;

	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {

		$actions = [
			'bulk-delete' => __('Delete')
		];

		return $actions;

	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		// Get columns
		$this->_column_headers = [$this->get_columns(), [], $this->get_sortable_columns()];

		// Vars
		$per_page = $this->get_items_per_page('options_per_page', 100);
		$current_page = $this->get_pagenum();

		// Search
		$search = (isset($_REQUEST['s'])) ? $_REQUEST['s'] : false;

		// Get items
		$this->items = self::get_options($per_page, $current_page, $search);
		/*
		foreach($this->items as &$item){
			$item = json_encode($item);
		}*/

		// Get total
		$total_items = self::record_count($search);

		if (!empty($search)) {
			$per_page = $total_items;
		}

		$this->set_pagination_args([
			'total_items' => $total_items,
			'per_page' => $per_page
		]);

	}

}