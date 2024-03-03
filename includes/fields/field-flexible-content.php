<?php

if (!defined('ABSPATH')) {
	exit;
}

class acfe_field_flexible_content extends acfe_field_extend {

	/**
	 * initialize
	 */
	function initialize() {

		$this->name = 'flexible_content';
		$this->replace = [
			'render_field',
		];

		$this->add_field_action('acf/render_field_settings', [$this, '_render_field_settings'], 0);
		$this->add_action('acf/render_field', [$this, 'render_layout_label'], 0);
		$this->add_action('acf/render_field', [$this, 'render_layout_settings']);

		$this->replace_action('wp_ajax_acf/fields/flexible_content/layout_title', [$this, 'ajax_layout_title']);
		$this->replace_action('wp_ajax_nopriv_acf/fields/flexible_content/layout_title', [$this, 'ajax_layout_title']);

	}


	/**
	 * input_admin_enqueue_scripts
	 */
	function input_admin_enqueue_scripts() {

		// localize
		acf_localize_text([
			'Layout data has been copied to your clipboard.' => __('Layout data has been copied to your clipboard.', 'acfe'),
			'Layouts data have been copied to your clipboard.' => __('Layouts data have been copied to your clipboard.', 'acfe'),
			'Please copy the following layout(s) data to your clipboard.' => __('Please copy the following layout(s) data to your clipboard.', 'acfe'),
			'Please paste previously copied layout data in the following field:' => __('Please paste previously copied layout data in the following field:', 'acfe'),
			'You can now paste it in the same Flexible Content on another page, using the "Paste" button action.' => __('You can now paste it in the same Flexible Content on another page, using the "Paste" button action.', 'acfe'),
		]);

	}


	/**
	 * field_group_admin_head
	 */
	function field_group_admin_head() {

		// clear fields cache
		// this fix an issue where plugins could query acf fields using acf_get_fields() very early
		// and push unwanted settings such as "inline title" on the Field Group UI
		acf_get_store('fields')->reset();

	}


	/**
	 * _render_field_settings
	 *
	 * acf/render_field_settings/type=flexible_content:0
	 *
	 * @param $field
	 */
	function _render_field_settings($field) {

		// Action
		do_action("acfe/flexible/render_field_settings", $field);

	}


	/**
	 * render_layout_label
	 *
	 * @param $field
	 */
	function render_layout_label($field) {

		// validate setting
		if ($field['_name'] !== 'label' || stripos($field['name'], '[layouts]') === false) {
			return;
		}

		echo '</li>';

		acf_render_field_wrap([
			'label' => __('Settings', 'acfe'),
			'type' => 'hidden',
			'name' => 'acfe_flexible_settings_label'
		], 'ul');

		echo '<li>';

	}


	/**
	 * render_layout_settings
	 *
	 * @param $field
	 */
	function render_layout_settings($field) {

		// validate setting
		if ($field['_name'] !== 'max' || stripos($field['name'], '[layouts]') === false) {
			return;
		}

		// Prefix
		$prefix = $field['prefix'];

		// Black magic
		parse_str($prefix, $output);
		$keys = acfe_array_keys_r($output);

		// ...
		$_field_id = $keys[1];
		$_layout_key = $keys[3];

		// Profit!
		$flexible = acf_get_field($_field_id);

		if (!is_array($flexible) or empty($flexible['type'])) {
			return;
		}

		if (!acf_maybe_get($flexible, 'layouts')) {
			return;
		}

		$layout = $flexible['layouts'][$_layout_key];

		// Do Actions
		do_action("acfe/flexible/render_layout_settings", $flexible, $layout, $prefix);

	}


