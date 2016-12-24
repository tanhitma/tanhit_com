<?php
/**
 * Template Name: Unstyled Form
 * Description: A completely unstyled form template.
 */
?>
<!-- Strong Testimonials: Unstyled Form Template -->
<div class="strong-view strong-form <?php wpmtst_container_class(); ?>">

	<div id="wpmtst-form">

		<p class="required-notice">
			<span class="required symbol"></span><?php wpmtst_form_message( 'required-field' ); ?>
		</p>

		<form <?php wpmtst_form_info(); ?>>

			<?php wpmtst_form_setup(); ?>

			<?php do_action( 'wpmtst_form_before_fields' ); ?>

			<?php wpmtst_all_form_fields(); ?>

			<?php do_action( 'wpmtst_form_after_fields' ); ?>

			<?php wpmtst_form_submit_button(); ?>

		</form>

	</div>

</div>
