<?php

class WPMShortcodesSettings
{

    public function __construct()
    {

        add_action('admin_footer', array($this, 'wpm_shortcode_settings_wrap'));
        add_action('media_buttons', array($this, 'add_wpm_buttons'), 999);

        /*load_plugin_textdomain( 'demo-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

        add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

        add_filter( 'the_content', array( $this, 'append_post_notification' ) );*/

    }


    function wpm_shortcode_settings_wrap()
    {
        echo '<div style="display: none" id="wpm-shortcode-settings-wrap"><div id="shortcode-settings-conent">&nbsp;</div></div>';
    }

    //=================

    function add_wpm_buttons($editor)
    {

        if (get_post_type() == 'wpm-page' || $_GET['post_type'] == 'wpm-page') {
            $buttons = '';
            if ($editor == 'content') {
                // $buttons = '<a class="button wpm-mce-button mce-i-wpm-social " button-id="social" button-editor="content" title="Социальные кнопки"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-audio " button-id="audio" button-editor="content" title="Аудио"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-video " button-id="video" button-editor="content" title="Видео"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-docs " button-id="docs" button-editor="content" title="Форма Google"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-mail " button-id="mail" button-editor="content" title="Подписки"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-buy " button-id="buy" button-editor="content" title="Продукты"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-list " button-id="list" button-editor="content" title="Списки"></a>';
                // $buttons .= '<a class="button wpm-mce-button mce-i-wpm-ribbon " button-id="ribbon" button-editor="content" title="Ленты"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-arrow " button-id="arrow" button-editor="content" title="Стрелки"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-box " button-id="box" button-editor="content" title="Боксы"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-satisfaction " button-id="satisfaction" button-editor="content" title="Гарантии"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-review " button-id="review" button-editor="content" title="Отзывы"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-header " button-id="header" button-editor="content" title="Заголовки"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-timer " button-id="timer" button-editor="content" title="Таймер"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-divider " button-id="divider" button-editor="content" title="Разделитель"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-infoprotector " button-id="infoprotector" button-editor="' . $editor . '" title="Infoprotector"></a>';
            } elseif ($editor == 'wpm_footer' || $editor == 'wpm_header' || $editor == 'tag_description' || $editor == 'no_access_content' || $editor == 'wpm_login_content') {
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-audio " button-id="audio" button-editor="' . $editor . '" title="Аудио"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-video " button-id="video" button-editor="' . $editor . '" title="Видео"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-docs " button-id="docs" button-editor="' . $editor . '" title="Форма Google"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-buy " button-id="buy" button-editor="' . $editor . '" title="Продукты"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-list " button-id="list" button-editor="' . $editor . '" title="Списки"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-arrow " button-id="arrow" button-editor="' . $editor . '" title="Стрелки"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-box " button-id="box" button-editor="' . $editor . '" title="Боксы"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-satisfaction " button-id="satisfaction" button-editor="' . $editor . '" title="Гарантии"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-review " button-id="review" button-editor="' . $editor . '" title="Отзывы"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-header " button-id="header" button-editor="' . $editor . '" title="Заголовки"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-timer " button-id="timer" button-editor="' . $editor . '" title="Таймер"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-divider " button-id="divider" button-editor="' . $editor . '" title="Разделитель"></a>';
                $buttons .= '<a class="button wpm-mce-button mce-i-wpm-infoprotector " button-id="infoprotector" button-editor="' . $editor . '" title="Infoprotector"></a>';

            } elseif ($editor == 'trafficbomb_message') {
                $buttons = '';
            }

            $main_options = get_option('wpm_main_options');
            if (!empty($main_options['headers']['priority'])) {
                $items = explode(',', $main_options['headers']['priority']);
                $headers = array();
                foreach ($items as $item) {
                    array_push($headers, 'wpm_header_' . $item);
                }
                if (in_array($editor, $headers)) {

                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-audio " button-id="audio" button-editor="' . $editor . '" title="Аудио"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-video " button-id="video" button-editor="' . $editor . '" title="Видео"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-docs " button-id="docs" button-editor="' . $editor . '" title="Форма Google"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-buy " button-id="buy" button-editor="' . $editor . '" title="Продукты"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-list " button-id="list" button-editor="' . $editor . '" title="Списки"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-arrow " button-id="arrow" button-editor="' . $editor . '" title="Стрелки"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-box " button-id="box" button-editor="' . $editor . '" title="Боксы"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-satisfaction " button-id="satisfaction" button-editor="' . $editor . '" title="Гарантии"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-review " button-id="review" button-editor="' . $editor . '" title="Отзывы"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-header " button-id="header" button-editor="' . $editor . '" title="Заголовки"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-timer " button-id="timer" button-editor="' . $editor . '" title="Таймер"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-divider " button-id="divider" button-editor="' . $editor . '" title="Разделитель"></a>';
                    $buttons .= '<a class="button wpm-mce-button mce-i-wpm-infoprotector " button-id="infoprotector" button-editor="' . $editor . '" title="Infoprotector"></a>';
                }
            }


            echo $buttons;
        };

    }


}

new WPMShortcodesSettings;


//===================
add_action('wp_ajax_get_wpm_shortcode_settings', 'wpm_button_shortcode_settings');
function wpm_button_shortcode_settings()
{

    switch ($_GET['button']) {
        case 'social':
            wpm_mce_settings_social();
            break;
        case 'audio':
            wpm_mce_settings_audio();
            break;
        case 'video':
            wpm_mce_settings_video();
            break;
        case 'docs':
            wpm_mce_settings_docs();
            break;
        case 'mail':
            wpm_mce_settings_mail();
            break;
        case 'list':
            wpm_mce_settings_list();
            break;
        case 'buy':
            wpm_mce_settings_buy();
            break;
        case 'ribbon':
            wpm_mce_settings_ribbon();
            break;
        case 'arrow':
            wpm_mce_settings_arrow();
            break;
        case 'box':
            wpm_mce_settings_box();
            break;
        case 'satisfaction':
            wpm_mce_settings_satisfaction();
            break;
        case 'review':
            wpm_mce_settings_review();
            break;
        case 'header':
            wpm_mce_settings_header();
            break;
        case 'timer':
            wpm_mce_settings_timer();
            break;
        case 'divider':
            wpm_mce_settings_divider();
            break;
        case 'infoprotector':
            wpm_mce_settings_infoprotector();
            break;
    }
    die();
}


function wpm_mce_settings_social()
{ ?>
    <div id="wpm-social-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick='wpm_open_help_win("http://www.youtube.com/watch?v=KHg6xc4vIqs&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=1")'>Видео
                    урок</a></span><input
                type="button" id="wpm-social-submit" class="button-primary wpm-shortcode-sumbit"
                value="Вставить социальные кнопки"
                name="social"/>
        </div>
        <div class="ps_socialbuttons_form coach_box">
            <ul id="wpp_sortable_social" class="wpp_sortable block">
                <li><label class="sbutton wpm_checkbox wpm_checked"><span class="wpp_facebook_thumb"></span><input
                            type="checkbox" name="facebook" value="facebook" checked="checked"/></label></li>
                <li><label class="sbutton wpm_checkbox wpm_checked"><span class="wpp_vk_like_thumb"></span><input
                            type="checkbox" name="vk_like" value="vk_like" checked="checked"/></label></li>
                <li><label class="sbutton wpm_checkbox wpm_checked"><span class="wpp_vk_share_thumb"></span><input
                            type="checkbox" name="vk_share" value="vk_share" checked="checked"/></label></li>
                <li><label class="sbutton wpm_checkbox wpm_checked"><span class="wpp_twitter_thumb"></span><input
                            type="checkbox" name="twitter" value="twitter" checked="checked"/></label></li>
                <li><label class="sbutton wpm_checkbox wpm_checked"><span class="wpp_gplus_thumb"></span><input
                            type="checkbox" name="gplus" value="gplus" checked="checked"/></label></li>
                <li><label class="sbutton wpm_checkbox wpm_checked"><span class="wpp_linkedin_thumb"></span><input
                            type="checkbox" name="linkedin" value="linkedin" checked="checked"/></label></li>
                <li><label class="sbutton wpm_checkbox wpm_checked"><span class="wpp_mailru_thumb"></span><input
                            type="checkbox" name="mailru" value="mailru" checked="checked"/></label></li>
                <li><label class="sbutton wpm_checkbox wpm_checked"><span
                            class="wpp_odnoklasniki_thumb"></span><input type="checkbox" name="odnoklasniki"
                                                                         value="odnoklasniki"
                                                                         checked="checked"/></label></li>
            </ul>
            <br><br>
        </div>
    </div>
<?php }

