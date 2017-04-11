<?php
/**
 * The header for our theme.
 *
 * @package tanhit
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=720">
    <meta name="description" content="">
    <link rel="icon" href="/wp-content/uploads/2017/02/cropped-logo_znak.png" type="image/png" />
    <link rel="shortcut icon" href="/wp-content/uploads/2017/02/cropped-logo_znak.png" type="image/png" />
	
    <link href='https://code.cdn.mozilla.net/fonts/fira.css' rel='stylesheet' type='text/css' />
    <title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
    <?php wp_head(); ?>

	<script>        
		(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,"script","//www.google-analytics.com/analytics.js","ga");
		ga("create", "UA-76367746-1", "auto");
		ga("send", "pageview");
		//	 Подгружаем плагин отслеживания электронной коммерции
	</script>
	
	<style>
		/* video borders */
		.video_wrap{
			display: block;
			margin: 10px auto;
			text-align: center;
			max-width: 100%;
			background-size: cover;
		}
		.video_wrap object, .video_wrap iframe{
			margin: 0!important;
			padding: 0!important;
			border: none!important;
			display: block;
		}
		.video_wrap video {
			background: #000;
			display: block;
		}
		.video_normal_normal object, .video_normal_normal iframe, .video_normal_normal>div{
			margin: 0 auto!important;
		}

		.video_normal_normal .jwplayer{
			margin: 0 auto;
		}
		.v_box video{
			max-width: 100%;
		}
		.no-style .v_box video{
			margin: 0 auto;
		}

		.video_normal_normal,
		.video_margin_center{
			text-align: center;
		}
		.video_margin_center object, .video_margin_center iframe{
			margin: 0 auto!important;
		}
		.wpm-video-size-wrap{
			margin: 30px auto;
		}
		.style-video{
			background-size: 100%;
			background-repeat: no-repeat;
			margin: 20px auto;
			overflow: hidden;
			position: relative;
		}
		.style-video iframe{

		}

		.style-1{
			background-image: url("http://static.wppage.ru/wppage/i/video/1/720x405.png");
			padding: 2.4% 2.4% 6% 2.4%;
		}
		.style-2{
			background-image: url("http://static.wppage.ru/wppage/i/video/2/720x405.png");
			padding: 0 5.0% 7% 5.2%;
		}
		.style-3{
			background-image: url("http://static.wppage.ru/wppage/i/video/3/720x405.png");
			padding: 0 5% 11% 5.7%;
		}
		.style-4{
			background-image: url("http://static.wppage.ru/wppage/i/video/4/720x405.png");
			padding: 2.7% 2.6% 6% 2.7%;
		}
		.style-5{
			background-image: url("http://static.wppage.ru/wppage/i/video/5/720x405.png");
			padding: 2.6% 7.7% 10% 7.8%;
		}
		.style-6{
			background-image: url("http://static.wppage.ru/wppage/i/video/6/720x405.png");
			padding: 3.1% 3.2% 4% 3.2%;
		}
		.style-7{
			background-image: url("http://static.wppage.ru/wppage/i/video/7/720x405.png");
			padding: 5.96% 3.1% 6% 3.0%;
		}
		.style-8{
			background-image: url("http://static.wppage.ru/wppage/i/video/8/720x405.png");
			padding: 5.7% 5.6% 14% 5.6%;
		}
		.style-9{
			background-image: url("http://static.wppage.ru/wppage/i/video/9/720x405.png");
			padding: 5.5% 10% 4.4% 8.3%;
		}
		.style-10{
			background-image: url("http://static.wppage.ru/wppage/i/video/10/720x405.png");
			padding: 0 4.6% 9% 4.3%;
		}
	</style>

  <link href="/wp-content/themes/tanhit/js/videojs/video-js.min.css" rel="stylesheet">
  <script type="text/javascript" src="/wp-content/themes/tanhit/js/videojs/video.min.js"></script>
  <script type="text/javascript" src="/wp-content/themes/tanhit/js/videojs/youtube.min.js"></script>
	<script type="text/javascript" src="/wp-content/themes/tanhit/js/videojs/video_init.js"></script>
</head>
<body <?php body_class(); ?>>
<div class="bgout"><div class="bg"></div></div>
<header>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="header upper">
                    <div class="row">
                        <div class="col-xs-6">
                            <a href="/"><img alt="" src="/wp-includes/img/logo_small.png"></a>
                            <span class="lbl"><?php pll_e('ВОЗЛЮБЛЕННАЯ', 'tanhit'); ?></span>
                        </div>
                        <div class="col-xs-6 align-right lang">
                            <a href="/%d0%ba%d0%be%d0%bd%d1%82%d0%b0%d0%ba%d1%82%d1%8b" class="up-contact"><span class="glyphicon glyphicon-envelope"></span> <?php pll_e('контакты', 'tanhit'); ?></a>
                            <a href="/my-account" class="up-login"><span class="glyphicon glyphicon-log-in"></span> <?php
                                if ( is_user_logged_in() ) {
                                    pll_e('личный кабинет', 'tanhit');
                                } else {
                                    pll_e('вход / регистрация', 'tanhit');
                                }
                                ?>
                            </a>
                            <?php if ( is_user_logged_in() ) { ?>
                              <a href="<?= wc_customer_edit_account_url(); ?>" class="up-login"><span class="glyphicon glyphicon-user"></span><?= pll_e('профиль', 'tanhit'); ?></a>
                                <span class="up-logout">/
                                    <a href="/my-account/customer-logout/"><?php
                                        pll_e('выход', 'tanhit');
                                    ?></a>
                                </span>
                                <?php
                            }
                            ?>

                            <?php /* <a href="/" class="lang-ru"></a> */ ?>
                            <a href="/#googtrans(en|ru)" class="lang-ru"></a>
                            <?php /* <a href="/en/" class="lang-en"></a> */ ?>
                            <a href="/#googtrans(ru|en)" class="lang-en"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="header logo">
                    <div class="logo-text"><?php pll_e('Жить Вознесённо в каждом мгновении, в каждом вдохе, в каждом шаге.'); ?></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="header menu">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-main-navbar-collapse-1">
                            <span class="glyphicon glyphicon-align-justify"></span>&nbsp;<b>МЕНЮ</b>
                        </button>
                    </div>
                    <?php
                    wp_nav_menu( array(
                            'menu'              => 'primary',
                            'theme_location'    => 'primary',
                            'depth'             => 0,
                            'container'         => 'div',
                            'container_class'   => 'collapse navbar-collapse',
                            'container_id'      => 'bs-main-navbar-collapse-1',
                            'menu_class'        => 'nav nav-justified',
                            'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
                            'walker'            => new wp_bootstrap_navwalker())
                    );
                    ?>
                </div>
				<?php 
				/**
				 * @todo check woocommerce page https://faish.al/2014/01/06/check-if-it-is-woocommerce-page/
                 * Search form
				 */
				/* include_once( 'searchform.php' ); */ ?>
            </div>
        </div>
    </div>
</header>