<?php



function not_active_admin_menu()
{
    add_menu_page('MemberLux', 'MemberLux', 'manage_options', 'memberluxe-not-active', 'wpm_not_active_memberluxe_page', '', 3);
}

/*
 * 
 */

function wpm_not_active_memberluxe_page()
{

    $top_message = '';

    $user_key = get_option('wpm_key');

    if (isset($_GET['do-action']) && $_GET['do-action'] == 'remove-key') {

        update_option('wpm_key', array());
        //wp_redirect( site_url('/wp-admin/admin.php?page=memberluxe-not-active'));
        //die();
        ?>
        <script type="text/javascript">
            setInterval(function(){window.location = "<?php  echo site_url('/wp-admin/admin.php?page=memberluxe-not-active'); ?>";}, 1000);
        </script>
    <?php
    }

    if (isset($_GET['do-action']) && $_GET['do-action'] == 'register') {
        $data = wpm_check_registration($_GET['user_code']);

        if (!empty($data['message'])) {
            $top_message = '<div class="updated fade"><p>' . $data['message'] . '</p></div>';
        }
        if (!$data['error']) {
            //wp_redirect( site_url('/wp-admin/edit.php?post_type=wpm-page&page=wpm-activation'));
            ?>
            <script type="text/javascript">
                setInterval(function(){window.location = "<?php  echo site_url('/wp-admin/edit.php?post_type=wpm-page&page=wpm-activation'); ?>";}, 1000);
            </script>
            <?php
            //die();
        }
    }


    if (!empty($user_key) && is_array($user_key)) {
        if ($user_key['duration'] != '-1') {
            $now = time();
            $end_date = strtotime($user_key['time_end']);
            $datediff = $end_date - $now;
            $days_to_end = floor($datediff / (60 * 60 * 24));
        }
    } else {
        $days_to_end = "";
    }

    if(empty($user_key) || !is_array($user_key)){
        $user_key = array(
            'code' => '',
            'status' => '',
            'duration' => '',
            'units' => '',
            'time_start' => '',
            'time_end' => '',
            'last_check' => ''
        );
    }

    ?>
    <style type="text/css">
        .content-wrap {
            padding: 30px;
            min-height: 400px;
            background-color: #ffffff;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
            box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
        }
    </style>
    <div class="options-wrap key-wrap">

        <div class="wrap wpm-options-page">

            <div id="icon-options-general" class="icon32"></div>
            <div class="wpm-admin-page-header">
               <!--<h2>Регистрация MemberLux</h2> -->
            </div>
            <?php
            echo $top_message;
            ?>
            <div class="options-wrap wpm-ui-wrap">
                <div class="content-wrap">
                    <form method="get" action="">
                        <h2>Активация MemberLux</h2>

                        <?php
                        if ($user_key['status'] == 'registered') {
                            echo '<h3>MemberLux активирован!</h3>';
                            if ($user_key['duration'] == '-1') {
                                echo '<p>Время действия лицензии: неограничено!</p>';
                            } else {
                                $units = array_key_exists('units', $user_key) ? $user_key['units'] : 'months';
                                $units = $units=='days' ? 'дн.' : 'мес.';
                                echo '<p>Время действия лицензии: ' . $user_key["duration"] . ' ' . $units . '!</p>';
                                echo '<p>Осталось дней: ' . $days_to_end . '</p>';
                            }
                            echo '<input type="hidden" name="post_type" value="wpm-page">';
                            echo '<input type="hidden" name="page" value="wpm-activation">';

                        } elseif ($user_key['status'] == 'suspended') {
                            echo '<input type="hidden" name="post_type" value="wpm-page">';
                            echo '<input type="hidden" name="page" value="memberluxe-not-active">';
                            echo '<h3>Ваш ключ заблокирован</h3>';
                            echo '<p>Для начала работы Вам нужно активировать плагин.</p>';

                        } else { ?>
                            <input type="hidden" name="post_type" value="wpm-page">
                            <input type="hidden" name="page" value="memberluxe-not-active">
                            <p style="font-weight: bold">
                                Чтобы начать работу в системе, введите код активации, присланный вам на е-мейл<br>
                                <span style="font-style: italic; color: #909191">Не получили код? Поддержка в онлайн чате на сайте <a href="http://memberlux.ru">http://memberlux.ru</a></span>
                            <p>
                       <?php } ?>
                        <p>
                            <input type="text" name="user_code" class="widefat "
                                   value="<?php echo $user_key['code']; ?>"
                                   placeholder="<?php _e('Вставьте код сюда', '') ?>">

                        <p>
                        <p>
                            <button type="submit" name="do-action" value="register"
                                    class="button button-primary">Активировать
                            </button>
                            <?php if (!empty($user_key['code'])) { ?>
                                <button type="submit" name="do-action" value="remove-key"
                                        class="button delete">Удалить ключ
                                </button>
                            <?php } ?>

                        </p>
                        <?php if($user_key['status'] != 'registered') { ?>
                        <p style="margin-top: 25px">
                           <span>
                               Если вы еще не покупали MemberLux,<br>
                               вы можете сделать это прямо сейчас здесь:
                           </span> <br> <a class="button button-primary" href="http://memberlux.ru/" target="_blank" style="background-color: #4db252; border-color: #37803a; margin-top: 5px; margin-right: 15px"> Купить MemberLux </a> <a class="button" href="http://blog.pluginex.ru/category/memberlux/" target="_blank">Что нового в обновлении? </a>
                        </p>
                        <?php }?>

                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
}