function wpm_mce_settings_audio()
{ ?>
    <div id="wpm-audio-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=dzCvAj4T17g&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=2')">Видео
                    урок</a></span><input
                type="button" id="wpm-audio-submit" class="button-primary wpm-shortcode-sumbit" value="Вставить"
                name="audio"/>
        </div>

        <div class="wpp_audio_form coach_box">
            <label for="audio-link">Ссылка</label><br>
            <input type="text" id="audio-link" name="link" class="width_100p" value=""/><br><br>
            <ul class="audio_setting">
                <li><label class=""><input type="radio" class="audio_color"
                                           name="audio_color" value="black"
                                           checked="checked"/><span
                            class="black_player"></span></label></li>
                <li><label class=""><input type="radio" class="audio_color" name="audio_color"
                                           value="white"/><span class="white_player"></span></label>
                </li>
            </ul>
            <br>
            <label class=""><input type="checkbox" id="autoplay" name="autoplay"
                                   value="on"/>&nbsp;Автовоспроизведение</label><br><br><br>
        </div>
        <div class="clearfix"></div>

    </div>
<?php }

function wpm_mce_settings_video()
{ ?>
    <div id="wpm-video-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=HybuTJ7oqrQ&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=3')">Видео
                    урок</a></span><input
                type="button" id="wpm-video-submit" class="button-primary wpm-shortcode-sumbit" value="Вставить видео"
                name="video"/>
        </div>
        <div class="wpp_video_form coach_box">
            <label for="wpm-video-link">Ссылка<br>
                <input type="text" id="wpm-video-link" name="videolink" class="width_100p" value=""/>
                <br/>При вставке ссылок на собственные файлы <b>поддерживается только формат .mp4</b> (он работает и в
                iOS, и на Windows).
            </label><br><br>
            <label for="video-width-full"><input type="checkbox" id="video-width-full" name="width-full" value="full"
                                                 checked/> На всю ширину</label>
            <div class="video-ratio-wrap">
                <label class=""><input type="radio" name="video_ratio" value="16by9"
                                       checked="checked">16x9</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label class=""><input type="radio" name="video_ratio" value="4by3">4x3</label>
            </div>
            <div class="video-size-options">
                <label for="video-width"><input type="number" id="video-width" name="width" value="560"
                                                class="width_100"/> Ширина</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="video-height"><input type="number" id="video-height" name="height" value="315"
                                                 class="width_100"/> Высота</label><br><br>
            </div>

            <label class=""><input type="checkbox" id="autoplay" name="autoplay"
                                   value="on"/>&nbsp;Автовоспроизведение</label><br><br>

            <div>
                <label class=""><input type="radio" name="video_border" class="wpm_video_no_border" value="no"
                                       checked="checked">Без рамки</label>
                <label class=""><input type="radio" name="video_border" value="yes">В рамке</label>
            </div>
            <br>

            <div class="video_border_sizes" style="display: none">
                <label class="wpm_radio_v video_border_480"><input type="radio" name="video_border_size"
                                                                   value="480x270"></label>
                <label class="wpm_radio_v video_border_560"><input type="radio" name="video_border_size"
                                                                   value="560x315"></label>
                <label class="wpm_radio_v video_border_640"><input type="radio" name="video_border_size"
                                                                   value="640x360"></label>
                <label class="wpm_radio_v video_border_720"><input type="radio" name="video_border_size"
                                                                   value="720x405"></label>
            </div>
            <br>

            <div class="video_styles">
                <label class="wpm_radio_v video_style video_style_1"><input type="radio"
                                                                            name="video_border_style"
                                                                            value="1"></label>
                <label class="wpm_radio_v video_style video_style_2"><input type="radio"
                                                                            name="video_border_style"
                                                                            value="2"></label>
                <label class="wpm_radio_v video_style video_style_3"><input type="radio"
                                                                            name="video_border_style"
                                                                            value="3"></label>
                <label class="wpm_radio_v video_style video_style_4"><input type="radio"
                                                                            name="video_border_style"
                                                                            value="4"></label>
                <label class="wpm_radio_v video_style video_style_5"><input type="radio"
                                                                            name="video_border_style"
                                                                            value="5"></label>
                <label class="wpm_radio_v video_style video_style_6"><input type="radio"
                                                                            name="video_border_style"
                                                                            value="6"></label>
                <label class="wpm_radio_v video_style video_style_7"><input type="radio"
                                                                            name="video_border_style"
                                                                            value="7"></label>
                <label class="wpm_radio_v video_style video_style_8"><input type="radio"
                                                                            name="video_border_style"
                                                                            value="8"></label>
                <label class="wpm_radio_v video_style video_style_9"><input type="radio"
                                                                            name="video_border_style"
                                                                            value="9"></label>
                <label class="wpm_radio_v video_style video_style_10"><input type="radio"
                                                                             name="video_border_style"
                                                                             value="10"></label>
            </div>
        </div>
    </div>
<?php }

