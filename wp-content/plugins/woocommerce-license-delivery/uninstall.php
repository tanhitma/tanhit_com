<?php
/**
 * WooCommerce License Delivery Uninstall
 *
 * If you've enabled the option for removing plugins data, it will remove all data including license codes table
 *
 * @author      HighThemes
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// checks whether the remove option is enabled or not
$status_options = get_option( 'wc_ld_uninstall_data');

if ( 'yes' == $status_options  ) {

	// Tables.
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wc_ld_license_codes" );

	// Delete options.
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'wc_ld\_%';");

	// delete meta
	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_wc_ld\_%';");

	// delete woo meta
	$wpdb->query("DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = '_license_code_ids'");


}
