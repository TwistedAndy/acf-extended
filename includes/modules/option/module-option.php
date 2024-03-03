<?php

if (!defined('ABSPATH')) {
	exit;
}

// check setting
if (!acf_get_setting('acfe/modules/options')) {
	return;
}

class acfe_module_options {

	// vars
	var $action = 'list';

	/**
	 * Construct
	 */
	function __construct() {

		acfe_include('includes/modules/option/module-option-table.php');

		add_filter('set-screen-option', [$this, 'set_screen_option'], 10, 3);
		add_action('admin_menu', [$this, 'admin_menu']);
		add_action('acf/save_post', [$this, 'save_post'], 5);

	}


	/**
	 * set_screen_option
	 *
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 */
	function set_screen_option($status, $option, $value) {

		if ($option === 'options_per_page') {
			return $value;
		}

		return $status;

	}


	/**
	 * admin_menu
	 */
	function admin_menu() {

		if (acf_get_setting('show_admin')) {

			$page = add_submenu_page('options-general.php', __('Options', 'acfe'), __('Options', 'acfe'), acf_get_setting('capability'), 'acfe-options', [
				$this,
				'admin_html'
			]);

			add_action("load-{$page}", [$this, 'admin_load']);

		}

	}


	/**
	 * admin_load
	 */
	function admin_load() {

		// messages
		if ($message = acf_maybe_get_GET('message')) {

			switch ($message) {

				case 'deleted':
				{
					acf_add_admin_notice(__('Option has been deleted', 'acfe'), 'success');
					break;
				}

				case 'bulk-deleted':
				{
					acf_add_admin_notice(__('Options have been deleted', 'acfe'), 'success');
					break;
				}

				case 'updated':
				{
					acf_add_admin_notice(__('Option has been updated', 'acfe'), 'success');
					break;
				}

				case 'added':
				{
					acf_add_admin_notice(__('Option has been added', 'acfe'), 'success');
					break;
				}

			}

		}

		// default: list
		$this->action = 'list';

		// edit or delete
		if (acfe_maybe_get_REQUEST('action', '-1') !== '-1') {
			$this->action = $_REQUEST['action'];

			// bulk-delete
		} elseif (acfe_maybe_get_REQUEST('action2', '-1') !== '-1') {
			$this->action = $_REQUEST['action2'];
		}

		// load
		switch ($this->action) {

			case 'list':
			{
				$this->load_list();
				break;
			}

			case 'edit':
			case 'add':
			{
				$this->load_edit();
				break;
			}

			case 'delete':
			{
				$this->load_delete();
				break;
			}

			case 'bulk-delete':
			{
				$this->load_bulk_delete();
				break;
			}

		}

		// enqueue
		acf_enqueue_scripts();

	}


	/**
	 * admin_html
	 */
	function admin_html() {

		if ($this->action === 'list') {
			$this->html_list();

		} elseif ($this->action === 'edit' || $this->action === 'add') {
			$this->html_edit();
		}

	}


	/**
	 * load_list
	 */
	function load_list() {

		add_screen_option('per_page', [
			'label' => 'Options',
			'default' => 100,
			'option' => 'options_per_page'
		]);

	}


	/**
	 * load_edit
	 */
	function load_edit() {

		// nonce
		if (acf_verify_nonce('acfe-options-edit')) {

			// save data
			if (acf_validate_save_post(true)) {

				acf_save_post('acfe_options_edit');

				$redirect = add_query_arg(['message' => 'updated']);

				if ($this->action === 'add') {
					$redirect = sprintf('?page=%s&message=added', esc_attr($_REQUEST['page']));
				}

				wp_redirect($redirect);
				exit;

			}

		}

		// actions
		add_action('acf/input/admin_head', [$this, 'add_metaboxes']);

		// add columns support
		add_screen_option('layout_columns', [
			'max' => 2,
			'default' => 2,
		]);

	}


	/**
	 * load_delete
	 */
	function load_delete() {

		// nonce
		$nonce = esc_attr($_REQUEST['_wpnonce']);

		// verify
		if (!wp_verify_nonce($nonce, 'acfe_options_delete_option')) {
			wp_die('Cheatin’, huh?');
		}

		// delete
		$this->delete_option(absint($_GET['option']));

		// redirect
		wp_redirect(sprintf('?page=%s&message=deleted', esc_attr($_REQUEST['page'])));
		exit;

	}


