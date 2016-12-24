<?php
/**
 * Ajax handlers
 *
 * @package Tanhit
 * @since 1.0.0
 */

if ( ! class_exists( 'Tanhit_Ajax' ) ) : 

	/**
	 * Class Tanhit_Ajax
	 */
	class Tanhit_Ajax {
	
		public static function controller() {
			
			add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array( __CLASS__, 'process_ajax' ) ); 		
		}
		
		/**
		 * Ajax handler
		 *
		 * @since 1.0.0
		 */		
		public static function process_ajax() {
			
			$response = array();

			$order = $_POST['order'];

			switch ( $order['action'] ) :
				case 'check_dupl':
					global $current_user;
					if ( empty( $current_user ) || $current_user->ID == 0 ) {
						break;	
					}	

					$time = get_user_meta( $current_user->ID, TANHIT_PREVENT_OPEN_KEY, true );
					$response[ 'result' ] = 'ok';

					if ( empty( $time ) || $order['key'] == $time ) {
						$response[ 'isKey' ] = 'granted';
					} else {
						$response[ 'isKey' ] = 'forbidden';
					}	
				break;	
				case 'get_cart':
					global $woocommerce;
					$response[ 'result' ] = 'ok';
					$response[ 'cart_url' ] = $woocommerce->cart->get_cart_url();
					$response[ 'cart_count' ] = $woocommerce->cart->cart_contents_count;
					$response[ 'cart_total' ] = $woocommerce->cart->get_cart_total();
				break;
				case 'reset':			
			
				break;
			endswitch;
			wp_send_json( $response );			
		}	
		
	}	

endif;