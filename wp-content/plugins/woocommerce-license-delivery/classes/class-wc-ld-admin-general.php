<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_LD_Admin_General
 *
 * handles some general admin actions
 */
class WC_LD_Admin_General {

	/**
	 * Setup hooks
	 */
	public function __construct() {

		// add a new column in woocommerce admin order list for product license codes
		add_action( 'woocommerce_admin_order_item_values', array( $this, 'admin_orders_new_column' ), 10, 3 );
		add_action( 'woocommerce_admin_order_item_headers', array( $this, 'admin_orders_new_column_header' ), 10, 1 );

		// removes the relation between a deleted order and its assigned license codes
		add_action( 'deleted_post', array( $this, 'remove_assigned_licenses' ) );

	}

	public function remove_assigned_licenses( $post_id ) {
		global $wpdb;
		if ( get_post_type( $post_id ) == 'shop_order' ) {
			$wpdb->query("UPDATE {$wpdb->wc_ld_license_codes} SET order_id = 0 WHERE order_id = $post_id");
		}
	}

	public function admin_orders_new_column_header() {
		$column_name = __( 'License Codes', 'highthemes' );
		echo '<th>' . $column_name . '</th>';
	}

	public function admin_orders_new_column( $_product, $item, $item_id = null ) {
		add_thickbox();
		$code_assign_obj = new WC_LD_Code_Assignment();
		$is_assigned = ( isset($item['license_code_ids'] ) && count( $item['license_code_ids'] ) > 0 ? '<a href="#TB_inline?width=600&height=300&inlineId=my-content-id-' . $item['product_id'] . '" class="thickbox">'
		     . __( '[+] View License', 'highthemes' ) . '</a>' : __('Not Assigned','highthemes') );

		echo '<td>' . $is_assigned ;
		echo '<div id="my-content-id-' . $item['product_id'] . '" style="display:none;">';
		$code_assign_obj->display_license_codes( $item );
		echo '</div></td>';

	}

}