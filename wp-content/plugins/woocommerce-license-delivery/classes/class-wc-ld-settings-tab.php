<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


class WC_LD_Settings_Tab {

	const SETTINGS_NAMESPACE = 'wc_ld';

	public static function setup() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_' . self::SETTINGS_NAMESPACE, __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_' . self::SETTINGS_NAMESPACE, __CLASS__ . '::update_settings' );
	}

	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs[ self::SETTINGS_NAMESPACE ] = __( 'License Code Delivery', 'highthemes' );

		return $settings_tabs;
	}

	public static function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}

	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	public static function get_settings() {
		$settings = array(
			array(
				'name' => __( 'Options', 'highthemes' ),
				'type' => 'title',
				'desc' => '',
				'id'   => self::SETTINGS_NAMESPACE . '_section_title'
			),


			array(
				'title'   => __( 'Delivery Order Status', 'highthemes' ),
				'id'      => self::SETTINGS_NAMESPACE . '_delivery_order_status',
				'type'    => 'select',
				'desc'    => __('Select the order status in which the license codes should be delivered.', 'highthemes'),
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'completed'  => __( 'Completed', 'highthemes' ),
					'processing' => __( 'Processing', 'highthemes' ),
				)
			),

			array(
				'title' => __( 'Remove All Data', 'highthemes' ),
				'desc'    => __( 'This tool will remove all WooCommerce License Delivery data (<strong>including all license codes table</strong>) when using the "Delete" link on the plugins screen. It will also remove all setting/option.', 'highthemes' ),
				'id'    => self::SETTINGS_NAMESPACE . '_uninstall_data',
				'default' => 'no',
				'type'    => 'checkbox',

			),

			array(
				'type' => 'sectionend',
				'id'   => self::SETTINGS_NAMESPACE . '_section_end',
			),

		);

		return apply_filters( 'wc_settings_tab_' . self::SETTINGS_NAMESPACE, $settings );
	}
}