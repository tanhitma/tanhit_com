<?php
/**
 * @package tanhit
 */
?>
<?php 

global $wp_query;
$category_name = urldecode( $wp_query->query[ 'category_name' ] );
$category_id   = $wp_query->query_vars[ 'cat' ];
get_header(); ?>

<section style="min-height: 300px">
    <div class="container">
        <div class="content">
            <div class="row">
                <div class="col-sm-12">
                    <h2><?php echo mb_convert_case( $category_name, MB_CASE_TITLE, 'UTF-8' ); ?></h2>
                </div>
            </div>
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="row news-item">
                    <div class="col-sm-12">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    </div>
                    <div class="col-sm-12">
                        <div class="entry">

								<?php /*echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); */ ?>
								<?php the_content(); ?>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php endif; ?>
            <div class="pagination">
                <?php echo paginate_links(); ?>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>
