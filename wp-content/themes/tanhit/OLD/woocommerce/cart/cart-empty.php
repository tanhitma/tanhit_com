<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

wc_print_notices();

?>

<p class="cart-empty">
    <br><br>
    <?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?>
</p>

<?php do_action( 'woocommerce_cart_is_empty' ); ?>

<p class="return-to-shop">
    <a class="button wc-backward" href="/schedule?filter=seminar">
        <?php pll_e( 'Семинары', 'tanhit' ) ?>
    </a>
    <a class="button wc-backward" href="/schedule?filter=webinar">
        <?php pll_e( 'Вебинары', 'tanhit' ) ?>
    </a>
    <a class="button wc-backward" href="/%D0%BF%D1%80%D0%B0%D0%BA%D1%82%D0%B8%D0%BA%D0%B8-%D0%BA%D0%B0%D1%82%D0%B0%D0%BB%D0%BE%D0%B3">
        <?php pll_e( 'Практики', 'tanhit' ) ?>
    </a>
</p>