	/**
	 * validate_field
	 *
	 * @param $field
	 *
	 * @return mixed|null
	 */
	function validate_field($field) {

		// Defaults
		$_field = [];
		$_layout = [];

		// Filters
		$_field = apply_filters("acfe/flexible/defaults_field", $_field);
		$_layout = apply_filters("acfe/flexible/defaults_layout", $_layout);

		foreach ($_field as $k => $v) {

			if (!isset($field[$k])) {
				$field[$k] = $v;
			}

			if (is_array($v)) {
				foreach ($v as $ak => $av) {

					if (!isset($field[$k][$ak])) {
						$field[$k][$ak] = $av;
					}

				}
			}

		}

		foreach ($field['layouts'] as &$layout) {
			foreach ($_layout as $k => $v) {

				if (!isset($layout[$k])) {
					$layout[$k] = $v;
				}

				if (is_array($v)) {
					foreach ($v as $ak => $av) {

						if (!isset($layout[$k][$ak])) {
							$layout[$k][$ak] = $av;
						}

					}
				}

			}
		}

		return apply_filters('acfe/flexible/validate_field', $field);

	}


	/**
	 * prepare_field
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function prepare_field($field) {

		foreach ($field['layouts'] as &$layout) {

			// Prepend
			$prepend = apply_filters("acfe/flexible/layouts/label_prepend", '', $layout, $field);

			// Atts
			$atts = apply_filters("acfe/flexible/layouts/label_atts", [], $layout, $field);

			// Label
			$layout['label'] = $prepend . '<span ' . acf_esc_atts($atts) . '>' . $layout['label'] . '</span>';

		}

		return $field;

	}


	/**
	 * load_fields
	 *
	 * @param $fields
	 * @param $field
	 *
	 * @return mixed|null
	 */
	function load_fields($fields, $field) {

		if (acfe_is_admin_screen()) {
			return $fields;
		}

		// check layouts
		if (empty($field['layouts'])) {
			return $fields;
		}

		return apply_filters("acfe/flexible/load_fields", $fields, $field);

	}


	/**
	 * field_wrapper_attributes
	 *
	 * @param $wrapper
	 * @param $field
	 *
	 * @return mixed|null
	 */
	function field_wrapper_attributes($wrapper, $field) {

		return apply_filters('acfe/flexible/wrapper_attributes', $wrapper, $field);

	}


