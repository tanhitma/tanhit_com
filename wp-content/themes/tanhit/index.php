<?php get_header();?>
<section>
    <div class="container">
        <div class="content">
            <div class="row">
                <div class="col-sm-12">

                    <main id="main" class="site-main" role="main">
                        <?php /* Start the Loop */ ?>
                        <?php while ( have_posts() ) : the_post(); ?>
                            <div class="row blog-item">
                                <div class="col-sm-12">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                </div>
                                <div class="col-sm-12">
                                    <div class="post-date">
                                        <?php pll_e( 'Дата:', 'tanhit' ) ?> <span><?php the_time('d F Y') ?></span>
                                        <?php
                                        // Comments
                                        $sNoComments = ''; // pll__( 'Комментариев нет', 'tanhit' );
                                        $s1Comment = '<span class="post-comments"><span>1</span></span>'; //'1 comment';
                                        $sNComments = '<span class="post-comments"><span>%</span></span>'; //'% comments';

                                        $sCommentsClosed = ''; //'Comments closed'
                                        comments_popup_link( $sNoComments, $s1Comment, $sNComments, 'comments-link', $sCommentsClosed);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="entry">

                                        <?php the_content(); ?>

                                        <p class="post-tag">
                                            <?php
                                            $PageURL = '/category/blog';
                                            $posttags = get_the_tags();
                                            if ($posttags) {

                                                pll_e( 'Рубрики: ', 'tanhit' );

                                                foreach($posttags as $tag) {
                                                    echo '<a href="' . $PageURL . '?tag=' . $tag->slug . '" ' . '>' . $tag->name.'</a>' . ' ';
                                                }
                                            }
                                            ?>

                                        </p>

                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </main><!-- .site-main -->

                </div>
            </div>


            <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) {
                    comments_template();
                }
            ?>

        </div>
    </div>
</section>
<?php get_footer(); ?>
    
