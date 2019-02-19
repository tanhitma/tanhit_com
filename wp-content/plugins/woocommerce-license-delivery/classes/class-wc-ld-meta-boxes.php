<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WC_LD_Meta_Boxes
 *
 * Metaboxes class
 */
class WC_LD_Meta_Boxes {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

		// Save Product Meta Boxes
		add_action( 'woocommerce_process_product_meta', 'WC_LD_Product_Metabox::save', 10, 1 );

	}

	public function add_meta_boxes() {
		// Products
		add_meta_box( 'product_code_description', __( 'Product License Code Description', 'highthemes' ),
			'WC_LD_Product_Metabox::output', 'product', 'normal' );

	}


}
