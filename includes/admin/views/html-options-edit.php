<div class="wrap acf-settings-wrap">

	<?php
	if (!empty($_REQUEST['action']) and $_REQUEST['action'] === 'add') {
		$title = __('Add Option');
	} else {
		$title = __('Edit Option');
	}
	?>
	<h1 class="wp-heading-inline"><?php echo $title; ?></h1>

	<hr class="wp-header-end" />

	<form id="post" method="post" name="post">

		<?php

		// render post data
		acf_form_data([
			'screen' => 'acfe-options-edit',
			'post_id' => 'acfe_options_edit',
		]);

		wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
		wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);

		?>

		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

				<div id="postbox-container-1" class="postbox-container">

					<?php do_meta_boxes('acf_options_page', 'side', null); ?>

				</div>

				<div id="postbox-container-2" class="postbox-container">

					<?php do_meta_boxes('acf_options_page', 'normal', null); ?>

				</div>

			</div>

			<br class="clear" />

		</div>

	</form>

</div>