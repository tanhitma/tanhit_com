<?php
/**
 * @package Tanhit
 */
get_header();

$access_granted = false;
if ( is_user_logged_in() ) {
	$access_granted = true;	
}	
?>
<section style="min-height: 300px">
    <div class="container">
        <div class="content current-webinar">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="row">
                    <div class="col-sm-12">
						 <?php if ( $access_granted ) :	?>
							<h2><?php the_title(); ?></h2>
						<?php endif; ?>	
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php if ( $access_granted ) :	?>
                            <div style="text-align:center;">
                                <?php the_content(); ?>
                            </div>
                        <?php else: ?>
                            <?php pll_e( 'You need to register to read this post', 'tanhit' ); ?>
						<?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php 
get_footer(); 
?>
