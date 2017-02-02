<?php
/**
 * @package tanhit
 */
/**
 * Init vars
 */
add_action('woocommerce_before_single_product', 'tanhit_woocommerce_before_single_product', 0);

function tanhit_woocommerce_before_single_product() {

    global $tanhit_add_button_enter_to_webinar;
    $tanhit_add_button_enter_to_webinar = false;

    global $product, $current_user, $post;

    global $product_meta;
    $product_meta = array();

    $product_meta['date_start'] = get_post_meta($post->ID, 'product_date_start', true);
    $product_meta['date_end'] = get_post_meta($post->ID, 'product_date_end', true);
    $product_meta['time_start'] = get_post_meta($post->ID, 'product_time_start', true);

    $show_link = false;


    if (is_user_logged_in()) {
        $user_access = get_post_meta($product->id, 'access', TRUE);
        if (tanhit_customer_bought_product() || $product->price == 0 || (current_user_can('vip') && in_array($user_access, array(1, 3)))) {
            $show_link = true;
        }
    }


    /*
     * If product free or bought Remove add to cart
     */
    $has_access_to_offer = false;
    $user_access = get_post_meta($product->id, 'access', TRUE);
    if (tanhit_customer_bought_product() || $product->price == 0 || (current_user_can('vip') && in_array($user_access, array(1, 3)))) {
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        $has_access_to_offer = true;
    }

    /*
     * Get product type
     */
    $product_prop = get_the_terms($post->ID, 'product_cat');
    $product_cat = $product_prop[0]->slug; // 'seminar' / 'webinar'

    /*
     * Get product downloadable files
     */
    $_downloadable_files = get_post_meta($post->ID, '_downloadable_files', true);
    $product_has_files = false;
    if (!empty($_downloadable_files)) {
        $product_has_files = true;
    }

    $now = gmdate('Ymd', time() + 3 * HOUR_IN_SECONDS);
    if (
            ($has_access_to_offer && $product_cat == 'webinar' && strtotime($product_meta['date_start']) >= strtotime($now)) // Webinars - today or future
    ) {
        $tanhit_add_button_enter_to_webinar = 'enter_to_webinar';  // Button [Enter to webinar]
    } elseif (
            ($has_access_to_offer && empty($product_meta['date_start']) ) // For free practices
            || ($has_access_to_offer && $product_has_files) // For product has files
    ) {
        $tanhit_add_button_enter_to_webinar = 'download_in_myaccount'; // Button [Download in my account]
    }

    /*

      if ( empty( $product_meta[ 'date_start' ] ) && $product->sale_price == 0 ) {

      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
      $tanhit_add_button_enter_to_webinar = 'download_in_myaccount';

      //} else if ( $show_link || ( strtotime( $product_meta[ 'date_start' ] ) == strtotime( $now ) && $product->sale_price == 0 ) ) {
      } else if ( $show_link ) {
      // remove add to cart button - @see template content-single-product.php
      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
      $tanhit_add_button_enter_to_webinar = 'enter_to_webinar';

      }
     */
}

/**
 * We need to move short description below Add to cart button
 * @see functions.php remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
 */
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 32);

/**
 * Add link to webinar room if date of webinar is current date and sale price is 0
 */
add_action('woocommerce_single_product_summary', 'tanhit_woocommerce_template_current_webinar', 31);

