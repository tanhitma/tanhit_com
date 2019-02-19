<?php
/**
 * @package tanhit
 */
/**
 * Remove unneeded fields
 */
add_filter( 'woocommerce_billing_fields', 'tanhit_woocommerce_billing_fields' );
function tanhit_woocommerce_billing_fields( $fields ) {
	
	unset( $fields[ 'billing_company' ] );
	
	unset( $fields[ 'billing_country' ] );
	
	unset( $fields[ 'billing_address_1' ] );
	unset( $fields[ 'billing_address_2' ] );
	
	unset( $fields[ 'billing_state' ] );
	
	unset( $fields[ 'billing_postcode' ] );
	
	return $fields;	

}	