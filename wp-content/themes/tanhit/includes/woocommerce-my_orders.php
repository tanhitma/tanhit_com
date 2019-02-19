<?php
/**
 * @package Tanhit
 */
add_filter( 'woocommerce_my_account_my_orders_title', 'tanhit_my_account_my_orders_title' );
function tanhit_my_account_my_orders_title( $title ) {
	return pll__( 'Мои заказы', 'tanhit' );
}	