<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.multidots.com/
 * @since             1.0.0
 * @package           Woo_Ecommerce_Tracking_For_Google_And_Facebook
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Ecommerce tracking for Google and Facebook
 * Plugin URI:        http://www.multidots.com/
 * Description:       WooCommerce Ecommerce Tracking for Google and Facebook plugin is tracking order using Ecommerce tracking and boost your Marketing.
 * Version:           1.0.2
 * Author:            Multidots
 * Author URI:        http://www.multidots.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-ecommerce-tracking-for-google-and-facebook
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-ecommerce-tracking-for-google-and-facebook-activator.php
 */
function activate_woo_ecommerce_tracking_for_google_and_facebook() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-ecommerce-tracking-for-google-and-facebook-activator.php';
	Woo_Ecommerce_Tracking_For_Google_And_Facebook_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-ecommerce-tracking-for-google-and-facebook-deactivator.php
 */
function deactivate_woo_ecommerce_tracking_for_google_and_facebook() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-ecommerce-tracking-for-google-and-facebook-deactivator.php';
	Woo_Ecommerce_Tracking_For_Google_And_Facebook_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_ecommerce_tracking_for_google_and_facebook' );
register_deactivation_hook( __FILE__, 'deactivate_woo_ecommerce_tracking_for_google_and_facebook' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-ecommerce-tracking-for-google-and-facebook.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_ecommerce_tracking_for_google_and_facebook() {

	$plugin = new Woo_Ecommerce_Tracking_For_Google_And_Facebook();
	$plugin->run();

}
run_woo_ecommerce_tracking_for_google_and_facebook();