/*
 * 
 */
$user_key = get_option('wpm_key');


if (!empty($user_key) && is_array($user_key) && $user_key['status'] == 'registered') {

    add_action('init', 'wpm_page_post_type');
    add_action('init', 'wpm_taxonomies', 0);
    add_action('init', 'wpm_user_level_taxonomies', 5);
//    add_action('init', 'wpm_view_autotraining_taxonomies');
    //add_action('init', 'wpm_home_task_taxonomies');

    add_action('admin_menu', 'wpm_admin_menu');

    add_action('init', 'wpm_rewrite_init');
    add_action('init', 'wpm_remove_all_tinymce_ext_plugins', 10000);
    add_filter('tiny_mce_before_init', 'wpm_tinymce_config', 9998);
    add_filter('template_include', 'wpm_get_template', 100);
    add_action('admin_bar_menu', 'wpm_admin_nav_bar', 999);
    add_action('admin_menu', 'wpm_admin_menu_customer', 999);

    add_action('pre_get_posts', 'wpm_custom_number_of_posts', 1);
    add_filter('pre_get_posts', 'wpm_custom_get_posts');

} else {
    add_action('admin_menu', 'not_active_admin_menu', 999);
}


function wpm_check_registration($code)
{

    //update_option('wpm_key', '');
    $code = trim($code);
    $base_url = 'aHR0cDovL2FwaS53cHBhZ2UucnUvYXBpL3JlZ2lzdGVyLw==';

    global $current_user;
    get_currentuserinfo();

    $args = array(
        'timeout' => '50',
        'headers' => array(
            'site' => get_bloginfo('wpurl')
        )
    );
    preg_match('%[^/]+\.[^/:]{2,3}%m', get_bloginfo('wpurl'), $matches);

    $site = !empty($matches) ? $matches[0] : '';

    $request_info = '';
    $request_info .= 'code=' . $code;
    $request_info .= '&site=' . $site;
    $request_info .= '&user_email=' . $current_user->user_email;
    $request_info .= '&user_name=' . $current_user->user_firstname;

    $data = wp_remote_get(base64_decode($base_url) . '?' . $request_info, $args);

    $result = array(
        'message' => '',
        'error'   => false,
        'data'    => $data
    );

    if (is_wp_error($data)) {
        /*
         * Request faild
         */
        $result['message'] = 'Запрос не удался.';
        $result['error'] = true;
    } else if ((int)$data['response']['code'] !== 200) {
        /*
         * API key issues
         */
        $body = json_decode($data['body']);
        $result['message'] = $body->message;
        $result['error'] = true;
    } else {
        /*
         * Success
         */
        $body = json_decode($data['body']);

        $units = isset($body->code->units) ? $body->code->units : 'months';

        if ($body->status == 'registered') {
            $key_args = array(
                'code'       => $code,
                'status'     => $body->status,
                'duration'   => $body->code->duration,
                'units'      => $units,
                'time_start' => $body->code->time_start,
                'time_end'   => $body->code->time_end,
                'last_check' => current_time('mysql')
            );
            update_option('wpm_key', $key_args);
            $result['message'] = $body->message;
            $result['error'] = false;

        } elseif ($body->status == 'used') {
            $result['message'] = $body->message;
            $result['error'] = true;

        } elseif ($body->status == 'suspended') {

            $key_args = array(
                'code'       => $code,
                'status'     => $body->status,
                'duration'   => $body->code->duration,
                'units'      => $units,
                'time_start' => $body->code->time_start,
                'time_end'   => $body->code->time_end,
                'last_check' => current_time('mysql')
            );
            update_option('wpm_key', $key_args);

            $result['message'] = $body->message;
            $result['error'] = true;
        } else {
            $result['message'] = $body->message;
            $result['error'] = false;
        }

    }
    return $result;

}

add_action('init', 'wpm_check_key_authentication');
function wpm_check_key_authentication()
{
    $user_key = get_option('wpm_key');

    // if key exist
    if (!empty($user_key) && is_array($user_key)) {

        if(!array_key_exists('last_check', $user_key)){
            $user_key['last_check'] = '';
            update_option('wpm_key', $user_key);
        }

        $current_time = current_time('mysql');

        $user_key = get_option('wpm_key');

        // if key was checked before
        if(!empty($user_key['last_check'])){

            if(strtotime($current_time) > strtotime($user_key['last_check'].'+ 1 day')){
                wpm_check_registration($user_key['code']);
            }
        // if key wasn't checked before
        }else{
            // update key, add current time as time of last checking key
            //update_option('wpm_key', $user_key);
            // get actual key information

            if(strtotime($current_time) > strtotime($user_key['last_check'].'+ 1 day')){
                wpm_check_registration($user_key['code']);
            }
        }
    }
}