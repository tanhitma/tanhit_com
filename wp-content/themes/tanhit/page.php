<?php 
$add_offer = false;
$ancestors = get_post_ancestors( $post->ID );
foreach( $ancestors as $ancestor ) {
	$ancestor_post = get_post( $ancestor );	
	$ancestor_title = mb_strtolower( $ancestor_post->post_title );
	if ( false !== mb_strpos( $ancestor_title, 'семинар' ) ) {
		$add_offer = true;
		break;
	}	
}	
get_header();
?>
<section style="min-height: 300px">
    <div class="container">
        <div class="content">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                
                <div class="row">
                    <div class="col-sm-12">
                        <?php the_content(); ?>
                    </div>
                </div>

		<?php /* Partial offers */
		/*
				if ( $add_offer ) {
					get_template_part( 'partials/offer', '' );
				}	
				*/?>

                <?php
                    // If comments are open or we have at least one comment, load up the comment template.
                    if ( comments_open() || get_comments_number() ) {
                        comments_template();
                    }
                ?>


            <?php endwhile; ?>
            <?php endif; ?>
            <?php /* $post->ID == 129 ? do_shortcode( '[instashow id="1"]' ): ''; */?>
        </div>
    </div>
</section>
<?php get_footer(); ?>