<!DOCTYPE html>
<?php
$main_options   = get_option('wpm_main_options');
$design_options = get_option('wpm_design_options');

$yt_protection_is_enabled = wpm_yt_protection_is_enabled ($main_options);

if($main_options['home_id']){
    $home_url = get_permalink($main_options['home_id']);
}else{
    $home_url = '';
}


//---------
$wpm_head_code = $wpm_body_code = $wpm_footer_code = '';

if(is_single()){
    $page_meta = get_post_meta($post->ID, '_wpm_page_meta', true);

    if(isset($page_meta['code'])){
        $wpm_head_code = stripcslashes(wpm_prepare_val($page_meta['code']['head']));
        $wpm_body_code = stripcslashes(wpm_prepare_val($page_meta['code']['body']));
        $wpm_footer_code = stripcslashes(wpm_prepare_val($page_meta['code']['footer']));
    }else{
        $wpm_head_code = $wpm_body_code = $wpm_footer_code = '';
    }

}
//----------

?>
<html <?php language_attributes(); ?> xmlns:og="http://ogp.me/ns#" itemscope itemtype="http://schema.org/Article">
<head>
    <meta name="generator" content="wpm <?php echo WP_MEMBERSHIP_VERSION; ?> | http://wpm.wppage.ru"/>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width"/>
    <?php
    $wpm_favicon = $main_options['favicon']['url'];
    if (!empty($wpm_favicon)) {
        $ext = pathinfo($wpm_favicon, PATHINFO_EXTENSION);
        if ($ext == 'ico') echo '<link rel="shortcut icon" href="' . $wpm_favicon . '" />';
        if ($ext == 'png') echo ' <link rel="icon" type="image/png" href="' . $wpm_favicon . '" />';
    } ?>

    <meta name="keywords" content="<?php echo $keywords; ?>">
    <meta name="description" content="<?php echo $desc; ?>">
    <title><?php

        $wp_title = substr(wp_title(' | ', false, 'right'), 0, -3);
        if(is_home() || is_front_page()) echo $wp_title;
        elseif(is_archive() || is_category()) single_cat_title();
        else the_title();

        ?></title>
    <?php
    wpm_head();
    ?>
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';

        function changeLinks (children)
        {
            var li_class = children.parent('.cat-item')
                .attr('class')
                .replace(' current-cat-parent', '')
                .replace('cat-item ', '.')
                .split(' ')[0];

            $(li_class).each(function(){
                var $this = $(this);

                if($this.find('>.plus').length) {
                    $this.find('>a').attr('href', '#');
                    $this.on('click', '>a', function(){
                        $this.find('>.plus').click();
                        return false;
                    });
                }
            })
        }

        function cc(data) {
            var val = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
            var o1, o2, o3, h1, h2, h3, h4, p, i = 0, res = '';

            do {
                h1 = val.indexOf(data.charAt(i++));
                h2 = val.indexOf(data.charAt(i++));
                h3 = val.indexOf(data.charAt(i++));
                h4 = val.indexOf(data.charAt(i++));
                p = h1 << 18 | h2 << 12 | h3 << 6 | h4;
                o1 = p >> 16 & 0xff;
                o2 = p >> 8 & 0xff;
                o3 = p & 0xff;
                if (h3 == 64)      res += String.fromCharCode(o1);
                else if (h4 == 64) res += String.fromCharCode(o1, o2);
                else               res += String.fromCharCode(o1, o2, o3);
            } while (i < data.length);

            return res;
        }

        jQuery(function ($) {
            //============
            $('.main-menu .children').each(function () {
                var children = $(this);
                if (children.is(':visible')) {
                    children.before('<span class="plus">-</span>');
                    changeLinks(children);
                } else {
                    children.before('<span class="plus">+</span>');
                    changeLinks(children);
                }
            });
            $('.main-menu .plus').on('click', function () {
                var plus = $(this);
                var childern = $(this).next('.children');
                $(this).next('.children').slideToggle('fast', function () {
                    if (childern.is(':visible')) {
                        plus.html('-');
                    } else {
                        plus.html('+');

                        $(this).find('.plus').each(function () {
                            var elem = $(this);
                            if(elem.html()=='-') {
                                elem.next('.children').slideToggle('fast');
                                elem.html('+');
                            }
                        });
                    }
                });
            });
            $('.interkassa-payment-button').fancybox({
                'padding': '20',
                'type': 'inline',
                'href': '#order_popup'
            });
            $('.fancybox').fancybox();

            //---------------

        });
    </script>

    <?php
    if(is_user_logged_in()){
        $current_user = wp_get_current_user();
        if (is_array($current_user->roles) && in_array('customer', $current_user->roles) && $main_options['protection']['one_session']['status'] == 'on') {
            ?>
            <script type="text/javascript">
                jQuery(function($){
                    window.setInterval(wpm_check_auth, <?php echo $main_options['protection']['one_session']['interval']*1000; ?>);
                    function wpm_check_auth() {
                        $.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            dataType: 'json',
                            data: {
                                action: "wpm_auth_check_action",
                                user_id: <?php echo get_current_user_id(); ?>
                            },
                            success: function (data) {

                                if(data.auth == false){
                                    $('#user-auth-fail').modal('show');
                                    window.setTimeout(function(){window.location.href = "<?php echo $home_url; ?>"}, 7000);
                                }
                            },
                            error: function (errorThrown) {
//                                    alert(errorThrown);
                            }
                        });
                       /* if(window.blurred == false){

                        }*/
                    }
                });
               // window.onblur = function() { window.blurred = true; };
               // window.onfocus = function() { window.blurred = false; };
            </script>
        <?php } // end if role is customer
    } // end if_user_logged_in ?>

    <?php if($yt_protection_is_enabled): ?>
        <?php wpm_enqueue_script('jwplayer', plugins_url('/member-luxe/js/jwplayer/jwplayer.js')); ?>
        <?php wpm_enqueue_script('wmp-video', plugins_url('/member-luxe/js/video.js')); ?>

        <?php if(array_key_exists('protection', $main_options) && array_key_exists('jwplayer_code', $main_options['protection']) && !empty($main_options['protection']['jwplayer_code'])): ?>
            <script>jwplayer.key="<?php echo $main_options['protection']['jwplayer_code']; ?>";</script>
        <?php else: ?>
            <script>jwplayer.key="OcQXu3Cx/pgNE/bx3qqXx0piOJ0qudFU1CgBzQ==";</script>
        <?php endif; ?>

    <?php endif; ?>

    <!-- wpm head code -->
    <?php echo $wpm_head_code; ?>
    <!-- / wpm head code -->

    <?php
    if(array_key_exists('header_scripts', $main_options)){ ?>
        <!-- wpm global head code -->
        <?php echo stripslashes($main_options['header_scripts']); ?>
        <!-- // wpm global head code -->
    <?php } ?>
    <?php include_once(WP_PLUGIN_DIR . '/member-luxe/inc/theme-settings.php'); ?>


</head>
<?php $protected_body_class = post_password_required() ? 'protected' : ''; ?>
<body <?php body_class($protected_body_class);
echo ' ' . $wpm_body_code; ?>>
<?php
if (is_user_logged_in() || $main_options['main']['opened'] == 'on') echo '<div style="height: 32px"></div>';
 ?>
