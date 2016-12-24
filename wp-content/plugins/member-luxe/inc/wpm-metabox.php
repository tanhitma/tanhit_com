<?php


function wpm_page_extra()
{
    global $post;
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="wpm_page_noncename" id="wpm_page_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

    include_once('js/wpm-admin-js.php');

    //add_filter('tiny_mce_before_init', 'wpm_footer_tinymce_config', 9999);

    if (version_compare(get_bloginfo('version'), '3.9', '>=')) {
        $wpm_tinymce_options = array(
            'quicktags'     => true,
            'media_buttons' => true,
            'editor_height' => 100,
            'textarea_name' => 'page_meta[homework_description]',
            'editor_class'  => 'large-text',
            'tinymce'       => array(
                'toolbar1'          => 'bold italic underline strikethrough | forecolor backcolor | justifyleft justifycenter justifyright | bullist numlist outdent indent |removeformat | link unlink hr',
                'toolbar2'          => false,
                'toolbar3'          => false,
                'forced_root_block' => 'p',
                'force_br_newlines' => false,
                'force_p_newlines'  => true,
                'remove_linebreaks' => true,
                'wpautop'           => true,
                'content_css_force' => ( plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                                            .', ' . plugins_url() . '/member-luxe/css/editor-style-wpm-homework.css?' . time()
                                            .', ' . plugins_url() . '/member-luxe/css/bullets.css'
                                        )
            )
        );

    } else {
        $wpm_tinymce_options = array(
            'media_buttons'     => true,
            'teeny'             => false,
            'quicktags'         => true,
            'textarea_rows'     => 20,
            'textarea_name'     => 'page_meta[homework_description]',
            'editor_class'      => 'large-text',
            'content_css'       => '',
            'tinymce'           => array(
                'theme_advanced_buttons1'   => 'bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,outdent,indent,|,removeformat,|,link,unlink,hr',
                'theme_advanced_buttons2'   => '',
                'theme_advanced_buttons3'   => '',
                'theme_advanced_buttons4'   => '',
                'theme_advanced_font_sizes' => '10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,32px,42px,48px,52px',
                'forced_root_block'         => 'p',
                'wpautop'                   => true,
                'force_br_newlines'         => false,
                'force_p_newlines'          => true,
                'remove_linebreaks'         => true,
                'content_css_force'         => ( plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                                                    .', ' . plugins_url() . '/member-luxe/css/editor-style-wpm-homework.css?' . time()
                                                    .', ' . plugins_url() . '/member-luxe/css/bullets.css'
                                                )
            )
        );

    }
    //=====

    $page_meta = get_post_meta(get_the_ID(), '_wpm_page_meta', true);

    if(empty($page_meta)){
        $page_meta = array(
            /* Cyber.Paw Edition Start */
            'startday' => '', // переменная старт
            'stopday' => '',   // переменная стоп
            /* Cyber.Paw Edition End */
            'description' => '',
            'shift_is_on' => false,
            'is_homework' => false,
            'shift_value' => 0,
            'confirmation_method' => 'auto',
            'homework_shift_value' => 0,
            'homework_description' => '',
            'subscription' => array(
                'getresponse' => '',
                'mailchimp' => '',
                'unisender' => '',
                'smartresponder' => '',
                'justclick' => '',
            ),
            'interkassa' => array(
                'name' => '',
                'desc' => '',
                'price' => '',
                'currency' => '',
                'id' => '',
                'fields' => array(
                    'Ф.И.О',
                    'Страна',
                    'Город',
                    'Адрес',
                    'Индекс',
                    'Телефон',
                    'E-mail'
                ),
                'show_fields' => array(
                    'Ф.И.О'
                )
            ),
            'comments' => array(
                'show' => true,
                'layout_list' => array(
                    'Закладки',
                    'Друг под другом',
                    'Колонки'
                ),
                'layout' => 'Закладки',
                'comments' => array(
                    'Wordpress',
                    'Facebook',
                    'VKontakte'
                ),
                'comments_to_show' => array(
                    'Wordpress',
                    'Facebook',
                    'VKontakte'
                ),
                'order' => array(
                    'Wordpress',
                    'Facebook',
                    'VKontakte'
                )
            ),
            'code' => array(
                'head' => '',
                'body' => '',
                'footer' => ''
            ),
            'feedback' => array(
                'title' => __('Обратная связь', 'wpm'),
                'email' => '',
                'href' => '#wpm_contact_form',
                'fields' => array(
                    '0' => '',
                    '1' => '',
                    '2' => ''
                ),
                'show' => array(
                    '0',
                    '1',
                    '2'
                ),
                'message' => '',
                'show_message' => false
            ),
            'homework' => array(
                'visible' => '',
                'required' => '',
                'content' => '',
                'checking_type' => '',
                'type_list' => array(
                    'manual',
                    'auto',
                    'semi-auto')
                )
        );
    }
    update_post_meta(get_the_ID(), '_wpm_page_meta', $page_meta);

    ?>
    <style type="text/css">
        .wpm-tabs-vertical .tab{
            border-left: 1px solid #eee!important;
            margin-left: 129px!important;

        }
        .wpm-tabs-vertical .tab .wpm-tab-content, .wpm-options-page .content-wrap{
            box-shadow: none!important;
            min-height: 200px!important;
        }
        .auto-settings, .semi-auto-settings{
            display: none;
        }


    </style>
    <script type="text/javascript">




        // Uploading files
        var wpm_file_frame;

        jQuery(function ($) {

            $('.homework-checking-type').on('change', function (){
                if($(this).val() == 'auto'){
                    $('.auto-settings').show();
                    $('.semi-auto-settings').hide();
                }
                if($(this).val() == 'semi-auto'){
                    $('.auto-settings').hide();
                    $('.semi-auto-settings').show();
                }
                if($(this).val() == 'manual'){
                    $('.auto-settings').hide();
                    $('.semi-auto-settings').hide();
                }

            });

            $('#wpm-sortable-comments-1').sortable();

            // Tabs
            $(".wpm-tabs").tabs({
                autoHeight: false,
                collapsible: false,
                fx: {
                    opacity: 'toggle',
                    duration: 'fast'
                },
                activate: function (e, ui) {
                    $.cookie('selected-tab', ui.newTab.index(), { path: '/' });
                },
                active: $.cookie('selected-tab')
            }).addClass('ui-tabs-vertical ui-helper-clearfix');

            $('.wpm-inner-tabs').tabs({
                collapsible: false,
                fx: {
                    opacity: 'toggle',
                    duration: 'fast'
                }
            });


            <?php if(!empty($wpm_head_image)){ ?>
            $('#delete-wpm-head-image').show();
            <?php } ?>
            $('.upload_wpm_head_image_button').live('click', function (event) {
                event.preventDefault();

                // If the media frame already exists, reopen it.
                if (wpm_file_frame) {
                    wpm_file_frame.open();
                    return;
                }

                // Create the media frame.
                wpm_file_frame = wp.media.frames.downloadable_file = wp.media({
                    title: 'Выберите файл',
                    button: {
                        text: 'Использовать изображение'
                    },
                    multiple: false
                });

                // When an image is selected, run a callback.
                wpm_file_frame.on('select', function () {
                    var attachment = wpm_file_frame.state().get('selection').first().toJSON();
                    // console.log(attachment);
                    $('input[name=wpm_head_image]').val(attachment.url);
                    $('input[name=wpm_head_image_id]').val(attachment.id);
                    $('#wpm-head-image-preview').attr('src', attachment.url);
                    $('#delete-wpm-head-image').show();
                    $('.wpm-head-image-preview-box').show();

                });

                // Finally, open the modal.
                wpm_file_frame.open();
            });
            $('#delete-wpm-head-image').live('click', function () {
                $('input[name=wpm_head_image]').val('');
                $('input[name=wpm_head_image_id]').val('');
                $('#delete-wpm-head-image').hide();
                $('.wpm-head-image-preview-box').hide();
            });

            /**/
            $('.wpm-code').click(function () {
                $(this).select();
            });


        });
    </script>
    <div class="wpm_box">
    <div class="options-wrap wpm-ui-wrap">
    <div class="wpm-tabs wpm-tabs-vertical ui-tabs" id="wpm-options-tabs">
    <ul class="ui-tabs-nav tabs-nav">
        <li><a href="#wpm_tab_1"><?php _e('Краткое описание', 'wpm'); ?></a></li>
        <li><a href="#wpm_tab_3"><?php _e('Подписки', 'wpm'); ?></a></li>
        <li><a href="#wpm_tab_4"><?php _e('Интеркасса', 'wpm'); ?></a></li>
        <!-- <li><a href="#wpm_tab_5"><?php _e('Комментарии', 'wpm'); ?></a></li> // -->
        <li><a href="#wpm_tab_7"><?php _e('Скрипты', 'wpm'); ?></a></li>
        <!-- <li><a href="#wpm_tab_8"><?php _e('Обратная связь', 'wpm'); ?></a></li> // -->
        <!-- <li><a href="#wpm_tab_10"><?php _e('Доступ по расписанию', 'wpm'); ?></a></li> -->
        <li><a href="#wpm_tab_10"><?php _e('Автотренинг', 'wpm'); ?></a></li>
    </ul>
    <div class="tab" id="wpm_tab_1">

        <div class="wpm-tab-content">
            <textarea class="large-text" name="page_meta[description]" rows="10"><?php echo $page_meta['description']; ?></textarea>
            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="5" accesskey="p" value="<?php _e('Обновить', 'wpm'); ?>">
            </div>
        </div>
    </div>
    <div class="tab" id="wpm_tab_3">
        <div class="wpm-tab-content">
            <div class="wpm-inner-tabs">
                <ul class="wpm-inner-tabs-nav">
                    <li><a href="#wpm_inner_tab_3_1"><?php _e('GetResponse', 'wpm'); ?></a></li>
                    <li><a href="#wpm_inner_tab_3_2"><?php _e('MailChimp', 'wpm'); ?></a></li>
                    <li><a href="#wpm_inner_tab_3_3"><?php _e('UniSender', 'wpm'); ?></a></li>
                    <li><a href="#wpm_inner_tab_3_4"><?php _e('SmartResponder', 'wpm'); ?></a></li>
                    <li><a href="#wpm_inner_tab_3_5"><?php _e('JustClick', 'wpm'); ?></a></li>
                </ul>
                <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_1">
                    <div class="section">
                        <label for="wpp_getresponse"><?php _e('Код формы', 'wpm'); ?></label>
                        <textarea name="page_meta[subscription][getresponse]" id="wpp_getresponse" class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['getresponse']; ?></textarea>
                    </div>
                    <div class="wpm-helper-box"><a
                            onclick="wpm_open_help_win('http://www.youtube.com/watch?v=546KJehwzzw&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=5')"><?php _e('Видео урок', 'wpm'); ?></a>
                    </div>
                </div>
                <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_2">
                    <div class="section">
                        <label for="wpp_mailchimp_code"><?php _e('Код формы', 'wpm'); ?></label>
                        <textarea name="page_meta[subscription][mailchimp]" id="wpp_mailchimp_code" class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['mailchimp']; ?></textarea>
                    </div>
                    <div class="wpm-helper-box"><a
                            onclick="wpm_open_help_win('http://www.youtube.com/watch?v=546KJehwzzw&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=5')"><?php _e('Видео урок', 'wpm'); ?></a>
                    </div>
                </div>
                <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_3">
                    <div class="section">
                        <label for="wpp_unisender_code"><?php _e('Код формы', 'wpm'); ?></label>
                        <textarea name="page_meta[subscription][unisender]" id="wpp_unisender_code"
                                  class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['unisender']; ?></textarea>
                    </div>
                    <div class="wpm-helper-box"><a
                            onclick="wpm_open_help_win('http://www.youtube.com/watch?v=546KJehwzzw&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=5')"><?php _e('Видео урок', 'wpm'); ?></a>
                    </div>
                </div>
                <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_4">
                    <div class="section">
                        <label for="wpp_smartresponder_code"><?php _e('Код формы', 'wpm'); ?></label>
                        <textarea name="page_meta[subscription][smartresponder]" id="wpp_smartresponder_code"
                                  class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['smartresponder']; ?></textarea>
                    </div>
                    <div class="wpm-helper-box"><a
                            onclick="wpm_open_help_win('http://www.youtube.com/watch?v=LI5TqWaH-qg&feature=youtu.be')"><?php _e('Видео урок', 'wpm'); ?></a>
                    </div>
                </div>
                <div class="wpm-inner-tab-content" id="wpm_inner_tab_3_5">
                    <div class="section">
                        <label for="wpp_unisender_code"><?php _e('Код формы', 'wpm'); ?></label>
                        <textarea name="page_meta[subscription][justclick]" id="wpp_justclick_code"
                                  class="wpp_textarea large-text" rows="10"><?php echo $page_meta['subscription']['justclick']; ?></textarea>
                    </div>
                    <div class="wpm-helper-box"><a
                            onclick="wpm_open_help_win('http://www.youtube.com/watch?v=LI5TqWaH-qg&feature=youtu.be')"><?php _e('Видео урок', 'wpm'); ?></a>
                    </div>
                </div>
            </div>
            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="5" accesskey="p" value="<?php _e('Обновить', 'wpm'); ?>">
            </div>
        </div>
    </div>
    <div class="tab" id="wpm_tab_4">

        <div class="wpm-tab-content">
            <div class="wpm-inner-tabs">
                <ul class="wpm-inner-tabs-nav">
                    <li><a href="#wpm_inner_tab_4_1"><?php _e('Основное', 'wpm'); ?></a></li>
                    <li><a href="#wpm_inner_tab_4_2"><?php _e('Дополнительные поля', 'wpm'); ?></a></li>
                </ul>

                <div class="wpm-inner-tab-content" id="wpm_inner_tab_4_1">
                    <div class="section">
                        <label><?php _e('Название продукта', 'wpm'); ?> <br>
                            <input type="text" name="page_meta[interkassa][name]" id="product_title" class="wpp_input_text large-text"
                                   value="<?php echo $page_meta['interkassa']['name']; ?>"/>
                        </label><br/>
                        <label for="product_desc"><?php _e('Описание продукта', 'wpm'); ?><br>
                            <input type="text" name="page_meta[interkassa][desc]" id="product_desc" class="wpp_input_text large-text"
                                   value="<?php echo $page_meta['interkassa']['desc']; ?>"/>
                        </label><br/>
                        <label><?php _e('Цена', 'wpm'); ?><br/>
                            <input type="text" name="page_meta[interkassa][price]" id="product_price" class="wpp_input_text large-text"
                                   value="<?php echo $page_meta['interkassa']['price']; ?>"/>
                        </label><br/>
                        <label><?php _e('Валюта', 'wpm'); ?><br>
                            <input type="text" name="page_meta[interkassa][currency]" id="product_currency" class="wpp_input_text large-text"
                                   value="<?php echo $page_meta['interkassa']['currency']; ?>"/>
                        </label><br/>
                        <label><?php _e('Идентификатор кассы', 'wpm'); ?><br/>
                            <input type="text" name="page_meta[interkassa][id]" id="interkassa_shop_id" class="wpp_input_text large-text"
                                   value="<?php echo $page_meta['interkassa']['id']; ?>"/>
                        </label>
                    </div>
                </div>
                <div class="wpm-inner-tab-content" id="wpm_inner_tab_4_2">
                    <div class="section">
                        <?php
                        foreach($page_meta['interkassa']['fields'] as $field){
                            echo '<input type="hidden" name="page_meta[interkassa][fields][]" value="'.$field.'">';
                            if(!empty($page_meta['interkassa']['show_fields']) && is_array($page_meta['comments']['comments_to_show']) && in_array($field, $page_meta['interkassa']['show_fields'])){ ?>
                                <p><label class=""><input type="checkbox" name="page_meta[interkassa][show_fields][]"
                                                                      value="<?php echo $field; ?>" checked="checked"><?php echo $field; ?></label></p>
                            <?php }else{ ?>
                                <p><label class=""><input type="checkbox" name="page_meta[interkassa][show_fields][]"
                                                          value="<?php echo $field; ?>"><?php echo $field; ?></label></p>
                           <?php }
                        } ?>
                    </div>

                </div>
            </div>

            <div class="wpm_clear"></div>
            <div class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=Yde8-R6L6vM&feature=youtu.be')"><?php _e('Видео урок', 'wpm'); ?></a>
            </div>
            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="5" accesskey="p" value="<?php _e('Обновить', 'wpm'); ?>">
            </div>

        </div>
    </div>

    <div class="tab" id="wpm_tab_7">

        <div class="wpm-tab-content">
            <div class="wpp_top_nav_bar">

            </div>
            <div class="section ">
                <label>&lt;head&gt;&nbsp; <span class="text_green"><?php _e('ваш код', 'wpm'); ?></span> &lt;/head&gt;</label><br>
                <textarea name="page_meta[code][head]" id="wpm_head_code"
                          class="wpp_textarea large-text"><?php echo $page_meta['code']['head'];
                    ?></textarea>
                <label>&lt;body <span class="text_green"><?php _e('ваш код', 'wpm'); ?></span> &gt;&nbsp;&lt;/body&gt;</label><br>
                <textarea name="page_meta[code][body]" id="wpm_body_code"
                          class="wpp_textarea large-text"><?php echo $page_meta['code']['body'];
                    ?></textarea>
                <label>&lt;body&gt;&nbsp; <span class="text_green"><?php _e('ваш код', 'wpm'); ?></span> &lt;/body&gt;</label><br>
                <textarea name="page_meta[code][footer]" id="wpm_footer_code"
                          class="wpp_textarea large-text"><?php echo $page_meta['code']['footer'];
                    ?></textarea>
            </div>
            <div class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=_kTPYCTPGYA&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=18')"><?php _e('Видео урок', 'wpm'); ?></a>
            </div>
            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="5" accesskey="p" value="<?php _e('Обновить', 'wpm'); ?>">
            </div>
        </div>
    </div>

    <div class="tab" id="wpm_tab_10">

        <div class="wpm-tab-content">
            <div class="wpm-row">
                <label>
                    <input type="checkbox" name="page_meta[shift_is_on]" id="shift_is_on" <?php echo $page_meta['shift_is_on'] ? 'checked' : '';?>><?php _e('Смещение','wpm'); ?>
                </label>
                <label id="shift_value_label" class="<?php echo $page_meta['shift_is_on'] ? '' : 'invisible';?>">
                    <?php _e('на ','wpm');?> &nbsp; <input type="text" name="page_meta[shift_value]" id="shift_value" class="wpp_input_text" size="4" value="<?php echo intval($page_meta['shift_value']); ?>"/>
                    <?php _e('часов', 'wpm'); ?>
                    &nbsp;
                    <select name="page_meta[shift_value_minutes]" id="shift_value_minutes" class="wpp_input_text">
                        <?php $shiftMinutes = wpm_get_minutes($page_meta['shift_value']); ?>
                        <?php foreach (range(0, 55, 5) AS $minute) : ?>
                            <option value="<?php echo $minute; ?>" <?php echo $minute == $shiftMinutes ? 'selected="selected"' : ''; ?>><?php echo $minute; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php _e('минут', 'wpm'); ?>
                </label>
            </div>

            <div class="wpm-row">
                <label>
                    <input type="checkbox" name="page_meta[is_homework]" id="is_homework" <?php echo $page_meta['is_homework'] ? 'checked' : '';?>><?php _e('Домашнее задание','wpm');?>
                </label>
                <div id="homework_options" class="<?php echo $page_meta['is_homework'] ? '' : 'invisible';?>">
                    <dl>
                        <dt><?php _e('Выберите способ подтверждения:','wpm');?></dt>
                        <dd>
                            <label for="confirmation_method_auto">
                                <input type="radio" name="page_meta[confirmation_method]" id="confirmation_method_auto" value="auto" <?php echo ($page_meta['confirmation_method']=='auto' || !$page_meta['confirmation_method']) ? 'checked' : '';?> />
                                <?php _e('Автоматически', 'wpm'); ?>
                            </label>
                        </dd>
                        <dd>
                            <label for="confirmation_method_auto_with_shift">
                                <input type="radio"
                                       name="page_meta[confirmation_method]"
                                       id="confirmation_method_auto_with_shift"
                                       value="auto_with_shift"
                                       <?php echo $page_meta['confirmation_method']=='auto_with_shift' ? 'checked' : '';?>
                                    />
                                <?php _e('Автоматически через ', 'wpm'); ?>
                            </label> &nbsp;
                            <label id="homework_shift_value_label" class="<?php echo $page_meta['confirmation_method']=='auto_with_shift' ? '' : 'disabled_field';?>">
                                <input type="text"
                                       name="page_meta[homework_shift_value]"
                                       id="homework_shift_value"
                                       class="wpp_input_text"
                                       size="4"
                                       value="<?php echo $page_meta['homework_shift_value'] ? $page_meta['homework_shift_value'] : 0; ?>"
                                       <?php echo $page_meta['confirmation_method']=='auto_with_shift' ? '' : ' disabled ';?>
                                    />
                                <?php _e('часов', 'wpm'); ?>
                            </label>
                        </dd>
                        <dd>
                            <label for="confirmation_method_manually">
                                <input type="radio" name="page_meta[confirmation_method]" id="confirmation_method_manually" value="manually" <?php echo $page_meta['confirmation_method']=='manually' ? 'checked' : '';?> />
                                <?php _e('Вручную', 'wpm'); ?>
                            </label>
                        </dd>
                        <dt><br /><?php _e('Описание задания:','wpm');?></dt>
                        <dt>
                            <?php wp_editor( $page_meta['homework_description'], 'page_meta_homework_description', $wpm_tinymce_options ); ?>
                        </dt>
                    </dl>
                </div>

            </div>

            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="10" accesskey="p" value="<?php _e('Обновить', 'wpm'); ?>">
            </div>
        </div>
    </div>

    <!--
    <div class="tab" id="wpm_tab_10">

        <div class="wpm-tab-content">
            <div class="wpm-row">
                <label><?php _e('Показать через (дней), после активации кода доступа. Оставьте пустым чтобы показать сразу', 'wpm'); ?> <br>
                    <input type="text" name="page_meta[startday]" id="startday" class="wpp_input_text" size="3" value="<?php echo $page_meta['startday']; ?>"/>
                </label><br/>
                <label><?php _e('Скрыть через (дней), после активации кода доступа. Оставьте пустым чтобы не скрывать', 'wpm'); ?> <br>
                    <input type="text" name="page_meta[stopday]" id="stopday" class="wpp_input_text" size="3" value="<?php echo $page_meta['stopday']; ?>"/>
                </label><br/>
            </div>

            <div class="wpm-row bottom-row">
                <input name="save" type="submit" class="button-primary" tabindex="10" accesskey="p" value="<?php _e('Обновить', 'wpm'); ?>">
            </div>
        </div>
    </div>
    -->
    </div>
    </div>
    <div class="wpm_clear"></div>
    </div>
<?php
}
add_action('save_post', 'wpm_page_save_meta', 1, 2);
function wpm_page_save_meta($post_id, $post)
{
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if (array_key_exists('wpm_page_noncename', $_POST) && !wp_verify_nonce($_POST['wpm_page_noncename'], plugin_basename(__FILE__))) {
        return $post_id;
    }

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    if(isset($_POST['page_meta'])){
        $new_meta = $_POST['page_meta'];

        if(!isset($new_meta['is_homework'])) {
            $new_meta['is_homework'] = false;
        }

        if(!isset($new_meta['shift_is_on'])) {
            $new_meta['shift_is_on'] = false;
        }

        if(isset($new_meta['shift_value_minutes']) && intval($new_meta['shift_value_minutes'])) {
            $shiftHours = intval($new_meta['shift_value']);
            $shiftHours += intval($new_meta['shift_value_minutes']) * 1/60;
            $new_meta['shift_value'] = $shiftHours;
        }

        $page_meta = get_post_meta(get_the_ID(), '_wpm_page_meta', true);
        $page_meta = array_merge($page_meta, $new_meta );
        update_post_meta($post_id, '_wpm_page_meta', $page_meta);
    }

    wpm_update_rearranged_schedules($post_id);
}

function wpm_form_to_array(&$array, $path, $value)
{
    $key = array_shift($path);
    if (empty($path)) {
        $array[$key] = stripslashes(wp_filter_post_kses(addslashes($value)));
    } else {
        if (!isset($array[$key]) || !is_array($array[$key])) {
            $array[$key] = array();
        }
        wpm_set_value($array[$key], $path, $value);
    }
}