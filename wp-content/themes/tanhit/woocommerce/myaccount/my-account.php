<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

wc_print_notices(); 

//Получаем максимальный статус имеющегося сертификата у пользователя
$sQuery = "SELECT MAX(UM_STATUS.meta_value) as `status`
FROM {$wpdb->prefix}posts P 
INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user') 
INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value && U.ID = '".get_current_user_id()."')  
INNER JOIN {$wpdb->prefix}term_relationships TR ON (TR.object_id = P.ID) 
INNER JOIN {$wpdb->prefix}termsmeta UM_STATUS ON (UM_STATUS.terms_id = TR.term_taxonomy_id && UM_STATUS.meta_key = 'cert_status') 
WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' && (UM_STATUS.meta_value IN (220,221,222,223))";

$iCertStatusMax = $wpdb->get_var( $sQuery );
?>

<div class="myaccount_user" style="margin-bottom: 20px;">
	<?php
	printf('Здравствуйте, <strong>%s</strong>! Выберите, пожалуйста, раздел.',
		$current_user->display_name);
	
	/**
	 * check for MailPoet Newsletters plugin
	 */	
	/*if ( defined( 'WYSIJA_SIDE' ) && 'front' == WYSIJA_SIDE ) {
		$mail_poet_link = '';
		if ( class_exists( 'Tanhit_Site_Manager' ) ) {
			$mail_poet_link = Tanhit_Site_Manager::get_options( 'mail_poet_link' );	
		}
		if ( empty( $mail_poet_link ) ) {
			// home_url() . '?wysija-page=1&controller=confirm&wysija-key=266f9d3c9bd9ac9259ceeb611bbb4e56&action=subscriptions&demo=1&wysijap=subscriptions#wysija-subscriptions' 
			echo '&nbsp;';
			printf( pll__( 'А так же <a href="%s">управлять подписками</a>.', 'tanhit' ),
				home_url() . '#' 
			);	
		} else {
			echo '&nbsp;';
			printf( pll__( 'А так же <a href="%s">управлять подписками</a>.', 'tanhit' ),
				$mail_poet_link 
			);			
		}	
	}	*/
	?>
</div>

<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Главная</a></li>
  <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Профиль</a></li>
  <li role="presentation"><a href="#certificates" aria-controls="certificates" role="tab" data-toggle="tab">Сертификаты</a></li>
  <li role="presentation"><a href="#webinars" aria-controls="webinars" role="tab" data-toggle="tab">Вебинары и практики</a></li>
  <li role="presentation"><a href="#orders" aria-controls="orders" role="tab" data-toggle="tab">Заказы</a></li>
  <li role="presentation"><a href="#pins" aria-controls="pins" role="tab" data-toggle="tab">Пин-коды</a></li>
  <?if($iCertStatusMax){?>
	<li role="presentation"><a href="#manager" aria-controls="manager" role="tab" data-toggle="tab"><?=(220==$iCertStatusMax ? 'ВЕДУЩИЙ' : 'МАСТЕР')?></a></li>
  <?}?>
</ul>

<div class="tab-content">
	<div role="tabpanel" class="tab-pane active" id="home">
		<?php do_action( 'woocommerce_before_my_account' ); ?>
		<?php do_action( 'tanhit_my_account' ); ?>
		<?php do_action( 'woocommerce_after_my_account' ); ?>
	</div>
	<div role="tabpanel" class="tab-pane fade in" id="profile">
		<?php wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) );?>
	</div>
	<div role="tabpanel" class="tab-pane fade" id="certificates">
		<div class='content'>
			<h2>Выданные сертификаты</h2>
			<?php echo do_shortcode('[cert_list my=1 id_list=list]');?>
		</div>
	</div>
	<div role="tabpanel" class="tab-pane fade" id="webinars">
		<?php wc_get_template( 'myaccount/my-downloads.php' ); ?>
	</div>
	<div role="tabpanel" class="tab-pane fade" id="orders">
		<?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
	</div>
	<div role="tabpanel" class="tab-pane fade" id="pins">
		<?php do_action('display_pincodes'); ?>
	</div>
	<?if($iCertStatusMax){?>
	<div role="tabpanel" class="tab-pane fade" id="manager">
		<div class='content'>
			<div style='clear:both;'></div>
			<h2 style='float:left;'>Сертификаты учеников</h2>
			<button style='float:right;margin:20px 0px 0 0;' type='button' onClick='get_cert_archive()'>Сформировать и выслать архив на почту</button>
			<div style='clear:both;'></div>
			
			<?php echo do_shortcode('[cert_list manager=1'.(220 == $iCertStatusMax ? '' : ' full=1').' filter=1 sort=1 id_list=manager]');?>
		</div>
	</div>
	<script>
		function get_cert_archive(){
			jQuery.ajax({
			  method: "POST",
			  url: "<?=admin_url('admin-ajax.php')?>",
			  data: { action: "get_cert_archive"}
			}).done(function( msg ) {
				//console.log(msg);
			});
			  
			alert('В ближайшее время ссылка на архив будет отправлена на вашу почту');
			  
			return false;
		}
	</script>
	<?}?>
</div>

<?php /**
 * don't load my-address at my-account page
 */
// wc_get_template( 'myaccount/my-address.php' ); ?>
