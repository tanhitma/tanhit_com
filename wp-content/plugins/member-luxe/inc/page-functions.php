<?php

function wpm_get_page($page_id)
{

    $user_id = get_current_user_id();
    $args = array(
        'post_type' => 'wpm-page',
        'page_id' => $page_id
    );
    wpm_enqueue_script('jquery-migrate', includes_url('js/jquery/jquery-migrate.js'));
    wpm_enqueue_script('wplink', includes_url('js/wplink.js'));
    did_action( 'init' ) && wp_localize_script( 'wplink', 'wpLinkL10n', array(
        'title' => __('Insert/edit link'),
        'update' => __('Update'),
        'save' => __('Add Link'),
        'noTitle' => __('(no title)'),
        'noMatchesFound' => __('No matches found.')
    ) );
    wpm_enqueue_script('wpdialogs', includes_url('js/wpdialog.js'));
    wpm_enqueue_style('editor_css', plugins_url('../css/editor-frontend.css', __FILE__));

    if(version_compare(get_bloginfo('version'), '3.9', '>=')) {
        $wppage_tinymce_options = array(
            'quicktags'     => false,
            'media_buttons' => false,
            'editor_height' => 200,
            'textarea_name' => 'response-content',
            'editor_class'  => 'response-text',
            'tinymce'       => array(
                'toolbar1' => 'bold italic underline strikethrough | forecolor backcolor | justifyleft justifycenter justifyright | bullist numlist outdent indent |removeformat | link unlink hr',
                'toolbar2' => false,
                'toolbar3' => false,
                'content_css' =>  ( plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                                        .', ' . plugins_url() . '/member-luxe/css/editor-frontend.css'
                                        .', ' . plugins_url() . '/member-luxe/css/bullets.css'
                                    )
            )
        );

    } else {
        $wppage_tinymce_options = array(
            'media_buttons' => false,
            'teeny'         => false,
            'quicktags'     => false,
            'textarea_rows' => 30,
            'textarea_name' => 'response-content',
            'editor_class'  => 'response-text',
            'content_css'   => '',
            'tinymce'       => array(
                'theme_advanced_buttons1'   => 'bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,outdent,indent,|,removeformat,|,link,unlink,hr',
                'theme_advanced_buttons2'   => '',
                'theme_advanced_buttons3'   => '',
                'theme_advanced_buttons4'   => '',
                'theme_advanced_font_sizes' => '10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,32px,42px,48px,52px',
                'content_css' =>  ( plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                                        .', ' . plugins_url() . '/member-luxe/css/editor-frontend.css'
                                        .', ' . plugins_url() . '/member-luxe/css/bullets.css'
                                    )
            )
        );
    }

    $page = new WP_Query($args);
    if ($page->have_posts()): while ($page->have_posts()):
        $page->the_post();


        $page_meta = get_post_meta(get_the_ID(), '_wpm_page_meta', true);

        if (!empty($page_meta)) $descriptions = $page_meta['description'];
        else $descriptions = '$descriptions';

        remove_all_actions('the_content');
        remove_all_filters('the_content');
        add_filter('the_content', 'wpautop');
        add_filter('the_content', 'do_shortcode');
        add_filter('the_content', 'wpm_add_infoprotector_key_to_url');

        $accessible_levels = wpm_get_all_user_accesible_levels($user_id);

        $has_access = wpm_check_access($page_id, $accessible_levels);

        $main_options = get_option('wpm_main_options');
        $date_is_hidden = wpm_date_is_hidden($main_options);

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
<!--             <div class="wpm-page-header-wrap">
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
--><!--					
                    <?php if(!empty($descriptions)): ?>
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
                    <?php endif; ?>
-->					
                    <?php if (!$date_is_hidden): ?>
                    <div class="info-row row">
                        <div class="col-label col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <span class="icon-calendar2"></span> <?php _e("Дата", "wpm"); ?>
                        </div>
                        <div class="col-info col-lg-9 col-md-8 col-sm-8 col-xs-8">
                            <div class="description col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                <span class="date"><?php echo get_the_date(); ?></span>
                            </div>
<!--							
                            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-12">
                                <?php $design_options = get_option('wpm_design_options');?>
                                <?php $back_btn_text = array_key_exists('text', $design_options['buttons']['back']) ? $design_options['buttons']['back']['text'] : 'Вернуться к списку материалов';?>
                                <button
                                    class="wpm-button back-button pull-right"><?php _e($back_btn_text, "wpm"); ?></button>
                            </div>
-->							
                        </div>
                    </div>
                    <?php else: ?>
                        <!--div class="info-row row">
                            <div class="col-label col-xs-12">
                                <?php $design_options = get_option('wpm_design_options');?>
                                <?php $back_btn_text = array_key_exists('text', $design_options['buttons']['back']) ? $design_options['buttons']['back']['text'] : 'Вернуться к списку материалов';?>
                                <button
                                    class="wpm-button back-button pull-right"><?php _e($back_btn_text, "wpm"); ?></button>
                            </div>
                        </div-->
                    <?php endif; ?>
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
                $attachments = UploadHandler::getHomeworkAttachmentsHtml(get_the_ID(), get_current_user_id());
                ?>
                <div class="homework-wrap wpm-content">

                    <h3 class="homework-title">Задание
                        <?php if (!in_array($data['status'], array('accepted', 'approved'))): ?>

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
                        <?php endif; ?>
                    </h3>

                    <div class="wpm-content-text"><?php echo wpautop($page_meta['homework_description']);?></div>

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
                                    <?php echo apply_filters('the_content', wpautop($data['content'])); ?>
                                    <?php echo $attachments; ?>
                                </span>

                                <div class="response-reviews">
                                    <div class="response-title <?php echo (count($data['reviews'])) ? '' : 'hidden'; ?>"><i></i>Комментарии к ответу:</div>
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
                                        <form class="homework-respnose-form" page-id="<?php the_ID(); ?>" enctype="multipart/form-data">
                                            <div>
                                                <?php wp_editor( wpautop($data['content']), 'response-content'.get_the_ID(), $wppage_tinymce_options ); ?>
                                            </div>
                                            <?php jquery_html5_file_upload_hook(); ?>
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
            $term_list = wp_get_post_terms(get_the_ID(), 'wpm-levels', array("fields" => "ids"));
            $taxonomy_term_metas = array();

            foreach ($term_list AS $_term_id) {
                $_taxonomy_term_meta = get_option("taxonomy_term_$_term_id");
                if($_taxonomy_term_meta && !empty($_taxonomy_term_meta['no_access_content'])) {
                    $taxonomy_term_metas[$_term_id] = $_taxonomy_term_meta;
                }
            }

            $no_access_content = '';

            if (count($taxonomy_term_metas) > 1) {
                $no_access_content .= '<h2 class="accordion-title">Данная страница доступна для слушателей:</h2>';
                $no_access_content .= '<div id="no-access-content">';

                foreach ($taxonomy_term_metas as $term_id => $taxonomy_term_meta) {

                    $term = get_term($term_id, 'wpm-levels');

                    $no_access_content .= '<div data-term-id="' . $term_id . '">' .
                                              '<h3>' . $term->name . '</h3>' .
                                              '<div class="term-content">'. stripslashes($taxonomy_term_meta['no_access_content']) . '</div>' .
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
 <!--             <div class="wpm-page-header-wrap">
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
-->	                    <!--div class="info-row row">
                        <div class="col-label col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <span class="icon-file"></span>
                            <?php _e("Краткое описание", "wpm"); ?>
                        </div>
                        <div class="col-info col-lg-9 col-md-8 col-sm-8 col-xs-8">
                            <div class="description">
                                <?php echo $descriptions; ?>
                            </div>
                        </div>
                    </div-->
                    <?php if (!$date_is_hidden): ?>
                    <div class="info-row row">
                        <div class="col-label col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <span class="icon-calendar2"></span> <?php _e("Дата", "wpm"); ?>
                        </div>
                        <div class="col-info col-lg-9 col-md-8 col-sm-8 col-xs-8">
                            <div class="description col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                <span class="date"><?php echo get_the_date(); ?></span>
                            </div>
<!--							
                            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-12">
                                <?php $design_options = get_option('wpm_design_options');?>
                                <?php $back_btn_text = array_key_exists('text', $design_options['buttons']['back']) ? $design_options['buttons']['back']['text'] : 'Вернуться к списку материалов';?>
                                <button
                                    class="wpm-button back-button pull-right"><?php _e($back_btn_text, "wpm"); ?></button>
                            </div>
-->							
                        </div>
                    </div>
                    <?php else: ?>
                        <!--div class="info-row row">
                            <div class="col-label col-xs-12">
                                <?php $design_options = get_option('wpm_design_options');?>
                                <?php $back_btn_text = array_key_exists('text', $design_options['buttons']['back']) ? $design_options['buttons']['back']['text'] : 'Вернуться к списку материалов';?>
                                <button
                                    class="wpm-button back-button pull-right"><?php _e($back_btn_text, "wpm"); ?></button>
                            </div>
                        </div-->
                    <?php endif; ?>
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
                                $(function() {
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

                                                if(inactive_term_id!==term_id && inactive_item.hasClass('active')) {
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
                        <?php endif;?>
                    </div>
                </div>
            </div>
        <?php
        }

    endwhile;
    endif;

}


function get_interkassa_form($page_meta)
{
    if(!is_page() || !is_single() || !is_array($page_meta['interkassa'])) return false;


    $product_title = array_key_exists('name',$page_meta['interkassa']);
    echo '$product_title = '.$product_title;
    $shop_id = $page_meta['interkassa']['id'];
    $price = $page_meta['interkassa']['price'];
    $currency = $page_meta['interkassa']['currency'];
    $desc = $page_meta['interkassa']['desc'];
    $payment_id = time() . (number_format((double)microtime(),4) * 10000);

    $interkassa_fields_id = $page_meta['interkassa']['show_fields'];
    $interkassa_fields = array(
        array('Ф.И.О' => 'Ваше имя'),
        array('Страна' => 'Ваша страна'),
        array('Город' => 'Ваш город'),
        array('Адрес' => 'Ваш адрес'),
        array('Индекс' => 'Ваш индекс'),
        array('Телефон' => 'Ваш телефон'),
        array('E-mail' => 'Ваш e-mail')
    );
    $product_thml = '
<div style="display:none">
    <div id="order_popup" class="interkassa-popup-wrap">
        <div class="p_content">
            <h2 class="p_title">' . $product_title . '</h2>

            <div class="p_thumb">' . get_the_post_thumbnail(get_the_ID(), array(150, 150), false) . '</div>
            <div class="p_info" style="text-align: center;">';

    if (!empty($price)) {
        $product_thml .= '
                <div>Цена: <span class="price">' . $price . '</span><span class="currency"> ' . $currency . '</span>
                </div>
                                                            ';
    }
    if (!empty($desc)) {
        $product_thml .= '
                <div>Описание: ' . $desc . '</div>
                                                            ';
    }
    $product_thml .= '
            </div>
            <form id="ik_form" name="payment" method="post" action="https://sci.interkassa.com/" enctype="utf-8"
                  target="_blank">
                <input type="hidden" name="ik_co_id" value="' . $shop_id . '"/>
                <input type="hidden" name="ik_pm_no" value="' . $payment_id . '"/>
                <input type="hidden" name="ik_am" value="' . $price . '"/>
                <input type="hidden" name="ik_desc" value="' . $product_title . '"/>';

    if (!empty($interkassa_fields_id)) {
        $product_thml .= '<span>Заполните форму</span><br/>';
        foreach ($interkassa_fields_id as $i) {
            if(empty($i)) $i = 0;
                $product_thml .= '<label>' . $i . '
                    <input type="text" name="ik_x_ik_x_baggage_' . $i . '"
                           placeholder="" value=""/></label><br/>';
        }
    }
    $product_thml .= '<input type="submit" id="ik_submit" value="Оплатить"></form>
        </div>
    </div>
</div>';

    return $product_thml;
}
