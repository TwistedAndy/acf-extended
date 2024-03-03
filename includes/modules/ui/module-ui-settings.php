<?php

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('acfe_enhanced_ui_settings')) {
	return;
}

// check setting
if (!acf_get_setting('acfe/modules/ui')) {
	return;
}

class acfe_enhanced_ui_settings extends acfe_enhanced_ui {

	/**
	 * initialize
	 */
	function initialize() {

		// hooks
		add_action('acfe/load_settings', [$this, 'load_settings']);
		add_action('acfe/add_settings_meta_boxes', [$this, 'add_settings_meta_boxes']);

	}


	/**
	 * load_settings
	 */
	function load_settings() {

		// enqueue
		$this->enqueue_scripts();

		// Settings
		add_action('acf/admin_footer', [$this, 'settings_footer']);

	}


	/**
	 * add_settings_meta_boxes
	 */
	function add_settings_meta_boxes() {

		$screen = get_current_screen()->id;

		// post id
		$post_id = acf_get_valid_post_id($screen);

		// field groups
		$field_groups = acf_get_field_groups([
			'wp_settings' => $screen
		]);

		if ($field_groups) {

			// form data
			acf_form_data([
				'screen' => 'wp_settings',
				'post_id' => $post_id,
			]);

			$this->add_metaboxes($field_groups, $post_id, $screen);

		}

		// Sidebar submit
		add_meta_box('submitdiv', __('Edit'), [$this, 'render_metabox_submit'], $screen, 'side', 'high', 'settings');

	}


	/**
	 * settings_footer
	 */
	function settings_footer() {

		global $pagenow;

		?>
		<script type="text/javascript">
			(function($) {

				var pageTitle = false;

				<?php if(!in_array($pagenow, ['options-permalink.php', 'options-media.php'])){ ?>
				pageTitle = true;
				<?php } ?>

				acfe.enhancedEditUI({
					screen: 'settings',
					pageTitle: pageTitle
				});

			})(jQuery);
		</script>
		<?php
	}


}

new acfe_enhanced_ui_settings();