	/**
	 * render_field
	 *
	 * @param $field
	 */
	function render_field($field) {

		// defaults
		if (empty($field['button_label'])) {
			$field['button_label'] = __('Add Row', 'acf');
		}

		// sort layouts into names
		$layouts = [];

		foreach ($field['layouts'] as $layout) {
			$layouts[$layout['name']] = $layout;
		}

		// vars
		$div = [
			'class' => 'acf-flexible-content',
			'data-min' => $field['min'],
			'data-max' => $field['max']
		];

		// empty
		if (empty($field['value'])) {
			$div['class'] .= ' -empty';
		}

		// no value message
		$no_value_message = __('Click the "%s" button below to start creating your layout', 'acf');
		$no_value_message = apply_filters('acf/fields/flexible_content/no_value_message', $no_value_message, $field);

		$values = [
			'class' => 'values'
		];

		$values = apply_filters("acfe/flexible/div_values", $values, $field);

		?>
		<div <?php echo acf_esc_atts($div); ?>>

			<?php acf_hidden_input(['name' => $field['name']]); ?>

			<div class="no-value-message">
				<?php printf($no_value_message, $field['button_label']); ?>
			</div>

			<div class="clones">
				<?php foreach ($layouts as $layout):

					// Models
					$model = false;
					$model = apply_filters("acfe/flexible/layouts/model", $model, $field, $layout);

					if (!$model) {
						$this->render_layout($field, $layout, 'acfcloneindex', []);
					}

				endforeach; ?>
			</div>

			<div <?php echo acf_esc_atts($values); ?>>
				<?php if (!empty($field['value'])):

					foreach ($field['value'] as $i => $value):

						// validate
						if (empty($layouts[$value['acf_fc_layout']])) {
							continue;
						}

						// render
						$this->render_layout($field, $layouts[$value['acf_fc_layout']], $i, $value);

					endforeach;

				endif; ?>
			</div>

			<?php

			// Remove actions
			$remove_actions = false;
			$remove_actions = apply_filters("acfe/flexible/remove_actions", $remove_actions, $field);

			if (!$remove_actions) {

				// Wrapper
				$wrapper = [];
				$wrapper = apply_filters('acfe/flexible/action_wrapper', $wrapper, $field);

				// Button
				$button = [
					'class' => 'acf-button button',
					'href' => '#',
					'data-name' => 'add-layout',
				];

				$button = apply_filters('acfe/flexible/action_button', $button, $field);

				if (!empty($wrapper)) {
					echo '<div ' . acf_esc_atts($wrapper) . '>';
				}

				?>

				<div class="acf-actions">
					<a <?php echo acf_esc_atts($button); ?>><?php echo $field['button_label']; ?></a>

					<?php

					$secondary_actions = [];
					$secondary_actions = apply_filters("acfe/flexible/secondary_actions", $secondary_actions, $field);

					if (!empty($secondary_actions)) {

						$button_secondary = [
							'class' => 'button',
							'style' => 'padding-left:5px;padding-right:5px; margin-left:3px;',
							'href' => '#',
							'data-name' => 'acfe-flexible-control-button',
						];

						$button_secondary = apply_filters('acfe/flexible/action_button_secondary', $button_secondary, $field);
						?>

						<a <?php echo acf_esc_atts($button_secondary); ?>>
							<span class="dashicons dashicons-arrow-down-alt2" style="vertical-align:text-top;width:auto;height:auto;font-size:13px;line-height:20px;"></span>
						</a>

						<script type="text-html" class="tmpl-acfe-flexible-control-popup">
							<ul>
							<?php foreach ($secondary_actions as $secondary_action) { ?>
                                <li><?php echo $secondary_action; ?></li>
                            <?php } ?>
							</ul>
						</script>

					<?php } ?>

				</div>

			<?php
			if (!empty($wrapper)) {
				echo '</div>';
			}
			?>

				<script type="text-html" class="tmpl-popup">
					<ul>
					<?php foreach ($layouts as $layout):

						$atts = [
							'href' => '#',
							'data-layout' => $layout['name'],
							'data-min' => $layout['min'],
							'data-max' => $layout['max'],
						];

						?><li><a <?php echo acf_esc_atts($atts); ?>><?php echo $layout['label']; ?></a></li><?php

					endforeach; ?>
					</ul>
				</script>

			<?php } ?>

		</div>
		<?php

	}


	/**
	 * render_layout
	 *
	 * @param $field
	 * @param $layout
	 * @param $i
	 * @param $value
	 */
	function render_layout($field, $layout, $i, $value) {

		// vars
		$id = ($i === 'acfcloneindex') ? 'acfcloneindex' : "row-$i";
		$prefix = $field['name'] . '[' . $id . ']';

		// div
		$div = [
			'class' => 'layout',
			'data-id' => $id,
			'data-layout' => $layout['name']
		];

		// is clone?
		if (!is_numeric($i)) {
			$div['class'] .= ' acf-clone';
		}

		$div = apply_filters("acfe/flexible/layouts/div", $div, $layout, $field, $i, $value, $prefix);

		// handle
		$handle = [
			'class' => 'acf-fc-layout-handle',
			'title' => __('Drag to reorder', 'acf'),
			'data-name' => 'collapse-layout',
		];

		$handle = apply_filters("acfe/flexible/layouts/handle", $handle, $layout, $field, $i, $value, $prefix);

		?>
		<div <?php echo acf_esc_atts($div); ?>>

			<?php acf_hidden_input(['name' => $prefix . '[acf_fc_layout]', 'value' => $layout['name']]); ?>

			<div <?php echo acf_esc_atts($handle); ?>>
				<?php echo $this->get_layout_title($field, $layout, $i, $value); ?>
			</div>

			<?php

			$layout = apply_filters("acfe/flexible/prepare_layout", $layout, $field, $i, $value, $prefix);

			do_action("acfe/flexible/pre_render_layout", $layout, $field, $i, $value, $prefix);

			// Prepare Editor
			add_filter('acf/prepare_field/type=wysiwyg', [$this, 'prepare_layout_editor']);

			// Render Layout Fields
			$this->render_layout_fields($layout, $field, $i, $value, $prefix);

			// Unprepare Editor
			remove_filter('acf/prepare_field/type=wysiwyg', [$this, 'prepare_layout_editor']);

			do_action("acfe/flexible/render_layout", $layout, $field, $i, $value, $prefix);

			?>

		</div>
		<?php

	}


