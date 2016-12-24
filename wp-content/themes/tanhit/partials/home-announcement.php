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
	?> 
	<div id="slider">
		<a href="#" class="control_next">&gt;</a>
		<a href="#" class="control_prev">&lt;</a>	<?php
		
		$now = date( 'Ymd', time() );	?>

		
		<ul>
		<?php foreach( $list as $item ) {
			
			$date_start  = strtotime( get_post_meta( $item->ID, 'product_date_start', true ) );
			
			if ( $date_start < strtotime( $now ) ) {
				continue;
			}			?>
			<li>

				<?php
				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $item->ID ), 'full' );
				$url = $thumb['0'];
				if ( empty( $url ) ) {
					/**
					 * Set default thumbnail for announcement
					 * @todo make correct creating of url
					 */
					$url = '';
				}	
				?>
				<a href="<?php the_permalink( $item->ID ); ?>" 
					class="imgmg" 
					style="background: url('<?php echo $url; ?>');">
				</a>
				
				<?php pll_e( 'Начало', 'tanhit' ); echo '&nbsp;' . date_i18n( 'd F Y', $date_start ) . '<br />'; ?>
				<a href="<?php the_permalink( $item->ID ); ?>" class="link">
					<?php echo $item->post_title; ?>
				</a>
			</li>
		<?php } 	?>	
		</ul>
	</div>
	<script>
		/* Slider */
		jQuery(document).ready(function ($) {

			var time_on_slide=3000;
			var slide_speed=1000;

			var slide_timer = setInterval(function () {
				moveRight();
			}, time_on_slide);

			$('#slider').hover(function(ev){
				clearInterval(slide_timer);
			}, function(ev){
				slide_timer = setInterval(function () { moveRight(); }, time_on_slide);
			});


			var slideCount = $('#slider ul li').length;
			var slideWidth = $('#slider ul li').width();
			var slideHeight = $('#slider ul li').height();
			var sliderUlWidth = slideCount * slideWidth;

			$('#slider').css({ width: slideWidth, height: slideHeight });
			$('#slider ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
			$('#slider ul li:last-child').prependTo('#slider ul');

			function moveLeft() {
				$('#slider ul').animate({
					left: + slideWidth
				}, slide_speed, function () {
					$('#slider ul li:last-child').prependTo('#slider ul');
					$('#slider ul').css('left', '');
				});
			};

			function moveRight() {
				$('#slider ul').animate({
					left: - slideWidth
				}, slide_speed, function () {
					$('#slider ul li:first-child').appendTo('#slider ul');
					$('#slider ul').css('left', '');
				});
			};

			$('a.control_prev').click(function (event) { event.preventDefault(); moveLeft(); });
			$('a.control_next').click(function (event) { event.preventDefault(); moveRight(); });

		});
	</script>	<?php

endif; 