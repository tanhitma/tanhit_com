<?php get_header(); ?>
<section style="min-height: 300px">
    <div class="container">
        <div class="content">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
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
            <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
