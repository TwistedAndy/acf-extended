<?php

if (!defined('ABSPATH')) {
	exit;
}

// check setting
if (!acf_get_setting('acfe/modules/ui')) {
	return;
}

class acfe_enhanced_ui_user extends acfe_enhanced_ui {

	/**
	 * initialize
	 */
	function initialize() {

		// load
		add_action('acfe/load_user', [$this, 'load_user']);
		add_action('acfe/load_user_new', [$this, 'load_user_new']);

		// meta boxes
		add_action('acfe/add_user_meta_boxes', [$this, 'add_user_meta_boxes']);
		add_action('acfe/add_user_new_meta_boxes', [$this, 'add_user_new_meta_boxes']);

	}


	/**
	 * load_user
	 */
	function load_user() {

		// enqueue
		$this->enqueue_scripts();

		// class
		// advanced-custom-fields-pro/includes/forms/form-user.php
		$acf_form_user = acf_get_instance('ACF_Form_User');

		// remove acf render
		remove_action('show_user_profile', [$acf_form_user, 'render_edit']);
		remove_action('edit_user_profile', [$acf_form_user, 'render_edit']);

		// footer
		add_action('acf/admin_footer', [$this, 'user_footer']);

	}


	/**
	 * load_user_new
	 */
	function load_user_new() {

		// enqueue
		$this->enqueue_scripts();

		// class
		// advanced-custom-fields-pro/includes/forms/form-user.php
		$acf_form_user = acf_get_instance('ACF_Form_User');

		// remove acf render
		remove_action('user_new_form', [$acf_form_user, 'render_new']);

		// footer
		add_action('acf/admin_footer', [$this, 'user_new_footer']);

	}


	/**
	 * add_user_meta_boxes
	 *
	 * @param $user
	 */
	function add_user_meta_boxes($user) {

		// add compatibility with front-end user profile edit forms such as bbPress
		if (!is_admin()) {
			acf_enqueue_scripts();
		}

		// render
		$this->user_add_metaboxes([
			'user_id' => $user->ID,
			'view' => 'edit'
		]);

	}


	/**
	 * add_user_new_meta_boxes
	 */
	function add_user_new_meta_boxes() {

		// render
		$this->user_add_metaboxes([
			'user_id' => 0,
			'view' => 'add'
		]);

	}


	/**
	 * user_add_metaboxes
	 *
	 * @param $args
	 */
	function user_add_metaboxes($args = []) {

		// Native ACF Form user
		$acf_form_user = acf_get_instance('ACF_Form_User');

		// Allow $_POST data to persist across form submission attempts.
		if (isset($_POST['acf'])) {
			add_filter('acf/pre_load_value', [$acf_form_user, 'filter_pre_load_value'], 10, 3);
		}

		// args
		$args = wp_parse_args($args, [
			'user_id' => 0,
			'view' => 'edit'
		]);

		// screen
		$screen = 'user'; // new

		if ($args['view'] == 'edit') {
			$screen = IS_PROFILE_PAGE ? 'profile' : 'user-edit';
		}

		// post id
		$post_id = 'user_' . $args['user_id'];

		// field groups
		$field_groups = acf_get_field_groups([
			'user_id' => $args['user_id'] ? $args['user_id'] : 'new',
			'user_form' => $args['view']
		]);

		if ($field_groups) {

			// form data
			acf_form_data([
				'screen' => 'user',
				'post_id' => $post_id,
				'validation' => ($args['view'] == 'register') ? 0 : 1
			]);

			$this->add_metaboxes($field_groups, $post_id, $screen);

			// actions
			add_action('acf/input/admin_footer', [$acf_form_user, 'admin_footer'], 10, 1);

		}

		$args = $args['view'] === 'add' ? 'user_new' : 'user';

		// Sidebar submit
		add_meta_box('submitdiv', __('Edit'), [$this, 'render_metabox_submit'], $screen, 'side', 'high', $args);

	}


	/**
	 * user_footer
	 */
	function user_footer() {

		global $user_id;

		?>
		<div id="edit-slug-box">
			<strong>Permalink:</strong> <a href="<?php echo get_author_posts_url($user_id); ?>"><?php echo get_author_posts_url($user_id); ?></a>
		</div>
		<script type="text/javascript">
			(function($) {

				acfe.enhancedEditUI({
					screen: 'user-edit'
				});

			})(jQuery);
		</script>
		<?php

	}


	/**
	 * user_new_footer
	 */
	function user_new_footer() {
		?>
		<script type="text/javascript">
			(function($) {

				acfe.enhancedEditUI({
					screen: 'user-new',
					pageTitle: true
				});

			})(jQuery);
		</script>
		<?php
	}

}

new acfe_enhanced_ui_user();