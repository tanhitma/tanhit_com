<?php
/**
 * Template for /архив-вебинаров-и-практик/ page
 * 
 * @package Tanhit
 */

$tanhit_url = explode( '?', 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
$tanhit_page_id = url_to_postid( $tanhit_url[0] );

if ( 697 == $tanhit_page_id ) :

	/**
	 * Remove price after product title
	 * @see woocommerce template content-product.php
	 */
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	
	/**
	 * Relocate price before product title
	 * 
	 * @see woocommerce_template_loop_product_title() in wc-template-functions.php
	 * @see wc-template-hooks.php
	 */
	/**
 	 * closed with comment since 13.05.2016
	 */
	/* 
	add_action( 'woocommerce_before_shop_loop_item_title', 'tanhit_before_shop_loop_item_title'  );
	function tanhit_before_shop_loop_item_title() {
		
		global $product;

		if ( $price_html = $product->get_price_html() ) : ?>
			<span class="price"><?php echo $price_html; ?></span>
		<?php endif;

	}
	*/
	
	/**
	 * Set price format for product with zero price 
	 * @see get_price_html() in abstract-wc-product.php
	 */
	/**
	 * @see tanhit-functions.php
	 */	
	/* 
	add_filter( 'woocommerce_free_sale_price_html', 'tanhit_free_sale_price_html', 10, 2 );
	function tanhit_free_sale_price_html( $price, $wc_product ) {
		return '<del><span class="amount">' . $wc_product->regular_price . '&nbsp;&#8381;</span></del> <ins>0 ₽ !!!</ins>';
	}
	// */
	
endif;	