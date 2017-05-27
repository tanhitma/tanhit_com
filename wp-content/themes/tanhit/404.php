<?php get_header(); ?>
<section style="min-height: 300px">
    <div class="container">
        <div class="content">
            <?php/* if (have_posts()) : while (have_posts()) : the_post(); ?>
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
            <?php else: */?>

                <?php /*
                <!-- <h2><?php pll_e( 'Page not found', 'tanhit' ); ?></h2> -->
                <br>
                <br>
                <div style="text-align: center">
                <a style="font-size: 35px; color: red;" href="http://tanhit.com/product/%D0%B2%D0%B5%D0%B1%D0%B8%D0%BD%D0%B0%D1%80-%D0%BA%D0%B0%D0%BB%D0%B8-%D0%BC%D0%B5%D0%B4%D0%B8%D1%82%D0%B0%D1%86%D0%B8%D1%8F">Страница вебинара `Кали-медитация`</a>
                <br>
                <br>
                <a style="font-size: 35px; color: red;" href="http://tanhit.com/product/%D0%BC%D0%BE%D1%81%D0%BA%D0%B2%D0%B0-%D0%BC%D0%B0%D1%80%D0%B0%D1%84%D0%BE%D0%BD-%D0%BA%D0%B0%D0%BB%D0%B8-%D0%BC%D0%B5%D0%B4%D0%B8%D1%82%D0%B0%D1%86%D0%B8%D0%B8">Ссылка на `Москва, Марафон Кали медитации`</a>
                </div>
 */?>
                <br>
                <br>
				<div>Страница не найдена</div>
				<br>
                <p>
                    Если Вы были зарегистрированы на старой версии сайта и/или получали наши рассылки, то скорее всего Ваш профиль уже зарегистрирован - попробуйте
                    восстановить пароль по <a href="/wp-login.php?action=lostpassword">этой ссылке</a>.
                    <br>
                    <br>
                    Список доступных вебинаров Вы можете увидеть по ссылке в меню "<a href="/архив-вебинаров-и-практик">Вебинары</a>"

                </p>
            <?php/* endif; */?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
