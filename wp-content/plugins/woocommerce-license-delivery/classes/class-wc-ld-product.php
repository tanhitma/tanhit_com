<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class WC_LD_Product
 *
 * All about product relates stuff
 */
class WC_LD_Product {

	static $instance;

	public function __construct() {

		add_action( 'wc_ld_license_code_deleted', 'WC_LD_Model::update_stocks_qty' );
		add_action( 'wc_ld_license_code_inserted', 'WC_LD_Model::update_stocks_qty' );
		add_action( 'wc_ld_license_code_updated', 'WC_LD_Model::update_stocks_qty' );
		add_action( 'wc_ld_license_code_updated_previous', 'WC_LD_Model::update_stocks_qty' );

		add_filter( 'product_type_options', array( $this, 'product_type_options' ), 10, 1 );
		add_action( 'save_post', array( $this, 'product_save_actions' ), 15, 3 );


	}

	/**
	 * @param $post_id
	 * @param $post
	 * @param $update
	 *
	 * custom meta for products that have license codes
	 */
	public function product_save_actions( $post_id, $post, $update ) {
		$post = get_post( $post_id );

		if ( $post->post_type == "product" ) {

			$total_license = WC_LD_Model::get_product_total_license( $post_id );

			if ( isset( $_POST['_inline_edit'] ) && wp_verify_nonce( $_POST['_inline_edit'], 'inlineeditnonce' ) ) {
				if ( get_post_meta( $post_id, '_wc_ld_license_code', true ) == 'yes' ) {
					update_post_meta( $post_id, '_stock', $total_license );
				}

				return;
			}

			$is_license_code = isset( $_POST['_wc_ld_license_code'] ) ? 'yes' : 'no';

			if ( isset( $is_license_code ) && $is_license_code == 'yes' ) {
				update_post_meta( $post_id, '_manage_stock', 'yes' );
				update_post_meta( $post_id, '_virtual', 'yes' );
				update_post_meta( $post_id, '_stock', $total_license );

			}

			update_post_meta( $post_id, '_wc_ld_license_code', $is_license_code );

		}
	}

	/**
	 * @param $options
	 *
	 * add an option for license code products
	 *
	 * @return mixed
	 */
	public function product_type_options( $options ) {


		$options['wc_ld_license_code'] = array(
			'id'            => '_wc_ld_license_code',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'License Code', 'highthemes' ),
			'description'   => __( 'If you product is a deliverable pin code or license, check this box.',
				'highthemes' ),
			'default'       => 'no'
		);


		return $options;
	}

	/** Singleton instance */
	public static function setup() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

