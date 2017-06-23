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

//Получаем максимальный статус имеющегося сертификата у пользователя
$iCertStatusMax = getUserStatus();
?>

<?php wc_print_notices(); ?>
<h2><?php _e( 'Профиль', 'woocommerce' ); ?></h2>
<form class="edit-account" action="/my-account/edit-account" method="post" id="tanhit-edit-account" enctype="multipart/form-data">
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
		<legend><?php _e( 'Данные публичной страницы', 'woocommerce' ); ?></legend>

		<?if($iCertStatusMax){?>
			<div>Ваш адрес публичного профиля на сайте Танит (на него идет переход с карты сертифицированных ведущих / мастеров + вы можете предоставлять его любому желающему пользователю): <a href='<?=(site_url('users/'.get_current_user_id()))?>'><?=(site_url('users/'.get_current_user_id()))?></a></div>
		<?}?>
		
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
		
		<?if($iCertStatusMax){
			$aUserExtra = get_user_meta($user->ID, 'user_extra', true);
			$aUserSocial = get_user_meta($user->ID, 'user_social',true);
		?>
		
			<p class="form-row form-row-wide">
				<label for="user_extra_email"><?php _e( 'Контактный e-mail', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_extra[email]" id="user_extra_email" value="<?=$aUserExtra['email']?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_phone"><?php _e( 'Контактный телефон', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_extra[phone]" id="user_extra_phone" value="<?=$aUserExtra['phone']?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_anons"><?php _e( 'Анонс ближайших мероприятий', 'woocommerce' ); ?></label>
				<textarea style='min-height:200px;' name='user_extra[anons]'><?=$aUserExtra['anons']?></textarea>
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_adress1"><?php _e( 'Адрес1', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_extra_adress1" id="user_extra_adress1" value="<?=get_user_meta($user->ID, 'user_extra_adress1', true)?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_adress2"><?php _e( 'Адрес2', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_extra_adress2" id="user_extra_adress2" value="<?=get_user_meta($user->ID, 'user_extra_adress2', true)?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_site"><?php _e( 'Адрес сайта', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_extra[site]" id="user_extra_site" value="<?=$aUserExtra['site']?>" />
			</p>
			
			<legend><?php _e( 'Профили в соц сетях', 'woocommerce' ); ?></legend>
			<p class="form-row form-row-wide">
				<label for="user_extra_social_in"><?php _e( 'Instagram', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_social[in]" id="user_extra_social_in" value="<?=$aUserSocial['in']?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_social_fb"><?php _e( 'Facebook', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_social[fb]" id="user_extra_social_fb" value="<?=$aUserSocial['fb']?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_social_vk"><?php _e( 'VK', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_social[vk]" id="user_extra_social_vk" value="<?=$aUserSocial['vk']?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_social_ok"><?php _e( 'Odnoklassniki', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_social[ok]" id="user_extra_social_ok" value="<?=$aUserSocial['ok']?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_social_youtube"><?php _e( 'Youtube', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_social[youtube]" id="user_extra_social_youtube" value="<?=$aUserSocial['youtube']?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="user_extra_social_google"><?php _e( 'Google+', 'woocommerce' ); ?></label>
				<input type="text" class="input-text" name="user_social[google]" id="user_extra_social_google" value="<?=$aUserSocial['google']?>" />
			</p>
		<?}?>
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