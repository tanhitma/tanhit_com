<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Class WC_LD_Model
 */
class WC_LD_Model {

	/**
	 * returns total number of license codes in the database
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->wc_ld_license_codes}";

		if ( isset( $_GET['product-filter'] ) && $_GET['product-filter'] > 0 ) {
			$sql = $sql . ' WHERE product_id=' . $_GET['product-filter'];
		}

		return $wpdb->get_var( $sql );
	}

	/**
	 * Retrieve codes from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_codes( $per_page = 25, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->wc_ld_license_codes}";

		if ( isset( $_GET['product-filter'] ) && $_GET['product-filter'] > 0 ) {
			$sql = $sql . ' WHERE product_id=' . $_GET['product-filter'];
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Delete a code record.
	 *
	 * @param int $id code id
	 */
	public static function delete_code( $id ) {
		global $wpdb;

		// select the code from database in order to get product id
		$license        = $wpdb->get_row( "SELECT * FROM {$wpdb->wc_ld_license_codes} WHERE id=$id", ARRAY_A );
		$product_id = $license['product_id'];

		if ( $license['order_id'] < 1 ) {
			$res = $wpdb->delete(
				"{$wpdb->wc_ld_license_codes}",
				array('id' => $id ),
				array( '%d' )
			);
			if ( $res > 0 ) {
				do_action( 'wc_ld_license_code_deleted', array( 'id' => $product_id ) );
				return true;
			}
		}

	}

	/**
	 * @param $ids
	 *
	 * returns a license codes details by its ID
	 *
	 * @return array|null|object
	 */
	public static function get_codes_by_id( $ids ) {

		global $wpdb;
		$sql    = "SELECT * FROM {$wpdb->wc_ld_license_codes} WHERE id IN ($ids)";
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * @param $col_number
	 * @param $product_id
	 *
	 * returns the customized license code column title
	 *
	 * @return mixed|string
	 */
	public static function get_code_title( $col_number, $product_id ) {
		$column_title = get_post_meta( $product_id, '_wc_ld_code' . $col_number . '_title', true );

		return empty( $column_title ) ? '#' . $col_number : $column_title;
	}

	/**
	 * @param $arr
	 *
	 * updates the stock quantity for the specified product
	 *
	 * @return bool
	 */
	public static function update_stocks_qty( $arr ) {
		global $wpdb;
		$result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = %d WHERE post_id = %d AND meta_key='_stock'",
			self::get_product_total_license( $arr['id'] ), $arr['id'] ) );

		if ( $result > 0 ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * @param      $product_id
	 * @param bool $used
	 *
	 * returns the number of license codes for a particular product
	 *
	 * @return null|string
	 */
	public static function get_product_total_license( $product_id, $used = false ) {
		global $wpdb;
		$license_status = ( $used ) ? '1' : '0';
		$rowcount   = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->wc_ld_license_codes} WHERE product_id = $product_id AND license_status = '$license_status'" );

		return $rowcount;

	}

	/**
	 * @param $license_code_id
	 * @param $status
	 *
	 * updates the license status : 0, 1 (sold)
	 */
	public static function change_license_codes_status( $license_code_id, $status ) {
		global $wpdb;
		$current_time = current_time( mysql );
		$wpdb->query( "UPDATE {$wpdb->wc_ld_license_codes} SET license_status = '$status', sold_date = '$current_time' WHERE id in ($license_code_id)" );
	}


}