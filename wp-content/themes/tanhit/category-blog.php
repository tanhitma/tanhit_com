<?php
/**
 * @package tanhit
 */
?>
<?php
get_header();



// Default blog category
global $wp_query;
$category = get_category_by_slug('blog');
$args = array(
    'cat' 	 => $category->cat_ID,
);

// Check for
$current_tag = get_query_var( 'tag');
if ($current_tag!='') {
    $args['tag'] = $current_tag;
}

//print_r($args);

if (!empty(get_query_var('paged'))) {
    $page = (get_query_var('paged')) ? get_query_var('paged') : 1;
} else {
    $page = (get_query_var('page')) ? get_query_var('page') : 1;
}
$args['paged'] = $page;


//print_r( $wp_query );
query_posts($args);

$PageURL = '/category/blog';

?>

<section style="min-height: 300px">
    <div class="container">
        <div class="content" id="blog">
            <div class="row">
                <div class="col-sm-12">
                    <h2><a href="<?php echo $PageURL; ?>"><?php pll_e( 'Блог Танит', 'tanhit' ); ?></a></h2>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <div class="sidebar">
                        <h3><?php pll_e( 'Рубрики', 'tanhit' ); ?></h3>
                        <ul class="tags">
                            <?php
                            $tags = get_tags();
                            if ($tags) {
                                foreach ($tags as $tag) {
                                    echo '<li><a href="' . $PageURL . '?tag=' . $tag->slug . '" ' . '>' . $tag->name.'</a></li>';
                                }
                            }
                            ?>
                        </ul>

                        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Blog Sidebar')) : ?>
                                [ do default stuff if no widgets ]
                        <?php endif; ?>

                        <?php /*

                        <h3><?php pll_e( 'Ссылки', 'tanhit' ); ?></h3>

                        <ul class="links">
                            <li><a href="https://www.facebook.com/TanhitBeloved" class="soc-link icon-fb" target="_blank"></a><a href="https://www.facebook.com/TanhitBeloved">Facebook</a></li>
                            <li><a href="https://vk.com/tanhit" class="soc-link icon-vk" target="_blank"></a><a href="https://vk.com/tanhit">ВКонтакте</a></li>
                            <li><a href="http://www.youtube.com/channel/UCNzOcukRjk69ELB78XXj-VA" class="soc-link icon-youtube" target="_blank"></a><a href="http://www.youtube.com/channel/UCNzOcukRjk69ELB78XXj-VA">Youtube</a></li>
                        </ul>


                        <h3>Календарь</h3>



                        <h3>Соц сети</h3>
                        <div style="margin: 5px">
                            <script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>
                            <!-- VK Widget -->
                            <div id="vk_groups"></div>
                            <script type="text/javascript">
                                VK.Widgets.Group("vk_groups", {mode: 0, width: "205", height: "400", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 44931363);
                            </script>
                        </div>

 */ ?>

                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="entries">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                        <div class="entry blog-item">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                </div>
                            </div>
                            <div class="row">
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
                            </div>
                            <div class="row">
                                <div class="col-sm-12">

                                    <?php the_content(); ?>

                                    <p class="post-tag">
                                        <?php
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
                    <?php endif; ?>
                    </div>

                    <div class="pagination">
                        <?php echo paginate_links(); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>
