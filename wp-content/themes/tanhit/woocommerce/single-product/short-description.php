<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

$post->post_excerpt = apply_filters( 'woocommerce_short_description', $post->post_excerpt );

//add_filter( 'woocommerce_short_description', 'do_shortcode', 12 );
//$post->post_excerpt = str_replace('[private_potice]', custom_private_potice(), $post->post_excerpt);

?>
<div itemprop="description">
	<?php echo $post->post_excerpt ?>
</div>
