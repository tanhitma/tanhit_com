<?php
/**
 * @package tanhit
 */
/**
 * Remove unneeded fields
 */
add_filter( 'woocommerce_checkout_fields' , 'tanhit_woocommerce_checkout_fields' );
function tanhit_woocommerce_checkout_fields( $fields ) {
	
	unset( $fields[ 'billing' ][ 'billing_company' ] );
	
	unset( $fields[ 'billing' ][ 'billing_country' ] );
	
	unset( $fields[ 'billing' ][ 'billing_address_1' ] );
	unset( $fields[ 'billing' ][ 'billing_address_2' ] );
	
	unset( $fields[ 'billing' ][ 'billing_state' ] );
	
	unset( $fields[ 'billing' ][ 'billing_postcode' ] );

	return $fields;	
	
}	
