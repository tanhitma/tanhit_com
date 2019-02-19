<?php
/**
 * @link              http://highthemes.com
 * @package           WooCommerce_License_Delivery
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce License Delivery
 * Plugin URI:        http://highthemes.com
 * Description:       A WooCommerce Addon for selling license codes, gift cards, digital pins, etc
 * Version:           1.0.4
 * Author:            HighThemes
 * Author URI:        http://highthemes.com/
 * Text Domain:       highthemes
 * Domain Path:       /languages
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


define( 'WC_LD_PLUGIN_FILE', __FILE__ );
define( 'WC_LD_PLUGIN_VER', '1.0.4' );

/**
 * the core class
 */
require plugin_dir_path( __FILE__ ) . 'classes/class-woocommerce-license-delivery.php';

/**
 * activate the plugin
 */
function activate_wc_license_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'classes/class-wc-ld-activator.php';
	WC_LD_Activator::activate();
}

/**
 * deactivate the plugin
 */
function deactivate_wc_license_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'classes/class-wc-ld-deactivator.php';
	WC_LD_Deactivator::deactivate();
}

/**
 * register the activation/deactivation hooks
 */
register_activation_hook( __FILE__, 'activate_wc_license_delivery' );
register_deactivation_hook( __FILE__, 'deactivate_wc_license_delivery' );

/**
 * run the plugin
 */
function run_woocommerce_license_delivery() {
	new WooCommerce_License_Delivery();
}

// start plugin
add_action( 'plugins_loaded', 'run_woocommerce_license_delivery', 10 );