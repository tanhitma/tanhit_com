<?php
/**
 * MemberLux admin JS
 */
class WPMShortcodesSettingsJs{

    public function __construct(){
        add_action('admin_footer', array($this,'wpm_shortcode_settings_admin_js'));
    }
    function wpm_shortcode_settings_admin_js()
    {
        ?>
        <script type="text/javascript">

        jQuery(function ($) {

            function wpm_insert_shortcode(shortcode){
                <?php if(version_compare(get_bloginfo('version'), '3.9', '>=')){ ?>
                // tinymce.get().insertContent(shortcode);
                tinyMCE.activeEditor.insertContent(shortcode);
                <?php }else{ ?>
                tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);
                <?php } ?>
            }

            /**
             * Insert arrows shortcode
             */
            $('#wpm-arrow-submit').live('click', function () {
                var extention = '.png';
                var arrow = $('input[name=arrow]:checked');
                var arrow_name = arrow.val();
                if (arrow.hasClass('gif')) {
                    extention = '.gif';
                }
                var shortcode = '<p class="aligncenter"><img class="aligncenter ps_arrow" src="http://static.wppage.ru/wppage/i/arrows/' + arrow_name + extention + '" alt="" /></p>';

                wpm_insert_shortcode(shortcode);
                tb_remove();
            });
            /**
             * Insert audio shortcode
             */
            $('#wpm-audio-submit').live('click', function () {

                var audio_link = $('#audio-link').val();
                if (audio_link == '') {
                    audio_link = 'null';
                }

                var autoplay = ($('#autoplay:checked').val()) ? 'on' : 'off';
                var audio_color = $('.audio_color:checked').val();
                var shortcode = '<p class="aligncenter">[wpm_uppod audio=' + audio_link + ' color=' + audio_color + ' autoplay=' + autoplay + ']</p>';

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert infoprotector shortcode
             */
            $('#wpm-infoprotector-submit').live('click', function () {

                var ipr_link = $('#ps_infoprotector_link').val();
                if (ipr_link == '') {
                    ipr_link = 'null';
                }

                var shortcode = '<p class="aligncenter">[wpm_ipr link=' + ipr_link + ']</p>';

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert ribbon shortcode
             */
            $('#wpm-ribbon-submit').live('click', function () {

                var bonus_style = $('input[name=bonus_style]:checked').val();

                var shortcode_old = '<h2 class="ps_bonus_box_wide ps_bonus_wide_' + bonus_style + '"><span class="left">&nbsp;</span><span class="ps_bonus_text_wide">Текст</span><span class="right">&nbsp;</span></h2><p>&nbsp;</p>';

                var shortcode = '<div class="bonus_table_box_t"><table class="ps_bonus_box_wide_t ps_bonus_wide_t_' + bonus_style + '"><tr><td class="td_left_box left"></td><td class="wp_bonus_text_box_t"><span class="ps_bonus_text_wide_t">Текст</span></td><td class="td_right_box right"></td></tr></table></div><p>&nbsp;</p>';


                wpm_insert_shortcode(shortcode);
                tb_remove();
            });
            /**
             * Insert box shortcode
             */
            $('#wpm-box-submit').live('click', function () {

                var text_box_style = $('input[name=text_box_style]:checked').val();
                var shortcode = '<p class="aligncenter"><div class="ps_text_box ps_text_box_' + text_box_style + '"><p class="ps_text_box_text">Текст</p></div></p><p>&nbsp;</p>';

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert list shortcode
             */
            $('#wpm-list-submit').live('click', function () {

                var bullets_style = $('input[name=bullet_style]:checked').val();

                var shortcode = '<ul class="ps_ul ps_bullet ps_bullet_' + bullets_style + '"><li>Список</li><li>Список</li></ul>';


                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert timer shortcode
             */
            $('#wpm-timer-submit').live('click', function() {

                var counter_image = '';
                var c_type = $('input[name=c-type]:checked').val();
                var c_date = $('input[name=c-date]').val();
                var c_time = $('input[name=c-time]').val();
                var c_days = $('input[name=c-days]').val();
                var c_hours = $('input[name=c-hours]').val();
                var c_minutes = $('input[name=c-minutes]').val();
                var c_redirect_fixed = $('input[name=c-redirect-fixed]:checked').val();
                var c_redirect_fixed_url = $('input[name=c-redirect-fixed-url]').val();
                var c_redirect_interval = $('input[name=c-redirect-interval]:checked').val();
                var c_redirect_interval_url = $('input[name=c-redirect-interval-url]').val();
                var c_size = $('input[name=c-size]:checked').val();
                var c_color = $('input[name=c-color]:checked').val();
                var c_skin = $('input[name=c-design]:checked').val();
                var c_image = $('input[name=c-image]:checked').val();
                var cMessage = "Задайте все недостающие параметры:\n";
                var cError = false;


                var d = new Date();
                var counterID = d.getTime();
                counterID = ' id=' + counterID;

                if (c_type == 'fixed') {

                    if (c_date == '') {
                        cError = true;
                        cMessage += "- Дату \n";
                    } else {
                        c_date = ' date=' + c_date;
                    }

                    if (c_time == '') {
                        cError = true;
                        cMessage += "- Время \n";
                    } else {
                        c_time = ' time=' + c_time;
                    }

                    if (c_redirect_fixed != '' && c_redirect_fixed == 'redirect') {
                        if (c_redirect_fixed_url != '') {
                            c_redirect = ' redirect=' + c_redirect_fixed_url;
                        } else {
                            cError = true;
                            cMessage += "- Ссылку для переадресации \n";
                        }
                    }
                    if (c_redirect_fixed != '' && c_redirect_fixed == 'hide') {
                        c_redirect = '';
                    }

                }
                if (c_type == 'interval') {
                    if (c_redirect_interval != '' && c_redirect_interval == 'redirect') {
                        if (c_redirect_interval_url != '') {
                            c_redirect = ' redirect=' + c_redirect_interval_url;
                        } else {
                            cError = true;
                            cMessage += "- Ссылку для переадресации \n";
                        }
                    }
                    if (c_redirect_interval != '' && c_redirect_interval == 'renew') {
                        c_redirect = ' renew=true';
                    }
                    if (c_redirect_interval != '' && c_redirect_interval == 'hide') {
                        c_redirect = '';
                    }

                    if (c_days == '') {
                        cError = true;
                        cMessage += "- Дни \n";
                    } else {
                        c_days = ' days=' + c_days;
                    }

                    if (c_hours == '') {
                        cError = true;
                        cMessage += "- Часы \n";
                    } else {
                        c_hours = ' hours=' + c_hours;
                    }

                    if (c_minutes == '') {
                        cError = true;
                        cMessage += "- Минуты \n";
                    } else {
                        c_minutes = ' minutes=' + c_minutes;
                    }

                }


                if (c_size) {
                    c_size = ' size=' + c_size;
                }
                if (c_color) {
                    c_color = ' color=' + c_color;
                }
                if (c_skin) {
                    c_skin = ' skin=' + c_skin;
                }
                if ($('input[name=c-use-image]').is(':checked')) {
                    c_image = ' image=' + c_image;
                    //counter_image = '<img src="http://static.wppage.ru/wppage/i/timer/' + c_image + '.png"><br>';
                } else {
                    c_image = '';
                }
                if (cError) {
                    alert(cMessage);
                    return false;
                }

                var shortcode = '';
                if (c_type == 'fixed') {
                    shortcode = '<p class="aligncenter">[wpm_countdown' + counterID + ' type=' + c_type + c_date + c_time + c_size + c_color + c_skin + c_redirect + c_image + ']</p>';
                } else {
                    shortcode = '<p class="aligncenter">[wpm_countdown' + counterID + ' type=' + c_type + c_days + c_hours + c_minutes + c_size + c_color + c_skin + c_redirect + c_image + ']</p>';
                }

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert divier sortcode
             */
            $('#wpm-divider-submit').live('click', function () {

                var width_1 = width_2 = '';
                width_1 = jQuery('#ps_divide_width').val();
                if (width_1 == '') {
                    width_1 = 65;
                }
                width_2 = 100 - width_1;

                var shortcode = '<table width="100%" class="wpm_divider"><tr><td style="width:' + width_1 + '%"><div>текст</div></td><td style="width:' + width_2 + '%"><div>текст</div></td></tr></table>';

                wpm_insert_shortcode(shortcode);
                tb_remove();
            });
            /**
             * Insert docs shortcode
             */
            function get_formkey(url) {
                var request = [];
                var pairs = url.substring(url.indexOf('?') + 1).split('&');
                for (var i = 0; i < pairs.length; i++) {
                    var pair = pairs[i].split('=');
                    if (decodeURIComponent(pair[0]) == 'formkey') return decodeURIComponent(pair[1]);
                    //request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
                }

            }

            $('#wpm-docs-submit').live('click', function () {

                var url = $('#googleform-key').val();
                var src = '';
                if (url.substring(0, 7) == "<iframe") {
                    $('#temp-form-code').html(url);
                    src = $('#temp-form-code iframe').attr('src');
                } else {
                    src = url + '?embedded=true';
                }
                var height = ($('#googleform-height').val()) ? $('#googleform-height').val() : 500;
                var width = ($('#googleform-width').val()) ? $('#googleform-width').val() : 600;

                var shortcode = '<p class="aligncenter">[wpm_googleform url="' + src + '" width="' + width + '" height="' + height + '"]</p>';

                wpm_insert_shortcode(shortcode);
                tb_remove();
            });

            /**
             * Insert header
             */

            $('#wpm-header-submit').live('click', function () {

                var header = $('input[name=header]:checked').val();
                var shortcode = '<p class="aligncenter"><img class="aligncenter wpm_header" src="http://static.wppage.ru/wppage/i/headers/' + header + '.png" alt="" /></p><p>&nbsp;</p>';

                wpm_insert_shortcode(shortcode);
                tb_remove();
            });

            /**
             * Insert buy button
             */
            $('.wpp_buy_button_type input[name=type]').live('change', function(){
               if($(this).val() == 'interkasa'){
                   $('.wpm-product-link-options-box').hide();
               }else{
                   $('.wpm-product-link-options-box').show();
               }
            });


            $('#wpm-buy-submit').live('click', function () {

                var url = '';
                var shortcode = '';
                var type = $('input.type:checked').val();
                var button_style = $('input[name=button_style]:checked').val();
                var target = $('input[name=target]:checked').val();
                

                if (type == 'interkasa') {
                    url = '#order_popup';
                }
                else {
                    url = $('#external_url').val();
                }

                if (type == 'interkasa') {
                    //shortcode = '<p class="aligncenter"><input type="button" class="product_cbutton ps_make_order ps_product_button_' + button_size + '_' + button_style + '" value="" /></p><br>'
                    shortcode = '<p class="aligncenter">&nbsp;<br><a class="fancybox interkassa-payment-button buy-button" href="#order_popup"><img data-type="buy-button" class="interkassa-payment-button" src="'+ button_style +'"></a>&nbsp;<br></p>';
                } else {
                    //shortcode = '<p class="aligncenter"><input type="button" formtarget="' + link_type + '" class="product_cbutton ps_external_make_order ps_product_button_' + button_size + '_' + button_style + '" alt="' + url + '"/></p><br>';
                    shortcode = '<p class="aligncenter">&nbsp;<br> <a class="buy-button" href="' + url + '" target="'+ target +'"><img data-type="buy-button" src="'+ button_style +'" ></a> &nbsp;<br></p>';

                }

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert review
             */
            $('#wpm-review-submit').live('click', function () {

                var shortcode = '';
                var review_style = jQuery('input[name=review_style]:checked').val();

                shortcode = '<p>&nbsp;</p><table class="wpm_review wpm_review_' + review_style + '"><tr><td class="review_header"></td></tr>' +
                    '<tr><td class="review_text"><p>Текст</p></td></tr>' +
                    '<tr><td class="review_footer"></td></tr></table><p>&nbsp;</p>';

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert satisfaction
             */
            $('#wpm-satisfaction-submit').live('click', function () {

                var satisfaction = $('input[name=satisfaction]:checked').val();
                var shortcode = '<p><img class="aligncenter ps_satisfaction" src="http://static.wppage.ru/wppage/i/satisfaction/' + satisfaction + '.png" alt="" /></p>';

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert social buttons shortcode
             */
            $('#wpm-social-submit').live('click', function () {

                var sb = '';
                $('.ps_socialbuttons_form input:checked').each(function () {
                    if (sb == '') {
                        sb += $(this).val();

                    } else {
                        sb += (',' + $(this).val());

                    }
                });
                var shortcode = '';
                if (sb == '') shortcode = '';
                else shortcode = '<p class="aligncenter">[wpm_socialbuttons buttons="' + sb + '"]</p>';

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert mail shortcode
             */
            $('#wpm-mail-submit').live('click', function () {

                var subscription = $('input[name=wpm_subscription]:checked').val();
                var shortcode = '[wpm_' + subscription + ']';

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });
            /**
             * Insert video shortcode
             */
            $('#video-width-full').live('click', function(){
                $('.video-size-options').toggleClass('visible');
                $('.video-ratio-wrap').toggleClass('hidden');
                if($('#video-width-full').is(':checked')){
                    $('.wpm_video_no_border').click();
                    console.log(':checked');
                }
            });
            $('#wpm-video-submit').live('click', function () {
                var shortcode = '';
                var video = $('#wpm-video-form #wpm-video-link').val();
                if(!video){
                    video = 'none';
                }
                var width = $('#wpm-video-form #video-width').val();
                var ratio = $('#wpm-video-form input[name=video_ratio]:checked').val();
                var height = $('#wpm-video-form #video-height').val();
                var autoplay = ($('#wpm-video-form #autoplay:checked').val()) ? 'on' : 'off';
                var size = $('#wpm-video-form input[name=video_border_size]:checked').val();
                var style = $('#wpm-video-form input[name=video_border_style]:checked').val();
                var video_border = $('#wpm-video-form input[name=video_border]:checked').val();
                if (video_border == 'yes') {
                    if($('#video-width-full').is(':checked')){
                        shortcode = '<p class="aligncenter">[wpm_video video=' + video + ' autoplay=' + autoplay + ' style=' + style + ' size=' + size + ']</p>';
                    }else{
                        shortcode = '<p class="aligncenter">[wpm_video video=' + video + ' width=' + width + ' height=' + height + ' autoplay=' + autoplay + ' style=' + style + ' size=' + size + ']</p>';
                    }

                } else {
                    if($('#video-width-full').is(':checked')){
                        shortcode = '<p class="aligncenter">[wpm_video video=' + video + ' ratio='+ ratio +' autoplay=' + autoplay + ']</p>';
                    }else{
                        shortcode = '<p class="aligncenter">[wpm_video video=' + video + ' width=' + width + ' height=' + height + ' autoplay=' + autoplay + ']</p>';
                    }
                }

                wpm_insert_shortcode(shortcode);
                tb_remove();

            });


        });

        function wpm_open_help_win(url) {
            var video_id = url.split('v=')[1];
            var ampersandPosition = video_id.indexOf('&');
            if (ampersandPosition != -1) {
                video_id = video_id.substring(0, ampersandPosition);
            }
            var embed_url = 'http://www.youtube.com/embed/' + video_id + '?rel=0';
            myWindow = window.open(embed_url, 'wpm', 'width=640,height=390,location=no,left=300,top=200,location=no,scrollbar=no,toolbar=no,statusbar=no');
            myWindow.focus();
            return false;
        }
        </script>
    <?php }
}

new WPMShortcodesSettingsJs;


