<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class WooCommerce_License_Delivery
 *
 * Starts the plugin
 */
class WooCommerce_License_Delivery {

	/**
	 * __construct function.
	 *
	 * @access public
	 */
	public function __construct() {
		global $wpdb;

		// add plugin action links
		add_filter( 'plugin_action_links_' . plugin_basename( WC_LD_PLUGIN_FILE ), array( 'WC_LD_Activator', 'plugin_action_links' ) );

		// wpmu support
		add_action( 'wpmu_new_blog', 'WC_LD_Activator::new_blog_created', 10, 6 );
		add_filter( 'wpmu_drop_tables', 'WC_LD_Activator::wpmu_drop_tables', 10, 2 );


		// define the table name
		if ( ! isset( $wpdb->wc_ld_license_codes ) ) {
			$wpdb->wc_ld_license_codes = $wpdb->prefix . 'wc_ld_license_codes';
		}


		// Setup autoloader
		self::setup_autoloader();

		// check for woo activation
		self::check_wc_activation();


		// Load plugin textdomain
		load_plugin_textdomain( 'highthemes', false,
			plugin_basename( dirname( self::get_plugin_file() ) ) . '/languages/' );

		// Setup admin classes
		if ( is_admin() ) {

			// Setup admin scripts
			$admin_scripts = new WC_LD_Admin_Scripts();
			$admin_scripts->setup();

			// setup plugins pages
			WC_LD_License_Codes_Pages::setup();

			// Setup settings page
			WC_LD_Settings_Tab::setup();

			// Setup Metaboxes
			new WC_LD_Meta_Boxes();

			// Setup admin
			new WC_LD_Admin_General();


		}

		// Setup Product
		WC_LD_Product::setup();

		// setup code assignment
		$code_assignment = new WC_LD_Code_Assignment();
		$code_assignment->setup();


		// Setup actions
		$this->setup_actions();
	}

	/**
	 * A static method that will setup the autoloader
	 */
	private static function setup_autoloader() {
		require_once( plugin_dir_path( self::get_plugin_file() ) . 'classes/class-wc-ld-autoloader.php' );
		$autoloader = new WC_LD_Autoloader( plugin_dir_path( self::get_plugin_file() ) . 'classes/' );
		spl_autoload_register( array( $autoloader, 'load' ) );
	}

	public function check_wc_activation() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}
	}

	/**
	 * Setup actions
	 */
	private function setup_actions() {
		/**
		 * load front-end script
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

	}

	public static function get_plugin_version() {
		return WC_LD_PLUGIN_VER;
	}

	public static function get_plugin_path() {
		return plugin_dir_path( self::get_plugin_file() );
	}

	public static function get_plugin_file() {
		return WC_LD_PLUGIN_FILE;
	}

	public static function get_plugin_url() {
		return plugins_url( basename( plugin_dir_path( self::get_plugin_file() ) ),
			basename( self::get_plugin_file() ) );
	}

	public function notice_activate_wc() {
		?>
		<div class="error">
			<p><?php printf( __( 'Please install and activate %sWooCommerce%s in order for the WooCommerce License Delivery extension to work!',
					'highthemes' ),
					'<a href="' . admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) . '">',
					'</a>' ); ?></p>
		</div>
		<?php
	}

	/**
	 *
	 */
	public function frontend_scripts() {
		// nothing for now
	}
}
