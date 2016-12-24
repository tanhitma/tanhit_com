<?php
class WPMShortcodes{
    private static $_galleryCounter = 0;

    public function __construct(){
        global $post;
        add_shortcode('wpm_product', array($this,'product_shortcode'));
        add_shortcode('wpm_countdown', array($this,'countdown_shortcode'));
        add_shortcode('wpm_countdown', array($this,'wpm_countdown_shortcode'));
        add_shortcode('wpm_smartresponder', array($this,'smartresponder_shortcode'));
        add_shortcode('wpm_getresponse', array($this,'getresponse_shortcode'));
        add_shortcode('wpm_mailchimp', array($this,'wpm_mailchimp_shortcode'));
        add_shortcode('wpm_unisender', array($this,'wpm_unisender_shortcode'));
        add_shortcode('wpm_justclick', array($this,'wpm_justclick_shortcode'));
        add_shortcode('wpm_socialbuttons', array($this,'socialbuttons_shortcode'));
        add_shortcode('wpm_googleform', array($this,'wpm_google_shortcode'));
        add_shortcode('wpm_video', array($this, 'wpm_video_shortcode'));
        add_shortcode('wpm_uppod', array($this, 'wpm_audio_shortcode'));
        add_shortcode('wpm_audio', array($this, 'wpm_audio_shortcode'));
        add_shortcode('wpm_ipr', array($this, 'wpm_ipr_shortcode'));
        add_shortcode('gallery', array($this, 'wpm_gallery_shortcode'));
    }

//------------------------------
    function wpm_gallery_shortcode($attr)
    {
        $post = get_post();

        if($post->post_type != 'wpm-page') {
            return gallery_shortcode($attr);
        }

        $output = '';

        if (empty($attr['ids'])) {
            return $output;
        }

        $args = array(
            'post_type'      => 'attachment',
            'numberposts'    => -1,
            'post_mime_type' => 'image',
            'order'          => 'ASC',
            'orderby'        => 'post__in',
            'post__in'       => explode(',', $attr['ids'])
        );

        $attachments = get_posts($args);


        if (empty($attachments)) {
            return $output;
        }

        $tpl = '<div class="gallery-item"><a href="%s" class="fancybox" rel="wpm_gallery_%d"><img src="%s"></a></div>';

        foreach ($attachments as $image) {
            $full_image = wp_get_attachment_image_src($image->ID, 'full', 0);
            $slide_image = wp_get_attachment_image_src($image->ID, 'wpm-slider', 0);

            $output .= sprintf($tpl, $full_image[0], self::$_galleryCounter, $slide_image[0]);
        }

        self::$_galleryCounter++;

        $script = 'jQuery(window).load(function(){jQuery(".owl-carousel").owlCarousel({loop:true,margin:10,nav:true,items:1,autoHeight:true,navText:["",""]});});';

        return sprintf('<div class="owl-carousel wpm-gallery-slider">%s</div><script>%s</script>', $output, $script);
    }

//------------------------------
    function smartresponder_shortcode($atts)
    {
        global $post;
        $page_meta = get_post_meta($post->ID, '_wpm_page_meta', true);
        $html = '<!-- smartresponder -->';
        $html .= $page_meta['subscription']['smartresponder'];
        $html .= '<!-- //smartresponder -->';
        return $html;
    }

//------------------------------

    function getresponse_shortcode($atts)
    {
        global $post;
        $page_meta = get_post_meta($post->ID, '_wpm_page_meta', true);
        $html = '<!-- getresponse -->';
        $html .= $page_meta['subscription']['getresponse'];
        $html .= '<!-- //getresponse -->';
        return $html;
    }

//--------------------------

    function wpm_mailchimp_shortcode($atts)
    {
        global $post;
        $page_meta = get_post_meta($post->ID, '_wpm_page_meta', true);
        $html = '<!-- mailchimp -->';
        $html .= $page_meta['subscription']['mailchimp'];
        $html .= '<!-- //mailchimp -->';
        return $html;
    }

//----------------------

