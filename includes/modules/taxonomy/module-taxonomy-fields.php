<?php

if (!defined('ABSPATH')) {
	exit;
}

if (class_exists('acfe_module_taxonomy_field_groups')) {
	return;
}

class acfe_module_taxonomy_field_groups {

	/**
	 * construct
	 */
	function __construct() {

		add_filter('acfe/module/register_field_groups/module=taxonomy', [$this, 'register_field_groups'], 10, 2);

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
			'key' => 'group_acfe_taxonomy',
			'title' => __('Taxonomy', 'acfe'),

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
					'instructions' => __('The name of the taxonomy. Name should only contain lowercase letters and the underscore character, and not be more than 32 characters long (database structure restriction)', 'acfe'),
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
					'maxlength' => 32,
				],
				[
					'key' => 'field_description',
					'label' => 'Description',
					'name' => 'description',
					'type' => 'text',
					'instructions' => __('Include a description of the taxonomy', 'acfe'),
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
					'key' => 'field_hierarchical',
					'label' => 'Hierarchical',
					'name' => 'hierarchical',
					'type' => 'true_false',
					'instructions' => __('Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. Default: false', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_post_types',
					'label' => 'Post types',
					'name' => 'post_types',
					'type' => 'acfe_post_types',
					'instructions' => __('The name of the object type for the taxonomy object. Object-types can be built-in Post Type or any Custom Post Type that may be registered. Default is None.', 'acfe'),
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
					'key' => 'field_public',
					'label' => 'Public',
					'name' => 'public',
					'type' => 'true_false',
					'instructions' => __('Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users. The default settings of <code>publicly_queryable</code>, <code>show_ui</code>, and <code>show_in_nav_menus</code> are inherited from <code>public</code>. Default: true.', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_publicly_queryable',
					'label' => 'Publicly queryable',
					'name' => 'publicly_queryable',
					'type' => 'true_false',
					'instructions' => __('Whether the taxonomy is publicly queryable. Default: value of <code>public</code>', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_update_count_callback',
					'label' => 'Update count callback',
					'name' => 'update_count_callback',
					'type' => 'text',
					'instructions' => __('A function name that will be called when the count of an associated <code>object_type</code>, such as post, is updated. Works much like a hook. Default: None.', 'acfe'),
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
					'key' => 'field_meta_box_cb',
					'label' => 'Meta box callback',
					'name' => 'meta_box_cb',
					'type' => 'radio',
					'instructions' => __('Provide a callback function for the meta box display. If not set, <a href="https://developer.wordpress.org/reference/functions/post_categories_meta_box/" target="_blank">post_categories_meta_box()</a> is used for hierarchical taxonomies, and <a href="https://developer.wordpress.org/reference/functions/post_tags_meta_box/" target="_blank">post_tags_meta_box()</a> is used for non-hierarchical. If false, no meta box is shown. Default: null.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'choices' => [
						'null' => 'Null (default)',
						'false' => 'False',
						'custom' => 'Custom',
					],
					'default_value' => 'null',
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
					'unparse_type' => true,
				],
				[
					'key' => 'field_meta_box_cb_custom',
					'label' => 'Meta box callback',
					'name' => 'meta_box_cb_custom',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_meta_box_cb',
								'operator' => '==',
								'value' => 'custom',
							]
						]
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
					'key' => 'field_sort',
					'label' => 'Sort',
					'name' => 'sort',
					'type' => 'true_false',
					'instructions' => __('Whether terms in this taxonomy should be sorted in the order they are provided to <code>wp_set_object_terms()</code>. Default null which equates to false.', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_tab_menu',
					'label' => 'Menu',
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
					'key' => 'field_show_ui',
					'label' => 'Show UI',
					'name' => 'show_ui',
					'type' => 'true_false',
					'instructions' => __('Whether to generate and allow a UI for managing terms in this taxonomy in the admin. Default: value of <code>public</code>.', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_show_in_menu',
					'label' => 'Show in menu',
					'name' => 'show_in_menu',
					'type' => 'true_false',
					'instructions' => __('Whether to show the taxonomy in the admin menu. If true, the taxonomy is shown as a submenu of the object type menu. If false, no menu is shown. <code>show_ui</code> must be true. Default: value of <code>show_ui</code>.', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_show_in_nav_menus',
					'label' => 'Show in nav menus',
					'name' => 'show_in_nav_menus',
					'type' => 'true_false',
					'instructions' => __('Makes this taxonomy available for selection in navigation menus. Default: value of <code>public</code>.', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_show_tagcloud',
					'label' => 'Show tagcloud',
					'name' => 'show_tagcloud',
					'type' => 'true_false',
					'instructions' => __('Whether to list the taxonomy in the Tag Cloud Widget controls. Default: value of <code>show_ui</code>.', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_show_in_quick_edit',
					'label' => 'Show in quick edit',
					'name' => 'show_in_quick_edit',
					'type' => 'true_false',
					'instructions' => __('Whether to show the taxonomy in the quick/bulk edit panel. Default: value of <code>show_ui</code>.', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_show_admin_column',
					'label' => 'Show admin column',
					'name' => 'show_admin_column',
					'type' => 'true_false',
					'instructions' => __('Whether to display a column for the taxonomy on its post type listing screens. Default: false.', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_tab_single',
					'label' => 'Single',
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
					'key' => 'field_acfe_single_template',
					'label' => 'Template',
					'name' => 'acfe_single_template',
					'type' => 'text',
					'instructions' => __('Which template file to load for the archive query. Default: <a href="https://developer.wordpress.org/themes/basics/template-hierarchy/">Template hierarchy</a>', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => 'my-template.php',
					'prepend' => trailingslashit(acfe_get_setting('theme_folder')),
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_acfe_single_ppp',
					'label' => 'Posts per page',
					'name' => 'acfe_single_ppp',
					'type' => 'number',
					'instructions' => __('Number of posts to display in the archive page. Default: 10', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => 10,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'min' => -1,
					'max' => '',
					'step' => '',
				],
				[
					'key' => 'field_acfe_single_orderby',
					'label' => 'Order by',
					'name' => 'acfe_single_orderby',
					'type' => 'text',
					'instructions' => __('Sort retrieved posts by parameter in the archive page. Defaults: date (<code>post_date</code>).', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => 'date',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_acfe_single_order',
					'label' => 'Order',
					'name' => 'acfe_single_order',
					'type' => 'select',
					'instructions' => __('Designates the ascending or descending order of the <code>orderby</code> parameter in the archive page. Defaults: DESC.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'choices' => [
						'ASC' => 'ASC',
						'DESC' => 'DESC',
					],
					'default_value' => [
						0 => 'DESC',
					],
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
				],
				[
					'key' => 'field_acfe_single_meta_key',
					'label' => 'Meta key',
					'name' => 'acfe_single_meta_key',
					'type' => 'text',
					'instructions' => __('Custom field used for the <code>orderby</code> parameter in the archive page', 'acfe'),
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
					'key' => 'field_acfe_single_meta_type',
					'label' => 'Meta type',
					'name' => 'acfe_single_meta_type',
					'type' => 'text',
					'instructions' => __('Custom field type (NUMERIC, BINARY, CHAR, DATE, DATETIME, DECIMAL, SIGNED, TIME, UNSIGNED) used for the <code>orderby</code> parameter in the archive page', 'acfe'),
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
					'key' => 'field_rewrite',
					'label' => 'Rewrite',
					'name' => 'rewrite',
					'type' => 'true_false',
					'instructions' => __('Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an argument array to override default URL settings for permalinks', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_rewrite_args_select',
					'label' => 'Rewrite Arguments',
					'name' => 'rewrite_args_select',
					'type' => 'true_false',
					'instructions' => 'Use additional rewrite arguments',
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_rewrite',
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
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'cleanup_key' => true,
				],
				[
					'key' => 'field_rewrite_args',
					'label' => 'Rewrite Arguments',
					'name' => 'rewrite_args',
					'type' => 'group',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => [
						[
							[
								'field' => 'field_rewrite',
								'operator' => '==',
								'value' => '1',
							],
							[
								'field' => 'field_rewrite_args_select',
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
					'layout' => 'row',
					'cleanup_key' => true,
					'sub_fields' => [
						[
							'key' => 'field_slug',
							'label' => 'Slug',
							'name' => 'slug',
							'type' => 'text',
							'instructions' => __('Used as pretty permalink text (i.e. <code>/tag/</code>). Default: value of <code>name</code>', 'acfe'),
							'required' => 0,
							'conditional_logic' => [
								[
									[
										'field' => 'field_rewrite_args_select',
										'operator' => '==',
										'value' => '1',
									],
								],
							],
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
								'data-instruction-placement' => 'field',
							],
							'default_value' => '',
							'placeholder' => 'Default',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						],
						[
							'key' => 'field_with_front',
							'label' => 'With front',
							'name' => 'with_front',
							'type' => 'true_false',
							'instructions' => __('Allowing permalinks to be prepended with front base. Default: true.', 'acfe'),
							'required' => 0,
							'conditional_logic' => [
								[
									[
										'field' => 'field_rewrite_args_select',
										'operator' => '==',
										'value' => '1',
									],
								],
							],
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
								'data-instruction-placement' => 'field',
							],
							'message' => '',
							'default_value' => 1,
							'ui' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
						],
						[
							'key' => 'field_rewrite_hierarchical',
							'label' => 'Hierarchical',
							'name' => 'hierarchical',
							'type' => 'true_false',
							'instructions' => __('Either hierarchical rewrite tag or not. Default: false.', 'acfe'),
							'required' => 0,
							'conditional_logic' => [
								[
									[
										'field' => 'field_rewrite_args_select',
										'operator' => '==',
										'value' => '1',
									],
								],
							],
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
								'data-instruction-placement' => 'field',
							],
							'message' => '',
							'default_value' => 0,
							'ui' => 1,
							'ui_on_text' => '',
							'ui_off_text' => '',
						],
					],
				],
				[
					'key' => 'field_tab_admin',
					'label' => 'Admin',
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
					'key' => 'field_acfe_admin_ppp',
					'label' => 'Terms per page',
					'name' => 'acfe_admin_ppp',
					'type' => 'number',
					'instructions' => __('Number of terms to display on the admin list screen', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => 10,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'min' => -1,
					'max' => '',
					'step' => '',
				],
				[
					'key' => 'field_acfe_admin_orderby',
					'label' => 'Order by',
					'name' => 'acfe_admin_orderby',
					'type' => 'text',
					'instructions' => __('Sort retrieved terms by parameter in the admin list screen. Accepts term fields <code>name</code>, <code>slug</code>, <code>term_group</code>, <code>term_id</code>, <code>id</code>, <code>description</code>, <code>parent</code>, <code>count</code> (for term taxonomy count), or <code>none</code> to omit the ORDER BY clause', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => 'name',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				],
				[
					'key' => 'field_acfe_admin_order',
					'label' => 'Order',
					'name' => 'acfe_admin_order',
					'type' => 'select',
					'instructions' => __('Designates the ascending or descending order of the <code>orderby</code> parameter in the admin list screen. Default: ASC.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'choices' => [
						'ASC' => 'ASC',
						'DESC' => 'DESC',
					],
					'default_value' => [
						0 => 'ASC',
					],
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
				],
				[
					'key' => 'field_acfe_admin_meta_key',
					'label' => 'Meta key',
					'name' => 'acfe_admin_meta_key',
					'type' => 'text',
					'instructions' => __('Custom field used for the <code>orderby</code> parameter in the admin list screen', 'acfe'),
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
					'key' => 'field_acfe_admin_meta_type',
					'label' => 'Meta type',
					'name' => 'acfe_admin_meta_type',
					'type' => 'text',
					'instructions' => __('Custom field type (NUMERIC, BINARY, CHAR, DATE, DATETIME, DECIMAL, SIGNED, TIME, UNSIGNED) used for the <code>orderby</code> parameter in the admin list screen', 'acfe'),
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
					'key' => 'field_tab_labels',
					'label' => 'Labels',
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
					'key' => 'field_labels',
					'label' => 'Labels',
					'name' => 'labels',
					'type' => 'group',
					'instructions' => __('An array of labels for this taxonomy. By default tag labels are used for non-hierarchical types and category labels for hierarchical ones.<br /><br />Default: if empty, <code>name</code> is set to <code>label</code> value, and <code>singular_name</code> is set to <code>name</code> value.', 'acfe'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'layout' => 'row',
					'sub_fields' => [
						[
							'key' => 'field_singular_name',
							'label' => 'Singular name',
							'name' => 'singular_name',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_menu_name',
							'label' => 'Menu name',
							'name' => 'menu_name',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_all_items',
							'label' => 'All items',
							'name' => 'all_items',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_edit_item',
							'label' => 'Edit item',
							'name' => 'edit_item',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_view_item',
							'label' => 'View item',
							'name' => 'view_item',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_update_item',
							'label' => 'Update item',
							'name' => 'update_item',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_add_new_item',
							'label' => 'Add new item',
							'name' => 'add_new_item',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_new_item_name',
							'label' => 'New item name',
							'name' => 'new_item_name',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_parent_item',
							'label' => 'Parent item',
							'name' => 'parent_item',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_parent_item_colon',
							'label' => 'Parent item colon',
							'name' => 'parent_item_colon',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_search_items',
							'label' => 'Search items',
							'name' => 'search_items',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_popular_items',
							'label' => 'Popular items',
							'name' => 'popular_items',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_separate_items_with_commas',
							'label' => 'Separate items with commas',
							'name' => 'separate_items_with_commas',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_add_or_remove_items',
							'label' => 'Add or remove items',
							'name' => 'add_or_remove_items',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_choose_from_most_used',
							'label' => 'Choose from most used',
							'name' => 'choose_from_most_used',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_not_found',
							'label' => 'Not found',
							'name' => 'not_found',
							'type' => 'text',
							'instructions' => '',
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
							'key' => 'field_back_to_items',
							'label' => 'Back to items',
							'name' => 'back_to_items',
							'type' => 'text',
							'instructions' => '',
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
				],
				[
					'key' => 'field_tab_capability',
					'label' => 'Capability',
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
					'key' => 'field_capabilities',
					'label' => 'Capabilities',
					'name' => 'capabilities',
					'type' => 'textarea',
					'instructions' => __('An array of the capabilities for this taxonomy:<br /><br />manage_terms : edit_posts<br />edit_terms : edit_posts<br />delete_terms : edit_posts<br />assign_terms : edit_posts', 'acfe'),
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
					'rows' => '',
					'new_lines' => '',
					'encode_value' => true,
				],
				[
					'key' => 'field_tab_rest',
					'label' => 'REST',
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
					'key' => 'field_show_in_rest',
					'label' => 'Show in rest',
					'name' => 'show_in_rest',
					'type' => 'true_false',
					'instructions' => __('Whether to include the taxonomy in the REST API. You will need to set this to true in order to use the taxonomy in your gutenberg metablock. Default: false.', 'acfe'),
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
					'ui_on_text' => '',
					'ui_off_text' => '',
				],
				[
					'key' => 'field_rest_base',
					'label' => 'Rest base',
					'name' => 'rest_base',
					'type' => 'text',
					'instructions' => __('To change the base url of REST API route. Default: <code>name</code>.', 'acfe'),
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
					'key' => 'field_rest_controller_class',
					'label' => 'Rest controller class',
					'name' => 'rest_controller_class',
					'type' => 'text',
					'instructions' => 'REST API Controller class name. Default: <a href="https://developer.wordpress.org/reference/classes/wp_rest_terms_controller/" target="_blank">WP_REST_Terms_Controller</a>',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => 'WP_REST_Terms_Controller',
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

acf_new_instance('acfe_module_taxonomy_field_groups');