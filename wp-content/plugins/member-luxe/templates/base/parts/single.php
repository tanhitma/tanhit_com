<?php

$page_meta = get_post_meta(get_the_ID(), '_wpm_page_meta', true);
$user_id = get_current_user_id();

if (!empty($page_meta)) $descriptions = $page_meta['description'];
else $descriptions = '';

remove_all_actions('the_content');
remove_all_filters('the_content');
add_filter('the_content', 'wpautop');
add_filter('the_content', 'do_shortcode');

$accessible_levels = wpm_get_all_user_accesible_levels($user_id);

$has_access = wpm_check_access(get_the_ID(), $accessible_levels);

$main_options = get_option('wpm_main_options');
$date_is_hidden = wpm_date_is_hidden($main_options);

$terms = wp_get_post_terms(get_the_ID(), 'wpm-category', array('fields' => 'all'));

if(is_array($terms) && !empty($terms)){
    $term_link = get_term_link($terms[0], 'wpm-category');
}else{
    $term_link = '';
}



if ($has_access) {

    if (wpm_text_protection_is_enabled($main_options, get_the_ID())) {
        ?>
        <style type="text/css">
            .wpm-content {
                -webkit-user-select: none;
                -moz-user-select: -moz-none;
                -ms-user-select: none;
                user-select: none;
            }
        </style>
        <script>
            $(".wpm-content").on("contextmenu", function (event) {
                event.preventDefault();
            });
        </script>
    <?php } ?>
    <div class="wpm-page-header-wrap">
        <div class="wpm-page-header">
            <div class="info-row row">
                <div class="col-label col-lg-3 col-md-4 col-sm-4 col-xs-4">
                    <span class="icon-pencil"></span>
                    <?php _e("Название", "wpm"); ?>
                </div>
                <div class="col-info col-lg-9 col-md-8 col-sm-8 col-xs-8">
                    <h1 class="title">
                        <?php the_title(); ?>
                    </h1>
                </div>
            </div>
            <?php if (!empty($descriptions)){ ?>
                <div class="info-row row">
                    <div class="col-label col-lg-3 col-md-4 col-sm-4 col-xs-4">
                        <span class="icon-file"></span>
                        <?php _e("Краткое описание", "wpm"); ?>
                    </div>
                    <div class="col-info col-lg-9 col-md-8 col-sm-8 col-xs-8">
                        <div class="description">
                            <?php echo $descriptions; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (!$date_is_hidden){ ?>
                <div class="info-row row">
                    <div class="col-label col-lg-3 col-md-4 col-sm-4 col-xs-4">
                        <span class="icon-calendar2"></span> <?php _e("Дата", "wpm"); ?>
                    </div>
                    <div class="col-info col-lg-9 col-md-8 col-sm-8 col-xs-8">
                        <div class="description col-lg-6 col-md-5 col-sm-5 col-xs-12">
                            <span class="date"><?php echo get_the_date(); ?></span>
                        </div>
                        <div class="col-lg-6 col-md-7 col-sm-7 col-xs-12">
                            <?php if(!empty($term_link)){ ?>
                            <?php $design_options = get_option('wpm_design_options'); ?>
                            <?php $back_btn_text = array_key_exists('text', $design_options['buttons']['back']) ? $design_options['buttons']['back']['text'] : 'Вернуться к списку материалов'; ?>
                            <a class="wpm-button back-button pull-right"
                               href="<?php echo esc_url($term_link); ?>"><?php _e($back_btn_text, "wpm"); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php }else{ ?>

                <div class="info-row row">
                    <div class="col-label col-xs-12">
                        <?php $design_options = get_option('wpm_design_options'); ?>
                        <?php $back_btn_text = array_key_exists('text', $design_options['buttons']['back']) ? $design_options['buttons']['back']['text'] : 'Вернуться к списку материалов'; ?>
                        <a class="wpm-button back-button pull-right"
                           href="<?php echo esc_url($term_link); ?>"><?php _e($back_btn_text, "wpm"); ?></a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="wpm-content wpm-content-text">
        <?php the_content();
        echo get_interkassa_form($page_meta);
        ?>
    </div>
    <?php
    $has_homework = wpm_has_homework($page_meta);
    if ($has_homework) {
        $data = wpm_get_responses(get_current_user_id(), get_the_ID(), $page_meta);
        ?>
        <div class="homework-wrap wpm-content wpm-content-text">
            <h3 class="homework-title">Задание
                <?php if (!in_array($data['status'], array('accepted', 'approved'))): ?>
                    <a class="link pull-right response-link wpm-button"
                       href="#response"
                       data-toggle="modal"
                       data-target="#response_modal">
                        <?php if (empty($data['status'])): ?>
                            <a class="link pull-right response-link wpm-button wpm-homework-respond-button"
                               href="#response"
                               data-toggle="modal"
                               data-target="#response_modal">
                                <?php echo $design_options['buttons']['home_work_respond_on_page']['text']; ?>
                            </a>
                        <?php else: ?>
                            <a class="link pull-right response-link wpm-button wpm-homework-edit-button"
                               href="#response"
                               data-toggle="modal"
                               data-target="#response_modal">
                                <?php echo $design_options['buttons']['home_work_edit']['text']; ?>
                            </a>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </h3>

            <?php echo wpautop($page_meta['homework_description']); ?>

            <div class="response">
                <div id="homework-response-wrapper"
                     class="<?php echo empty($data['content']) ? 'hidden' : ''; ?>">
                    <h3>
                        Ваш ответ
                        <?php if (!in_array($data['status'], array('accepted', 'approved'))): ?>
                            <a class="link pull-right response-link wpm-button wpm-homework-edit-button"
                               href="#response"
                               data-toggle="modal"
                               data-target="#response_modal"><?php echo $design_options['buttons']['home_work_edit']['text']; ?></a>
                        <?php endif; ?>
                    </h3>

                    <div class="response-body">
                        <div class="response-header">
                            <div class="response-title">

                                <i class="clocks"></i>

                                <span class="response-date" id="response-date"><?php echo $data['date']; ?></span>

                                        <span class="response-status <?php echo $data['status']; ?>">
                                            <i class="status-icon"></i><span><?php echo $data['status_msg']; ?></span>
                                        </span>

                            </div>
                        </div>

                                <span id="response_content" class="response_content">
                                    <?php echo apply_filters('the_content', stripslashes($data['content'])); ?>
                                </span>

                        <div class="response-reviews">
                            <div class="response-title <?php echo (count($data['reviews'])) ? '' : 'hidden'; ?>">
                                <i></i>Комментарии к ответу:
                            </div>
                            <div id="response-reviews">
                                <?php if (count($data['reviews'])) : ?>
                                    <?php foreach ($data['reviews'] as $review): ?>
                                        <div class="response-review">
                                                    <span class="response-date">
                                                        <?php echo $review['date']; ?>
                                                    </span>
                                            <?php echo wpautop(stripslashes($review['content'])); ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="response_modal" tabindex="-1" role="dialog"
                     aria-labelledby="response_label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button"
                                        class="close"
                                        data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                    <span class="sr-only">Закрыть</span>
                                </button>
                                <h4 class="modal-title" id="activation_label">
                                    <?php if (empty($data['status'])): ?>
                                        Ответить на задание
                                    <?php else: ?>
                                        Редактировать задание
                                    <?php endif; ?>
                                </h4>
                            </div>
                            <div class="modal-body">
                                <form class="homework-respnose-form" page-id="<?php the_ID(); ?>">
                                    <div>
                                        <?php wp_editor($data['content'], 'response-content' . get_the_ID(), $wppage_tinymce_options); ?>
                                    </div>
                                    <p>
                                        <?php if (empty($data['status'])): ?>
                                            <input type="submit" class="button wpm-respond-popup-button" value="<?php echo $design_options['buttons']['home_work_respond_on_popup']['text']; ?>">
                                        <?php else: ?>
                                            <input type="submit" class="button wpm-homework-edit-popup-button" value="<?php echo $design_options['buttons']['home_work_edit_on_popup']['text']; ?>">
                                        <?php endif; ?>
                                    </p><br>

                                    <div class="response-result"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php _WP_Editors::editor_js() ?>

                <script type="text/javascript">
                    function reviews(homework) {
                        if (jQuery.isEmptyObject(homework['reviews']) === false) {
                            $('#response-reviews').html('');
                            $.each(homework['reviews'], function (key, value) {
                                var review = '<div class="response-review">' +
                                    '<span class="response-date">' + value['date'] + '</span><br/>' +
                                    value['content'] +
                                    '</div>';
                                $('#response-reviews').append(review);
                            });
                        }
                    }

                    function homework(homework) {
                        if (jQuery.isEmptyObject(homework) === false) {
                            $('#response-date').html(homework['date']);
                            $('#response_content').html(homework['content']);
                            $('.response-status span').html(homework['status_msg']);
                            $('.response-status').attr('class', 'response-status ' + homework['status']);
                            $('#homework-response-wrapper').removeClass('hidden');

                            if (homework['status'] == 'approved' || homework['status'] == 'accepted') {
                                $('.response-link').hide();
                            } else {
                                $('.response-link').html('Редактировать');
                                $('#activation_label').html('Редактировать задание');
                            }
                            reviews(homework);
                        }
                    }

                    jQuery(function ($) {
                        $('.response-link').data('edit', 'yes');
                        var response = $('form.homework-respnose-form');
                        var result = $('.response-result');
                        result.hide().html('');

                        response.on('submit', function (e) {
                            result.hide().html('');
                            $.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                dataType: 'json',
                                data: {
                                    'action': 'wpm_add_response_action',
                                    'post_id': "<?php the_ID(); ?>",
                                    'response_content': $('.response-text').val(),
                                    'response_type': "<?php echo $page_meta['homework']['checking_type'] ?>"
                                },
                                success: function (data) {
                                    if (data.error) {
                                        result.html('<p class="alert alert-warning">' + data.message + '</p>').show();
                                    } else {
                                        result.html('<p class="alert alert-success">' + data.message + '</p>').show();
                                        homework(data.homework);
                                        setTimeout(function () {
                                            $('#response_modal').find('.close').click();
                                            result.hide().html('');
                                        }, 1000);
                                    }
                                }
                            });

                            e.preventDefault();
                        });
                    });
                </script>
            </div>
        </div>
    <?php } ?>
    <?php

} else {
    //echo 'no-access';
    $term_list = wp_get_post_terms(get_the_ID(), 'wpm-levels', array("fields" => "ids"));

    print_r($term_list);
    $taxonomy_term_metas = array();

    foreach ($term_list AS $_term_id) {
        $_taxonomy_term_meta = get_option("taxonomy_term_$_term_id");
        if ($_taxonomy_term_meta && !empty($_taxonomy_term_meta['no_access_content'])) {
            $taxonomy_term_metas[$_term_id] = $_taxonomy_term_meta;
        }
    }

    $no_access_content = '';

    if (count($taxonomy_term_metas) > 1) {
        $no_access_content .= '<h2 class="accordion-title">Материал доступен для следующих тарифных планов:</h2>';
        $no_access_content .= '<div id="no-access-content">';

        foreach ($taxonomy_term_metas as $term_id => $taxonomy_term_meta) {

            $term = get_term($term_id, 'wpm-levels');
            if (empty($taxonomy_term_meta['no_access_content'])) $no_access_text = '<p>У вас нет доступа к этому материалу.</p>';
            else $no_access_text = stripslashes($taxonomy_term_meta['no_access_content']);
            $no_access_content .= '<div data-term-id="' . $term_id . '">' .
                '<h3>' . $term->name . '</h3>' .
                '<div class="term-content">' . $no_access_text . '</div>' .
                '</div>';
        }

        $no_access_content .= '</div>';

    } else {
        foreach ($taxonomy_term_metas as $term_id => $taxonomy_term_meta) {
            $taxonomy_term_meta = get_option("taxonomy_term_$term_id");
            $no_access_content .= stripslashes($taxonomy_term_meta['no_access_content']);
        }
    }

    ?>
    <div class="wpm-page-header-wrap">
        <div class="wpm-page-header">
            <div class="info-row row">
                <div class="col-label col-lg-3 col-md-4 col-sm-4 col-xs-4">
                    <span class="icon-pencil"></span>
                    <?php _e("Название", "wpm"); ?>
                </div>
                <div class="col-info col-lg-9 col-md-8 col-sm-8 col-xs-8">
                    <h1 class="title">
                        <?php the_title(); ?>
                    </h1>
                </div>
            </div>
            <div class="info-row row">
                <div class="col-label col-lg-3 col-md-4 col-sm-4 col-xs-4">
                    <span class="icon-file"></span>
                    <?php _e("Краткое описание", "wpm"); ?>
                </div>
                <div class="col-info col-lg-9 col-md-8 col-sm-8 col-xs-8">
                    <div class="description">
                        <?php echo $descriptions; ?>
                    </div>
                </div>
            </div>
            <div class="info-row row">
                <div class="col-label col-lg-3 col-md-4 col-sm-4 col-xs-4">
                    <span class="icon-calendar2"></span> <?php _e("Дата", "wpm"); ?>
                </div>
                <div class="col-info col-lg-9 col-md-8 col-sm-8 col-xs-8">
                    <div class="description col-lg-6 col-md-5 col-sm-5 col-xs-12">
                        <span class="date"><?php echo get_the_date(); ?></span>
                    </div>
                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-12">
                        <?php $design_options = get_option('wpm_design_options'); ?>
                        <?php $back_btn_text = array_key_exists('text', $design_options['buttons']['back']) ? $design_options['buttons']['back']['text'] : 'Вернуться к списку материалов'; ?>
                        <button class="wpm-button back-button pull-right"><?php _e($back_btn_text, "wpm"); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wpm-content no-access-content wpm-content-text">
        <div class="post">
            <div class="ps_content ">
                <?php
                echo apply_filters('the_content', $no_access_content);
                ?>

                <?php if (count($taxonomy_term_metas) > 1): ?>
                    <script>
                        $(function () {
                            setTimeout(function () {
                                $('[data-term-id]').each(function () {
                                    var elem_id = $(this).attr('data-term-id');
                                    var content = $('[data-term-id="' + elem_id + '"] .term-content').addClass('evaluate');
                                    content.removeClass('evaluate');
                                });
                            }, 2000);
                            $(document).off('click', '[data-term-id] h3');
                            $(document).on('click', '[data-term-id] h3', function () {
                                var header = $(this);
                                var term_item = header.parents('[data-term-id]');
                                var term_id = term_item.attr('data-term-id');
                                var term_content = $('[data-term-id="' + term_id + '"] .term-content');

                                if (term_item.hasClass('active')) {
                                    term_item.removeClass('active');
                                    term_content.slideUp();
                                } else {
                                    $('[data-term-id]').each(function () {
                                        var inactive_item = $(this);
                                        var inactive_term_id = inactive_item.attr('data-term-id');
                                        var inactive_content = inactive_item.find('.term-content');

                                        if (inactive_term_id !== term_id && inactive_item.hasClass('active')) {
                                            inactive_item.removeClass('active');
                                            inactive_content.slideUp();
                                        }
                                    });

                                    term_item.addClass('active')
                                    term_content.slideDown();
                                }

                            });
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}