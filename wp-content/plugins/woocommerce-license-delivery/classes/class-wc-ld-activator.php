<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_LD_Activator
 *
 * creates the database tables
 */
class WC_LD_Activator {

	public static function activate( $network_wide = false ) {
		global $wpdb;

		if ( is_multisite() && $network_wide ) {

			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				self::run_install();
				restore_current_blog();
			}
		} else {
			self::run_install();
		}

	}

	public static function run_install() {
		global $wpdb;
		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$sql = "
			CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wc_ld_license_codes (
				id bigint(20) unsigned NOT NULL auto_increment,
				product_id bigint(20) unsigned NOT NULL,
				license_status enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
				creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				sold_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				license_code1 text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
				license_code2 text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
				license_code3 text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
				license_code4 text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
				order_id  bigint(20) unsigned NOT NULL,
				PRIMARY KEY (id)
			) $collate;
		";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=wc_ld' ) . '" title="' . esc_attr( __( 'View WooCommerce License Delivery Settings', 'highthemes' ) ) . '">' . __( 'Settings', 'highthemes' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	public static function new_blog_created( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

		if ( is_plugin_active_for_network( plugin_basename( WC_LD_PLUGIN_FILE ) ) ) {

			switch_to_blog( $blog_id );
			self::activate();
			restore_current_blog();

		}

	}

	public static function wpmu_drop_tables( $tables, $blog_id ) {
		global $wpdb;

		switch_to_blog( $blog_id );

		$tables[] = $wpdb->prefix . 'wc_ld_license_codes';

		restore_current_blog();

		return $tables;

	}


}