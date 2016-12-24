<?php
/**
 * @package Tanhit
 */

/**
 * [tanhit_offers]
 */
add_shortcode( 'tanhit_offers', array( 'tanhitShortcodes', 'tanhit_get_offers' ) );

/**
 * [tanhit_products]
 * example: [tanhit_products for_post_id="189"]
 */
add_shortcode( 'tanhit_products', array( 'tanhitShortcodes', 'tanhit_get_products' ) );

/**
 * [tanhit_main_anounce] - anounce on main page
 */
add_shortcode( 'tanhit_main_anounce', array( 'tanhitShortcodes', 'tanhit_get_main_anounce' ) );
add_shortcode( 'tanhit_main_nearest_event', array( 'tanhitShortcodes', 'tanhit_main_nearest_event' ) );
add_shortcode( 'tanhit_main_schedule', array( 'tanhitShortcodes', 'tanhit_main_schedule' ) );
add_shortcode( 'tanhit_main_news', array( 'tanhitShortcodes', 'tanhit_main_news' ) );
add_shortcode( 'tanhit_main_subscribe', array( 'tanhitShortcodes', 'tanhit_main_subscribe' ) );

class tanhitShortcodes {

	public static function tanhit_get_main_anounce( $attrs, $content = "" )
	{
		ob_start();
		get_template_part( 'partials/home-announcement', '' );
		return ob_get_clean();
	}

	public static function tanhit_main_nearest_event( $attrs, $content = "" )
	{
		ob_start();
		get_template_part( 'partials/home-nearest-event', '' );
		return ob_get_clean();
	}

	public static function tanhit_main_schedule( $attrs, $content = "" )
	{
		ob_start();
		get_template_part( 'partials/home-schedule', '' );
		return ob_get_clean();
	}

	public static function tanhit_main_news( $attrs, $content = "" )
	{
		ob_start();
		get_template_part( 'partials/home-news', '' );
		return ob_get_clean();
	}

	public static function tanhit_main_subscribe( $attrs, $content = "" )
	{
		ob_start();
        dynamic_sidebar('Mainpage subscribe');

		return ob_get_clean();
	}

	
	public static function tanhit_get_products( $attrs, $content = "" )
	{
		
		$no_result = false;
	
		$select_id = 0;
		
		/**
		 * attrs for_post_id="{post_id}"
		 */
		if (  empty( $attrs[ 'for_post_id' ] ) ) {

			global $post;
			
			if ( empty( $post ) ) {
				$no_result = true;
			} else {
				$select_id = $post->ID;
			}
			
		} else {
			$select_id = $attrs[ 'for_post_id' ];
		}	

		if ( ! $no_result ) {
		
			$all_events_obsolete = true;
			
			global $wpdb;

			$meta_query =
				$wpdb->prepare( 
					"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'product_link_to_page' AND meta_value = %s",
					$select_id
				);


			$results = $wpdb->get_results( $meta_query, ARRAY_A );	

			$post_in = array();
			
			if ( empty( $results ) ) :

				$no_result = true;
			
			else:
				
				foreach( $results as $result ) {
				
					foreach( $result as $key=>$id ) {
						$post_in[] = $id;
					}
				
				}	
				
				$args = array( 
					'post_type' 	=> 'product', 
					'post_starus' 	=> 'publish', 
					'post__in'  	=> $post_in,
                    'meta_key'   	 => 'product_date_start',
                    'orderby'    	 => 'meta_value_num',
                    'order'      	 => 'ASC'
				);
				$loop = new WP_Query( $args );
				
				$products = array();
				if ( ! empty( $loop->posts ) ) {
					$products = $loop->posts;
				}
			
				if ( empty( $products ) ) :
				
					$no_result = true;
				
				else:	

					ob_start(); ?>

					<div class="offers-wrapper tanhit-shortocode">

						<h4><?php pll_e('Our offers:', 'tanhit'); ?></h4>
						<ul class="ref-products">    <?php
						
							$now = date('Ymd', time());
						
							foreach ($products as $offer) :
								/**
								 * @see meta fields: product_date_start, product_date_end
								 * value holds in DB in Ymd format
								 */
								$product_date_start = get_post_meta($offer->ID, 'product_date_start', true);

								if ( ! empty( $product_date_start ) ) {

									if (strtotime($product_date_start) < strtotime($now)) {
										/**
										 * don't offer event with obsolete date start
										 */
										continue;
									}
									$all_events_obsolete = false;
									?>
									<li>
									<span class="offer-date"><?= date_i18n('d F Y', strtotime($product_date_start)) ?></span> <a
										class="offer-link"
										href="<?php the_permalink($offer->ID); ?>"><?php echo $offer->post_title; ?></a>
									</li><?php

								} ?>


							<?php endforeach;	?>
							
						</ul><!--/.products-->

					</div><!-- /.offers-wrapper-->
					<?php
					
					if ( $all_events_obsolete ) {
						$no_result = true;
						ob_end_clean();
					}
					
				endif;
			
			endif;
		
		}

		if ( $no_result ) :
			ob_start(); ?>
			<div class="offers-wrapper tanhit-shortocode no-offers">
				<h4><?php pll_e('Our offers:', 'tanhit'); ?></h4>
				<ul class="ref-products no-products">
					<li>	
						<?php pll_e( 'Cобытий пока в расписании нет.', 'tanhit' ); ?>
					</li>	
				</ul><!--/.products-->	
			</div><!-- /.offers-wrapper-->	

			<?php
		endif;

		return ob_get_clean();	
		
	}

	
	/**
	 * Callback function for [tanhit_offers] shortcode
	 */
	public static function tanhit_get_offers( $attrs, $content = "" )
	{
		/**
		 * We can use attributes in shortcode like [tanhit_offers categoty="webinar" show_if_empty="no"]
		 */

		global $post;

		/**
		 * To get "offers" post must to have meta field 'tanhit_type_of_event' @see metabox inside of post
		 */
		$type_of_event = get_post_meta($post->ID, 'tanhit_type_of_event', true);

		$offers = array();

		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'slug',
					'terms' => $type_of_event,
				),
			),
			'meta_query' => array(
				array(
					'key' => '_visibility',
					'value' => array('catalog', 'visible'),
					'compare' => 'IN'
				)
			)
		);

		$loop = new WP_Query($args);

		if (!empty($loop->posts)) {
			$offers = $loop->posts;
		}

		if (!empty($offers)) {

			ob_start(); ?>

			<div class="offers-wrapper tanhit-shortocode">

				<h4><?php pll_e('Our offers:', 'tanhit'); ?></h4>
				<ul class="ref-products">    <?php

					foreach ($offers as $offer) :
						/**
						 * @see meta fields: product_date_start, product_date_end
						 * value holds in DB in Ymd format
						 */
						$product_date_start = get_post_meta($offer->ID, 'product_date_start', true);

						if (!empty($product_date_start)) {

							$now = date('Ymd', time());

							if (strtotime($product_date_start) < strtotime($now)) {
								/**
								 * don't offer event with obsolete date start
								 */
								continue;
							}

							?>
							<li>
							<span class="offer-date"><?= date_i18n('d f Y', strtotime($product_date_start)) ?></span> <a
								class="offer-link"
								href="<?php the_permalink($offer->ID); ?>"><?php echo $offer->post_title; ?></a>
							</li><?php

						} ?>


					<?php endforeach; ?>

				</ul>
				<!--/.products-->

			</div><!-- /.offers-wrapper-->
			<?php

			return ob_get_clean();

		} else {
			return pll_e( 'События пока не запланированы', 'tanhit' );
		}
	}
 }