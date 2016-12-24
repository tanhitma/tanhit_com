<?php
/**
 * @package Tanhit
 */

$args = array(
	'post_type' 	 => 'product',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'meta_key'   	 => 'product_date_start',
	'orderby'    	 => 'meta_value_num',
	'order'      	 => 'ASC',	
	'meta_query'	 => array(
		array(
			'key'     => 'product_date_start',
			'value'   => '\d*',
			'compare' => 'REGEXP',
		)
	)
);

$loop = new WP_Query( $args ); 

if ( ! empty( $loop->posts ) ) {
	$list = $loop->posts;
}	

if ( empty( $list ) ) :
	/** do nothing **/
else :		

		
		$now = date( 'Ymd', time() );
		
		foreach( $list as $item ) { 	

			$date_start = strtotime( get_post_meta( $item->ID, 'product_date_start', true ) ) ;
			
			if ( $date_start <= strtotime( $now ) ) {
				continue;
			}



			/**
			 * @see class.yith-woocommerce-audio-video-content.php in YITH WooCommerce Featured Video plugin
			 * example	$free_featured_video = 'https://www.youtube.com/embed/CA6pdQkFGG0';
			 * example	$free_featured_video = 'https://youtu.be/bIZl06PBpKk';
			 */
			$free_featured_video = get_post_meta( $item->ID, '_video_url', true );
			
            if ( ! empty( $free_featured_video ) ) {
				/**
				 * for example https://www.youtube.com/embed/CA6pdQkFGG0
				 */
				 
				/**
				 * Get video_id as array
				 */	
				/*$video_id = explode( 'embed/', $free_featured_video );
		
				if ( ! empty( $video_id[1] ) ) {
					$video_id[1] = str_replace( '/', '', $video_id[1] );
				} else {
					$video_id[1] = 'video';
				}	*/
					
				include_once( 'home-nearest-event-player.php' );
				
			} else {
			
				/**
				 * @see https://developer.wordpress.org/reference/functions/get_the_post_thumbnail/
				 * $image_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large' ); // Standard sizes
				 * @see global $_wp_additional_image_sizes for additional image size
				 * in our case: widget-thumbnail[75x75], shop_thumbnail[180x180], shop_catalog[300x300], shop_single[600x600]
				 */ 
				global $_wp_additional_image_sizes;
				
				$img_size = 'medium';
				if ( array_key_exists( 'shop_catalog', $_wp_additional_image_sizes ) ) {	
					$img_size = 'shop_catalog';
				}

				$thumb = get_the_post_thumbnail( $item->ID, $img_size, array( 'class' => 'aligncenter' ) );
				if ( ! empty( $thumb ) ) {
					echo $thumb;
				} else {
					/**
					 * We can add video from youtube
					 */
					//include_once( 'home-nearest-event-player.php' );
					
				} 
			
			}	?>
			
			<p style="text-align: center;     margin-top: 20px;">
				<a href="<?php the_permalink( $item->ID ); ?>"><?php echo $item->post_title; ?></a>
			</p>
			<p style="text-align: center; font-weight: bold; margin-top: 5px;">
				<?php

					echo date_i18n( 'j F', strtotime( get_post_meta( $item->ID, 'product_date_start', true ) ) ).'. ';

					$remain = ( strtotime( get_post_meta( $item->ID, 'product_date_start', true ) ) - strtotime( $now ) ) / DAY_IN_SECONDS;

					if ($remain>0) {
						echo "(".pll__('осталось дней: ', 'tanhit');
						echo $remain.")";
					} else {
						echo pll__('Событие сегодня!', 'tanhit');
					}
				?>

			</p>

            <?php /*
            <p>
				<?php echo $item->post_excerpt; ?>
			</p>
            */
            ?>

			
			<?php 
			/**
			 * We need to get only one event
			 */
			break; 	?>
		<?php }	?>	
		<?php

endif;
