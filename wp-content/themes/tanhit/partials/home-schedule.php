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

if ( ! empty( $list ) ) :

	$now = date( 'Ymd', time() );

	foreach( $list as $item ) {	
	
		$date_start = strtotime( get_post_meta( $item->ID, 'product_date_start', true ) );

		if ( $date_start < strtotime( $now ) ) {
			continue;
		}	
	
		$date_end   = strtotime( get_post_meta( $item->ID, 'product_date_end', true ) );

		$date_in_shed = '';
		
		if ( empty( $date_end ) ) {

			$date_in_shed = date_i18n( 'j F', $date_start ); 
		
		} else {
			/**
			 * Get numeric representation of a month, without leading zeros	1 through 12
			 */ 
			$ms = (int) date( 'n', $date_start );
			$me = (int) date( 'n', $date_end );
			
			if ( $ms == $me ) {
				$mn = date_i18n( 'j F', $date_start ); 
				$ds = date( 'j', $date_start );
				$date_in_shed = $ds . '-' . date( 'j', $date_end ) . ' ' . str_replace( $ds, '', $mn );
			} else if ( $ms < $me ) {
				$date_in_shed = date_i18n( 'j F', $date_start ) . '-' . date_i18n( 'j F', $date_end );
			}	
		}	

		?>
		<div class="sched-row">
			<div class="sched-date">
				<?php /*echo date( 'd', $date_start ).' '.$monthes[date( 'm', $date_start )].' '.date( 'Y', $date_start ); */ ?>
				<?php /*echo '<br>'.strftime("%d %B, %H:%M",strtotime($date_start)); */ ?>
				<?php echo $date_in_shed; ?>
			</div>
			<div class="sched-desc">
				<?php echo ' <a href="'; ?>
				<?php the_permalink( $item->ID ); ?>
				<?php echo'">' . $item->post_title . '</a>'; ?>
			</div>
			<!--
			<div class="sched-link">
				<a href="<?php //the_permalink( $item->ID ); ?>">
					<?php // echo $action; ?>
				</a>
			</div> -->
		</div>		<?php	
	}
	
endif;