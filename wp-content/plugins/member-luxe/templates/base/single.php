<?php

global $post;

wpm_redirect_filter();
include_once('header.php');

$main_options = get_option('wpm_main_options');
$design_options = get_option('wpm_design_options');

$page_meta = get_post_meta($post->ID, '_wpm_page_meta', true);

if (isset($page_meta['code'])) {
    $wpm_head_code = stripcslashes(wpm_prepare_val($page_meta['code']['head']));
    $wpm_body_code = stripcslashes(wpm_prepare_val($page_meta['code']['body']));
    $wpm_footer_code = stripcslashes(wpm_prepare_val($page_meta['code']['footer']));
} else {
    $wpm_head_code = $wpm_body_code = $wpm_footer_code = '';
}

wpm_enqueue_style('homework_response_css', plugins_url('../../css/homework-response.css', __FILE__));

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$user_keys = get_user_meta($current_user->ID, 'user_key', true);
$has_access = false;

$cat_id = get_queried_object()->term_id;
$is_autotraining = wpm_is_autotraining($cat_id);
$prev_post_id = null;
$prev_post_meta = null;
$is_postponed_due_to_homework = false;
$accessible_levels = wpm_get_all_user_accesible_levels($current_user->ID);
$date_is_hidden = wpm_date_is_hidden($main_options);

if ($is_autotraining) {
    $user_cat_data = wpm_user_cat_data($cat_id, $current_user->ID);
}
?>
<?php if (post_password_required()) { // Show this content if page is protected ?>
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
                        <?php

                        if (have_posts()): while (have_posts()): the_post();


                            if ($main_options['home_id'] != get_the_ID()) {
                                include_once(WP_PLUGIN_DIR . '/member-luxe/templates/base/parts/single.php');
                            } else {
                                include_once(WP_PLUGIN_DIR . '/member-luxe/templates/base/parts/home.php');
                            }

                        endwhile;
                        endif;
                        ?>
                    </div>
                    <?php if (comments_open() && (is_user_logged_in() || wpm_comments_is_visible())) { ?>
                        <div class="wpm-comments-wrap">
                            <?php wpm_comments_wordpress(get_the_ID()); ?>
                        </div>
                    <?php } ?>

                </div>
            </div>
            <?php if (isset($main_options['footer']['visible']) && $main_options['footer']['visible'] == 'on') { ?>
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