	/**
	 * load_bulk_delete
	 */
	function load_bulk_delete() {

		// nonce
		$nonce = esc_attr($_REQUEST['_wpnonce']);

		// verify
		if (!wp_verify_nonce($nonce, 'bulk-options')) {
			wp_die('Cheatin’, huh?');
		}

		// ids
		$delete_ids = esc_sql($_REQUEST['bulk-delete']);

		// loop
		foreach ($delete_ids as $id) {
			$this->delete_option($id);
		}

		wp_redirect(sprintf('?page=%s&message=bulk-deleted', esc_attr($_REQUEST['page'])));
		exit;

	}


	/**
	 * html_list
	 */
	function html_list() {
		acfe_get_view('html-options-list');
	}


	/**
	 * html_edit
	 */
	function html_edit() {
		acfe_get_view('html-options-edit');
	}


	/**
	 * save_post
	 *
	 * @param $post_id
	 */
	function save_post($post_id) {

		// validate
		if ($post_id !== 'acfe_options_edit') {
			return;
		}

		// vars
		$option_name = wp_unslash($_POST['acf']['field_acfe_options_edit_name']);
		$option_value = wp_unslash($_POST['acf']['field_acfe_options_edit_value']);
		$autoload = $_POST['acf']['field_acfe_options_edit_autoload'];

		// value serialized?
		$option_value = maybe_unserialize($option_value);

		// update
		update_option($option_name, $option_value, $autoload);

		// flush acf
		$_POST['acf'] = [];

	}


	/**
	 * delete_option
	 *
	 * @param $id
	 */
	function delete_option($id) {

		global $wpdb;

		$wpdb->delete("{$wpdb->options}", ['option_id' => $id], ['%d']);

	}


