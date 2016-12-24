<?php
/**
 * To get current language use pll_current_language()
 *
 * @package Tanhit
 */
get_header(); 
?>
<section style="min-height: 300px">
    <div class="container">
        <div class="content main-page">

                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile; ?>
                <?php endif; ?>

            <?php /*
                <div class="row">
                    <div class="col-sm-3">
						<h3><?php pll_e( 'Анонс', 'tanhit' ); ?></h3>
                        <div class="main-module main-anounce">
                            <?php get_template_part( 'partials/home-announcement', '' ); 	?>
                        </div>
                    </div>
                    <div class="col-sm-6">
						<h3><?php pll_e( 'Ближайшее событие', 'tanhit' ); ?></h3>
                        <div class="main-module main-nearest">
						    <?php get_template_part( 'partials/home-nearest-event', '' ); ?>
                        </div>
                    </div>

                    <div class="col-sm-3">
						<h3><a href="/schedule"><?php pll_e( 'Расписание', 'tanhit' ); ?></a></h3>
						<div class="main-module main-schedule">
                            <div class="up">
                                <?php get_template_part( 'partials/home-schedule', '' ); ?>
                            </div>
                            <div class="down">
                                <a class="more-link" href="/schedule"><?php pll_e( 'Все события', 'tanhit' ); ?></a>
                            </div>

						</div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-6">
						<h3><a href="/category/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8"><?php pll_e( 'Новости', 'tanhit' ); ?></a></h3>
                        <div class="main-module main-news">

                            <div class="up">
                                <?php get_template_part( 'partials/home-news', '' ); ?>
                            </div>
                            <div class="down">
                                <a class="more-link" href="/category/%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8">Просмотреть все новости</a>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-3">
						<h3><a href="/%D0%B2%D0%B5%D0%B1%D0%B8%D0%BD%D0%B0%D1%80-%D0%BA%D0%B0%D0%BB%D0%B8-%D0%BC%D0%B5%D0%B4%D0%B8%D1%82%D0%B0%D1%86%D0%B8%D1%8F"><?php pll_e( 'Вебинары', 'tanhit' ); ?></a></h3>
                        <div class="main-module main-webinar">
                            <iframe width="200" height="170" src="https://www.youtube.com/embed/xAheaLv9OpM" frameborder="0" allowfullscreen></iframe>
                            <br/>
                            <a href="/product-cat/webinar">Вебинар Пробуждение - сотворение реальности</a>
                            <br/>
                            <iframe width="200" height="170" src="https://www.youtube.com/embed/mxRzQ6LgwvA" frameborder="0" allowfullscreen></iframe>
                            <br/>
                            <a href="/product-cat/webinar">Вебинар Рождение - осознанное воплощение</a>
                            <br/>
                            <a class="more-link" href="/product-cat/webinar"><?php pll_e( 'Все вебинары', 'tanhit' ); ?></a>
						</div>
                    </div>
                    <div class="col-sm-3">
						<h3><a href="http://www.youtube.com/channel/UCNzOcukRjk69ELB78XXj-VA"><?php pll_e( 'Видео', 'tanhit' ); ?></a></h3>
                        <div class="main-module main-video">
                            <iframe width="200" height="170" src="https://www.youtube.com/embed/lxHAZbcHKYE" frameborder="0" allowfullscreen></iframe>
                            <br/>
                            <a href="https://www.youtube.com/watch?v=lxHAZbcHKYE">Иисус: по пути вознесения</a>
                            <br/>
                            <br/>
                            <iframe width="200" height="170" src="https://www.youtube.com/embed/h6KsgB3ZcqM" frameborder="0" allowfullscreen></iframe>
                            <br/>
                            <a href="https://www.youtube.com/watch?v=h6KsgB3ZcqM">Кали марафон с Танит</a>
                            <br/>
                            <br/>
                            <a class="more-link" href="http://www.youtube.com/channel/UCNzOcukRjk69ELB78XXj-VA"><?php pll_e( 'Все видео', 'tanhit' ); ?></a>
						</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <h3><a href="http://vk.com/tanhit"><?php pll_e( 'В соц сетях', 'tanhit' ); ?></a></h3>
                        <div class="main-module main-soc">
                            <script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>
                            <!-- VK Widget -->
                            <div id="vk_groups"></div>
                            <script type="text/javascript">
                                VK.Widgets.Group("vk_groups", {mode: 0, width: "480", height: "325", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 44931363);
                            </script>
                        </div>
					</div>
                    <div class="col-sm-6">
                        <h3><a href="/%D1%84%D0%BE%D1%82%D0%BE%D0%B3%D0%B0%D0%BB%D0%B5%D1%80%D0%B5%D0%B8"><?php pll_e( 'Фото семинаров', 'tanhit' ); ?></a></h3>
                        <div class="main-module main-gallery">
                            <?php echo do_shortcode( '[slideshow id=8 gallery_width=480 gallery_height=335]' ); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h3><?php pll_e( 'Отзывы', 'tanhit' ); ?></h3>
                        <div class="main-module main-testimonials">
                            <?php echo do_shortcode('[testimonial_view id=3]'); ?>
                        </div>
					</div>
                </div>
            */ ?>

        </div>
    </div>
</section>
<?php get_footer(); ?>