function wpm_mce_settings_buy()
{
    $buttons_url = plugins_url() . '/member-luxe/i/buy-buttons/';
    ?>
    <div id="wpm-buy-form">
        <div class="wpm-top-popup-nav">
            <input type="button" id="wpm-buy-submit" class="button-primary"
                   value="Вставить кнопку заказа" name="buy"/>
        </div>
        <div class="ps_product_form coach_box">
            <ul class="wpp_buy_button_type">
                <li><label class=""><input type="radio" name="type" class="type"
                                           value="external" checked="checked"/>Своя ссылка</label>
                </li>
                <li><label class=""><input type="radio" name="type" class="type" value="interkasa"/>Интеркасса</label>
                </li>
            </ul>
            <div class="wpm-product-link-options-box">
                <label>Ссылка<br>
                    <input type="text" name="external_url" id="external_url" value="" class="width_100p"/></label>
                <ul class="wpm-product-link-options">
                    <li><label class=""><input type="radio" name="target" class="link-type" value="_self"
                                               checked="checked">Открывать в текущей вкладке</label>
                    </li>
                    <li>&nbsp;&nbsp;&nbsp;<label class=""><input type="radio" name="target" class="link-type"
                                                                 value="_blank">Открывать на новой вкладке</label></li>
                </ul>
            </div>
            <div class="buttons_style">


                <label class="p_cbutton ps_p_button_1 wpm_checked"><input type="radio" name="button_style"
                                                                          value="<?php echo $buttons_url; ?>1.png"
                                                                          checked="checked"/></label>
                <label class="p_cbutton ps_p_button_2"><input type="radio" name="button_style"
                                                              value="<?php echo $buttons_url; ?>2.png"/></label>
                <label class="p_cbutton ps_p_button_3"><input type="radio" name="button_style"
                                                              value="<?php echo $buttons_url; ?>3.png"/></label>
                <label class="p_cbutton ps_p_button_4"><input type="radio" name="button_style"
                                                              value="<?php echo $buttons_url; ?>4.png"/></label>
                <label class="p_cbutton ps_p_button_5"><input type="radio" name="button_style"
                                                              value="<?php echo $buttons_url; ?>5.png"/></label>
                <label class="p_cbutton ps_p_button_6"><input type="radio" name="button_style"
                                                              value="<?php echo $buttons_url; ?>6.png"/></label>
                <label class="p_cbutton ps_p_button_7"><input type="radio" name="button_style"
                                                              value="<?php echo $buttons_url; ?>7.png"/></label>
                <label class="p_cbutton ps_p_button_8"><input type="radio" name="button_style"
                                                              value="<?php echo $buttons_url; ?>8.png"/></label>
                <label class="p_cbutton ps_p_button_9"><input type="radio" name="button_style"
                                                              value="<?php echo $buttons_url; ?>9.png"/></label>
                <label class="p_cbutton ps_p_button_10"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>10.png"/></label>
                <label class="p_cbutton ps_p_button_11"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>11.png"/></label>
                <label class="p_cbutton ps_p_button_12"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>12.png"/></label>
                <label class="p_cbutton ps_p_button_13"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>13.png"/></label>
                <label class="p_cbutton ps_p_button_14"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>14.png"/></label>
                <label class="p_cbutton ps_p_button_15"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>15.png"/></label>
                <label class="p_cbutton ps_p_button_16"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>16.png"/></label>
                <label class="p_cbutton ps_p_button_17"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>17.png"/></label>
                <label class="p_cbutton ps_p_button_18"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>18.png"/></label>
                <label class="p_cbutton ps_p_button_19"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>19.png"/></label>
                <label class="p_cbutton ps_p_button_20"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>20.png"/></label>
                <label class="p_cbutton ps_p_button_21"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>21.png"/></label>
                <label class="p_cbutton ps_p_button_22"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>22.png"/></label>
                <label class="p_cbutton ps_p_button_23"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>23.png"/></label>
                <label class="p_cbutton ps_p_button_24"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>24.png"/></label>
                <label class="p_cbutton ps_p_button_25"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>25.png"/></label>
                <label class="p_cbutton ps_p_button_26"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>26.png"/></label>
                <label class="p_cbutton ps_p_button_27"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>27.png"/></label>
                <label class="p_cbutton ps_p_button_28"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>28.png"/></label>
                <label class="p_cbutton ps_p_button_29"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>29.png"/></label>
                <label class="p_cbutton ps_p_button_30"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>30.png"/></label>
                <label class="p_cbutton ps_p_button_31"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>31.png"/></label>
                <label class="p_cbutton ps_p_button_32"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>32.png"/></label>
                <label class="p_cbutton ps_p_button_33"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>33.png"/></label>
                <label class="p_cbutton ps_p_button_34"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>34.png"/></label>
                <label class="p_cbutton ps_p_button_35"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>35.png"/></label>
                <label class="p_cbutton ps_p_button_36"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>36.png"/></label>
                <label class="p_cbutton ps_p_button_37"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>37.png"/></label>
                <label class="p_cbutton ps_p_button_38"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>38.png"/></label>
                <label class="p_cbutton ps_p_button_39"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>39.png"/></label>
                <label class="p_cbutton ps_p_button_40"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>40.png"/></label>
                <label class="p_cbutton ps_p_button_41"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>41.png"/></label>
                <label class="p_cbutton ps_p_button_42"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>42.png"/></label>
                <label class="p_cbutton ps_p_button_43"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>43.png"/></label>
                <label class="p_cbutton ps_p_button_44"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>44.png"/></label>
                <label class="p_cbutton ps_p_button_45"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>45.png"/></label>
                <label class="p_cbutton ps_p_button_46"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>46.png"/></label>
                <label class="p_cbutton ps_p_button_47"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>47.png"/></label>
                <label class="p_cbutton ps_p_button_48"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>48.png"/></label>
                <label class="p_cbutton ps_p_button_49"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>49.png"/></label>
                <label class="p_cbutton ps_p_button_50"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>50.png"/></label>
                <label class="p_cbutton ps_p_button_51"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>51.png"/></label>
                <label class="p_cbutton ps_p_button_52"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>52.png"/></label>
                <label class="p_cbutton ps_p_button_53"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>53.png"/></label>
                <label class="p_cbutton ps_p_button_54"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>54.png"/></label>
                <label class="p_cbutton ps_p_button_55"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>55.png"/></label>
                <label class="p_cbutton ps_p_button_56"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>56.png"/></label>
                <label class="p_cbutton ps_p_button_57"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>57.png"/></label>
                <label class="p_cbutton ps_p_button_58"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>58.png"/></label>
                <label class="p_cbutton ps_p_button_59"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>59.png"/></label>
                <label class="p_cbutton ps_p_button_60"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>60.png"/></label>
                <label class="p_cbutton ps_p_button_61"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>61.png"/></label>
                <label class="p_cbutton ps_p_button_62"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>62.png"/></label>
                <label class="p_cbutton ps_p_button_63"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>63.png"/></label>
                <label class="p_cbutton ps_p_button_64"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>64.png"/></label>
                <label class="p_cbutton ps_p_button_65"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>65.png"/></label>
                <label class="p_cbutton ps_p_button_66"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>66.png"/></label>
                <label class="p_cbutton ps_p_button_67"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>67.png"/></label>
                <label class="p_cbutton ps_p_button_68"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>68.png"/></label>
                <label class="p_cbutton ps_p_button_69"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>69.png"/></label>
                <label class="p_cbutton ps_p_button_70"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>70.png"/></label>
                <label class="p_cbutton ps_p_button_71"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>71.png"/></label>
                <label class="p_cbutton ps_p_button_72"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>72.png"/></label>
                <label class="p_cbutton ps_p_button_73"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>73.png"/></label>
                <label class="p_cbutton ps_p_button_74"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>74.png"/></label>
                <label class="p_cbutton ps_p_button_75"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>75.png"/></label>
                <label class="p_cbutton ps_p_button_76"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>76.png"/></label>
                <label class="p_cbutton ps_p_button_77"><input type="radio" name="button_style"
                                                               value="<?php echo $buttons_url; ?>77.png"/></label>
            </div>
        </div>
    </div>
<?php }

function wpm_mce_settings_docs()
{ ?>
    <div id="wpm-docs-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=-XCJhRmLz2A&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=4')">Видео
                    урок</a></span><input
                type="button" id="wpm-docs-submit" class="button-primary" value="Вставить форму" name="submit"/>
        </div>

        <div class="coach_form coach_box">
            <label for="googleform-key">URL Адрес<br>
                <input type="text" id="googleform-key" name="key" class="width_100p" value=""/></label><br><br>
            <label for="googleform-height"><input type="number" id="googleform-height" name="height" value="500"
                                                  style="width:100px"/> Высота </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label for="googleform-width"><input type="number" id="googleform-width" name="width" value="600"
                                                 style="width:100px"/> Ширина </label><br><br>
        </div>
        <div style="display:none" id="temp-form-code"></div>
    </div>
<?php }

function wpm_mce_settings_mail()
{ ?>
    <div id="wpm-main-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=546KJehwzzw&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=5')">Видео
                    урок</a></span><input
                type="button" id="wpm-mail-submit" class="button-primary" value="Вставить" name="submit"/>
        </div>
        <div class="ps_subscription_form coach_box">
            <ul>
                <li>
                    <label class="wpm_subsc_thumb subsc_getresponse">
                        <input type="radio" name="wpm_subscription" value="getresponse"/>
                    </label>
                </li>
                <li>
                    <label class="wpm_subsc_thumb subsc_mailchimp">
                        <input type="radio" name="wpm_subscription" value="mailchimp"/>
                    </label>
                </li>
                <li>
                    <label class="wpm_subsc_thumb subsc_justclick">
                        <input type="radio" name="wpm_subscription" value="justclick"/>
                    </label>
                </li>
                <li>
                    <label class="wpm_subsc_thumb subsc_unisender">
                        <input type="radio" name="wpm_subscription" value="unisender"/>
                    </label>
                </li>
                <li>
                    <label class="wpm_subsc_thumb subsc_smartresponder">
                        <input type="radio" name="wpm_subscription" value="smartresponder"/>
                    </label>
                </li>
            </ul>
        </div>
    </div>
<?php }

