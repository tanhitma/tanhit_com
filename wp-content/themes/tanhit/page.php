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

$post_access_protect = get_field( "access_protect", $post->ID );
$iShowPage = getPageProtectShow($post->ID);


get_header();

if ($iShowPage){?>
<section <?if($post_access_protect){?>class='protection-content'<?}?> style="min-height: 300px">
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
<?}else{?>
	<section style="min-height: 300px">
		<div class="container">
			<div class="content">
				<div class="row">
                    <div class="col-sm-12">
                        <div style='padding:20px;text-align:center;'>Страница защищена от просмотра</div>
                    </div>
                </div>
			</div>
		</div>
	</section>
<?}?>

<?php get_footer(); ?>