<?php 
/**
 * Add offer for seminar|webinar
 */
?>
<?php

$type_of_event = get_post_meta( $post->ID, 'tanhit_type_of_event', true );

$offers = array();

$args = array(
	'post_type' => 'product',
	'posts_per_page' => -1,
	'post_status'    => 'publish',	
	'tax_query' => array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $type_of_event,
		),
	),
	'meta_query' => array(
		array(
			'key' 		=> '_visibility',
			'value' 	=> array( 'catalog', 'visible' ),
			'compare' 	=> 'IN'
		)
	)	
);

$loop = new WP_Query( $args );

if ( ! empty( $loop->posts ) ) {
	$offers = $loop->posts;
}	

if ( ! empty( $offers ) ) :		?>

	<h3><?php pll_e( 'Our offers:', 'tanhit' ); ?></h3>
	<ul class="ref-products">	<?php

		foreach( $offers as $offer ) :		
			/**
			 * @see meta fields: product_date_start, product_date_end
			 * value holds in DB in Ymd format
			 */		
			$product_date_start = get_post_meta( $offer->ID, 'product_date_start', true );

			if ( $product_date_start !="" ) {
			
				$now = date( 'Ymd', time() );

				if ( strtotime( $product_date_start ) < strtotime( $now ) ) {
					/**
					 * don't offer event with obsolete date start
					 */
					continue;
					
				}

				?><li>

				<span class="offer-date">[<?=date( 'd.m.Y', strtotime( $product_date_start ) )?>]</span> <a class="offer-link" href="<?php the_permalink( $offer->ID ); ?>"><?php echo $offer->post_title; ?></a>
			</li><?php

			}	?>


		<?php endforeach;		?>
		
	</ul><!--/.products-->	<?php

endif;