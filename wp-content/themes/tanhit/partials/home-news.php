<?php
/**
 * The template part for displaying news at home page
 *
 * @package Tanhit
 */

$args = array(
	'post_type' => 'post',
	'posts_per_page' => 5,
	'post_status'    => 'publish',	
	'tax_query' => array(
		array(
			'taxonomy' => 'category',
			'field'    => 'slug',
			'terms'    => 'news',
		),
	)
);

$loop = new WP_Query( $args );

$news = array();

if ( ! empty( $loop->posts ) ) {
	$news = $loop->posts;
}	

if ( ! empty( $news ) ) : ?>

	<ul>	<?php
	
	foreach( $news as $item ) {	?>
		
		<li>
			<?php echo get_the_post_thumbnail( $item->ID, 'thumbnail' ); ?>
			<a href="<?php the_permalink( $item->ID ); ?>"><?php echo $item->post_title; ?></a>

		</li> 	<?php
		
	}	

endif;