    function wpm_unisender_shortcode($atts)
    {
        global $post;
        $page_meta = get_post_meta($post->ID, '_wpm_page_meta', true);
        $html = '<!-- unisender -->';
        $html .= '<div class="unisender_form_wrap">';
        $html .= $page_meta['subscription']['unisender'];
        $html .= '</div>';
        $html .= '<!-- //unisender -->';
        return $html;
    }

//-------------------

    function wpm_justclick_shortcode($atts)
    {
        global $post;
        $page_meta = get_post_meta($post->ID, '_wpm_page_meta', true);
        $replace = array(
            '<script language="JavaScript" src="/media/subscribe/helper2.js.php"></script>',
            '<script language="JavaScript">jc_setfrmfld()</script>'
        );
        $code = str_replace($replace, '', $page_meta['subscription']['justclick']);
        $html = '<!-- justclick -->';
        $html .= '<div class="justclick_form_wrap">';
        $html .= $code;
        $html .= '</div>';
        $html .= '<!-- //justclick -->';
        return $html;
    }

//---------------------

    function socialbuttons_shortcode($atts)
    {
        global $post;
        $vk_like_id = rand(1, 1000000);

        $seo_title = get_post_meta($post->ID, '_wpm_seo_title', true);
        $seo_desc = get_post_meta($post->ID, '_wpm_seo_desc', true);
        $vk_thumb_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'vk_thumb');

        $buttons = $atts['buttons'];
        $buttons = explode(',', $buttons);
        $vkontakte_apiId = get_option('vkontakte_apiId');

        $google_script = "<script type='text/javascript'>
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })(); </script>";

        $buttons_content['gplus'] = (in_array('gplus', $buttons)) ? '<div class="ps_float_box wpm_googleplus"><g:plusone
        callback="google_share"
        size="tall"></g:plusone>' . $google_script . '</div>' : '';

        $facebook_script = "<script type='text/javascript'>
    window.fbAsyncInit = function () {
        FB.init({
            status:true, // check login status
            cookie:true, // enable cookies to allow the server to access the session
            xfbml:true  // parse XFBML
        });
        FB.Event.subscribe('edge.create', function(response) {
                   share_ok();
                }
        );

    };
    // Load the SDK Asynchronously
    (function (d) {
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement('script');
        js.id = id;
        js.async = true;
        js.src = '//connect.facebook.net/ru_RU/all.js';
        ref.parentNode.insertBefore(js, ref);
    }(document));

</script>";

        $buttons_content['facebook'] = '<div class="ps_float_box wpm_facebook"><div id="fb-root"></div>' . $facebook_script . '<fb:like class="fb-like" data-send="false" data-layout="box_count" data-width="96" data-show-faces="false"
        data-action="like"></fb:like>
