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
$iCertStatusMax = getUserStatus();
?>

<style>
#ajax-tabs.nav>li>a{padding: 15px 10px;}
</style>

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
	
	<br /><br />
	<p style='color:red;'>Все приобретенные Вами вебинары, видео и аудио практики будут находится в разделе: &laquo;ВЕБИНАРЫ и ПРАКТИКИ&raquo;</p>
</div>

<ul id='ajax-tabs' class="nav nav-tabs" role="tablist">
	<li role="presentation"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Главная</a></li>
	<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Профиль</a></li>
	<li role="presentation"><a href="#certificates" aria-controls="certificates" role="tab" data-toggle="tab">Сертификаты</a></li>
	<li role="presentation"><a href="#webinars" aria-controls="webinars" role="tab" data-toggle="tab">Вебинары и практики</a></li>
	<li role="presentation"><a href="#orders" aria-controls="orders" role="tab" data-toggle="tab">Заказы</a></li>
	<li role="presentation"><a href="#pins" aria-controls="pins" role="tab" data-toggle="tab">Пин-коды</a></li>
	<li role="presentation"><a href="#docs" aria-controls="docs" role="tab" data-toggle="tab">Методички</a></li>
	<?if($iCertStatusMax){?>
		<li role="presentation"><a href="#manager" aria-controls="manager" role="tab" data-toggle="tab" data-load="1"><?=(220==$iCertStatusMax ? 'ВЕДУЩИЙ' : 'МАСТЕР')?></a></li>
	<?}?>
</ul>

<div id='ajax-tabs-panels' class="tab-content">
	<div role="tabpanel" class="tab-pane" id="home"></div>
	<div role="tabpanel" class="tab-pane fade" id="profile"></div>
	<div role="tabpanel" class="tab-pane fade" id="certificates"></div>
	<div role="tabpanel" class="tab-pane fade" id="webinars"></div>
	<div role="tabpanel" class="tab-pane fade" id="orders"></div>
	<div role="tabpanel" class="tab-pane fade" id="pins"></div>
	<div role="tabpanel" class="tab-pane fade" id="docs"></div>
	<?if($iCertStatusMax){?>
	<div role="tabpanel" class="tab-pane fade" id="manager"><?php wc_get_template( 'myaccount/my-manager-certificates.php' );?></div>
	<?}?>
</div>

<style>
#ajax-tabs-panels .tab-preloader{
	background: url('/wp-content/themes/tanhit/images/preloader/2.gif') no-repeat center center;
	width: 100%;
    height: 200px;
}
</style>
<script>
	//Текущая вкладка
	var start_tab = (window.location.hash ? window.location.hash.substring(1) : '');
	
	var set_default = true;
	jQuery('#ajax-tabs li a').each(function(i,v){
		if ( set_default && jQuery(v).attr('aria-controls') == start_tab){
			set_default = false;
		}
	});
	
	//Вкладка по умолчнаию
	if (set_default){
		start_tab = 'home';
	}
	
	jQuery('#ajax-tabs li a').click(function(){
		var el = this;
		
		var tab_name = jQuery(el).attr('aria-controls');
		
		window.location.hash = tab_name;
		
		//Если вкладка не подгружена
		if ( ! jQuery(el).attr('data-load')){
			jQuery('.tab-content #'+tab_name).html('<div class="tab-preloader"></div>');
			
			jQuery.ajax({
				method: "POST",
				url: "<?=admin_url('admin-ajax.php')?>",
				data: { action: 'load_user_tab', session_id: '<?=session_id()?>', tab_name : tab_name },
				success: function(html){
					jQuery('.tab-content #'+tab_name).html(html);
					
					jQuery(el).attr('data-load', 1);
				}
			})
		}
	});
	
	jQuery(document).ready(function($) {
		"use strict";
		
		jQuery('#ajax-tabs li a[aria-controls="'+start_tab+'"]').click();
	});
</script>