function wpm_mce_settings_list()
{ ?>
    <div id="wpm-list-form" class="">
        <div class="wpm-top-popup-nav">
           <span class="wpm-helper-box"><a
                   onclick="wpm_open_help_win('http://www.youtube.com/watch?v=vEPf5_ltmnQ&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=7')">Видео
                   урок</a></span><input
                type="button" id="wpm-list-submit" class="button-primary bullets_submit" value="Вставить"
                name="submit"/>

        </div>
        <div class="ps_bullets_form coach_box">
            <div>
                <span>Маленькие (16px)</span>
                <br>
                <label class="ps_bullet ps_bullet_1 wpm_checked"><input type="radio" name="bullet_style" value="1"
                                                                        checked="checked"/></label>
                <label class="ps_bullet ps_bullet_2"><input type="radio" name="bullet_style" value="2"/></label>
                <label class="ps_bullet ps_bullet_3"><input type="radio" name="bullet_style" value="3"/></label>
                <label class="ps_bullet ps_bullet_4"><input type="radio" name="bullet_style" value="4"/></label>
                <label class="ps_bullet ps_bullet_5"><input type="radio" name="bullet_style" value="5"/></label>
                <label class="ps_bullet ps_bullet_6"><input type="radio" name="bullet_style" value="6"/></label>
                <label class="ps_bullet ps_bullet_7"><input type="radio" name="bullet_style" value="7"/></label>
                <label class="ps_bullet ps_bullet_8"><input type="radio" name="bullet_style" value="8"/></label>
                <label class="ps_bullet ps_bullet_9"><input type="radio" name="bullet_style" value="9"/></label>
                <label class="ps_bullet ps_bullet_10"><input type="radio" name="bullet_style" value="10"/></label>
                <label class="ps_bullet ps_bullet_11"><input type="radio" name="bullet_style" value="11"/></label>
                <label class="ps_bullet ps_bullet_12"><input type="radio" name="bullet_style" value="12"/></label>
                <label class="ps_bullet ps_bullet_13"><input type="radio" name="bullet_style" value="13"/></label>
                <label class="ps_bullet ps_bullet_14"><input type="radio" name="bullet_style" value="14"/></label>
                <label class="ps_bullet ps_bullet_15"><input type="radio" name="bullet_style" value="15"/></label>
                <label class="ps_bullet ps_bullet_16"><input type="radio" name="bullet_style" value="16"/></label>
                <label class="ps_bullet ps_bullet_17"><input type="radio" name="bullet_style" value="17"/></label>
                <label class="ps_bullet ps_bullet_18"><input type="radio" name="bullet_style" value="18"/></label>
                <label class="ps_bullet ps_bullet_23"><input type="radio" name="bullet_style" value="23"/></label>
                <label class="ps_bullet ps_bullet_25"><input type="radio" name="bullet_style" value="25"/></label>
                <label class="ps_bullet ps_bullet_26"><input type="radio" name="bullet_style" value="26"/></label>
                <label class="ps_bullet ps_bullet_27"><input type="radio" name="bullet_style" value="27"/></label>
                <label class="ps_bullet ps_bullet_28"><input type="radio" name="bullet_style" value="28"/></label>
                <label class="ps_bullet ps_bullet_29"><input type="radio" name="bullet_style" value="29"/></label>
                <label class="ps_bullet ps_bullet_30"><input type="radio" name="bullet_style" value="30"/></label>
                <label class="ps_bullet ps_bullet_31"><input type="radio" name="bullet_style" value="31"/></label>
                <label class="ps_bullet ps_bullet_32"><input type="radio" name="bullet_style" value="32"/></label>
                <label class="ps_bullet ps_bullet_33"><input type="radio" name="bullet_style" value="33"/></label>
                <br><br>
                <span>Средние (24px)</span>
                <br>
                <label class="ps_bullet ps_bullet_24_1"><input type="radio" name="bullet_style"
                                                               value="24_1 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_2"><input type="radio" name="bullet_style"
                                                               value="24_2 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_3"><input type="radio" name="bullet_style"
                                                               value="24_3 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_4"><input type="radio" name="bullet_style"
                                                               value="24_4 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_5"><input type="radio" name="bullet_style"
                                                               value="24_5 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_6"><input type="radio" name="bullet_style"
                                                               value="24_6 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_7"><input type="radio" name="bullet_style"
                                                               value="24_7 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_8"><input type="radio" name="bullet_style"
                                                               value="24_8 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_9"><input type="radio" name="bullet_style"
                                                               value="24_9 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_10"><input type="radio" name="bullet_style"
                                                                value="24_10 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_11"><input type="radio" name="bullet_style"
                                                                value="24_11 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_12"><input type="radio" name="bullet_style"
                                                                value="24_12 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_13"><input type="radio" name="bullet_style"
                                                                value="24_13 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_14"><input type="radio" name="bullet_style"
                                                                value="24_14 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_15"><input type="radio" name="bullet_style"
                                                                value="24_15 middle_bullets"/></label>
                <label class="ps_bullet ps_bullet_24_16"><input type="radio" name="bullet_style"
                                                                value="24_16 middle_bullets"/></label>
                <br><br>
                <span>Большие (32px)</span>
                <br>
                <label class="ps_bullet ps_bullet_big_1 big_bullet"><input type="radio" name="bullet_style"
                                                                           value="big_1 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_2 big_bullet"><input type="radio" name="bullet_style"
                                                                           value="big_2 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_3 big_bullet"><input type="radio" name="bullet_style"
                                                                           value="big_3 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_4 big_bullet"><input type="radio" name="bullet_style"
                                                                           value="big_4 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_5 big_bullet"><input type="radio" name="bullet_style"
                                                                           value="big_5 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_6 big_bullet"><input type="radio" name="bullet_style"
                                                                           value="big_6 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_7 big_bullet"><input type="radio" name="bullet_style"
                                                                           value="big_7 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_8 big_bullet"><input type="radio" name="bullet_style"
                                                                           value="big_8 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_9 big_bullet"><input type="radio" name="bullet_style"
                                                                           value="big_9 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_10 big_bullet"><input type="radio" name="bullet_style"
                                                                            value="big_10 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_11 big_bullet"><input type="radio" name="bullet_style"
                                                                            value="big_11 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_12 big_bullet"><input type="radio" name="bullet_style"
                                                                            value="big_12 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_13 big_bullet"><input type="radio" name="bullet_style"
                                                                            value="big_13 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_14 big_bullet"><input type="radio" name="bullet_style"
                                                                            value="big_14 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_15 big_bullet"><input type="radio" name="bullet_style"
                                                                            value="big_15 big_bullets"/></label>
                <label class="ps_bullet ps_bullet_big_16 big_bullet"><input type="radio" name="bullet_style"
                                                                            value="big_16 big_bullets"/></label>
                <a class="nice-button" href="http://service.grafikator.ru/signup"
                   target="_blank">Новый сервис Grafikator.ru сделает ваши страницы еще красивее!</a>
            </div>
        </div>
    </div>
<?php }

function wpm_mce_settings_ribbon()
{ ?>
    <div id="wpm-ribbon-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=_eEixBWq_6U&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=8')">Видео
                    урок</a></span><input
                type="button" id="wpm-ribbon-submit" class="button-primary bonus_submit" value="Вставить"
                name="submit"/>

        </div>
        <div class="ps_bonus_form coach_box"><br>

            <div>
                <label class="ps_bonus ps_bonus_1 wpm_checked"><input type="radio" name="bonus_style" checked="checked"
                                                                      value="1"/></label>
                <label class="ps_bonus ps_bonus_2"><input type="radio" name="bonus_style" value="2"/></label>
                <label class="ps_bonus ps_bonus_3"><input type="radio" name="bonus_style" value="3"/></label>
                <label class="ps_bonus ps_bonus_4"><input type="radio" name="bonus_style" value="4"/></label>
                <label class="ps_bonus ps_bonus_5"><input type="radio" name="bonus_style" value="5"/></label>
                <label class="ps_bonus ps_bonus_6"><input type="radio" name="bonus_style" value="6"/></label>
                <label class="ps_bonus ps_bonus_7"><input type="radio" name="bonus_style" value="7"/></label>
                <label class="ps_bonus ps_bonus_8"><input type="radio" name="bonus_style" value="8"/></label>
                <label class="ps_bonus ps_bonus_9"><input type="radio" name="bonus_style" value="9"/></label>
                <label class="ps_bonus ps_bonus_10"><input type="radio" name="bonus_style" value="10"/></label>
                <label class="ps_bonus ps_bonus_11"><input type="radio" name="bonus_style" value="11"/></label>
                <label class="ps_bonus ps_bonus_12"><input type="radio" name="bonus_style" value="12"/></label>
                <label class="ps_bonus ps_bonus_13"><input type="radio" name="bonus_style" value="13"/></label>
                <label class="ps_bonus ps_bonus_14"><input type="radio" name="bonus_style" value="14"/></label>
                <label class="ps_bonus ps_bonus_15"><input type="radio" name="bonus_style" value="15"/></label>
                <label class="ps_bonus ps_bonus_16"><input type="radio" name="bonus_style" value="16"/></label>
                <label class="ps_bonus ps_bonus_17"><input type="radio" name="bonus_style" value="17"/></label>
                <label class="ps_bonus ps_bonus_18"><input type="radio" name="bonus_style" value="18"/></label>
                <label class="ps_bonus ps_bonus_19"><input type="radio" name="bonus_style" value="19"/></label>
                <label class="ps_bonus ps_bonus_20"><input type="radio" name="bonus_style" value="20"/></label>
                <label class="ps_bonus ps_bonus_21"><input type="radio" name="bonus_style" value="21"/></label>
                <label class="ps_bonus ps_bonus_22"><input type="radio" name="bonus_style" value="22"/></label>
                <label class="ps_bonus ps_bonus_23"><input type="radio" name="bonus_style" value="23"/></label>
            </div>
            <div class="clearfix"></div>
            <a class="nice-button" href="http://service.grafikator.ru/signup"
               target="_blank">Новый сервис Grafikator.ru сделает ваши страницы еще красивее!</a>
        </div>
    </div>
<?php }

