<?php
/**
 * @package Tanhit
 * @subpackage My Account
 */

add_action( 'woocommerce_my_account', 'tanhit_my_account_init', 0 );
function tanhit_my_account_init() {	
	
	/**
	 * we need get meta fields for products
	 *  - '_downloadable_files'
	 *  - 'product_date_start'
	 *  - 'product_time_start'
	 *  - 'product_date_end'
	 */
	global $tanhit_customer_orders;
	global $tanhit_customer_products;
	
	$tanhit_customer_orders = array();
	$tanhit_customer_products = array();
	
	/**
	 * Get all customer orders
	 * @see https://www.skyverge.com/blog/get-all-woocommerce-orders-for-a-customer/
	 *
	 * to get all order types ('post_type') @see wc_get_order_types() ([ shop_order, shop_order_refund ])
	 * to get all order statuses ('post_status') @see wc_get_order_statuses()
	 */	
    $orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys( wc_get_order_statuses() ),
    ) );
	
	/**
	 * @see http://docs.woothemes.com/wc-apidocs/class-WC_Order.html
	 *
	 * array of Product
	 * [qty] => 2
	 * [tax_class] => 
	 * [product_id] => 42
	 * [variation_id] => 0
	 * [line_subtotal] => 0
	 * [line_total] => 0
	 * [line_subtotal_tax] => 0
	 * [line_tax] => 0
	 * [line_tax_data] => a:2:{s:5:"total";a:0:{}s:8:"subtotal";a:0:{}}			
	 */
	foreach( $orders as $order ) {
		$tanhit_customer_orders[ $order->ID ] = new WC_Order( $order->ID );
	}
	
	$i = 0;
	foreach ( $tanhit_customer_orders as $order_ID=>$order ) :
		
		$products = $order->get_items();

		foreach ( $products as $product ) {
			
			$terms = get_the_terms( $product[ 'product_id' ], 'product_cat');
			
			$tanhit_customer_products[$i][ 'order_id' ]   	 = $order_ID;
			$tanhit_customer_products[$i][ 'product_id' ]    = $product[ 'product_id' ];
			$tanhit_customer_products[$i][ 'product_name' ]  = $product[ 'name' ];
			$tanhit_customer_products[$i][ 'type' ] 		 = $product[ 'type' ];
			$tanhit_customer_products[$i][ 'product_cat' ]	 = $terms[0];
			$tanhit_customer_products[$i][ 'qty' ] 		 	 = $product[ 'qty' ];
			$tanhit_customer_products[$i][ 'permalink' ] 	 = get_the_permalink( $product[ 'product_id' ] );
			$tanhit_customer_products[$i][ '_downloadable_files' ] = get_post_meta( $product[ 'product_id' ], '_downloadable_files' , true );
			$tanhit_customer_products[$i][ 'product_date_start' ]  = get_post_meta( $product[ 'product_id' ], 'product_date_start' , true );
			$tanhit_customer_products[$i][ 'product_time_start' ]  = get_post_meta( $product[ 'product_id' ], 'product_time_start' , true );
			$tanhit_customer_products[$i][ 'product_date_end' ]    = get_post_meta( $product[ 'product_id' ], 'product_date_end' , true );
			$tanhit_customer_products[$i][ 'item' ]    			   = $product;
			$tanhit_customer_products[$i][ 'order' ]    		   = $order;
		
			$i++;
			
		}

	endforeach;

}

/**
 * Enqueue scripts for account page
 */
add_action( 'wp_print_scripts', 'tanhit_my_account_enqueue_scripts' );
function tanhit_my_account_enqueue_scripts() {
	
	if ( ! is_account_page() ) {
		return;	
	}	
	
	wp_register_script(
		'video-player',
		TM_URL . '/js/video_player/video-player.js',
		array( 'jquery' ),
		TANHIT_VERSION,
		true
	);
	wp_enqueue_script( 'video-player' );
	
}
