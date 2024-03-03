<?php

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('acfe_module_block_type_field_groups')) {
	return;
}

class acfe_module_block_type_field_groups {

	/**
	 * construct
	 */
	function __construct() {

		add_filter('acfe/module/register_field_groups/module=block_type', [$this, 'register_field_groups'], 10, 2);

	}


	/**
	 * register_field_groups
	 *
	 * @param $field_groups
	 * @param $module
	 *
	 * @return mixed
	 */
	function register_field_groups($field_groups, $module) {

		$field_groups[] = [
			'key' => 'group_acfe_block_type',
			'title' => __('Block Type', 'acfe'),

			'location' => [
				[
					[
						'param' => 'post_type',
						'operator' => '==',
						'value' => $module->post_type,
					],
				],
			],

			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'left',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',

			'fields' => [
				[
					'key' => 'field_tab_general',
					'label' => 'General',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
						'data-no-preference' => true,
					],
					'placement' => 'top',
					'endpoint' => 0,
				],
				[
					'key' => 'field_name',
					'label' => 'Name',
					'name' => 'name',
					'type' => 'acfe_slug',
					'instructions' => __('A unique name that identifies the block (without namespace).<br />Note: A block name can only contain lowercase alphanumeric characters and dashes, and must begin with a letter.', 'acfe'),
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_description',
					'label' => 'Description',
					'name' => 'description',
					'type' => 'textarea',
					'instructions' => __('This is a short description for your block.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => 3,
					'new_lines' => '',
				],
				[
					'key' => 'field_category',
					'label' => 'Category',
					'name' => 'category',
					'type' => 'text',
					'instructions' => __('Blocks are grouped into categories to help users browse and discover them. The core provided categories are [ common | formatting | layout | widgets | embed ]. Plugins and Themes can also register custom block categories.<br /><br />Plugins and Themes can also register <a href="https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#managing-block-categories" target="_blank">custom block categories</a>.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => 'common',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_keywords',
					'label' => 'Keywords',
					'name' => 'keywords',
					'type' => 'textarea',
					'instructions' => __('An array of search terms to help user discover the block while searching.<br />One line for each keyword. ie:<br /><br />quote<br />mention<br />cite', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => '5',
					'new_lines' => '',
					'encode_value' => true,
				],
				[
					'key' => 'field_post_types',
					'label' => 'Post types',
					'name' => 'post_types',
					'type' => 'acfe_post_types',
					'instructions' => __('An array of post types to restrict this block type to.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'field_type' => 'checkbox',
					'return_format' => 'name',
				],
				[
					'key' => 'field_mode',
					'label' => 'Mode',
					'name' => 'mode',
					'type' => 'radio',
					'instructions' => __('The display mode for your block. Available settings are "auto", "preview" and "edit". Defaults to "preview".<br /><br /><strong>Preview</strong>: Preview is always shown. Edit form appears in sidebar when block is selected.<br /><strong>Auto</strong>: Preview is shown by default but changes to edit form when block is selected.<br /><strong>Edit</strong>: Edit form is always shown.<br /><br />Note. When in "preview" or "edit" modes, an icon will appear in the block toolbar to toggle between modes.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'choices' => [
						'preview' => 'Preview',
						'auto' => 'Auto',
						'edit' => 'Edit',
					],
					'default_value' => 'preview',
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
				],
				[
					'key' => 'field_align',
					'label' => 'Align',
					'name' => 'align',
					'type' => 'radio',
					'instructions' => __('The default block alignment. Available settings are "left", "center", "right", "wide" and "full". Defaults to an empty string.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'choices' => [
						'' => 'None',
						'left' => 'Left',
						'center' => 'Center',
						'right' => 'Right',
						'wide' => 'Wide',
						'full' => 'Full',
					],
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
				],
				[
					'key' => 'field_align_text',
					'label' => 'Align text',
					'name' => 'align_text',
					'type' => 'radio',
					'instructions' => __('The default block text alignment (see supports setting for more info). Available settings are "left", "center" and "right". Defaults to the current language\'s text alignment.', 'acfe'),
					'required' => 0,
					'conditional_logic' => [],
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'choices' => [
						'' => 'Default',
						'left' => 'Left',
						'center' => 'Center',
						'right' => 'Right',
					],
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
				],
				[
					'key' => 'field_align_content',
					'label' => 'Align content',
					'name' => 'align_content',
					'type' => 'text',
					'instructions' => __('The default block content alignment (see supports setting for more info). Available settings are "top", "center" and "bottom".<br /><br />When utilising the "Matrix" control type, additional settings are available to specify all 9 positions from “top left” to “bottom right”. Defaults to "top".', 'acfe'),
					'required' => 0,
					'conditional_logic' => [],
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => 'top',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],

				[
					'key' => 'field_tab_supports',
					'label' => 'Supports',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'placement' => 'top',
					'endpoint' => 0,
				],

				[
					'key' => 'field_supports_anchor',
					'label' => 'Anchor',
					'name' => 'supports_anchor',
					'type' => 'true_false',
					'instructions' => __('Enable Anchor attribute. Defaults to false', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => 'True',
					'ui_off_text' => 'False',
					'cleanup_key' => true,
				],

				[
					'key' => 'field_supports_jsx',
					'label' => 'Inner Block (JSX)',
					'name' => 'supports_jsx',
					'type' => 'true_false',
					'instructions' => __('Parse the block HTML as JSX for the Inner Block Component to function within the React based block editor.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => 'True',
					'ui_off_text' => 'False',
					'cleanup_key' => true,
				],

				[
					'key' => 'field_supports_align',
					'label' => 'Align',
					'name' => 'supports_align',
					'type' => 'true_false',
					'instructions' => __('This property adds block controls which allow the user to change the block’s alignment. Defaults to true. Set to false to hide the alignment toolbar. Set to an array of specific alignment names to customize the toolbar.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'message' => '',
					'default_value' => 1,
					'ui' => 1,
					'ui_on_text' => 'True',
					'ui_off_text' => 'False',
					'cleanup_key' => true,
				],
				[
					'key' => 'field_supports_align_args',
					'label' => 'Align arguments',
					'name' => 'supports_align_args',
					'type' => 'textarea',
					'instructions' => __('Set to an array of specific alignment names to customize the toolbar.<br />One line for each name. ie:<br /><br />left<br />right<br />full', 'acfe'),
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_supports_align',
								'operator' => '==',
								'value' => '1',
							],
						],
					],
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => 5,
					'new_lines' => '',
					'encode_value' => true,
					'cleanup_key' => true,
				],

				[
					'key' => 'field_supports_align_text',
					'label' => 'Align text',
					'name' => 'supports_align_text',
					'type' => 'true_false',
					'instructions' => __('This property enables a toolbar button to control the block’s text alignment. Defaults to false. Set to true to show the alignment toolbar button.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => 'True',
					'ui_off_text' => 'False',
					'cleanup_key' => true,
				],

				[
					'key' => 'field_supports_align_content',
					'label' => 'Align content',
					'name' => 'supports_align_content',
					'type' => 'radio',
					'instructions' => __('This property enables a toolbar button to control the block’s inner content alignment. Defaults to false. Set to true to show the alignment toolbar button, or set to "matrix" to enable the full alignment matrix in the toolbar', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'choices' => [
						'false' => 'False',
						'true' => 'True',
						'matrix' => 'Matrix',
					],
					'default_value' => 'false',
					'layout' => 'vertical',
					'return_format' => 'value',
					'cleanup_key' => true,
				],

				[
					'key' => 'field_supports_full_height',
					'label' => 'Full height',
					'name' => 'supports_full_height',
					'type' => 'true_false',
					'instructions' => __('This property enables the full height button on the toolbar of a block', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => 'True',
					'ui_off_text' => 'False',
					'cleanup_key' => true,
				],

				[
					'key' => 'field_supports_mode',
					'label' => 'Mode',
					'name' => 'supports_mode',
					'type' => 'true_false',
					'instructions' => __('This property allows the user to toggle between edit and preview modes via a button. Defaults to true.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'message' => '',
					'default_value' => 1,
					'ui' => 1,
					'ui_on_text' => 'True',
					'ui_off_text' => 'False',
					'cleanup_key' => true,
				],
				[
					'key' => 'field_supports_multiple',
					'label' => 'Multiple',
					'name' => 'supports_multiple',
					'type' => 'true_false',
					'instructions' => __('This property allows the block to be added multiple times. Defaults to true.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'message' => '',
					'default_value' => 1,
					'ui' => 1,
					'ui_on_text' => 'True',
					'ui_off_text' => 'False',
					'cleanup_key' => true,
				],

				[
					'key' => 'field_tab_icon',
					'label' => 'Icon',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'placement' => 'top',
					'endpoint' => 0,
				],
				[
					'key' => 'field_icon_type',
					'label' => 'Icon Type',
					'name' => 'icon_type',
					'type' => 'radio',
					'instructions' => __('Simple: Specify a Dashicons class or SVG path<br />Colors: Specify colors & Dashicons class', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'choices' => [
						'simple' => 'Simple',
						'colors' => 'Colors',
					],
					'default_value' => 'simple',
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
					'cleanup_key' => true,
				],
				[
					'key' => 'field_icon_text',
					'label' => 'Icon',
					'name' => 'icon_text',
					'type' => 'text',
					'instructions' => __('An icon property can be specified to make it easier to identify a block. These can be any of WordPress’ Dashicons, or a custom svg element.', 'acfe'),
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_icon_type',
								'operator' => '==',
								'value' => 'simple',
							],
						],
					],
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'cleanup_key' => true,
				],
				[
					'key' => 'field_icon_background',
					'label' => 'Icon background',
					'name' => 'icon_background',
					'type' => 'color_picker',
					'instructions' => __('Specifying a background color to appear with the icon e.g.: in the inserter.', 'acfe'),
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_icon_type',
								'operator' => '==',
								'value' => 'colors',
							],
						],
					],
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'cleanup_key' => true,
				],
				[
					'key' => 'field_icon_foreground',
					'label' => 'Icon foreground',
					'name' => 'icon_foreground',
					'type' => 'color_picker',
					'instructions' => __('Specifying a color for the icon (optional: if not set, a readable color will be automatically defined)', 'acfe'),
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_icon_type',
								'operator' => '==',
								'value' => 'colors',
							],
						],
					],
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'cleanup_key' => true,
				],
				[
					'key' => 'field_icon_src',
					'label' => 'Icon src',
					'name' => 'icon_src',
					'type' => 'text',
					'instructions' => __('Specifying a dashicon for the block', 'acfe'),
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_icon_type',
								'operator' => '==',
								'value' => 'colors',
							],
						],
					],
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'cleanup_key' => true,
				],

