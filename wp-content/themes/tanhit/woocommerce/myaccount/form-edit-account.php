<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$aAvatar = wp_get_attachment_image_src( get_user_meta($user->ID, 'wp_user_avatar', true) );
?>

<?php wc_print_notices(); ?>
<h2><?php _e( 'Профиль', 'woocommerce' ); ?></h2>
<form class="edit-account" action="" method="post" id="tanhit-edit-account" enctype="multipart/form-data">
	<input type="hidden" name="submit" value="1" />
	
	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<p class="form-row form-row-first">
		<label for="account_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="form-row form-row-last">
		<label for="account_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</p>
	<div class="clear"></div>

	<p class="form-row form-row-wide">
		<label for="account_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="email" class="input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>
	
	<?php do_action( 'tanhit_edit_account_form' ); ?>

	<fieldset style="margin-top: 25px">
		<legend><?php _e( 'Дополнительная информация', 'woocommerce' ); ?></legend>

		<table style='width:100%;'>
			<tr>
				<td style='width:110px;'>
					<p class="form-row form-row-wide">
						<label for="password_1"><?php _e( 'Аватар', 'woocommerce' ); ?></label>
						<div class="clear"></div>
						<?if($aAvatar){?>
							<img style='max-width:100%;max-height:100%;' src='<?=$aAvatar[0]?>' />
							<div class="clear"></div> <br />
						<?}?>
						<input type='file' name='user_avatar' />
					</p>
				</td>
				<td>
					<p class="form-row form-row-wide">
						<label for="password_current"><?php _e( 'Биография', 'woocommerce' ); ?></label>
						<textarea style='min-height:200px;' name='user_description'><?=get_user_meta($user->ID, 'description', true)?></textarea>
					</p>
				</td>
			</tr>
		</table>
	</fieldset>
	<div class="clear"></div>
	
	<fieldset style="margin-top: 25px">
		<legend><?php _e( 'Смена пароля', 'woocommerce' ); ?></legend>

		<p class="form-row form-row-wide">
			<label for="password_current"><?php _e( 'Текущий пароль (не заполняйте, если не хотите менять)', 'woocommerce' ); ?></label>
			<input type="password" class="input-text" name="password_current" id="password_current" />
		</p>
		<p class="form-row form-row-wide">
			<label for="password_1"><?php _e( 'Новый пароль (не заполняйте, если не хотите менять)', 'woocommerce' ); ?></label>
			<input type="password" class="input-text" name="password_1" id="password_1" />
		</p>
		<p class="form-row form-row-wide">
			<label for="password_2"><?php _e( 'Новый пароль повторно (для подтверждения)', 'woocommerce' ); ?></label>
			<input type="password" class="input-text" name="password_2" id="password_2" />
		</p>
	</fieldset>
	<div class="clear"></div>

	<fieldset style="margin-top: 25px">
		<legend><?php _e( 'Новостная рассылка', 'woocommerce' ); ?></legend>

		<p class="form-row form-row-wide">
			<label style='font-weight:normal;' for="subscribe_all"><input type="checkbox" id="subscribe_all" name="subscribe_all" value='1' <?=(mailchimp_exists($user->user_email, 'be2d256a25') ? 'checked="checked"' : '')?> />&nbsp;&nbsp;<?php _e( 'Подключиться на общую рассылку от Танит.', 'woocommerce' ); ?></label>
		</p>
		
		<?if(current_user_can('vip')){?>
		<p class="form-row form-row-wide">
			<label style='font-weight:normal;' for="subscribe_vip"><input type="checkbox" id="subscribe_vip" name="subscribe_vip" value='1' <?=(mailchimp_exists($user->user_email, 'e11dd4d4b6') ? 'checked="checked"' : '')?> />&nbsp;&nbsp;<?php _e( 'Подключиться на рассылку для ближнего круга от Танит.', 'woocommerce' ); ?></label>
		</p>
		<?}?>
	</fieldset>
	<div class="clear"></div>
	
	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details' ); ?>
		<input type="submit" class="button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" />
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

</form>