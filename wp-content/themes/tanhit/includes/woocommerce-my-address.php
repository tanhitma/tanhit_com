<?php
/**
 * @package Tanhit
 */

add_filter( 'woocommerce_my_account_my_address_title', 'tanhit_my_account_my_address_title' );
function tanhit_my_account_my_address_title( $title ) {
	return '';
}	

add_filter( 'woocommerce_my_account_my_address_description', 'tanhit_my_account_my_address_description' );
function tanhit_my_account_my_address_description( $text ) {
	return '';	
}

add_filter( 'woocommerce_my_account_get_addresses', 'tanhit_my_account_get_addresses' );
function tanhit_my_account_get_addresses( $data ) {
	if ( ! empty( $data[ 'billing' ] ) ) {
		$data[ 'billing' ] = pll__( 'Личные данные', 'tanhit' );	
	}	
	return $data;
}

add_filter( 'woocommerce_my_account_edit_address_title', 'tanhit_my_account_edit_address_title' );
function tanhit_my_account_edit_address_title( $title ) {
	return pll__( 'Личные данные', 'tanhit' );	
}	