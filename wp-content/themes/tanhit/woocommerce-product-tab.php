<?php
/**
 * Adding new tab for product
 */
add_filter( 'woocommerce_product_tabs', 'tanhit_new_product_tab' );
function tanhit_new_product_tab( $tabs ) {
	
	global $post, $tanhit_product_link_to_page;
	
	$tanhit_product_link_to_page = get_post_meta( $post->ID, 'product_link_to_page', true );	

	if ( ! empty ( $tanhit_product_link_to_page ) && 'null' != $tanhit_product_link_to_page ) {
		
		$tabs['additional_description_tab'] = array(
			'title' 	=> pll__( 'Подробное описание', 'tanhit' ),
			'priority' 	=> 50,
			'callback' 	=> 'tanhit_new_product_tab_content'
		);
		
	}	

	return $tabs;

}

/**
 * Callback to add new tab content
 */
function tanhit_new_product_tab_content() {
	
	/**
	 * The new tab content
	 */
	//echo '<h2>' . pll__( 'Дополнительное описание', 'tanhit' ) . '</h2>';
	echo '<h2></h2>';

	global $tanhit_product_link_to_page;	
	
	if ( ! empty ( $tanhit_product_link_to_page ) && 'null' != $tanhit_product_link_to_page ) {
		/**
		 * @see http://codex.wordpress.org/Function_Reference/get_post_field for description
		 */
		echo preg_replace("/(\[.+\])|(<button.+\/button>)/i", "", get_post_field( 'post_content', $tanhit_product_link_to_page ));
	}
	
} 