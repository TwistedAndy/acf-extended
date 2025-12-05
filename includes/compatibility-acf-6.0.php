<?php

if (!defined('ABSPATH')) {
	exit;
}

// check version
if (!acfe_is_acf_6()) {
	return;
}

class acfe_compatibility_acf_6 {

	/**
	 * construct
	 */
	function __construct() {

		add_action('admin_menu', [$this, 'admin_menu']);

		// acf-field groups (6.0)
		add_action('acfe/load_posts/post_type=acf-field-group', [$this, 'load_posts']);
		add_action('acfe/load_post/post_type=acf-field-group', [$this, 'load_post']);

		// acf-post type (6.1)
		add_action('acfe/load_posts/post_type=acf-post-type', [$this, 'load_posts']);
		add_action('acfe/load_post/post_type=acf-post-type', [$this, 'load_post']);

		// acf-taxonomy (6.1)
		add_action('acfe/load_posts/post_type=acf-taxonomy', [$this, 'load_posts']);
		add_action('acfe/load_post/post_type=acf-taxonomy', [$this, 'load_post']);

		// acf-ui-options-page (6.2)
		add_action('acfe/load_posts/post_type=acf-ui-options-page', [$this, 'load_posts']);
		add_action('acfe/load_post/post_type=acf-ui-options-page', [$this, 'load_post']);

		// additional hooks
		add_action('current_screen', [$this, 'current_screen']);
		add_filter('acf/validate_field', [$this, 'validate_field']);

		// re-add sidebar submitdiv metabox
		acfe_replace_action('load-post.php', ['ACF_Form_Post', 'initialize'], [$this, 'acf_load_post']);
		acfe_replace_action('load-post-new.php', ['ACF_Form_Post', 'initialize'], [$this, 'acf_load_post']);

	}


	/**
	 * admin_menu
	 */
	function admin_menu() {

		// get pages
		$updates = get_plugin_page_hookname('acf-settings-updates', 'edit.php?post_type=acf-field-group');
		$tools = get_plugin_page_hookname('acf-tools', 'edit.php?post_type=acf-field-group');

		// actions
		add_action("load-{$updates}", [$this, 'load_acf_page']);
		add_action("load-{$tools}", [$this, 'load_acf_page']);

	}


	/**
	 * load_acf_page
	 */
	function load_acf_page() {
		add_filter('admin_body_class', [$this, 'admin_body_class']);
	}


	/**
	 * load_posts
	 */
	function load_posts() {
		add_filter('admin_body_class', [$this, 'admin_body_class']);
	}


	/**
	 * load_post
	 */
	function load_post() {
		add_filter('admin_body_class', [$this, 'admin_body_class']);
		add_action('acf/input/admin_head', [$this, 'admin_head'], 20);
	}


	/**
	 * acf_load_post
	 *
	 * Rewrites the ACF_Form_Post initialize which remove the submitdiv metabox
	 *
	 * advanced-custom-fields-pro/includes/forms/form-post.php:48
	 */
	function acf_load_post() {

		// globals
		global $typenow;

		// restrict specific post types
		$restricted = ['acf-field-group', 'acf-post-type', 'acf-taxonomy', 'acf-ui-options-page', 'attachment'];
		if (in_array($typenow, $restricted)) {
			return;
		}

		// enqueue scripts
		acf_enqueue_scripts([
			'uploader' => true,
		]);

		// actions
		add_action('add_meta_boxes', [acf_get_instance('ACF_Form_Post'), 'add_meta_boxes'], 10, 2);

	}


	/**
	 * admin_body_class
	 *
	 * Adds acf-admin-6 class to body
	 */
	function admin_body_class($classes) {
		$classes .= ' acf-admin-6';
		return $classes;
	}


