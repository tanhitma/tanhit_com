<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <link rel="icon" href="/wp-content/uploads/2015/03/657068.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="/wp-content/uploads/2015/03/657068.ico" type="image/x-icon" />
    <title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body>
<header>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="header upper">
                    <div class="row">
                        <div class="col-sm-10">
                            <a href="/"><img alt="" src="/wp-includes/img/logo_small.png"></a>
                            <span><?php pll_e('ВОЗЛЮБЛЕННАЯ'); ?></span>
                        </div>
                        <div class="col-sm-2 align-right lang">
                            <a href="/" class="lang-ru"></a>
                            <a href="/en/" class="lang-en"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="header logo">
                    <div class="logo-text"><?php pll_e('Жить Вознесённо в каждом мгновении, в каждом вдохе, в каждом шаге.'); ?></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="header menu">
                    <?php
                    wp_nav_menu( array(
                            'menu'              => 'primary',
                            'theme_location'    => 'primary',
                            'depth'             => 2,
                            'container'         => 'div',
                            'container_class'   => 'collapse navbar-collapse',
                            'container_id'      => 'bs-example-navbar-collapse-1',
                            'menu_class'        => 'nav navbar-nav',
                            'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
                            'walker'            => new wp_bootstrap_navwalker())
                    );
                    ?>
                </div>
            </div>
        </div>
    </div>
</header>