function tanhit_woocommerce_template_current_webinar() {

    global $product, $current_user, $product_meta, $tanhit_add_button_enter_to_webinar;

    if (getAccessToProduct($product->id) === 0) {
        ?>
        Доступно только для Ближний Круг
    <?
    } else {
        if ( ! $product->is_virtual()) {
            wc_get_template('single-product/add-to-cart/simple.php');
        } else {
            if ($tanhit_add_button_enter_to_webinar == 'download_in_myaccount') {
                ?>
                <a href="<?php echo wc_get_page_permalink('myaccount'); ?>?pid=<?= $product->id ?>" class="button btn-show btn-show-margin"><?php pll_e('Смотреть полную версию', 'tanhit'); ?></a>
                <?php
            } else if ($tanhit_add_button_enter_to_webinar == 'enter_to_webinar') {
                if (is_user_logged_in()) {
                    if ($iCntProduct = getAccessToProduct($product->id)) {
                        $aLinkstoWebinar = getLinkAutologinToRoom($product->id, $iCntProduct);
                        if ($aLinkstoWebinar) {
                            if (count($aLinkstoWebinar) > 1) {
                                ?>
                                <div>Ваши ссылки для входа на вебинар:</div>
                                <br />
                            <? }

                            foreach ($aLinkstoWebinar as $key => $link) {
                                ?>
                                <div>
                                    <a style='margin-bottom:10px!important;' href="<?= $link ?>" class="button btn-show btn-show-margin" target="_blank">Вход на вебинар<?= (count($aLinkstoWebinar) > 1 ? " (ссылка " . ($key + 1) . ")" : '') ?></a>
                                </div>
                            <?
                            }
                        } else {
                            ?>
                            <a href="<?php echo home_url() . '/current-webinar/?id=' . $product->id; ?>" class="button btn-show btn-show-margin"><?php pll_e('Вход на вебинар', 'tanhit'); ?></a>
                        <?
                        }
                    } else {
                        wc_get_template('loop/add-to-cart.php');
                    }
                } else {
                    ?>
                    для доступа авторизуйтесь
                <?
                }
                //<a href="echo home_url() . '/current-webinar/?id=' . $product->id;" class="button btn-show btn-show-margin">pll_e( 'Вход на вебинар', 'tanhit' );</a>
            }
        }
    }
}

/**
 * Add date start, date end, time start
 *
 * @see meta fields: product_date_start, product_date_end ( value holds in DB in Ymd format ), product_time_start
 */
add_action('woocommerce_single_product_summary', 'tanhit_woocommerce_template_date', 6);

function tanhit_woocommerce_template_date() {

    global $post;

    global $product_meta;

    if (!empty($product_meta['date_start']) || !empty($product_meta['date_end']) || !empty($product_meta['time_start'])) :
        ?>
        <div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                <?php /**
                 * Date start
                 */ ?>
            <?php if (!empty($product_meta['date_start'])) {
                $product_meta['date_start'] = date('d.m.Y', strtotime($product_meta['date_start']));
                ?>
                <p class="date">
                <?php pll_e('Start date:', 'tanhit'); ?>&nbsp;<span class="availability_starts event-date"><?php echo $product_meta['date_start']; ?></span>
                <?php if (empty($product_meta['time_start'])) { ?>	
                    </p>
                <?php } ?>
                <meta itemprop="availabilityStarts" content="<?php echo $product_meta['date_start']; ?>" href="http://schema.org/availabilityStarts">
        <?php } ?>
        <?php /**
         * Time start
         */ ?>			
        <?php if (!empty($product_meta['time_start'])) { ?>
            <?php pll_e('Start time:', 'tanhit'); ?>&nbsp;<span class="event-time"><?php echo $product_meta['time_start']; ?></span>
            </p> <?php /** @see <p class="date"> */ ?>
        <?php } ?>
        <?php /**
         * Date end
         */ ?>			
        <?php if (!empty($product_meta['date_end'])) {
            $product_meta['date_end'] = date('d.m.Y', strtotime($product_meta['date_end']));
            ?>
            <p class="date"><?php pll_e('End date:', 'tanhit'); ?>&nbsp;<span class="availability_ends event-date"><?php echo $product_meta['date_end']; ?></span></p>
            <meta itemprop="availabilityEnds" content="<?php echo $product_meta['date_end']; ?>" href="http://schema.org/availabilityEnds">
        <?php } ?>		
        </div>

        <?php
    endif;
}