</div>';


        $buttons_content['twitter'] = '<div class="ps_float_box wpm_twitter"><a href="https://twitter.com/share?url=' . get_permalink() . '&amp;text=' . $seo_title . '" class="twitter-share-button" data-lang="ru" data-count="vertical">Твитнуть</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>';

        $vk_like_id++;

        $vk_like1 = (in_array('vk_like', $buttons)) ? '<div class="ps_float_box wpm_vk_like"><!-- LIKE VKONTAKTE -->
    <iframe name="fXDadddf" frameborder="0" src="http://vkontakte.ru/widget_like.php?app=' . $vkontakte_apiId . '&amp;page=0&amp;url=' . get_permalink() . '&amp;type=vertical&amp;verb=0&amp;title=' . $seo_title . '&amp;description=' . $seo_desc . '&amp;image=' . $vk_thumb_url[0] . '" width="41" height="52" scrolling="no" id="vkwidget3"></iframe>
    <!-- end of LIKE VKONTAKTE --></div>' : '';

        $buttons_content['vk_like'] = '<div class="ps_float_box wpm_vk_like"><div id="vk_like' . $vk_like_id . '"
        class="vk_like_box"></div>
        <script type="text/javascript">
        VK.Widgets.Like("vk_like' . $vk_like_id . '", {type: "vertical"});
        </script></div>';

        $buttons_content['vk_share'] = '<div class="ps_float_box wpm_vk_share">
    <!-- SHARE VKONTAKTE -->
    <script type="text/javascript">
    <!--
    document.write(VK.Share.button({
    url: "' . get_permalink() . '",
    title: "' . $seo_title . '",
    description: "' . $seo_desc . '",
    image: "' . $vk_thumb_url[0] . '",
    noparse: true
    }));
    -->
    </script>
    <!-- end of SHARE VKONTAKTE -->
    </div>';


        $buttons_content['mailru'] = '<div class="ps_float_box wpm_mailru"><a target="_blank"
        class="mrc__plugin_uber_like_button" href="http://connect.mail.ru/share?url=' . get_permalink() . '&amp;title=' . $seo_title . '&amp;description=' . $seo_desc . '&amp;imageurl=' . $vk_thumb_url[0] . '" data-mrc-config="{\'cm\' : \'3\', \'ck\' : \'3\', \'sz\' : \'20\', \'st\' : \'2\', \'tp\' : \'mm\', \'vt\' : \'1\'}">Нравится</a>
<script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="UTF-8"></script></div>';

        $buttons_content['odnoklasniki'] = '<div class="ps_float_box wpm_odnoklasniki"><a target="_blank" class="mrc__plugin_uber_like_button" href="http://connect.mail.ru/share?title=' . $seo_title . '&description=' . $seo_desc . '&url=' . get_permalink() . '&amp;imageurl=' . $vk_thumb_url[0] . '" data-mrc-config="{\'cm\' : \'1\', \'ck\' : \'1\', \'sz\' : \'20\', \'st\' : \'2\', \'tp\' : \'ok\', \'vt\' : \'1\'}">Нравится</a>
<script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="UTF-8"></script></div>';


        $buttons_content['linkedin'] = '<div class="ps_float_box wpm_linkedin"><script src="//platform.linkedin.com/in.js"
        type="text/javascript"></script>
<script type="IN/Share" data-counter="top" data-onsuccess="linkedin_share_click"></script></div>';

        $html = '<div class="ps_social_buttons">';
        foreach ($buttons as $button) {
            $html .= $buttons_content[$button];
        }
        $html .= '<div style="clear:both; float: none"></div></div>';
        return $html;


    }