	/**
	 * add_metaboxes
	 */
	function add_metaboxes() {

		$option = [
			'option_id' => 0,
			'option_name' => '',
			'option_value' => '',
			'autoload' => 'no',
		];

		$option_id = absint(acfe_maybe_get_REQUEST('option'));

		if ($option_id) {

			global $wpdb;

			$get_option = $wpdb->get_row("SELECT * FROM {$wpdb->options} WHERE option_id = '$option_id'", 'ARRAY_A');

			if (!empty($get_option)) {
				$option = $get_option;
			}

		}

		$field_group = [
			'ID' => 0,
			'key' => 'group_acfe_options_edit',
			'style' => 'default',
			'label_placement' => 'left',
			'instruction_placement' => 'label',
			'fields' => []
		];

		$fields = [];

		$fields[] = [
			'label' => __('Name', 'acfe'),
			'key' => 'field_acfe_options_edit_name',
			'name' => 'field_acfe_options_edit_name',
			'type' => 'text',
			'prefix' => 'acf',
			'instructions' => '',
			'required' => true,
			'conditional_logic' => false,
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
			'value' => $option['option_name'],
			'wrapper' => [
				'width' => '',
				'class' => '',
				'id' => '',
			],
		];

		// serialized || html
		if (is_serialized($option['option_value']) || $option['option_value'] != strip_tags($option['option_value'])) {

			$type = 'serialized';
			$instructions = 'Use this <a href="https://duzun.me/playground/serialize" target="_blank">online tool</a> to unserialize/seriliaze data.';

			if ($option['option_value'] != strip_tags($option['option_value'])) {

				$type = 'HTML';
				$instructions = '';

			}

			$fields[] = [
				'label' => __('Value', 'acfe') . ' <code style="font-size:11px;float:right; line-height:1.2; margin-top:1px;">' . $type . '</code>',
				'key' => 'field_acfe_options_edit_value',
				'name' => 'field_acfe_options_edit_value',
				'type' => 'textarea',
				'prefix' => 'acf',
				'instructions' => $instructions,
				'required' => false,
				'conditional_logic' => false,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'value' => $option['option_value'],
				'class' => 'code',
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
			];

		} // json
		elseif (acfe_is_json($option['option_value'])) {

			$type = 'json';
			$instructions = 'Use this <a href="http://solutions.weblite.ca/php2json/" target="_blank">online tool</a> to decode/encode json.';

			$fields[] = [
				'label' => __('Value', 'acfe') . ' <code style="font-size:11px;float:right; line-height:1.2; margin-top:1px;">' . $type . '</code>',
				'key' => 'field_acfe_options_edit_value',
				'name' => 'field_acfe_options_edit_value',
				'type' => 'textarea',
				'prefix' => 'acf',
				'instructions' => $instructions,
				'required' => false,
				'conditional_logic' => false,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'value' => $option['option_value'],
				'class' => 'code',
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
			];

		} // string
		else {

			$type = '';
			if (!empty($option['option_value'])) {
				$type = '<code style="font-size:11px;float:right; line-height:1.2; margin-top:1px;">string</code>';
			}

			$fields[] = [
				'label' => __('Value', 'acfe') . ' ' . $type,
				'key' => 'field_acfe_options_edit_value',
				'name' => 'field_acfe_options_edit_value',
				'type' => 'textarea',
				'prefix' => 'acf',
				'instructions' => '',
				'required' => false,
				'conditional_logic' => false,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'value' => $option['option_value'],
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
			];

		}

		$fields[] = [
			'label' => __('Autoload', 'acfe'),
			'key' => 'field_acfe_options_edit_autoload',
			'name' => 'field_acfe_options_edit_autoload',
			'type' => 'select',
			'prefix' => 'acf',
			'instructions' => '',
			'required' => true,
			'conditional_logic' => false,
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
			'value' => $option['autoload'],
			'choices' => [
				'no' => __('No', 'acfe'),
				'yes' => __('Yes', 'acfe'),
			],
			'wrapper' => [
				'width' => '',
				'class' => '',
				'id' => '',
			],
		];

		$field_group['fields'] = $fields;

		$metabox_submit_title = __('Submit', 'acf');
		$metabox_main_title = __('Add Option', 'acfe');

		if (!empty($option['option_id'])) {

			$metabox_submit_title = __('Edit', 'acf');
			$metabox_main_title = __('Edit Option', 'acfe');

		}

		// submit Metabox
		add_meta_box('submitdiv', $metabox_submit_title, function($post, $args) use ($option) {

			$delete_nonce = wp_create_nonce('acfe_options_delete_option');

			?>
			<div id="major-publishing-actions">

				<?php if (!empty($option['option_id'])) { ?>

					<div id="delete-action">
						<a class="submitdelete deletion" style="color:#a00;" href="<?php echo sprintf('?page=%s&action=%s&option=%s&_wpnonce=%s', esc_attr($_REQUEST['page']), 'delete', $option['option_id'], $delete_nonce); ?>">
							<?php _e('Delete'); ?>
						</a>
					</div>

				<?php } ?>

				<div id="publishing-action">
					<span class="spinner"></span>
					<input type="submit" accesskey="p" value="<?php _e('Update'); ?>" class="button button-primary button-large" id="publish" name="publish">
				</div>

				<div class="clear"></div>

			</div>
			<?php
		}, 'acf_options_page', 'side', 'high');

		// main metabox
		add_meta_box('acf-group_acfe_options_edit', $metabox_main_title, function($post, $args) {

			// extract args
			extract($args); // all variables from the add_meta_box function
			extract($args); // all variables from the args argument

			// vars
			$o = [
				'id' => $id,
				'key' => $field_group['key'],
				'style' => $field_group['style'],
				'label' => $field_group['label_placement'],
				'editLink' => '',
				'editTitle' => __('Edit field group', 'acf'),
				'visibility' => true
			];

			// load fields
			$fields = $field_group['fields'];

			// render
			acf_render_fields($fields, 'acfe-options-edit', 'div', $field_group['instruction_placement']);

			?>
			<script type="text/javascript">
				if (typeof acf !== 'undefined') {
					acf.newPostbox(<?php echo json_encode($o); ?>);
				}
			</script>
			<?php

		}, 'acf_options_page', 'normal', 'high', ['field_group' => $field_group]);

	}

}

new acfe_module_options();