function wpm_mce_settings_arrow()
{ ?>
    <div id="wpm-arrow-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=Umic0RCOK1A&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=9')">Видео
                    урок</a></span>
            <input
                type="button" id="wpm-arrow-submit" class="button-primary arrows_submit" value="Вставить"
                name="submit"/>

        </div>
        <div class="ps_arrows_form coach_box"><br>

            <div>
                <label class="ps_arrows ps_arrows_1"><input type="radio" name="arrow" value="1"/></label>
                <label class="ps_arrows ps_arrows_2"><input type="radio" name="arrow" value="2"/></label>
                <label class="ps_arrows ps_arrows_3"><input type="radio" name="arrow" value="3"/></label>
                <label class="ps_arrows ps_arrows_4"><input type="radio" name="arrow" value="4"/></label>
                <label class="ps_arrows ps_arrows_5"><input type="radio" name="arrow" value="5"/></label>
                <label class="ps_arrows ps_arrows_6"><input type="radio" name="arrow" value="6"/></label>
                <label class="ps_arrows ps_arrows_7"><input type="radio" name="arrow" value="7"/></label>
                <label class="ps_arrows ps_arrows_8"><input type="radio" name="arrow" value="8"/></label>
                <label class="ps_arrows ps_arrows_9"><input type="radio" name="arrow" value="9"/></label>
                <label class="ps_arrows ps_arrows_10"><input type="radio" name="arrow" value="10"/></label>
                <label class="ps_arrows ps_arrows_11"><input type="radio" name="arrow" value="11"/></label>
                <label class="ps_arrows ps_arrows_12"><input type="radio" name="arrow" value="12"/></label>
                <label class="ps_arrows ps_arrows_13"><input type="radio" name="arrow" value="13"/></label>
                <label class="ps_arrows ps_arrows_14"><input type="radio" name="arrow" value="14"/></label>
                <label class="ps_arrows ps_arrows_15"><input type="radio" name="arrow" value="15"/></label>
                <label class="ps_arrows ps_arrows_16"><input type="radio" name="arrow" value="16"/></label>
                <label class="ps_arrows ps_arrows_17"><input type="radio" name="arrow" value="17"/></label>
                <label class="ps_arrows ps_arrows_18"><input type="radio" name="arrow" value="18"/></label>
                <label class="ps_arrows ps_arrows_19"><input type="radio" name="arrow" value="19"/></label>
                <label class="ps_arrows ps_arrows_20"><input type="radio" name="arrow" value="20"/></label>
                <label class="ps_arrows ps_arrows_21"><input type="radio" name="arrow" value="21"/></label>
                <label class="ps_arrows ps_arrows_22"><input type="radio" name="arrow" value="22"/></label>
                <label class="ps_arrows ps_arrows_23"><input type="radio" name="arrow" value="23"/></label>
                <label class="ps_arrows ps_arrows_24"><input type="radio" name="arrow" value="24"/></label>
                <label class="ps_arrows ps_arrows_25"><input type="radio" name="arrow" value="25"/></label>
                <label class="ps_arrows ps_arrows_26"><input type="radio" name="arrow" value="26"/></label>
                <label class="ps_arrows ps_arrows_27"><input type="radio" name="arrow" value="27"/></label>
                <label class="ps_arrows ps_arrows_28"><input type="radio" name="arrow" value="28"/></label>
                <label class="ps_arrows ps_arrows_29"><input type="radio" name="arrow" value="29"/></label>
                <label class="ps_arrows ps_arrows_30"><input type="radio" name="arrow" value="30"/></label>
                <label class="ps_arrows ps_arrows_31"><input type="radio" name="arrow" class="gif"
                                                             value="31"/></label>
                <label class="ps_arrows ps_arrows_32"><input type="radio" name="arrow" class="gif"
                                                             value="32"/></label>
                <label class="ps_arrows ps_arrows_33"><input type="radio" name="arrow" class="gif"
                                                             value="33"/></label>
                <label class="ps_arrows ps_arrows_34"><input type="radio" name="arrow" value="34"/></label>
                <label class="ps_arrows ps_arrows_35"><input type="radio" name="arrow" value="35"/></label>
                <label class="ps_arrows ps_arrows_36"><input type="radio" name="arrow" value="36"/></label>
                <label class="ps_arrows ps_arrows_37"><input type="radio" name="arrow" value="37"/></label>
                <label class="ps_arrows ps_arrows_38"><input type="radio" name="arrow" value="38"/></label>
                <label class="ps_arrows ps_arrows_39"><input type="radio" name="arrow" value="39"/></label>
                <label class="ps_arrows ps_arrows_40"><input type="radio" name="arrow" value="40"/></label>
                <label class="ps_arrows ps_arrows_41"><input type="radio" name="arrow" value="41"/></label>
                <label class="ps_arrows ps_arrows_42"><input type="radio" name="arrow" value="42"/></label>
                <label class="ps_arrows ps_arrows_43"><input type="radio" name="arrow" value="43"/></label>
                <label class="ps_arrows ps_arrows_44"><input type="radio" name="arrow" value="44"/></label>
                <label class="ps_arrows ps_arrows_45"><input type="radio" name="arrow" value="45"/></label>
                <label class="ps_arrows ps_arrows_46"><input type="radio" name="arrow" value="46"/></label>
                <label class="ps_arrows ps_arrows_47"><input type="radio" name="arrow" value="47"/></label>
                <label class="ps_arrows ps_arrows_48"><input type="radio" name="arrow" value="48"/></label>
                <label class="ps_arrows ps_arrows_49"><input type="radio" name="arrow" value="49"/></label>
                <label class="ps_arrows ps_arrows_50"><input type="radio" name="arrow" value="50"/></label>
                <label class="ps_arrows ps_arrows_51"><input type="radio" name="arrow" value="51"/></label>
                <label class="ps_arrows ps_arrows_52"><input type="radio" name="arrow" value="52"/></label>
                <label class="ps_arrows ps_arrows_53"><input type="radio" name="arrow" value="53"/></label>
                <label class="ps_arrows ps_arrows_54"><input type="radio" name="arrow" value="54"/></label>
                <label class="ps_arrows ps_arrows_55"><input type="radio" name="arrow" value="55"/></label>
                <label class="ps_arrows ps_arrows_56"><input type="radio" name="arrow" value="56"/></label>
                <label class="ps_arrows ps_arrows_57"><input type="radio" name="arrow" value="57"/></label>
                <label class="ps_arrows ps_arrows_58"><input type="radio" name="arrow" value="58"/></label>
                <label class="ps_arrows ps_arrows_59"><input type="radio" name="arrow" value="59"/></label>
                <label class="ps_arrows ps_arrows_60"><input type="radio" name="arrow" value="60"/></label>
                <label class="ps_arrows ps_arrows_61"><input type="radio" name="arrow" value="61"/></label>
                <label class="ps_arrows ps_arrows_62"><input type="radio" name="arrow" value="62"/></label>
                <label class="ps_arrows ps_arrows_63"><input type="radio" name="arrow" value="63"/></label>
                <label class="ps_arrows ps_arrows_64"><input type="radio" name="arrow" value="64"/></label>
                <label class="ps_arrows ps_arrows_65"><input type="radio" name="arrow" value="65"/></label>
            </div>
            <div class="clearfix"></div>
            <a class="nice-button" href="http://service.grafikator.ru/signup"
               target="_blank">Новый сервис Grafikator.ru сделает ваши страницы еще красивее!</a>
        </div>

    </div>
<?php }