//-----------------------

    function wpm_countdown_shortcode($atts)
    {

        $c_id = $atts['id'];
        $type = $atts['type'];
        $color = $atts['color'];
        $size = $atts['size'];
        $date = $atts['date'];
        $time = $atts['time'];
        $days = $atts['days'];
        $hours = $atts['hours'];
        $minutes = $atts['minutes'];
        $skin = $atts['skin'];
        $redirect = $atts['redirect'];
        $renew = $atts['renew'];
        $image = $atts['image'];

        $first_visit = "wpm_first_visit_".get_the_ID()."_c_".$c_id;

        $current_time = current_time( 'timestamp', 0 );
        $hide = false;
        $gmt_offset = get_option('gmt_offset');

        $timer_cookie = '';

        // get end time
        $countdown_time = '';
        if($type == 'fixed'){

            $countdown_time = strtotime("$date $time:00");

            if($current_time >= $countdown_time){
                if(!empty($redirect)){
                    wp_redirect($redirect);
                    die();
                }

                if( empty($redirect) ){
                    $hide = true;
                }
            }

        }elseif($type == 'interval'){
            //$c_time = strtotime("$atts[days] $atts[hours]:$atts[minutes]:00");

            $countdown_time = strtotime("+$days days $hours hours $minutes minutes", $current_time);

            if(!isset($_COOKIE[$first_visit]) || $_COOKIE[$first_visit] == '' ){
                //setcookie($first_visit, $countdown_time);
                $timer_cookie = '$.cookies.set("'.$first_visit.'","'.$countdown_time.'");';

            }else{

                if( $current_time >= $_COOKIE[$first_visit] ){

                    // if redirect enabled
                    if(!empty($redirect)){
                        echo "<script type='text/javascript'> window.location = '$redirect';</script>";
                        die();
                        //wp_redirect($redirect);
                        //die();
                    }
                    if(!empty($renew)){
                        // setcookie($first_visit, $countdown_time);
                        $timer_cookie = '$.cookies.set("'.$first_visit.'","'.$countdown_time.'");';
                    }
                    if(empty($renew) && empty($redirect)){
                        $hide = true;
                    }

                }else{
                    $countdown_time = $_COOKIE[$first_visit];
                }
            }

        }


        // check if redirection active
        if(!empty($redirect)){

            $c_redirect = ', expiryUrl: "'.$redirect.'"';
        }else{
            $c_redirect = ', onExpiry: function(){setTimeout(function(){location.reload();}, 2000)}';
        }

        // create timer image
        if(!empty($image)){
            $c_image = '<img class="c-image" src="http://static.wppage.ru/wppage/i/timer/'.$image.'.png">';
        }else{
            $c_image = '';
        }

        $skin_html = '<div id="countdown-'.$c_id.'" class="countdown"><div
        class="digits-wrap"><div
            class="digits-group"><span
                class="digit image-{d10}"></span><span
                class="digit image-{d1}"></span></div><span
                class="image-sep"></span><div
                class="digits-group"><span
                class="digit image-{h10}"></span><span
                class="digit image-{h1}"></span></div><span
                class="image-sep"></span><div
                class="digits-group"><span
                class="digit image-{m10}"></span><span
                class="digit image-{m1}"></span></div><span
                class="image-sep"></span><div
                class="digits-group"><span
                class="digit image-{s10}"></span><span
                class="digit image-{s1}"></span></div></div></div>';

        $html = '<div id="countdown-layout-wrap" class="countdown-wrap c-skin-'.$skin.' c-color-'.$color.' c-size-'.$size.'">'.$c_image;
        $html .= $skin_html;
        $html .= '<script type="text/javascript">
        var timeUntil_'.$c_id.' = new Date('.date("Y, m-1, d, H, i, s", $countdown_time).');
        jQuery(function($){
        '.$timer_cookie.'
        $("#countdown-'.$c_id.'").countdown({until: $.countdown.UTCDate('.$gmt_offset.' ,timeUntil_'.$c_id.'), compact: true, layout: $("#countdown-'.$c_id.'").html()'.$c_redirect.'}); });
        </script>';
        $html .= '</div>';

        if($hide) $html = '';

        return $html;
    }

