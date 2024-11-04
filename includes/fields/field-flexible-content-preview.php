<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_field_flexible_content_preview {

	/**
	 * construct
	 */
	function __construct() {

		// Hooks
		add_filter('acfe/flexible/defaults_field', [$this, 'defaults_field'], 2);
		add_filter('acfe/flexible/defaults_layout', [$this, 'defaults_layout'], 2);

		add_action('acfe/flexible/render_field_settings', [$this, 'render_field_settings'], 2);
		add_action('acfe/flexible/render_layout_settings', [$this, 'render_layout_settings'], 15, 3);

		add_action('acf/render_field/type=flexible_content', [$this, 'render_field'], 8);
		add_filter('acfe/flexible/wrapper_attributes', [$this, 'wrapper_attributes'], 10, 2);
		add_filter('acfe/flexible/prepare_layout', [$this, 'prepare_layout'], 25, 5);

		// Ajax
		add_action('wp_ajax_acfe/flexible/layout_preview', [$this, 'layout_preview']);

	}


	/**
	 * defaults_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function defaults_field($field) {

		$field['acfe_flexible_layouts_templates'] = false;
		$field['acfe_flexible_layouts_previews'] = false;
		$field['acfe_flexible_layouts_placeholder'] = false;

		return $field;

	}


	/**
	 * defaults_layout
	 *
	 * @param $layout
	 *
	 * @return mixed
	 */
	function defaults_layout($layout) {

		$layout['acfe_flexible_render_template'] = false;
		$layout['acfe_flexible_render_style'] = false;
		$layout['acfe_flexible_render_script'] = false;

		return $layout;

	}


	/**
	 * render_field_settings
	 *
	 * @param $field
	 */
	function render_field_settings($field) {

		// Render
		acf_render_field_setting($field, [
			'label' => __('Dynamic Render', 'acfe'),
			'name' => 'acfe_flexible_layouts_templates',
			'key' => 'acfe_flexible_layouts_templates',
			'instructions' => __('Render the layout using custom template, style & javascript files', 'acfe') . '. ' . '<a href="https://www.acf-extended.com/features/fields/flexible-content/dynamic-render" target="_blank">' . __('See documentation', 'acfe') . '</a>',
			'type' => 'true_false',
			'message' => '',
			'default_value' => false,
			'ui' => true,
			'ui_on_text' => '',
			'ui_off_text' => '',
			'conditional_logic' => [
				[
					[
						'field' => 'acfe_flexible_advanced',
						'operator' => '==',
						'value' => '1',
					],
				]
			]
		]);

		// Preview
		acf_render_field_setting($field, [
			'label' => __('Dynamic Preview', 'acfe'),
			'name' => 'acfe_flexible_layouts_previews',
			'key' => 'acfe_flexible_layouts_previews',
			'instructions' => __('Use layouts render settings to display a dynamic preview in the administration', 'acfe') . '. ' . '<a href="https://www.acf-extended.com/features/fields/flexible-content/dynamic-render#dynamic-preview" target="_blank">' . __('See documentation', 'acfe') . '</a>',
			'type' => 'true_false',
			'message' => '',
			'default_value' => false,
			'ui' => true,
			'ui_on_text' => '',
			'ui_off_text' => '',
			'conditional_logic' => [
				[
					[
						'field' => 'acfe_flexible_advanced',
						'operator' => '==',
						'value' => '1',
					],
					[
						'field' => 'acfe_flexible_layouts_templates',
						'operator' => '==',
						'value' => '1',
					],
				]
			]
		]);

		// Placholder
		acf_render_field_setting($field, [
			'label' => __('Layouts Placeholder', 'acfe'),
			'name' => 'acfe_flexible_layouts_placeholder',
			'key' => 'acfe_flexible_layouts_placeholder',
			'instructions' => __('Display a placeholder with an icon', 'acfe') . '. ' . '<a href="https://www.acf-extended.com/features/fields/flexible-content/advanced-settings#layouts-placeholder" target="_blank">' . __('See documentation', 'acfe') . '</a>',
			'type' => 'true_false',
			'message' => '',
			'default_value' => false,
			'ui' => true,
			'ui_on_text' => '',
			'ui_off_text' => '',
			'conditional_logic' => [
				[
					[
						'field' => 'acfe_flexible_advanced',
						'operator' => '==',
						'value' => '1',
					],
					[
						'field' => 'acfe_flexible_layouts_previews',
						'operator' => '!=',
						'value' => '1',
					],
				]
			]
		]);

	}


	/**
	 * render_layout_settings
	 *
	 * @param $flexible
	 * @param $layout
	 * @param $prefix
	 */
	function render_layout_settings($flexible, $layout, $prefix) {

		if (!acf_maybe_get($flexible, 'acfe_flexible_layouts_templates')) {
			return;
		}

		$prepend = acfe_get_setting('theme_folder') ? trailingslashit(acfe_get_setting('theme_folder')) : '';

		// Title
		echo '</li>';
		acf_render_field_wrap([
			'label' => __('Render', 'acfe'),
			'type' => 'hidden',
			'name' => 'acfe_flexible_render_label',
			'wrapper' => [
				'class' => 'acfe-flexible-field-setting acfe-flexible-field-setting-row',
			]
		], 'ul');
		echo '<li>';

		// Template
		$prepend = apply_filters("acfe/flexible/prepend/template", $prepend, $flexible, $layout);

		acf_render_field_wrap([
			'prepend' => $prepend,
			'name' => 'acfe_flexible_render_template',
			'type' => 'text',
			'class' => 'acf-fc-meta-name',
			'prefix' => $prefix,
			'value' => $layout['acfe_flexible_render_template'],
			'placeholder' => 'template.php',
		], 'ul');


		// Style
		$prepend = apply_filters("acfe/flexible/prepend/style", $prepend, $flexible, $layout);

		acf_render_field_wrap([
			'prepend' => $prepend,
			'name' => 'acfe_flexible_render_style',
			'type' => 'text',
			'class' => 'acf-fc-meta-name',
			'prefix' => $prefix,
			'value' => $layout['acfe_flexible_render_style'],
			'placeholder' => 'style.css',
		], 'ul');


		// Script
		$prepend = apply_filters("acfe/flexible/prepend/script", $prepend, $flexible, $layout);

		acf_render_field_wrap([
			'prepend' => $prepend,
			'name' => 'acfe_flexible_render_script',
			'type' => 'text',
			'class' => 'acf-fc-meta-name',
			'prefix' => $prefix,
			'value' => $layout['acfe_flexible_render_script'],
			'placeholder' => 'script.js',
		], 'ul');

	}


	/**
	 * render_field
	 *
	 * @param $field
	 */
	function render_field($field) {

		// check setting
		if (!acf_maybe_get($field, 'acfe_flexible_layouts_templates') || !acf_maybe_get($field, 'acfe_flexible_layouts_previews')) {
			return;
		}

		// vars
		global $is_preview;
		$is_preview = true;

		// actions
		do_action("acfe/flexible/enqueue", $field, $is_preview);

		// loop
		foreach ($field['layouts'] as $layout) {

			// Enqueue
			acfe_flexible_render_layout_enqueue($layout, $field);

		}

	}


	/**
	 * wrapper_attributes
	 *
	 * @param $wrapper
	 * @param $field
	 *
	 * @return mixed
	 */
	function wrapper_attributes($wrapper, $field) {

		if (acf_maybe_get($field, 'acfe_flexible_layouts_placeholder')) {
			$wrapper['data-acfe-flexible-placeholder'] = 1;
		}

		if (acf_maybe_get($field, 'acfe_flexible_layouts_templates') && acf_maybe_get($field, 'acfe_flexible_layouts_previews')) {
			$wrapper['data-acfe-flexible-placeholder'] = 1;
			$wrapper['data-acfe-flexible-preview'] = 1;
		}

		return $wrapper;

	}


	/**
	 * prepare_layout
	 *
	 * @param $layout
	 * @param $field
	 * @param $i
	 * @param $value
	 * @param $prefix
	 *
	 * @return mixed
	 */
	function prepare_layout($layout, $field, $i, $value, $prefix) {

		if (!acf_maybe_get($field, 'acfe_flexible_layouts_placeholder') && !acf_maybe_get($field, 'acfe_flexible_layouts_previews')) {
			return $layout;
		}

		// Vars
		$key = $field['key'];
		$l_name = $layout['name'];

		$placeholder = [
			'class' => 'acfe-fc-placeholder',
			'title' => __('Edit layout', 'acfe'),
		];

		$placeholder = apply_filters("acfe/flexible/layouts/placeholder", $placeholder, $layout, $field, $i, $value, $prefix);

		$html = false;

		if (!empty($value) && acf_maybe_get($field, 'acfe_flexible_layouts_previews')) {

			ob_start();

			$this->layout_preview([
				'post_id' => acf_get_valid_post_id(),
				'i' => $i,
				'field_key' => $key,
				'layout' => $l_name,
				'value' => $value,
			]);

			$html = ob_get_clean();

			if (strlen($html) > 0) {
				$placeholder['class'] .= ' acfe-fc-preview';
			}

		}

		?>

		<div <?php echo acf_esc_atts($placeholder); ?>>
			<a href="#" class="button">
				<span class="dashicons dashicons-edit"></span>
			</a>

			<div class="acfe-fc-overlay"></div>
			<div class="acfe-flexible-placeholder -preview"><?php echo $html; ?></div>
		</div>

		<?php

		return $layout;

	}

	var $no_post = false;

	/**
	 * layout_preview
	 *
	 * @param $options
	 *
	 * @return bool|null
	 */
	function layout_preview($options = []) {

		if (empty($options)) {

			// Options
			$options = acf_parse_args($_POST, [
				'post_id' => 0,
				'i' => 0,
				'field_key' => '',
				'nonce' => '',
				'layout' => '',
				'value' => []
			]);

		}

		// Load field
		$field = acf_get_field($options['field_key']);

		if (!is_array($field) or empty($field['type'])) {
			return $this->return_or_die();
		}

		// Layout
		$instance = acf_get_field_type('flexible_content');
		$layout = $instance->get_layout($options['layout'], $field);

		if (!$layout) {
			return $this->return_or_die();
		}

		// Global
		global $is_preview;

		// Vars
		$is_preview = true;
		$post_id = acf_uniqid('acfe/flexible_content/preview');

		// Prepare values
		$meta = [
			$options['field_key'] => [
				wp_unslash($options['value'])
			]
		];

		acfe_setup_meta($meta, $post_id, true);

		if (have_rows($options['field_key'])):
			while (have_rows($options['field_key'])): the_row();

				global $post;
				if ($this->no_post) {
					$post = null;
				}

				// context:ajax/taxonomy/user page
				if (!isset($post)) {

					$post_id = acfe_get_post_id('array');

					// ajax
					if ($post_id['type'] === 'post') {
						$post = get_post($post_id['id']);

						// /taxonomy/user page
					} else {

						// assign for next call
						// this fix the issue where doing new WP_Query() in a taxonomy page
						// will setup the last $post as global and break next get_field() calls
						$this->no_post = true;
					}

				}

				if ($this->no_post) {
					add_action('loop_end', [$this, 'loop_end']);
				}

				add_filter('acf/pre_load_post_id', [$this, 'pre_load_post_id'], 2, 2);
				add_action('loop_start', [$this, 'loop_start']);

				// update loop for get_row_index
				$i = (int) $options['i'];
				acf_update_loop('active', 'i', $i);

				// include template
				acfe_flexible_render_layout_template($layout, $field);

				remove_filter('acf/pre_load_post_id', [$this, 'pre_load_post_id'], 2);
				remove_action('loop_start', [$this, 'loop_start']);

				if ($this->no_post) {
					remove_action('loop_end', [$this, 'loop_end']);
				}

			endwhile;
		endif;

		acfe_reset_meta();

		return $this->return_or_die();

	}

	/**
	 * loop_start
	 *
	 * Allow to use new WP_Query() in the layout preview/admin
	 * https://core.trac.wordpress.org/ticket/18408
	 *
	 * @return void
	 */
	function loop_start() {

		if (is_admin()) {
			global $wp_query, $post;
			$wp_query->post = $post;
		}

	}


	/**
	 * loop_end
	 *
	 * Quick hack for taxonomy/user/options page
	 * This reset the global $post after the end of while($q->have_posts()): $q->the_post()
	 * Not ideal, but it works
	 *
	 * @return void
	 */
	function loop_end() {

		if (is_admin()) {
			global $post;
			$post = null;
		}

	}


	/**
	 * pre_load_post_id
	 *
	 * This only affect get_field() in the layout preview/admin
	 *
	 * @param $null
	 * @param $post_id
	 *
	 * @return false|mixed
	 */
	function pre_load_post_id($null, $post_id) {

		if (!$post_id) {

			global $post;
			if ($post) {
				return null; // let acf find the post id from the $post
			}

			// taxonomy/user/options page
			// retrieve the correct post id dynamically
			return acfe_get_post_id();

		}

		return $null;

	}


	/**
	 * return_or_die
	 *
	 * @return bool|void
	 */
	function return_or_die() {

		// check ajax & make sure the action is correct
		if (wp_doing_ajax() && acf_maybe_get_POST('action') === 'acfe/flexible/layout_preview') {
			die;
		}

		return true;

	}

}

acf_new_instance('acfe_field_flexible_content_preview');