function wpm_mce_settings_box()
{ ?>
    <div id="wpm-box-form" class="">
        <div class="wpm-top-popup-nav">
             <span class="wpm-helper-box"><a
                     onclick="wpm_open_help_win('http://www.youtube.com/watch?v=Y_7snOfYato&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=10')">Видео
                     урок</a></span><input
                type="button" id="wpm-box-submit" class="button-primary textbox_submit" value="Вставить" name="submit"/>

        </div>
        <div class="ps_text_box_form coach_box"><br>

            <div>
                <label class="ps_text_box wpm_checkbox ps_text_box_1"><input type="radio" name="text_box_style"
                                                                             value="1"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_1_1"><input type="radio" name="text_box_style"
                                                                               value="1_1"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_2"><input type="radio" name="text_box_style"
                                                                             value="2"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_2_1"><input type="radio" name="text_box_style"
                                                                               value="2_1"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_3"><input type="radio" name="text_box_style"
                                                                             value="3"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_3_1"><input type="radio" name="text_box_style"
                                                                               value="3_1"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_4"><input type="radio" name="text_box_style"
                                                                             value="4"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_4_1"><input type="radio" name="text_box_style"
                                                                               value="4_1"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_5"><input type="radio" name="text_box_style"
                                                                             value="5"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_5_1"><input type="radio" name="text_box_style"
                                                                               value="5_1"/></label>
                <label class="ps_text_box wpm_checkbox ps_text_box_6"><input type="radio" name="text_box_style"
                                                                             value="6"/></label>
            </div>
            <div class="clearfix"></div>
            <a class="nice-button" href="http://service.grafikator.ru/signup"
               target="_blank">Новый сервис Grafikator.ru сделает ваши страницы еще красивее!</a>
        </div>
    </div>
<?php }

function wpm_mce_settings_satisfaction()
{ ?>
    <div id="wpm-satisfaction-form">
        <div class="wpm-top-popup-nav"><span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=r2m4_ifhKSY&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=11\')">Видео
                    урок</a></span><input type="button" id="wpm-satisfaction-submit" class="button-primary"
                                          value="Вставить" name="submit"/></div>
        <div class="ps_satisfaction_form coach_box">
            <div>
                <label class="ps_satisfaction ps_satisfaction_1 wpm_checked"><input type="radio" name="satisfaction"
                                                                                    value="1"
                                                                                    checked="checked"/></label>
                <label class="ps_satisfaction ps_satisfaction_2"><input type="radio" name="satisfaction"
                                                                        value="2"/></label>
                <label class="ps_satisfaction ps_satisfaction_3"><input type="radio" name="satisfaction"
                                                                        value="3"/></label>
                <label class="ps_satisfaction ps_satisfaction_4"><input type="radio" name="satisfaction"
                                                                        value="4"/></label>
                <label class="ps_satisfaction ps_satisfaction_5"><input type="radio" name="satisfaction"
                                                                        value="5"/></label>
                <label class="ps_satisfaction ps_satisfaction_6"><input type="radio" name="satisfaction"
                                                                        value="6"/></label>
                <label class="ps_satisfaction ps_satisfaction_7"><input type="radio" name="satisfaction"
                                                                        value="7"/></label>
                <label class="ps_satisfaction ps_satisfaction_8"><input type="radio" name="satisfaction"
                                                                        value="8"/></label>
                <label class="ps_satisfaction ps_satisfaction_9"><input type="radio" name="satisfaction"
                                                                        value="9"/></label>
                <label class="ps_satisfaction ps_satisfaction_10"><input type="radio" name="satisfaction"
                                                                         value="10"/></label>
                <label class="ps_satisfaction ps_satisfaction_11"><input type="radio" name="satisfaction"
                                                                         value="11"/></label>
                <label class="ps_satisfaction ps_satisfaction_12"><input type="radio" name="satisfaction"
                                                                         value="12"/></label>
                <label class="ps_satisfaction ps_satisfaction_13"><input type="radio" name="satisfaction"
                                                                         value="13"/></label>
                <label class="ps_satisfaction ps_satisfaction_14"><input type="radio" name="satisfaction"
                                                                         value="14"/></label>
                <label class="ps_satisfaction ps_satisfaction_15"><input type="radio" name="satisfaction"
                                                                         value="15"/></label>
                <label class="ps_satisfaction ps_satisfaction_16"><input type="radio" name="satisfaction"
                                                                         value="16"/></label>
                <label class="ps_satisfaction ps_satisfaction_17"><input type="radio" name="satisfaction"
                                                                         value="17"/></label>
                <label class="ps_satisfaction ps_satisfaction_18"><input type="radio" name="satisfaction"
                                                                         value="18"/></label>
                <label class="ps_satisfaction ps_satisfaction_19"><input type="radio" name="satisfaction"
                                                                         value="19"/></label>
                <label class="ps_satisfaction ps_satisfaction_20"><input type="radio" name="satisfaction"
                                                                         value="20"/></label>
                <label class="ps_satisfaction ps_satisfaction_21"><input type="radio" name="satisfaction"
                                                                         value="21"/></label>
                <label class="ps_satisfaction ps_satisfaction_22"><input type="radio" name="satisfaction"
                                                                         value="22"/></label>
                <label class="ps_satisfaction ps_satisfaction_23"><input type="radio" name="satisfaction"
                                                                         value="23"/></label>
                <label class="ps_satisfaction ps_satisfaction_24"><input type="radio" name="satisfaction"
                                                                         value="24"/></label>
                <label class="ps_satisfaction ps_satisfaction_25"><input type="radio" name="satisfaction"
                                                                         value="25"/></label>
                <label class="ps_satisfaction ps_satisfaction_26"><input type="radio" name="satisfaction"
                                                                         value="26"/></label>
                <label class="ps_satisfaction ps_satisfaction_27"><input type="radio" name="satisfaction"
                                                                         value="27"/></label>
                <label class="ps_satisfaction ps_satisfaction_28"><input type="radio" name="satisfaction"
                                                                         value="28"/></label>
                <label class="ps_satisfaction ps_satisfaction_29"><input type="radio" name="satisfaction"
                                                                         value="29"/></label>
                <label class="ps_satisfaction ps_satisfaction_30"><input type="radio" name="satisfaction"
                                                                         value="30"/></label>
                <label class="ps_satisfaction ps_satisfaction_31"><input type="radio" name="satisfaction"
                                                                         value="31"/></label>
                <label class="ps_satisfaction ps_satisfaction_32"><input type="radio" name="satisfaction"
                                                                         value="32"/></label>
                <label class="ps_satisfaction ps_satisfaction_33"><input type="radio" name="satisfaction"
                                                                         value="33"/></label>
                <label class="ps_satisfaction ps_satisfaction_34"><input type="radio" name="satisfaction"
                                                                         value="34"/></label>
                <label class="ps_satisfaction ps_satisfaction_35"><input type="radio" name="satisfaction"
                                                                         value="35"/></label>
                <a class="nice-button" href="http://service.grafikator.ru/signup" target="_blank">Новый сервис
                    Grafikator.ru сделает ваши страницы еще красивее!</a>
            </div>
        </div>
    </div>

<?php }