//---------------------
    function countdown_shortcode($atts)
    {
        $c_id = rand(0,1000);

        $color = '1';
        $size = 'big';
        $date = $atts['date'];
        $hours = $atts['hours'];
        $minutes = $atts['minutes'];
        $skin = '1';
        $image = '';
        $c_redirect = '';

        $current_time = current_time( 'timestamp', 0 );
        $hide = false;
        $gmt_offset = get_option('gmt_offset');

        // get end time
        $c_time = strtotime("$date $hours:$minutes:00");

        if($current_time > $c_time){
            $hide = true;
        }
        // create timer image
        $c_image = '';

        $skin_html = '<div id="countdown-'.$c_id.'" class="countdown"><div
        class="digits-wrap"><div
            class="digits-group"><span
                class="digit image-{d10}"></span><span
                class="digit image-{d1}"></span></div><span
                class="image-sep"></span><div
                class="digits-group"><span
                class="digit image-{h10}"></span><span
                class="digit image-{h1}"></span></div><span
                class="image-sep"></span><div
                class="digits-group"><span
                class="digit image-{m10}"></span><span
                class="digit image-{m1}"></span></div><span
                class="image-sep"></span><div
                class="digits-group"><span
                class="digit image-{s10}"></span><span
                class="digit image-{s1}"></span></div></div></div>';

        $html = '<div id="countdown-layout-wrap" class="countdown-wrap c-skin-'.$skin.' c-color-'.$color.' c-size-'.$size.'">'.$c_image;
        $html .= $skin_html;
        $html .= '<script type="text/javascript">
        var timeUntil_'.$c_id.' = new Date('.date("Y, m-1, d, H, i, s", $c_time).');
        jQuery(function($){
        $("#countdown-'.$c_id.'").countdown({until: $.countdown.UTCDate('.$gmt_offset.' ,timeUntil_'.$c_id.'), compact: true, layout: $("#countdown-'.$c_id.'").html()'.$c_redirect.'}); });
        </script>';
        $html .= '</div>';

        if($hide) $html = '';

        return $html;
    }

    //-------------------

    function wpm_audio_shortcode($atts){

        $id = rand(0,1000);
        $html = '';

        if($atts['audio']){
            if($atts['autoplay'] == 'on'){
                $js = "<script type='text/javascript'>
                 jQuery(function($){
                        $('#audio-$id').mediaelementplayer({
                            success: function(mediaElement, domObject) {
                               mediaElement.play();
                            }
                        });
                 });
            </script>";
            }else{
                $js = "<script type='text/javascript'>
            jQuery(function($){
                $('#audio-$id').mediaelementplayer({
                success: function(mediaElement, domObject) {
                            }
                });
            });
            </script>";
            }
            $html = '<audio id="audio-'.$id.'" class="mbl-skin-'.$atts["color"].'" src="'.$atts["audio"].'" type="audio/mp3" controls="controls"></audio>'.$js;

        }
        return $html;
    }

    function wpm_video_shortcode($options)
    {
        return WPMVideoShortCode::parse($options);
    }

//--------------------

    function wpm_google_shortcode($options)
    {
        if ($options[key]) {
            return '<iframe src="http://spreadsheets.google.com/embeddedform?formkey=' . $options[key] . '" width="' . $options[width] . '" height="' . $options[height] . '" frameborder="0" marginheight="0" marginwidth="0">Загружается...</iframe>';
        } elseif($options[url]) {

            parse_str( parse_url( $options[url], PHP_URL_QUERY ), $google_form_vars );
            if(!$google_form_vars[formkey]){
                return '<iframe src="'.$options[url].'?embedded=true" width="' . $options[width] . '" height="' . $options[height] . '" frameborder="0" marginheight="0" marginwidth="0">Загружается...</iframe>';
            }elseif($google_form_vars[formkey]){
                return '<iframe src="http://spreadsheets.google.com/embeddedform?formkey=' . $google_form_vars[formkey] . '" width="' . $options[width] . '" height="' . $options[height] . '" frameborder="0" marginheight="0" marginwidth="0">Загружается...</iframe>';
            }


        }else{
            return "Ошибка при обработке формы";
        }

    }

//-------------------

    function product_shortcode($atts)
    {
        global $post;
        $product_id = $post->ID;
        $html = '<div class="order_button_wrap"><form action="/pidtverdzhennya-platezhu.html"><input type="hidden" name="product_id" value="' . $product_id . '" /><span><input type="submit" class="order_button" value="' . $atts['title'] . '"/></span></form></div>';
        return $html;
    }

    //-------------------

    function wpm_ipr_shortcode($atts)
    {
        global $post;

        $protected_video_link = str_replace(array('http://', 'https://'), 'infoprotector://', $atts['link']);

        $html = '<div class="text-center"><figure class="wpm-ipr-wrap"><a class="wpm-ipr-tut-link" href="'.$protected_video_link.'"><span class="wpm-ipr-play"></span><img src="'.plugins_url().'/member-luxe/templates/base/img/mbl-info.png"></a><figcaption class="wpm-ipr-button"><i class="glyphicon glyphicon-exclamation-sign"></i> Для воспроизведения видео пожалуйста <a target="_blank" href="http://infoprotector.ru/online/download"><span class="underline">установите плагин</span></a></figcaption></figure></div>';

        return $html;
    }
}

new WPMShortcodes;