	/**
	 * admin_head
	 *
	 */
	function admin_head() {

		// remove forced 1 column on 'screen_layout' options
		acfe_remove_filter('get_user_option_screen_layout_acf-field-group', ['acf_admin_field_group', 'screen_layout']);
		acfe_remove_filter('get_user_option_screen_layout_acf-post-type', ['ACF_Admin_Post_type', 'screen_layout']);
		acfe_remove_filter('get_user_option_screen_layout_acf-taxonomy', ['ACF_Admin_Taxonomy', 'screen_layout']);
		acfe_remove_filter('get_user_option_screen_layout_acf-ui-options-page', ['ACF_Admin_UI_Options_Page', 'screen_layout']);

		// base url
		$default_icon = acf_get_url('assets/images/icons/icon-fields.svg');

		// generate default field type missing icon
		?>
		<style>
			.field-type-icon:before {
				-webkit-mask-image: url(<?php echo $default_icon; ?>);
				mask-image: url(<?php echo $default_icon; ?>);
			}
		</style>
		<?php

	}


	/**
	 * current_screen
	 *
	 * Remove ACF Title header bar on ACFE modules
	 *
	 * @param $screen
	 */
	function current_screen($screen) {

		// allowed screens
		$allowed = [
			'edit-acf-field-group-category',
			'edit-acfe-dbt',
			'acfe-dbt',
			'edit-acfe-template',
			'acfe-template',
			'edit-acfe-form',
			'acfe-form'
		];

		// check screen
		if (acfe_maybe_get($screen, 'post_type') === 'acf-field-group' || acf_is_screen($allowed)) {

			add_action('admin_head', [$this, 'admin_head_navigation']);

			if (acf_is_screen($allowed)) {
				global $acf_page_title;
				$acf_page_title = '';
			}

		}

		// acf 6.1 removed topbar for third party submenu
		if (acf_is_screen($allowed)) {
			add_action('in_admin_header', [$this, 'in_admin_header']);
		}

	}


	function in_admin_header() {
		acf_get_view('global/navigation');
	}


	/**
	 * admin_head_navigation
	 */
	function admin_head_navigation() {

		// base url
		$base_url = acf_get_url('assets/images/');

		// pages rules
		$pages = [
			'categories' => 'field-type-icons/icon-field-taxonomy.svg',
			'edit-tagsphptaxonomyacf-field-group-category' => 'field-type-icons/icon-field-taxonomy.svg',
			'block-types' => 'icons/icon-fields.svg',
			'acfe-dbt' => 'icons/icon-fields.svg',
			'forms' => 'field-type-icons/icon-field-post-object.svg',
			'acfe-form' => 'field-type-icons/icon-field-post-object.svg',
			'options-pages' => 'field-type-icons/icon-field-group.svg',
			'acfe-dop' => 'field-type-icons/icon-field-group.svg',
			'settings' => 'icons/icon-settings.svg',
			'acfe-settings' => 'icons/icon-settings.svg',
			'templates' => 'field-type-icons/icon-field-wysiwyg.svg',
			'acfe-template' => 'field-type-icons/icon-field-wysiwyg.svg',
		];

		// generate css
		?>
		<style>
			<?php foreach($pages as $page => $icon): ?>
			.acf-admin-toolbar .acf-header-tab-<?php echo $page; ?> i.acf-icon {
				display: inline-flex;
				-webkit-mask-image: url(<?php echo $base_url . $icon; ?>);
				mask-image: url(<?php echo $base_url . $icon; ?>);
			}

			<?php endforeach; ?>

			.acf-icon.acf-icon-plus {
				-webkit-mask-image: url(<?php echo $base_url; ?>icons/icon-add.svg);
				mask-image: url(<?php echo $base_url; ?>icons/icon-add.svg);
			}
		</style>
		<?php

	}

	/**
	 * validate_field
	 *
	 * Change instructions to hint for appended field settings
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function validate_field($field) {

		if (acf_maybe_get($field, '_appended') && acf_maybe_get($field, 'instructions')) {
			$field['hint'] = $field['instructions'];
			$field['instructions'] = '';
		}

		return $field;

	}

}

acf_new_instance('acfe_compatibility_acf_6');