	/**
	 * ajax_layout_title
	 *
	 * wp_ajax_acf/fields/flexible_content/layout_title
	 */
	function ajax_layout_title() {

		// options
		$options = acf_parse_args($_POST, [
			'post_id' => 0,
			'i' => 0,
			'field_key' => '',
			'nonce' => '',
			'layout' => '',
			'value' => [],
		]);

		// load field
		$field = acf_get_field($options['field_key']);

		if (empty($field) or empty($field['type'])) {
			exit();
		}

		// vars
		$layout = $this->instance->get_layout($options['layout'], $field);

		if (!$layout) {
			exit();
		}

		// title
		$title = $this->get_layout_title($field, $layout, $options['i'], $options['value']);

		// echo
		echo $title;
		exit();

	}


	/**
	 * get_layout_title
	 *
	 * @param $field
	 * @param $layout
	 * @param $i
	 * @param $value
	 *
	 * @return string
	 */
	function get_layout_title($field, $layout, $i, $value) {

		// vars
		$rows = [];
		$rows[$i] = $value;

		// add loop
		acf_add_loop([
			'selector' => $field['name'],
			'name' => $field['name'],
			'value' => $rows,
			'field' => $field,
			'i' => $i,
			'post_id' => 0,
		]);

		// vars
		$title = $layout['label'];

		// filters
		$title = apply_filters("acf/fields/flexible_content/layout_title", $title, $field, $layout, $i);

		if (in_array('title', $field['acfe_flexible_add_actions'])) {

			// Get Layout Title
			$value = get_sub_field('acfe_flexible_layout_title');

			if (!empty($value)) {
				$title = wp_unslash($value);
			}

			$title = '<span class="acfe-layout-title-text">' . $title . '</span>';

		}

		$attrs = [
			'class' => 'acfe-layout-title'
		];

		$attrs = apply_filters("acf/fields/flexible_content/layout_attrs", $attrs, $field, $layout, $i);

		// remove loop
		acf_remove_loop();

		// prepend order
		$order = is_numeric($i) ? $i + 1 : 0;

		// return
		return '<span class="acf-fc-layout-order">' . $order . '</span> <span ' . acf_esc_atts($attrs) . '>' . acf_esc_html($title) . '</span>';

	}


