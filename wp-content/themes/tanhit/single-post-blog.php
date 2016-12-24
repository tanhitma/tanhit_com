<?php
/**
 * @package tanhit
 */
?>
<?php

$private_categories = explode( ',', Tanhit_Site_Manager::get_options( 'private_category' ) );

$cat = get_the_category();
$cat = $cat[0];

$BlogPageURL = '/category/blog';

get_header(); 
?>
<section style="min-height: 300px">
    <div class="container">
        <div class="content" id="blog">
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
				<div class="row">
                    <div class="col-sm-12">
                        <?php the_content(); ?>
                    </div>
                </div>
				<div class="row">
                    <div class="col-sm-12">
                        <?php
                        // If comments are open or we have at least one comment, load up the comment template.
                        if ( comments_open() || get_comments_number() ) {
                            comments_template();
                        }
                        ?>
                    </div>
                </div>
				<div class="row">
                    <div class="col-sm-12">
                        <p class="post-tag">
                            <?php
                            $posttags = get_the_tags();
                            if ($posttags) {

                                pll_e( 'Рубрики: ', 'tanhit' );

                                foreach($posttags as $tag) {
                                    echo '<a href="' . $BlogPageURL . '?tag=' . $tag->slug . '" ' . '>' . $tag->name.'</a>' . ' ';
                                }
                            }
                            ?>

                        </p>
                    </div>
                </div>

            <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
