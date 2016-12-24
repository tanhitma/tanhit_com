<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Woo_Ecommerce_Tracking_For_Google_And_Facebook
 * @subpackage Woo_Ecommerce_Tracking_For_Google_And_Facebook/admin/partials
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( !class_exists( 'WC_Settings_Advance_Ecommerce_Tracking' ) ) {
	class WC_Settings_Advance_Ecommerce_Tracking {
		const SETTINGS_NAMESPACE = 'ecommerce_tracking_settings';
		/**
		 * Get the setting fields
		 *
		 * @since  1.0.0
		 * @access private
		 *
		 * @return array $setting_fields
		 */
		private function get_fields() {
			$setting_fields = array(
				'section_title' => array(
					'name' => __( 'Ecommerce Tracking & Conversion code Settings', 'woocommerce-ecommerce-tracking' ),
					'type' => 'title',
					'desc' => '',
					'id'   => self::SETTINGS_NAMESPACE . '_title'
				),
				
				'check_load_ecommerce_tracking_code' =>	array(
					'title'   => __( 'Enable Ecommerce Tracking', 'woocommerce' ),
					'desc'    => __( 'Enable Ecommerce Tracking on Thank you Page', 'woocommerce' ),
					'id'      => self::SETTINGS_NAMESPACE . '_load_ecommerce_tracking_code',
					'default' => 'no',
					'type'    => 'checkbox'
				),
				
				'check_facebook_conversion_code' =>	array(
					'title'   => __( 'Enable Facebook Conversion', 'woocommerce' ),
					'desc'    => __( 'Enable Facebook Conversion', 'woocommerce' ),
					'id'      => self::SETTINGS_NAMESPACE . '_facebook_conversion_code',
					'default' => 'no',
					'type'    => 'checkbox'
				),
				
				'check_google_conversion_code' =>	array(
					'title'   => __( 'Enable Google Conversion', 'woocommerce' ),
					'desc'    => __( 'Enable Google Conversion', 'woocommerce' ),
					'id'      => self::SETTINGS_NAMESPACE . '_google_conversion_code',
					'default' => 'no',
					'type'    => 'checkbox'
				),
				
				'facebook_track_id' => array(
					'name'    => __( 'Facebook Track ID', 'woocommerce-ecommerce-tracking' ),
					'type'    => 'text',
					'desc'    => __( 'Enter Facebook Track ID', 'woocommerce-ecommerce-tracking' ),
					'id'      => self::SETTINGS_NAMESPACE . '_facebook_track_id',
					'default' => '',
				),
				
				'google_conversion_id' => array(
					'name'    => __( 'Google Conversion ID', 'woocommerce-ecommerce-tracking' ),
					'type'    => 'text',
					'desc'    => __( 'Google Conversion ID', 'woocommerce-ecommerce-tracking' ),
					'id'      => self::SETTINGS_NAMESPACE . '_google_conversion_id',
					'default' => '',
				),
				
				'google_conversion_label' => array(
					'name'    => __( 'Google Conversion Label', 'woocommerce-ecommerce-tracking' ),
					'type'    => 'text',
					'desc'    => __( 'Google Conversion Label', 'woocommerce-ecommerce-tracking' ),
					'id'      => self::SETTINGS_NAMESPACE . '_google_conversion_label',
					'default' => '',
				),

				'section_end'   => array(
					'type' => 'sectionend',
					'id'   => self::SETTINGS_NAMESPACE . '_section_end'
				)
			);
			return apply_filters( 'wc_settings_tab_' . self::SETTINGS_NAMESPACE, $setting_fields );
		}
		/**
		 * Get an option set in our settings tab
		 *
		 * @param $key
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return String
		 */
		public function get_option( $key ) {
			$fields = $this->get_fields();
			return apply_filters( 'wc_option_' . $key, get_option( 'wc_settings_' . self::SETTINGS_NAMESPACE . '_' . $key, ( ( isset( $fields[$key] ) && isset( $fields[$key]['default'] ) ) ? $fields[$key]['default'] : '' ) ) );
		}
		/**
		 * Setup the WooCommerce settings
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function setup() {
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 70 );
			add_action( 'woocommerce_settings_tabs_' . self::SETTINGS_NAMESPACE, array( $this, 'tab_content' ) );
			add_action( 'woocommerce_update_options_' . self::SETTINGS_NAMESPACE, array( $this, 'update_settings' ) );
		}
		/**
		 * Add a settings tab to the settings page
		 *
		 * @param array $settings_tabs
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return array
		 */
		public function add_settings_tab( $settings_tabs ) {
			$settings_tabs[self::SETTINGS_NAMESPACE] = __( 'Ecommerce Tracking Settings', 'woocommerce-ecommerce-tracking' );
			return $settings_tabs;
		}
		/**
		 * Output the tab content
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function tab_content() {
			//global $current_user;
			$current_user = wp_get_current_user();
			
			woocommerce_admin_fields( $this->get_fields() );
			
			if (!get_option('wetfgf_plugin_notice_shown')) {
				echo '<div id="wetfgf_dialog" title="Basic dialog"> <p> Subscribe for latest plugin update and get notified when we update our plugin and launch new products for free! </p> <p><input type="text" id="txt_user_sub_wetfgf" class="regular-text" name="txt_user_sub_wetfgf" value="'.$current_user->user_email.'"></p></div>'; ?>
			
			<script type="text/javascript">
			
				jQuery( document ).ready(function() {
					jQuery( "#wetfgf_dialog" ).dialog({
						modal: true, title: 'Subscribe Now', zIndex: 10000, autoOpen: true,
						width: '500', resizable: false,
						position: {my: "center", at:"center", of: window },
						dialogClass: 'dialogButtons',
						buttons: {
							Yes: function () {
								// $(obj).removeAttr('onclick');
								// $(obj).parents('.Parent').remove();
								var email_id = jQuery('#txt_user_sub_wetfgf').val();
								
								var data = {
								'action': 'add_plugin_user_wetfgf',
								'email_id': email_id
								};
								
								// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
								jQuery.post(ajaxurl, data, function(response) {
								jQuery('#wetfgf_dialog').html('<h2>You have been successfully subscribed');
								jQuery(".ui-dialog-buttonpane").remove();
								});
							},
							No: function () {
								var email_id = jQuery('#txt_user_sub_wetfgf').val();
								
								var data = {
									'action': 'hide_subscribe_wetfgf',
									'email_id': email_id
								};
								
								// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
								jQuery.post(ajaxurl, data, function(response) {
								
								});
								
								jQuery(this).dialog("close");
								
							}
						},
						close: function (event, ui) {
							jQuery(this).remove();
						}
					});
					
					jQuery("div.dialogButtons .ui-dialog-buttonset button").removeClass('ui-state-default');
					jQuery("div.dialogButtons .ui-dialog-buttonset button").addClass("button-primary woocommerce-save-button");
					jQuery("div.dialogButtons .ui-dialog-buttonpane .ui-button").css("width","80px");
				});
			</script>
			
		<?php }
	
		}
		
		/**
		 * Update the settings
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function update_settings() {
			woocommerce_update_options( $this->get_fields() );
		}
	}
}

// Only load in admin
if ( is_admin() ) {
    // Initiate the settings class
    $settings = new WC_Settings_Advance_Ecommerce_Tracking();
     
    // Setup the hooks and filters
    $settings->setup();
}