	/**
	 * render_layout_fields
	 *
	 * @param $layout
	 * @param $field
	 * @param $i
	 * @param $value
	 * @param $prefix
	 */
	function render_layout_fields($layout, $field, $i, $value, $prefix) {

		// vars
		$sub_fields = $layout['sub_fields'];
		$el = $layout['display'] === 'table' ? 'td' : 'div';

		if (empty($sub_fields)) {
			return;
		}

		if ($layout['display'] == 'table'): ?>
			<table class="acf-table">
			<thead>
			<tr>
				<?php foreach ($sub_fields as $sub_field):

					// prepare field (allow sub fields to be removed)
					$sub_field = acf_prepare_field($sub_field);

					// bail ealry if no field
					if (!$sub_field) {
						continue;
					}

					// vars
					$atts = [];
					$atts['class'] = 'acf-th';
					$atts['data-name'] = $sub_field['_name'];
					$atts['data-type'] = $sub_field['type'];
					$atts['data-key'] = $sub_field['key'];

					// Add custom width
					if ($sub_field['wrapper']['width']) {

						$atts['data-width'] = $sub_field['wrapper']['width'];
						$atts['style'] = 'width: ' . $sub_field['wrapper']['width'] . '%;';

					}

					?>
					<th <?php echo acf_esc_atts($atts); ?>>
						<?php echo acf_get_field_label($sub_field); ?>
						<?php if ($sub_field['instructions']): ?>
							<p class="description"><?php echo $sub_field['instructions']; ?></p>
						<?php endif; ?>
					</th>

				<?php endforeach; ?>
			</tr>
			</thead>

			<tbody>
			<tr class="acf-row">
		<?php else: ?>
			<div class="acf-fields <?php if ($layout['display'] == 'row'): ?>-left<?php endif; ?>">
		<?php endif; ?>

		<?php

		// loop though sub fields
		foreach ($sub_fields as $sub_field) {

			// add value
			if (isset($value[$sub_field['key']])) {

				$sub_field['value'] = $value[$sub_field['key']];

			} elseif (isset($sub_field['default_value'])) {

				$sub_field['value'] = $sub_field['default_value'];

			}

			// update prefix to allow for nested values
			$sub_field['prefix'] = $prefix;

			// render input
			acf_render_field_wrap($sub_field, $el);

		}

		?>

		<?php if ($layout['display'] == 'table'): ?>
			</tr>
			</tbody>
			</table>

			<?php if (!$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled'] && in_array('close', $field['acfe_flexible_add_actions'])) { ?>
				<div class="acfe-flexible-opened-actions"><a href="javascript:void(0);" class="button"><?php _e('Close', 'acf'); ?></button></a></div>
			<?php } ?>

		<?php else: ?>

			<?php if (!$field['acfe_flexible_modal_edit']['acfe_flexible_modal_edit_enabled'] && in_array('close', $field['acfe_flexible_add_actions'])) { ?>
				<div class="acfe-flexible-opened-actions"><a href="javascript:void(0);" class="button"><?php _e('Close', 'acf'); ?></button></a></div>
			<?php } ?>

			</div>
		<?php endif;

	}


	/**
	 * prepare_layout_editor
	 *
	 * @param $field
	 *
	 * @return mixed
	 */
	function prepare_layout_editor($field) {

		$field['delay'] = 1;
		$field['acfe_wysiwyg_auto_init'] = 1;
		return $field;

	}


	/**
	 * translate_field
	 *
	 * @param $field
	 */
	function translate_field($field) {

		if (isset($field['acfe_flexible_modal']['acfe_flexible_modal_title'])) {
			$field['acfe_flexible_modal']['acfe_flexible_modal_title'] = acf_translate($field['acfe_flexible_modal']['acfe_flexible_modal_title']);
		}

		// loop
		if (!empty($field['layouts'])) {

			foreach ($field['layouts'] as &$layout) {

				if (isset($layout['acfe_flexible_category'])) {
					$layout['acfe_flexible_category'] = acf_translate($layout['acfe_flexible_category']);
				}

			}

		}

		// return
		return $field;

	}

}

acf_new_instance('acfe_field_flexible_content');

// includes
acfe_include('includes/fields/field-flexible-content-actions.php');
acfe_include('includes/fields/field-flexible-content-async.php');
acfe_include('includes/fields/field-flexible-content-controls.php');
acfe_include('includes/fields/field-flexible-content-edit.php');
acfe_include('includes/fields/field-flexible-content-hide.php');
acfe_include('includes/fields/field-flexible-content-preview.php');
acfe_include('includes/fields/field-flexible-content-select.php');
acfe_include('includes/fields/field-flexible-content-settings.php');
acfe_include('includes/fields/field-flexible-content-state.php');
acfe_include('includes/fields/field-flexible-content-thumbnail.php');
