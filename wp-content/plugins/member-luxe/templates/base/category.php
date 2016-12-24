<?php
include_once('header.php');

wpm_enqueue_style('homework_response_css', plugins_url('../../css/homework-response.css', __FILE__));

$current_user = wp_get_current_user();
$user_keys = get_user_meta($current_user->ID, 'user_key', true);

$cat_id = get_queried_object()->term_id;
$is_autotraining = wpm_is_autotraining($cat_id);
$date_is_hidden = wpm_date_is_hidden($main_options);

if ($is_autotraining) {
    $user_cat_data = wpm_user_cat_data($cat_id, $current_user->ID);
}

//---------
$wpm_head_code = $wpm_body_code = $wpm_footer_code = '';

if (is_single()) {
    $page_meta = get_post_meta($post->ID, '_wpm_page_meta', true);

    $wpm_head_code = stripcslashes(wpm_prepare_val($page_meta['code']['head']));
    $wpm_body_code = stripcslashes(wpm_prepare_val($page_meta['code']['body']));
    $wpm_footer_code = stripcslashes(wpm_prepare_val($page_meta['code']['footer']));
}
//----------

if (post_password_required()) { // Show this content if page is protected ?>
    <div class="wpm-protected">
        <?php echo get_the_password_form(); ?>
    </div>
<?php } else { // ?>
    <?php if (is_user_logged_in() || ($main_options['main']['opened'] && !$is_autotraining)) {
        ?>
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
                <div class="col-md-9 content-col">
                    <script>
                        var wpLinkL10n = {
                            "title": "\u0412\u0441\u0442\u0430\u0432\u0438\u0442\u044c\/\u0438\u0437\u043c\u0435\u043d\u0438\u0442\u044c \u0441\u0441\u044b\u043b\u043a\u0443",
                            "update": "\u041e\u0431\u043d\u043e\u0432\u0438\u0442\u044c",
                            "save": "\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u0441\u0441\u044b\u043b\u043a\u0443",
                            "noTitle": "(\u0431\u0435\u0437 \u043d\u0430\u0437\u0432\u0430\u043d\u0438\u044f)",
                            "noMatchesFound": "\u0421\u043e\u0432\u043f\u0430\u0434\u0435\u043d\u0438\u0439 \u043d\u0435 \u043d\u0430\u0439\u0434\u0435\u043d\u043e."
                        };
                        function getUserSetting(name, def) {
                            if (typeof def != 'undefined')
                                return def;

                            return '';
                        }
                        $.widget("ui.dialog", $.ui.dialog, {
                            _allowInteraction: function (event) {
                                return !!$(event.target).closest(".mce-container").length || this._super(event);
                            }
                        });

                        jQuery(document).on('focusin', function (e) {
                            if (jQuery(e.target).closest("#wp-link-wrap").length) {
                                e.stopImmediatePropagation();
                                return false;
                            }
                        });

                        function UpdateComments(post_id) {
                            $.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: {
                                    'action': 'wpm_the_comments_action',
                                    'id': post_id
                                },
                                success: function (data) {
                                    if (data != 'no-comments') {
                                        $('.ajax-content').append('<div class="wpm-comments-wrap">' + data + '</div>');
                                    }

                                }
                            });
                        }

                    </script>
                    <?php
                    $main_options = get_option('wpm_main_options');
                    ?>
                    <script type="text/javascript">
                        function reloadPage(id, slug) {
                            slug = slug||null;
                            $('.ajax-content').html('');
                            $('.wpm-content-inner-wrap').css({'display': 'none'});
                            $('.loader').css({
                                'height': $('.wpm-content-inner-wrap').outerHeight() + 'px',
                                'position': 'relative'
                            }).fadeIn(300, function () {
                                $.ajax({
                                    type: 'POST',
                                    url: ajaxurl,
                                    data: {
                                        'action': 'wpm_get_page_action',
                                        'id': id
                                    },
                                    success : function (data) {
                                        var ajaxHolder = $('.ajax-content');
                                        ajaxHolder.html(data);
                                        if (slug) {
                                            location.hash = slug;
                                        }
                                        $('.loader').animate({'height' : ajaxHolder.outerHeight() + 'px'}, 200, function () {
                                            $('.loader').fadeOut(300, function () {
                                                ajaxHolder.css({'display' : 'block'});
                                                setTimeout(function () {
                                                    updateWPMGallery(ajaxHolder);
                                                    if(typeof initFileUpload != 'undefined') {
                                                        initFileUpload();
                                                    }
                                                }, 0);
                                            });
                                            UpdateComments(id);

                                            $('.back-button').click(function (e) {
                                                ajaxHolder.css({'display' : 'none'}).html('');
                                                $('.loader').fadeIn(300, function () {
                                                    $('.loader').animate({'height' : $('.wpm-content-inner-wrap').outerHeight() + 'px'}, 200, function () {
                                                        $('.wpm-content-inner-wrap').css({'display' : 'block'});
                                                        $('.loader').hide();
                                                    });
                                                });
                                                location.hash = '';
                                                if(history.pushState) {
                                                    history.pushState(null, null, ' ');
                                                }
                                                $('body>.mce-inline-toolbar-grp, body>#wp-link-backdrop, body>.mce-widget, body>#wp-link-wrap').remove();
                                            });
                                        });
                                    }
                                });
                            });
                        }

                        function updateWPMGallery(holder) {
                            holder.find('.wpm-gallery-slider').each(function () {
                                var $this = $(this),
                                    $instance = $this.data('owlCarousel');
                                if ($instance) {
                                    $instance.e._onResize();
                                    $this.trigger('initialized.owl.carousel');
                                } else {
                                    $this.owlCarousel({loop:true,margin:10,nav:true,items:1,autoHeight:true,navText:["",""]});
                                }
                            });
                        }

                        jQuery(function ($) {
                            var hash = location.hash.replace('#', '').replace(' ', '');

                            if (hash && hash != '') {
                                var currItem = $('.post-row[data-slug="' + hash + '"]');
                                if (currItem.length) {
                                    reloadPage(currItem.attr('data-id'), hash);
                                }
                            }

                            $('.post-row').click(function (e) {
                                var item = $(this);
                                reloadPage(item.attr('data-id'), item.data('slug'));
                            });
                        });
                    </script>


                    <div class="wpm-content-wrap">
                        <div class="loader">
                            <div id="preloader_1">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>

                            <p class="loader-text"><?php _e('Загрузка...', 'wpm'); ?></p>
                        </div>
                        <div class="ajax-content">
                        </div>
                        <div class="wpm-content-inner-wrap">
                            <?php
                            $term_desc = term_description();
                            if (!empty($term_desc)) { ?>
                                <div class="wpm-content term-desc wpm-content-text">
                                    <?php echo term_description(); ?>
                                </div>
                            <?php } ?>
                            <div class="wpm-content-inner">
                                <div class="wpm-posts">
                                    <?php if (!wpm_hide_materials($cat_id)) : ?>
                                    <div class="table-head <?php echo empty($term_desc) ? 'rounded' : ''; ?>">
                                        <div class="col-title col-lg-4 col-md-12 col-sm-12 col-xs-12">
                                    <span class="text-wrap"><?php _e('Название', 'wpm') ?><span
                                            class="icon-pencil wpm-icon"></span></span>
                                        </div>
                                        <div
                                            class="col-description <?php echo $date_is_hidden ? 'col-lg-6 col-md-12' : 'col-lg-4 col-md-12'; ?> col-sm-12 col-xs-12">
                                    <span class="text-wrap"><?php _e('Краткое описание', 'wpm') ?>
                                        <span class="icon-file wpm-icon"></span></span>
                                        </div>
                                        <?php if (!$date_is_hidden): ?>
                                            <div class="col-date col-lg-2 col-md-2 col-sm-6 col-xs-6">
                                            <span class="text-wrap"><?php _e('Дата', 'wpm') ?><span
                                                    class="icon-calendar2 wpm-icon"></span></span>
                                            </div>
                                        <?php endif; ?>

                                        <div class="col-button col-lg-2 col-md-2 col-sm-6 col-xs-6">
                                <span class="text-wrap"><?php _e('Контент', 'wpm') ?><span
                                        class="icon-eye wpm-icon"></span></span>
                                        </div>
                                    </div>

                                    <?php
                                    global $wp_query;
                                    if (!empty($wp_query->posts)) {
                                        $chain = array();
                                        foreach ($wp_query->posts as $item) {
                                            array_push($chain, $item->ID);
                                        }
                                    }
                                    $viewComposer = new AutoTrainingView($cat_id);

                                    if (have_posts()):
                                        while (have_posts()): the_post();
                                            $viewComposer->iterate();

                                            if (!$viewComposer->showPost() && !$viewComposer->showAll()) {
                                                continue;
                                            }
                                            ?>

                                            <?php include(__DIR__ . '/autotraining-row.php'); ?>

                                            <?php $viewComposer->updatePostIterator(); ?>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="post">
                                            <div class="no-posts">
                                                <p>
                                                    <?php _e('Нет материалов', 'wpm') ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (get_previous_posts_link() || get_next_posts_link() || $viewComposer->hasNextPosts()) { ?>
                                    <div>
                                        <div>
                                            <div class="navi-row">
                                                <div
                                                    class="pull-left prev"><?php previous_posts_link('&laquo; Предыдущая страница') ?></div>
                                                <?php if ($viewComposer->hasNextPosts()) : ?>
                                                    <div
                                                        class="pull-right next"><?php next_posts_link('Следующая страница &raquo;', '') ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if (is_array($main_options['footer']) && array_key_exists('visible', $main_options['footer']) && $main_options['footer']['visible'] == 'on') { ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 footer-wrap">
                        <div class="footer-content wpm-content-text">
                            <?php
                            add_filter('the_content', 'wpm_add_infoprotector_key_to_url');
                            echo apply_filters('the_content', $main_options['footer']['content']); ?>
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
<?php echo get_option('coach_analytics'); ?>
<!--
<?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds.
 -->
</body>
</html>