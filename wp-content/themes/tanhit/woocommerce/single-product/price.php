<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

	<p class="price"><?php

		/* pll_e( 'Price: ', 'tanhit' ); */
		global $product;
		/*$product_prop=get_the_terms($post->ID, 'product_cat');
		$product_cat = $product_prop[0]->slug;

		if ($product_cat == 'seminar') {
			pll_e( 'Предоплата: ', 'tanhit' );
		}*/

		if(get_post_meta($product->get_id(), '_wc_preorder', 1) == 'yes'){
		  pll_e( get_post_meta($product->get_id(), 'wc_product_preorder_text', 1). ': ', 'tanhit' );
    }

		echo $product->get_price_html();

		?>
	</p>

	<meta itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
</div>