				[
					'key' => 'field_tab_render',
					'label' => 'Render',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'placement' => 'top',
					'endpoint' => 0,
				],
				[
					'key' => 'field_render_template',
					'label' => 'Render template',
					'name' => 'render_template',
					'type' => 'text',
					'instructions' => __('The path to a template file used to render the block HTML. This can either be a relative path to a file within the active theme, parent theme, wp-content directory or a full path to any file.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_render_callback',
					'label' => 'Render callback',
					'name' => 'render_callback',
					'type' => 'text',
					'instructions' => __('Instead of providing a render_template, a callback function name may be specified to output the block\'s HTML.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],

				[
					'key' => 'field_tab_enqueue',
					'label' => 'Enqueue',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'placement' => 'top',
					'endpoint' => 0,
				],
				[
					'key' => 'field_enqueue_style',
					'label' => 'Enqueue style',
					'name' => 'enqueue_style',
					'type' => 'text',
					'instructions' => __('The url to a .css file to be enqueued whenever your block is displayed (front-end and back-end). This can either be a relative path to a file within the active theme, parent theme, wp-content directory or a full path to any file.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_enqueue_script',
					'label' => 'Enqueue script',
					'name' => 'enqueue_script',
					'type' => 'text',
					'instructions' => __('The url to a .js file to be enqueued whenever your block is displayed (front-end and back-end). This can either be a relative path to a file within the active theme, parent theme, wp-content directory or a full path to any file.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_enqueue_assets',
					'label' => 'Enqueue assets',
					'name' => 'enqueue_assets',
					'type' => 'text',
					'instructions' => __('A callback function that runs whenever your block is displayed (front-end and back-end) and enqueues scripts and/or styles.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
			],
		];

		return $field_groups;

	}

}

acf_new_instance('acfe_module_block_type_field_groups');