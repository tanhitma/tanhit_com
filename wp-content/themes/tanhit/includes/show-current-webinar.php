<?php
/**
 * @package Tanhit
 */
add_action( 'woocommerce_before_my_account', 'tanhit_show_current_webinar' );
function tanhit_show_current_webinar() {	

	/**
	 * Block: Current Webinars
	 *
	 * Блок "Текущий вебинар" - возник из-за разночтения, но оставляем его в теукщем виде. 
	 * Он содержит купленные и/или бесплатные вебинары с текущей датой == дата начала в товаре.
	 * Суть блока: Предоставить быстрый доступ пользователю к онлайн-вебинарам которые будут проводиться сегодня в онлайне.	 
	 */
	global $tanhit_customer_products;
	
	$products = $tanhit_customer_products;
	
	$now = gmdate( 'Ymd', time() + 3 * HOUR_IN_SECONDS );

	foreach( $products as $id=>$product ) {
		if ( strtotime( $product[ 'product_date_start' ] ) != strtotime( $now ) ) {
			unset( $products[ $id ] );
		}	
	}
	
	/** 
	 *  get free webinars for current date
     */
	$args = array(
		'post_type' 	 => 'product',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'	 => array(
			array(
				'key'     => 'product_date_start',
				'value'   => $now,
				'compare' => '=',
			)
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( 'webinar' ),
			)
		),
	);	 
	
    $events = new WP_Query( $args );
	
	$free_products = array();	
	$i = 0;
	foreach( $events->posts as $post ) {
		
		$pr = wc_get_product( $post->ID );
		
		if ( $pr->get_price() > 0 ) {
			/**
			 * Don't add product with price > 0
			 */			
			continue;
		}			
		$free_products[ $i ][ 'product_id' ] = 	$post->ID;
		$free_products[ $i ][ 'product_name'] = $post->post_title;
		$free_products[ $i ][ 'permalink' ]  = get_the_permalink( $post->ID );
		$free_products[ $i ][ 'product_date_start' ] = get_post_meta( $post->ID, 'product_date_start', true );
		$free_products[ $i ][ 'product_time_start' ] = get_post_meta( $post->ID, 'product_time_start', true );
		
		$i++;
	}

	if ( ! empty( $free_products ) ) {
		$products = array_merge( $products, $free_products );
	}	
	 
	if ( empty( $products ) ) {
		/**
		 * Customer doesn't have paid webinar for current date
		 */
		return;	
	}
	
	$checked_ids = array();
	?>

	<h2><?php pll_e( 'Текущий вебинар', 'tanhit' ); ?></h2>

	<ul class="current-webinar offer-block">
		<?php foreach( $products as $id=>$product ) :  
			/**
			 * We need exclude duplicate that can be in array due to different reasons
			 */
			if ( in_array( $product[ 'product_id' ], $checked_ids ) ) {
				continue;
			}
			$checked_ids[] = $product[ 'product_id' ];
			$time_start = empty( $product[ 'product_time_start' ] ) ? '' : $product[ 'product_time_start' ];
			?>
			<li data-product-id="<?php echo $product[ 'product_id' ]; ?>">
				<span class="item-date"><?php echo date_i18n( 'j F \<\b\r\>Y', strtotime( $product[ 'product_date_start' ] ) ); ?></span>
				<span class="item-time"><?php echo $time_start . '&nbsp;' . pll__( 'МСК', 'tanhit' ); ?></span>
				<a href="<?php echo $product[ 'permalink' ]; ?>" class="item-link" target="_blank"><?php echo $product[ 'product_name']; ?></a>
                <span style="line-height: 40px !important;" class="time-remains"><?php pll_e( 'Сегодня!', 'tanhit' ); ?> <?php echo $remain; ?></span>
                <a href="<?php echo home_url() . '/current-webinar/?id=' . $product[ 'product_id' ]; ?>" class="btn-show" target="_blank"><?php pll_e( 'Вход на вебинар', 'tanhit' ); ?></a>
			</li>
		<?php endforeach; 	?>	
	</ul>

	<?php
	
}