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

wc_print_notices(); ?>

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
      <?php wc_get_template( 'myaccount/my-certificates.php' ); ?>
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
</div>

<?php /**
 * don't load my-address at my-account page
 */
// wc_get_template( 'myaccount/my-address.php' ); ?>