function wpm_mce_settings_review()
{ ?>
    <div id="wpm-review-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=YNJDDMk2cwA&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=12')">Видео
                    урок</a></span><input
                type="button" id="wpm-review-submit" class="button-primary" value="Вставить" name="submit"/>
        </div>
        <div class="ps_review_form coach_box">
            <div>
                <label class="ps_review_box wpm_checkbox ps_review_1"><input type="radio" name="review_style"
                                                                             value="1"/></label>
                <label class="ps_review_box wpm_checkbox ps_review_2"><input type="radio" name="review_style"
                                                                             value="2"/></label>
                <label class="ps_review_box wpm_checkbox ps_review_3"><input type="radio" name="review_style"
                                                                             value="3"/></label>
                <label class="ps_review_box wpm_checkbox ps_review_4"><input type="radio" name="review_style"
                                                                             value="4"/></label>
                <label class="ps_review_box wpm_checkbox ps_review_5"><input type="radio" name="review_style"
                                                                             value="5"/></label>
                <label class="ps_review_box wpm_checkbox ps_review_6"><input type="radio" name="review_style"
                                                                             value="6"/></label>
                <label class="ps_review_box wpm_checkbox ps_review_7"><input type="radio" name="review_style"
                                                                             value="7"/></label>
                <label class="ps_review_box wpm_checkbox ps_review_8"><input type="radio" name="review_style"
                                                                             value="8"/></label>
                <label class="ps_review_box wpm_checkbox ps_review_9"><input type="radio" name="review_style"
                                                                             value="9"/></label>
                <label class="ps_review_box wpm_checkbox ps_review_10"><input type="radio" name="review_style"
                                                                              value="10"/></label>
                <a class="nice-button" href="http://service.grafikator.ru/signup"
                   target="_blank">Новый сервис Grafikator.ru сделает ваши страницы еще красивее!</a>
            </div>
        </div>
    </div>
<?php }

function wpm_mce_settings_header()
{ ?>
    <div id="wpm-header-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=zIzUMUOJzug&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=13')">Видео
                    урок</a></span><input
                type="button" id="wpm-header-submit" class="button-primary" value="Вставить" name="submit"/>

        </div>

        <div class="wpp_header_form coach_box">
            <div>
                <label class="wpp_header wpp_header_1 wpm_checked"><input type="radio" name="header" value="1"
                                                                          checked="checked"/></label>
                <label class="wpp_header wpp_header_2"><input type="radio" name="header" value="2"/></label>
                <label class="wpp_header wpp_header_3"><input type="radio" name="header" value="3"/></label>
                <label class="wpp_header wpp_header_4"><input type="radio" name="header" value="4"/></label>
                <label class="wpp_header wpp_header_5"><input type="radio" name="header" value="5"/></label>
                <label class="wpp_header wpp_header_6"><input type="radio" name="header" value="6"/></label>
                <label class="wpp_header wpp_header_7"><input type="radio" name="header" value="7"/></label>
                <label class="wpp_header wpp_header_8"><input type="radio" name="header" value="8"/></label>
                <label class="wpp_header wpp_header_9"><input type="radio" name="header" value="9"/></label>
                <label class="wpp_header wpp_header_10"><input type="radio" name="header" value="10"/></label>
                <label class="wpp_header wpp_header_11"><input type="radio" name="header" value="11"/></label>
                <label class="wpp_header wpp_header_12"><input type="radio" name="header" value="12"/></label>
                <label class="wpp_header wpp_header_13"><input type="radio" name="header" value="13"/></label>
                <label class="wpp_header wpp_header_14"><input type="radio" name="header" value="14"/></label>
                <label class="wpp_header wpp_header_15"><input type="radio" name="header" value="15"/></label>
                <label class="wpp_header wpp_header_16"><input type="radio" name="header" value="16"/></label>
                <label class="wpp_header wpp_header_17"><input type="radio" name="header" value="17"/></label>
                <label class="wpp_header wpp_header_18"><input type="radio" name="header" value="18"/></label>
                <label class="wpp_header wpp_header_19"><input type="radio" name="header" value="19"/></label>
                <label class="wpp_header wpp_header_20"><input type="radio" name="header" value="20"/></label>
                <label class="wpp_header wpp_header_21"><input type="radio" name="header" value="21"/></label>
                <label class="wpp_header wpp_header_22"><input type="radio" name="header" value="22"/></label>
                <label class="wpp_header wpp_header_23"><input type="radio" name="header" value="23"/></label>
                <label class="wpp_header wpp_header_24"><input type="radio" name="header" value="24"/></label>
                <a class="nice-button" href="http://service.grafikator.ru/signup"
                   target="_blank">Новый сервис Grafikator.ru сделает ваши страницы еще красивее!</a>
            </div>
        </div>
    </div>
<?php }

