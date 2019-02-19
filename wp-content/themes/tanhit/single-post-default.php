<?php
/**
 * @package tanhit
 */
?>
<?php

$private_categories = explode( ',', Tanhit_Site_Manager::get_options( 'private_category' ) );

$cat = get_the_category(); $cat = $cat[0];
get_header(); 
?>
<section style="min-height: 300px">
    <div class="container">
        <div class="content news">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <a href="<?php echo get_category_link( $cat->cat_ID ); ?>" title="" class="child" ><?php echo $cat->cat_name; ?></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h2><?php the_title(); ?></h2>
                    </div>
                </div>
				<?php if ( ! is_user_logged_in() && in_array( $cat->cat_ID, $private_categories ) ) { ?>
					<div class="row">
						<div class="col-sm-12">
							<?php pll_e( 'You need to register to read this post', 'tanhit' ); ?>
						</div>
					</div>		<?php				
				} else {	?>	
					<div class="row">
						<div class="col-sm-12">
							<?php the_content(); ?>
						</div>
					</div>
					<?php				
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
                } ?>
            <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
