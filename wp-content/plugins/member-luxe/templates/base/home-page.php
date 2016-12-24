<?php
include_once('header.php');

if (post_password_required()) { // Show this content if page is protected ?>
    <div class="wpm-protected">
        <?php echo get_the_password_form(); ?>
    </div>
<?php } else { // ?>
    <?php if (is_user_logged_in() || $main_options['main']['opened']) { ?>
        <div class="wpm-nav-bar-wrap wpm-top-admin-bar">
            <div class="wpm-nav-bar hidden-xs">
                <?php include_once('top-nav-bar.php'); ?>
            </div>
            <div class="visible-xs">
                <?php include_once('top-nav-bar-mobile.php'); ?>
            </div>
        </div>
        <div class="header">
            <?php
            $logo_url = $main_options['logo']['url'];
            if (!empty($logo_url)) {
                echo "<img class='logo' title='' src='$logo_url'>";
            }
            ?>
        </div>
        <div class="container container-content-wrap">

            <?php include_once('parts/page-header.inc.php'); ?>
            <div class="row">
                <div class="col-md-3 sidebar-col">
                    <?php include_once('sidebar.php'); ?>
                </div>
                <div class="col-md-9 content-col wpm-single">
                    <div id="wpm-content">

                        <?php if (have_posts()): while (have_posts()): the_post();
                            $term_list = wp_get_post_terms(get_the_ID(), 'wpm-levels', array("fields" => "all"));
                            ?>
                            <?php if (wpm_text_protection_is_enabled($main_options, get_the_ID())) : ?>
                                <style type="text/css">
                                    #wpm-content-<?php echo get_the_ID();?> {
                                        -webkit-user-select: none;
                                        -moz-user-select: -moz-none;
                                        -ms-user-select: none;
                                        user-select: none;
                                    }
                                </style>
                                <script>
                                    $("#wpm-content-<?php echo get_the_ID();?>").on("contextmenu", function (event) {
                                        event.preventDefault();
                                    });
                                </script>
                            <?php endif; ?>
                            <div class="wpm-content wpm-content-text" id="wpm-content-<?php echo get_the_ID();?>" style="margin-top: 0">
                                <?php
                                remove_all_actions('the_content');
                                remove_all_filters('the_content');
                                add_filter('the_content', 'wpautop');
                                add_filter('the_content', 'do_shortcode');
                                the_content();
                                ?>
                            </div>
                        <?php endwhile; endif; ?>
                    </div>

                </div>
            </div>
            <?php if ($main_options['footer']['visible'] == 'on') { ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 footer-wrap">
                        <div class="footer-content wpm-content-text">
                            <?php echo apply_filters('the_content', $main_options['footer']['content']); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

    <?php
    } else {
        include_once('not-logined.php');
    }
} // end if(post_password_required())
?>
<?php include_once('footer.php'); ?>
<?php wpm_footer(); ?>
<!-- wpm footer code -->
<?php echo $wpm_footer_code; ?>
<!-- / wpm footer code -->
<?php echo get_option('wpm-analytics'); ?>
<!--
<?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds.
 -->
</body>
</html>