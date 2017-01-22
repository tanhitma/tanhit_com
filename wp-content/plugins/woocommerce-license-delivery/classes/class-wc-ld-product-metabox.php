<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WC_LD_Product_Metabox
 *
 * custom metabox for products
 */
class WC_LD_Product_Metabox {

	public static function output( $post ) {

		$settings = array(
			'textarea_name' => '_wc_ld_product_code_description',
			'quicktags'     => array( 'buttons' => 'em,strong,link' ),
			'tinymce'       => array(
				'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
				'theme_advanced_buttons2' => '',
			),
			'editor_css'    => '<style>#wp-_wc_ld_product_code_description-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		);

		$content = get_post_meta( $post->ID, '_wc_ld_product_code_description', true );
		echo '<p>';
		_e("This short description is sent to the user's email after his/her order completed. Usually used for special usage instruction for using the license codes.", "highthemes");
		echo '</p>';
		wp_editor( htmlspecialchars_decode( $content ), '_wc_ld_product_code_description', $settings );
		echo '<div class="options_group">';
		echo '<h3>' . __( 'Custom Titles', 'highthemes' ) . '</h3>';
		echo '<p>';
		_e("Each license code can have up to 4 different fields. You can define custom field titles for each product here. <br>i.e. license, expire date, owner name, etc.");
		echo '</p>';
		woocommerce_wp_text_input( array(
			'id'          => '_wc_ld_code1_title',
			'label'       => __( 'License Main Field Title', 'highthemes' ),
			'desc_tip'    => true,
			'description' => __( 'Enter a custom title for the first filed. It is the main field. you can ignore the other ones if you do not need them.', 'highthemes' ),
			'type'        => 'text',
		) );
		woocommerce_wp_text_input( array(
			'id'          => '_wc_ld_code2_title',
			'label'       => __( 'Field 2 Title', 'highthemes' ),
			'desc_tip'    => true,
			'description' => __( 'Enter a custom title for the second field', 'highthemes' ),
			'type'        => 'text',
		) );
		woocommerce_wp_text_input( array(
			'id'          => '_wc_ld_code3_title',
			'label'       => __( 'Field 3 Title', 'highthemes' ),
			'desc_tip'    => true,
			'description' => __( 'Enter a custom title for the third field', 'highthemes' ),
			'type'        => 'text',
		) );
		woocommerce_wp_text_input( array(
			'id'          => '_wc_ld_code4_title',
			'label'       => __( 'Filed 4 Title', 'highthemes' ),
			'desc_tip'    => true,
			'description' => __( 'Enter a custom title for the fourth field', 'highthemes' ),
			'type'        => 'text',
		) );
		echo "</div>";
	}

	public static function save( $post_id ) {

		if ( empty( $post_id ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'],
				'woocommerce_save_data' )
		) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}


		// Sanitize user input.
		$product_code_description = $_POST['_wc_ld_product_code_description'];

		$ld_code1_title = sanitize_text_field( $_POST['_wc_ld_code1_title'] );
		$ld_code2_title = sanitize_text_field( $_POST['_wc_ld_code2_title'] );
		$ld_code3_title = sanitize_text_field( $_POST['_wc_ld_code3_title'] );
		$ld_code4_title = sanitize_text_field( $_POST['_wc_ld_code4_title'] );


		// Update the meta field in the database.
		update_post_meta( $post_id, '_wc_ld_product_code_description', $product_code_description );

		update_post_meta( $post_id, '_wc_ld_code1_title', $ld_code1_title );
		update_post_meta( $post_id, '_wc_ld_code2_title', $ld_code2_title );
		update_post_meta( $post_id, '_wc_ld_code3_title', $ld_code3_title );
		update_post_meta( $post_id, '_wc_ld_code4_title', $ld_code4_title );

	}

}