function wpm_mce_settings_timer()
{ ?>
    <div id="wpm-timer-form">

        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=u0eBt5cj7m0&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=14')">Видео
                    урок</a></span><input
                type="button" id="wpm-timer-submit" class="button-primary" value="Вставить таймер" name="submit"
            />
        </div>
        <div class="countdown-settings coach_form coach_box countdown_box">
            <div class="wpm_inner_tabs c-type-tabs">
                <div class="c-tabs popup-innter-tab-nav">
                    <label class=""><input type="radio" name="c-type" value="fixed"
                                           checked="checked"
                                           tab="count-down-tabs-1">Фиксированая дата</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;<label class=""><input type="radio" name="c-type" value="interval"
                                                                   tab="count-down-tabs-2">Отсчет от первого
                        захода</label>
                </div>
                <div id="count-down-tabs-1" class="popup-inner-tab-content tab">
                    <div class="">
                        <label><input type="text" name="c-date" id="c-date" value="" size="10"/> Дата</label> &nbsp;&nbsp;&nbsp;&nbsp;
                        <label><input type="text" id="c-time" name="c-time" value="" size="5"/> Время </label>
                    </div>
                    <br>

                    <div class="wpm-row">
                        <p>По истечению времени:</p>

                        <p><label class=""><input type="radio" value="hide" name="c-redirect-fixed" checked="checked">Не
                                отображать</label>
                        </p>

                        <p><label class=""><input type="radio" value="redirect"
                                                  name="c-redirect-fixed">Переадресация на страницу</label>
                            <input type="text" name="c-redirect-fixed-url" placeholder="http://wppage.ru" value=""
                                   size="30"></p><br>
                    </div>
                </div>
                <div id="count-down-tabs-2" class="popup-inner-tab-content tab">
                    <div class="c-date-time-wrap">
                        <label><input type="text" id="c-days" name="c-days" value="" size="4"/> Дней</label> &nbsp;&nbsp;&nbsp;&nbsp;
                        <label><input type="text" id="c-hours" name="c-hours" value=""
                                      size="2"/> Часов</label> &nbsp;&nbsp;&nbsp;&nbsp;
                        <label><input type="text" id="c-minutes" name="c-minutes" value="" size="2"/> Минут</label>
                    </div>
                    <br>

                    <div class="wpm-row">
                        <p>По истечению времени:</p>

                        <p><label class=""><input type="radio" value="hide"
                                                  name="c-redirect-interval"
                                                  checked="checked">Не отображать</label>
                        </p>

                        <p><label class=""><input type="radio" value="renew"
                                                  name="c-redirect-interval">Включить таймер заново</label>
                        </p>

                        <p><label class=""><input type="radio" value="redirect"
                                                  name="c-redirect-interval">Переадресация на страницу</label> &nbsp;&nbsp;
                            <input type="text" name="c-redirect-interval-url" placeholder="http://wppage.ru"
                                   value=""
                                   size="30"></p><br>
                    </div>
                </div>
            </div>
            <br>

            <div class="c-tabs wpm_inner_tabs">
                <ul class="popup-innter-tab-nav">
                    <li><a href="#c-design-tabs-1">Дизайн</a></li>
                    <li><a href="#c-design-tabs-2">Изображение</a></li>
                </ul>
                <div id="c-design-tabs-1" class="popup-inner-tab-content">
                    <div class="">
                        <label class=""><input type="radio" name="c-size" value="big"
                                               checked="checked">Большой</label> &nbsp;&nbsp;
                        <label class=""><input type="radio" name="c-size"
                                               value="medium">Средний</label> &nbsp;&nbsp;
                        <label class=""><input type="radio" name="c-size"
                                               value="small">Маленький</label>
                    </div>
                    <div class="c-colors">
                        <label class="c-color ps_timer_image color-1 wpp_radio wpm_checked"><input type="radio"
                                                                                                   name="c-color"
                                                                                                   value="1"
                                                                                                   checked="checked"></label>
                        <label class="c-color ps_timer_image color-2 wpp_radio"><input type="radio" name="c-color"
                                                                                       value="2"></label>
                        <label class="c-color ps_timer_image color-3 wpp_radio"><input type="radio" name="c-color"
                                                                                       value="3"></label>
                        <label class="c-color ps_timer_image color-4 wpp_radio"><input type="radio" name="c-color"
                                                                                       value="4"></label>
                        <label class="c-color ps_timer_image color-5 wpp_radio"><input type="radio" name="c-color"
                                                                                       value="5"></label>
                        <label class="c-color ps_timer_image color-6 wpp_radio"><input type="radio" name="c-color"
                                                                                       value="6"></label>
                        <label class="c-color ps_timer_image color-7 wpp_radio"><input type="radio" name="c-color"
                                                                                       value="7"></label>
                        <label class="c-color ps_timer_image color-8 wpp_radio"><input type="radio" name="c-color"
                                                                                       value="8"></label>
                    </div>
                    <div class="c-design color-1" current-color="color-1">
                        <label class="ps_timer_image c-design-1 wpm_checked"><input type="radio" name="c-design"
                                                                                    value="1"
                                                                                    checked="checked"></label>
                        <label class="ps_timer_image c-design-2"><input type="radio" name="c-design"
                                                                        value="2"></label>
                        <label class="ps_timer_image c-design-3"><input type="radio" name="c-design"
                                                                        value="3"></label>
                        <label class="ps_timer_image c-design-4"><input type="radio" name="c-design"
                                                                        value="4"></label>
                        <label class="ps_timer_image c-design-5"><input type="radio" name="c-design"
                                                                        value="5"></label>
                        <label class="ps_timer_image c-design-6"><input type="radio" name="c-design"
                                                                        value="6"></label>
                        <label class="ps_timer_image c-design-7"><input type="radio" name="c-design"
                                                                        value="7"></label>
                        <label class="ps_timer_image c-design-8"><input type="radio" name="c-design"
                                                                        value="8"></label>
                    </div>
                </div>
                <div id="c-design-tabs-2" class="popup-inner-tab-content">
                    <p><label class=""><input type="checkbox" name="c-use-image"
                                              value="">Использовать изображение</label></p>

                    <div class="c-design">
                        <label class="ps_timer_image timer_image_1 wpm_checked"><input type="radio"
                                                                                       name="c-image"
                                                                                       value="1"
                                                                                       checked="checked"></label>
                        <label class="ps_timer_image timer_image_2"><input type="radio" name="c-image"
                                                                           value="2"></label>
                        <label class="ps_timer_image timer_image_3"><input type="radio" name="c-image"
                                                                           value="3"></label>
                        <label class="ps_timer_image timer_image_4"><input type="radio" name="c-image"
                                                                           value="4"></label>
                        <label class="ps_timer_image timer_image_5"><input type="radio" name="c-image"
                                                                           value="5"></label>
                        <label class="ps_timer_image timer_image_6"><input type="radio" name="c-image"
                                                                           value="6"></label>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(function ($) {
                $.datepicker.setDefaults(jQuery.datepicker.regional['ru']);
                $("#c-date").datepicker({dateFormat: "yy-mm-dd"});
                $('#c-time').timepicker({
                    showMinutes: true,
                    showPeriod: false,
                    showPeriodLabels: false,
                    hourText: 'Часы',
                    minuteText: 'Минуты'
                });
                $('#c-days').timepicker({

                    hours: {
                        starts: 0,
                        ends: 30
                    },
                    rows: 5,
                    showMinutes: false,
                    showPeriod: false,
                    showPeriodLabels: false,
                    hourText: 'Дней'
                });
                $('#c-hours').timepicker({
                    showMinutes: false,
                    showPeriod: false,
                    showPeriodLabels: false,
                    hourText: 'Часов'
                });
                $('#c-minutes').timepicker({
                    showMinutes: true,
                    showHours: false,
                    showPeriod: false,
                    showPeriodLabels: false,
                    minuteText: 'Минут'
                });


                $('input[name=c-type]').live('change', function () {
                    jQuery('.c-type-tabs .tab').css('display', 'none');
                    jQuery('#' + jQuery(this).attr('tab')).css('display', 'block');
                });

                $('input[name=c-color]').live('change', function () {
                    jQuery('.c-design').removeClass(jQuery('.c-design').attr('current-color')).addClass('color-' + jQuery(this).val());
                    jQuery('.c-design').attr('current-color', 'color-' + jQuery(this).val());
                });

                $('.c-tabs').tabs();
            });
        </script>

    </div>
<?php }

function wpm_mce_settings_divider()
{ ?>
    <div id="wpm-divider-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a
                    onclick="wpm_open_help_win('http://www.youtube.com/watch?v=sYEuensDTNY&list=PLI8Gq0WzVWvJ60avoe8rMyfoV5qZr3Atm&index=15')">Видео
                    урок</a></span><input
                type="button" id="wpm-divider-submit" class="button-primary" value="Вставить" name="submit"/>
        </div>

        <div class="ps_divider_form coach_box"><br>

            <div class="divide_width_slider_box">
                <input type="text" name="ps_divide_width" id="ps_divide_width" value="65"/> - <input type="text"
                                                                                                     name="ps_divide_width_2"
                                                                                                     id="ps_divide_width_2"><br><br>

                <div id="wpm-divide-slider" class="wpm-slider"></div>
                <br>
                <table class="table_preview">
                    <tr>
                        <td class="wpp_divide_first" style="width: 65%">текст<br>&nbsp;<br>&nbsp;<br>&nbsp;</td>
                        <td class="wpp_divide_second" style="width: 35%">текст<br>&nbsp;<br>&nbsp;</td>
                    </tr>
                </table>
                <script type="text/javascript">
                    jQuery(function ($) {
                        $("#wpm-divide-slider").slider({
                            value: 65,
                            min: 5,
                            max: 95,
                            step: 5,
                            slide: function (event, ui) {
                                $("#ps_divide_width").val(ui.value);
                                $("#ps_divide_width_2").val(100 - ui.value);
                                $(".wpp_divide_first").css({"width": ui.value + "%"});
                                $(".wpp_divide_second").css({"width": 100 - ui.value + "%"});
                            },
                            create: function (event, ui) {
                                $("#ps_divide_width").val(65);
                                $("#ps_divide_width_2").val(35);
                                $(".wpp_divide_first").css({"width": ui.value + "%"});
                                $(".wpp_divide_second").css({"width": 100 - ui.value + "%"});
                            }
                        });

                        $("#ps_divide_width").bind('change', function () {
                            $("#ps_divide_width_2").val(100 - $(this).val());
                        });
                        $("#ps_divide_width_2").bind('change', function () {
                            $("#ps_divide_width").val(100 - $(this).val());
                        });

                    });
                </script>
            </div>
        </div>
    </div>
<?php }

function wpm_mce_settings_infoprotector()
{ ?>
    <div id="wpm-infoprotector-form">
        <div class="wpm-top-popup-nav">
            <span class="wpm-helper-box"><a href="https://www.youtube.com/watch?v=pO4akacx4b4" target="_blank">Что это?</a></span><input
                type="button" id="wpm-infoprotector-submit" class="button-primary" value="Вставить" name="submit"/>
        </div>

        <div class="ps_infoprotector_form coach_box"><br>
            <input type="text" class="width_100p" name="infoprotector-link" id="ps_infoprotector_link" value=""
                   placeholder="<?php _e('Ссылка'); ?>"/><br>
        </div>
    </div>
<?php }