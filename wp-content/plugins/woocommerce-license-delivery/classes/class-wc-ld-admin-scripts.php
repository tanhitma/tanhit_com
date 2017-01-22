<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_LD_Admin_Scripts
 *
 * enqueue admin scripts and styles
 */
class WC_LD_Admin_Scripts {
	/**
	 * Setup hooks
	 */
	public function setup() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue admin styles
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'select2',
			plugins_url( '/assets/css/select2.min.css', WooCommerce_License_Delivery::get_plugin_file() ), array(), false,
			'all' );
		wp_enqueue_style( 'license-delivery-admin-css',
			plugins_url( '/assets/css/wc-ld-admin.css', WooCommerce_License_Delivery::get_plugin_file() ), array(), false,
			'all' );
	}

	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_scripts() {
		global $pagenow;

		// list of pages that use the scripts
		$pages = array( 'license_code_edit', 'license_codes', 'license_code_csv_upload' );
		wp_register_script('wc-ld-select2',plugins_url( '/assets/js/select2.min.js', WooCommerce_License_Delivery::get_plugin_file() ),
				array( 'jquery' ), false, false );

		if ( $pagenow == 'admin.php' && in_array( $_GET['page'], $pages ) ) {
			wp_dequeue_script( 'select2' );
			wp_deregister_script( 'select2' );

			wp_enqueue_script( 'wc-ld-select2');
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );

		}

		// general admin script
		wp_enqueue_script( 'license-delivery-admin-js', plugins_url( '/assets/js/wc-ld-admin.js', WooCommerce_License_Delivery::get_plugin_file() ), array( 'jquery','wc-ld-select2' ), false, false );

		// ajax object
		wp_localize_script( 'license-delivery-admin-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

}