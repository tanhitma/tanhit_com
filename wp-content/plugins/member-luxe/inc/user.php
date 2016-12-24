<?php

/**
 * WPM register form
 */
function wpm_register_form()
{
    $main_options = get_option('wpm_main_options');
    $design_options = get_option('wpm_design_options');
    ?>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        jQuery(function ($) {
            var result = $('#ajax-result');
            var registered_alert = $('#ajax-user-registered');
            registered_alert.html('').css({'display': 'none'});
            result.html('').css({'display': 'none'});
            $('form[name=wpm-user-register-form]').submit(function (e) {
                $('#register-submit').val('Регистрация...');
                result.html('').css({'display': 'none'});
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        'action': 'wpm_ajax_register_user_action',
                        'fields': $(this).serializeArray()
                    },
                    success: function (data) {
                        if (data.registered === true) {
                            registered_alert.html(data.message).fadeIn('fast');
                            $('form#registration').slideUp('slow', function () {
                                setTimeout(function () {
                                    $('a[href="#wpm-login"]').click();
                                    $('form#login input[name=username]').val($('form#registration input[name=login]').val());
                                    $('form#login input[name=password]').val($('form#registration input[name=pass]').val());
                                    $('form#login').submit();
                                }, 1000);
                            });

                        } else {
                            result.html(data.message).fadeIn();
                            $('#register-submit').val('Зарегистрироваться');
                            //result.html(data.message).fadeIn();
                        }

                    },
                    error: function (data) {
                        //console.log(data);
                        result.html('Произошла ошибка.').fadeIn();
                        $('#register-submit').val('Зарегистрироваться');
                    }
                });
                e.preventDefault();
            });
        });
    </script>
   
    <form id="registration" name="wpm-user-register-form" method="post" class="text-center">


        <?php if (wpm_reg_field_is_enabled($main_options, 'surname')):?>
            <p class="wpm-user-last-name">
                <span class="icon-user wpm-icon"></span>
                <input type="text" name="last_name" id="wpm_user_last_name" class="input" value="" size="20"
                       placeholder="<?php _e('Фамилия', 'wpm'); ?>">
            </p>
        <?php endif;?>

        <?php if (wpm_reg_field_is_enabled($main_options, 'name')):?>
            <p class="wpm-user-first-name">
                <span class="icon-user wpm-icon"></span>
                <input type="text" name="first_name" id="wpm_user_first_name" class="input" value="" size="20"
                       placeholder="<?php _e('Имя', 'wpm'); ?>">
            </p>
        <?php endif;?>

        <?php if (wpm_reg_field_is_enabled($main_options, 'patronymic')):?>
            <p class="wpm-user-sur-name">
                <span class="icon-user wpm-icon"></span>
                <input type="text" name="surname" id="wpm_user_surname" class="input" value="" size="20"
                       placeholder="<?php _e('Отчество', 'wpm'); ?>">
            </p>
        <?php endif;?>
        <br>

        <p class="wpm-user-email">
            <span class="icon-envelope wpm-icon"></span>
            <input type="email" name="email" id="wpm_user_email" class="input" value="" size="20" required=""
                   placeholder="<?php _e('Email', 'wpm'); ?>">
        </p>

        <?php if (wpm_reg_field_is_enabled($main_options, 'phone')):?>
            <p class="wpm-user-phone">
                <span class="icon-phone wpm-icon"></span>
                <input type="text" name="phone" id="wpm_user_phone" class="input" value="" size="20"
                       placeholder="<?php _e('Телефон', 'wpm'); ?>">
            </p>
            <br>
        <?php endif;?>

        <p class="wpm-user-login">
            <span class="icon-user2 wpm-icon"></span>
            <input type="text" name="login" id="wpm_user_login" class="input" value="" size="20" required=""
                   placeholder="<?php _e('Желаемый логин', 'wpm'); ?>">
        </p>

        <p class="wpm-login-password">
            <span class="icon-lock wpm-icon"></span>
            <input type="text" name="pass" id="wpm_user_pass" class="input" value="" size="20" required="" min="6"
                   placeholder="<?php _e('Желаемый пароль', 'wpm'); ?>">
        </p>
        <br>

        <p class="wpm-login-code">
            <span class="icon-key wpm-icon"></span>
            <input type="text" name="code" id="wpm_user_code" class="input" value="" size="20" required=""
                   placeholder="<?php _e('Введите код активации', 'wpm'); ?>">
        </p>

        <?php if ($main_options['registration_form']['custom1'] == 'on'):?>
            <p class="wpm-user-pencil">
                <span class="icon-pencil wpm-icon"></span>
                <input type="text" name="custom1" id="wpm_user_custom1" class="input" value="" size="20"
                       placeholder="<?php echo $main_options['registration_form']['custom1_label'] ?>">
            </p>
        <?php endif;?>
        <?php if ($main_options['registration_form']['custom2'] == 'on'):?>
            <p class="wpm-user-pencil">
                <span class="icon-pencil wpm-icon"></span>
                <input type="text" name="custom2" id="wpm_user_custom2" class="input" value="" size="20"
                       placeholder="<?php echo $main_options['registration_form']['custom2_label'] ?>">
            </p>
        <?php endif;?>
        <?php if ($main_options['registration_form']['custom3'] == 'on'):?>
            <p class="wpm-user-pencil">
                <span class="icon-pencil wpm-icon"></span>
                <input type="text" name="custom3" id="wpm_user_custom3" class="input" value="" size="20"
                       placeholder="<?php echo $main_options['registration_form']['custom3_label'] ?>">
            </p>
        <?php endif;?>

        <p class="result alert alert-warning" id="ajax-result"></p>
        <br>

        <p class="register-submit">
            <input type="submit" name="wpm-register-submit" id="register-submit" class="button-primary wpm-register-button"
                   value="<?php echo $design_options['buttons']['register']['text']; ?>">
        </p>
    </form>
    <p class="result alert alert-success text-center" id="ajax-user-registered"></p>

<?php

}


/**
 * Ajax create new User
 */

function wpm_ajax_register_user()
{
    $error = false;
    $registered = false;
    $message = '';
    $form = array();

    foreach ($_POST['fields'] as $item) {
        $form[$item['name']] = trim($item['value']);
    }

    if (!array_key_exists('name', $form)) {
        $form['name'] = '';
    }

    if (!array_key_exists('surname', $form)) {
        $form['surname'] = '';
    }

    if (!array_key_exists('custom1', $form)) {
        $form['custom1'] = '';
    }
    if (!array_key_exists('custom2', $form)) {
        $form['custom2'] = '';
    }
    if (!array_key_exists('custom3', $form)) {
        $form['custom3'] = '';
    }

    if (!array_key_exists('patronymic', $form)) {
        $form['patronymic'] = '';
    }

    if (!array_key_exists('phone', $form)) {
        $form['phone'] = '';
    }

    $index = wpm_search_key_id($form['code']);

    // check if user key exist
    if ($index == null) {
        // if not exist
        $error = true;
        $message .= __('Неверный код доступа <br>', 'wpm');

        wpm_registration_result($message, $registered);

    } else if($index['key_info']['status'] == 'used') {
        $message .= __('Этот код доступа уже используется <br>', 'wpm');
        $error = true;
        wpm_registration_result($message, $registered);
    }

    if (!validate_username($form['login'])) {
        $message .= __('Некорректный логин. <br> Для логина разрешены только буквы латинского алфавита и цифры <br>', 'wpm');
        wpm_registration_result($message, $registered);
    }


    // check if username exist
    if (username_exists($form['login'])) {
        $message .= __('Этот логин уже используется <br>', 'wpm');
        wpm_registration_result($message, $registered);
    }
    // check if email exist
    if (email_exists($form['email'])) {
        $message .= __('Этот email уже используется <br>', 'wpm');
        wpm_registration_result($message, $registered);
    }

    if ($error) {
        wpm_registration_result($message, $registered);
    } else {

        $user_id = wp_insert_user(
            array(
                'user_login' => $form['login'],
                'user_pass'  => $form['pass'],
                'user_email' => $form['email'],
                'first_name' => $form['first_name'],
                'last_name'  => $form['last_name'],
                'role'       => 'customer'
            )
        );

        $registered = wpm_register_user(array(
            'user_id' => $user_id,
            'user_data' => $form,
            'index' => $index,
            'send_email' => true
        ));

        if ($registered) {
            $message = __('Спасибо за регистрацию!<br> Сообщение с подтверждением отправлено на указаные адрес<br>', 'wpm');
            wpm_registration_result($message, $registered);
        }
    }
    //On success
}

function wpm_register_user_hook($user_id)
{
    $form = array(
        'login' => $_POST['user_login'],
        'pass' => $_POST['pass'],
        'email' => $_POST['email'],
        'first_name' => $_POST['first_name'],
        'surname' => $_POST['last_name'],
        'custom1' => $_POST['custom1'],
        'custom2' => $_POST['custom2'],
        'custom3' => $_POST['custom3'],
        'phone' => '',
        'name' => '',
        'patronymic' => '',
    );

    wpm_register_user(array(
        'user_id' => $user_id,
        'user_data' => $form,
        'index' => '',
        'send_email' => true
    ));;
}

//add_action( 'user_register', 'wpm_register_user_hook', 10, 1 );

/*

$args = array(
    'user_id'=> '',
    'user_data' => array(),
    'index' => 'null',
    'send_email' => true);

 * */

function wpm_register_user($args)
{
    $registered = false;

    if (!is_wp_error($args['user_id'])) {

        if($args['send_email']){
            wpm_send_email('new-user', 'admin', $args['user_data']);
            wpm_send_email('new-user', 'user', $args['user_data']);
        }

        // update user meta

        update_user_meta($args['user_id'], 'surname', $args['user_data']['surname']);
        if(isset($args['user_data']['custom1'])){
            update_user_meta($args['user_id'], 'custom1', $args['user_data']['custom1']);
        }
        if(isset($args['user_data']['custom2'])){
            update_user_meta($args['user_id'], 'custom2', $args['user_data']['custom2']);
        }
        if(isset($args['user_data']['custom3'])){
            update_user_meta($args['user_id'], 'custom3', $args['user_data']['custom3']);
        }

        if(isset($args['user_data']['phone'])){
            update_user_meta($args['user_id'], 'phone', $args['user_data']['phone']);
        }

        if (!empty($args['index'])) {
            $term_id = $args['index']['term_id'];

            if ($args['index']['is_deleted']) {
                $term_keys = wpm_get_term_keys($term_id, 'wpm_keys_basket');
            } else {
                $term_keys = wpm_get_term_keys($term_id);
            }

            $duration = $args['index']['key_info']['duration'];
            $units = array_key_exists('units', $args['index']['key_info']) ? $args['index']['key_info']['units'] : 'months';
            $date_start = date('d-m-Y', time());
            $date_end = date('d-m-Y', strtotime("+$duration " . $units));



            if(isset($args['user_data']['code'])){

                update_user_meta($args['user_id'], 'user_key', array( $args['user_data']['code']));
            }

            if ($args['index']['is_deleted']) {
                $deleted_keys = wpm_get_term_keys($term_id, 'wpm_keys_basket');
                $deleted_keys[ $args['user_data']['code']] = array(
                    'status'     => 'used',
                    'date_start' => $date_start,
                    'date_end'   => $date_end
                );
                wpm_set_term_keys($term_id, $deleted_keys, 'wpm_keys_basket');
            } else {
                $item_id = $args['index']['item_id'];
                $term_keys[$item_id]['status'] = 'used';
                $term_keys[$item_id]['date_start'] = $date_start;
                $term_keys[$item_id]['date_end'] = $date_end;
                wpm_set_term_keys($term_id, $term_keys);
            }
            if(isset($form['code'])) {
                $auto_subscription = new MemberLuxAutoSubscriptions();
                $auto_subscription->subscribe_user_by_key($args['user_id'], $form['code']);
            }
        }

        $registered = true;
    }

    return $registered;
}

//add_action('wp_ajax_wpm_ajax_register_user_action', 'wpm_ajax_register_user'); // ajax for logged in users
add_action('wp_ajax_nopriv_wpm_ajax_register_user_action', 'wpm_ajax_register_user'); // ajax for not logged in users


/**/

function wpm_send_email( $event='new-user', $receiver, $form )
{
    $user_name = (isset($form['first_name'])) ? $form['first_name'] : '';
    $user_login = $form['login'];
    $user_email = $form['email'];
    $user_pass = $form['pass'];

    add_filter('wp_mail_content_type', 'wpm_register_set_content_type');
    $main_options = get_option('wpm_main_options');

    // send email to user
    if ($receiver == 'user') {
        $start_url = '<a href="' . get_permalink($main_options['home_id']) . '">' . get_permalink($main_options['home_id']) . '</a>';

        $user_message = apply_filters('the_content', stripslashes($main_options['letters']['registration']['content']));
        $user_message = str_replace('[user_name]', $user_name, $user_message);
        $user_message = str_replace('[user_login]', $user_login, $user_message);
        $user_message = str_replace('[user_pass]', $user_pass, $user_message);
        $user_message = str_replace('[start_page]', $start_url, $user_message);

        if (empty($main_options['letters']['registration']['title'])) {
            $subject = 'Спасибо за регистрацию!';
        } else {
            $subject = $main_options['letters']['registration']['title'];
        }

        wpm_send_mail($user_email, $subject, $user_message);

    } elseif ($receiver == 'admin') {
        $admin_email = get_option('admin_email');
        $admin_message = "<p>На вашем сайте «" . get_option("blogname") . "» зарегистрирован новый пользователь. </p>";
        $admin_message .= "<p>Имя пользователя: $user_login <br>";
        $admin_message .= "E-mail: $user_email </p>";

        $subject = 'Регистрация нового пользователя';

        wpm_send_mail($admin_email, $subject, $admin_message);
    }
}

/**/


function wpm_registration_result($message, $registered)
{
    echo json_encode(array(
        'message' => $message,
        'registered' => $registered
    ));
    die();
}


/**
 *
 */
function wpm_ajax_login_form()
{
    $design_options = get_option('wpm_design_options');
    ?>
    <script>
        jQuery(function ($) {

            // Perform AJAX login on form submit
            $('form#login').submit(function (e) {
                $('form#login p.status').show().text('<?php _e('Проверка...', 'wpm'); ?>');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                        'username': $('form#login #username').val(),
                        'password': $('form#login #password').val(),
                        'security': $('form#login #security').val()
                    },
                    success: function (data) {
                        //console.log(data);
                        $('form#login p.status').text(data.message);
                        if (data.loggedin == true) {
                            location.reload(false);
                            // document.location.href = ajax_login_object.redirecturl;
                        }
                    }
                });
                e.preventDefault();
            });

        });
    </script>
    <form id="login" method="post">
        <p class="status"></p>

        <p>
            <span class="icon-user2"></span>
            <input id="username" type="text" name="username" placeholder="<?php _e('Логин', 'wpm'); ?>">
        </p>

        <p>
            <span class="icon-lock"></span>
            <input id="password" type="password" name="password" placeholder="<?php _e('Пароль', 'wpm'); ?>">
        </p>

        <p>
            <input class="submit_button wpm-sign-in-button" type="submit" value="<?php echo $design_options['buttons']['sign_in']['text']; ?>" name="submit">
        </p>

        <p class="text-left">
            <a class="lost" href="<?php echo wp_lostpassword_url(); ?>"><?php _e('Забыли пароль?', 'wpm'); ?></a>
        </p>
        <?php wp_nonce_field('wpm-ajax-login-nonce', 'security'); ?>
    </form>
<?php
}

/**
 *
 */

function wpm_ajax_login_init()
{
    /*
    wp_enqueue_script('ajax-login-script', plugins_url('member-luxe/js/wpm-ajax-login-script.js'));

    wp_localize_script( 'ajax-login-script', 'ajax_login_object', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => home_url(),
        'loadingmessage' => __('Sending user info, please wait...')
    )); */

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action('wp_ajax_nopriv_ajaxlogin', 'wpm_ajax_login');
}

// Execute the action only if the user isn't logged in
add_action('init', 'wpm_ajax_login_init');

function wpm_ajax_login()
{

    // First check the nonce, if it fails the function will break
    check_ajax_referer('wpm-ajax-login-nonce', 'security');

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon($info, false);
    if (is_wp_error($user_signon)) {
        echo json_encode(array('loggedin' => false, 'message' => __('Логин или пароль не правильные. Попробуйте ввести еще раз, возможно включен CapsLock.')));
    } else {
        echo json_encode(array('loggedin' => true, 'message' => __('Вход выполнен, переадресация...')));
    }
    die();
}

/**
 *
 */
add_action('show_user_profile', 'wpm_show_extra_profile_fields');
add_action('edit_user_profile', 'wpm_show_extra_profile_fields');

function wpm_show_extra_profile_fields($user)
{
    global $wpdb;
    //if (!in_array('customer', $user->roles)) return;
    wp_enqueue_media(); // Include Wordpress Media Library

    $avatar = get_user_meta($user->ID, 'avatar', true);

    $main_options = get_option('wpm_main_options');

    $terms_table = $wpdb->prefix . "terms";
    $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";
    $autotraining_terms = $wpdb->get_results("SELECT a.*, b.count, b.parent
                                         FROM " . $terms_table . " AS a
                                         JOIN " . $term_taxonomy_table . " AS b ON a.term_id = b.term_id
                                         WHERE b.taxonomy='wpm-category';", OBJECT);

    $autotrainings = array();

    if (count($autotraining_terms)) {
        foreach ($autotraining_terms as $autotraining) {
            if (wpm_is_autotraining($autotraining->term_id)) {
                $autotrainings[] = $autotraining;
            }
        }
    }


    ?>
    <script>
        jQuery(function ($) {
            $('.add-new-key').click(function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        action: "wpm_add_key_to_user_action",
                        key: $('#user_key').val(),
                        user_id: "<?php echo $user->ID; ?>"
                    },
                    success: function (data) {

                        $('.result').html(data.message);
                        if (!data.error) setTimeout(function () {
                            location.reload();
                        }, 1000);

                    },
                    error: function (errorThrown) {
                        //console.log(errorThrown);
                    }
                });
            });
            $('.add-new-auto-training-access').click(function () {
                var tr = $(this).closest('tr'),
                    term_id = tr.find('[name="auto_training_access"]').val(),
                    level = tr.find('[name="auto_training_access_number"]').val();

                tr.closest('table').css({opacity:'0.5'});

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        action: "wpm_add_auto_training_access",
                        term_id: term_id,
                        level: level,
                        user_id: "<?php echo $user->ID; ?>"
                    },
                    success: function (data) {
                        if (!data.error) {
                            tr.find('.access-result').html(data.result);
                            tr.closest('table').css({opacity:'1'});
                        }
                    },
                    error: function (errorThrown) {
                    }
                });
            });

            // Upload media file ====================================

            var wpm_file_frame;
            var image_id = '';
            $('.wpm-media-upload-button').live('click', function (event) {
                image_id = $(this).attr('data-id');

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if (wpm_file_frame) {
                    wpm_file_frame.open();
                    return;
                }

                // Create the media frame.
                wpm_file_frame = wp.media.frames.downloadable_file = wp.media({
                    title: '<?php _e('Выберите файл', 'wpm'); ?>',
                    button: {
                        text: '<?php _e('Использовать изображение', 'wpm'); ?>'
                    },
                    multiple: false
                });

                // When an image is selected, run a callback.
                wpm_file_frame.on('select', function () {
                    var attachment = wpm_file_frame.state().get('selection').first().toJSON();

                    $('#' + image_id).val(attachment.id);
                    $('input#wpm_' + image_id).val(attachment.sizes.thumbnail.url);
                    $('#wpm-' + image_id + '-preview').attr('src', attachment.sizes.thumbnail.url).show();
                    $('#delete-wpm-' + image_id).show();
                });
                // Finally, open the modal.
                wpm_file_frame.open();
            });
            $('.wpm-delete-media-button').on('click', function () {
                image_id = $(this).attr('data-id');

                $('#avatar').val('');
                $('input#wpm_' + image_id).val('');
                $('#delete-wpm-' + image_id).hide();
                $('#wpm-' + image_id + '-preview').hide();
            });

            // End upload media file ====================================
        });
    </script>

    <h3><?php _e('Дополнительная информация', 'wpm'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="surname">Отчество</label></th>
            <td>
                <input type="text" name="surname" id="surname"
                       value="<?php echo esc_attr(get_the_author_meta('surname', $user->ID)); ?>" class="regular-text"/><br/>
            </td>
        </tr>
        <tr>
            <th><label for="custom1"><?php echo $main_options['registration_form']['custom1_label'] ?></label></th>
            <td>
                <input type="text" name="custom1" id="custom1"
                       value="<?php echo esc_attr(get_the_author_meta('custom1', $user->ID)); ?>" class="regular-text"/><br/>
            </td>
        </tr>
        <tr>
            <th><label for="custom2"><?php echo $main_options['registration_form']['custom2_label'] ?></label></th>
            <td>
                <input type="text" name="custom2" id="custom2"
                       value="<?php echo esc_attr(get_the_author_meta('custom2', $user->ID)); ?>" class="regular-text"/><br/>
            </td>
        </tr>
        <tr>
            <th><label for="custom3"><?php echo $main_options['registration_form']['custom3_label'] ?></label></th>
            <td>
                <input type="text" name="custom3" id="custom3"
                       value="<?php echo esc_attr(get_the_author_meta('custom3', $user->ID)); ?>" class="regular-text"/><br/>
            </td>
        </tr>
    </table>
    <table class="form-table">
        <tr>
            <th><label for="avatar">Аватарка</label></th>
            <td>
                <input type="hidden" id="avatar" name="avatar"
                       value="<?php echo $avatar; ?>"
                       class="wide">

                <div class="wpm-avatar-preview-wrap">
                    <div class="wpm-avatar-preview-box">
                        <?php
                        if (!empty($avatar)) {
                            echo wp_get_attachment_image($avatar, 'thumbnail', '', array('class' => "thumbnail", 'id' => 'wpm-avatar-preview'));
                        } else {
                            echo '<img src="" class="thumbnail" alt="" id="wpm-avatar-preview">';
                        }
                        ?>
                    </div>
                </div>
                <div class="wpm-control-row">
                    <p>
                        <button type="button" class="wpm-media-upload-button button"
                                data-id="avatar"><?php _e('Загрузить', 'wpm'); ?></button>
                        &nbsp;&nbsp; <a id="delete-wpm-avatar"
                                        class="wpm-delete-media-button button submit-delete"
                                        data-id="avatar"<?php if (empty($avatar)) echo 'style="display:none"'; ?>><?php _e('Удалить', 'wpm'); ?></a>
                    </p>
                </div>
            </td>
        </tr>
    </table>

    <h3><?php _e('Код доступа', 'wpm'); ?><a id="activation"></a></h3>
    <table class="form-table">
        <tr>
            <th><label for="twitter">Ключи</label></th>
            <td>
                <p>
                    <span class="description"><?php _e('Добавить новый ключ', 'wpm'); ?></span><br>
                </p>

                <p>
                    <input type="text" id="user_key" value="" placeholder="Ваш ключ" class="regular-text">
                    <button type="button" class="button add-new-key"><?php _e('Добавить', 'wpm'); ?></button>
                    <span class="result"></span><br>
                </p>
                <p>
                    <span class="description"><?php _e('Ваши ключи', 'wpm'); ?></span><br>
                </p>
                <?php

                $html = wpm_user_keys($user, false, true);

                if (!empty($html)) echo '<ul class="user-keys-list">' . $html . '</ul>';

                ?>

                <script>
                    jQuery(document).ready(function () {
                        jQuery(document).on('click', '.remove-key', function () {
                            var elem = jQuery(this);
                            var key = elem.attr('data-key');

                            jQuery.ajax({
                                type: 'GET',
                                dataType: 'json',
                                url: ajaxurl,
                                data: {
                                    action: "wpm_move_key_to_ban_action",
                                    key: key,
                                    user: <?php echo $user->ID;?>
                                },
                                success: function (data) {
                                    if(!data.error){
                                        elem.parent('li').addClass('banned_key');
                                        location.reload();
                                    }
                                },
                                error: function (errorThrown) {
                                    console.log(errorThrown);
                                }
                            });
                        });
                    });
                </script>
            </td>
        </tr>
    </table>
    <?php
    $user_ID = get_current_user_id();
    $user_data = get_userdata($user_ID);
    if (in_array('administrator', $user_data->roles) || in_array('teacher', $user_data->roles) || in_array('editor', $user_data->roles)) : ?>
    <table class="form-table">
            <tr>
                <th><label><?php _e('Настройки автотренингов', 'wpm'); ?></label></th>
                <td style="width:160px; vertical-align: baseline;">
                    <p>
                        <span class="description"><?php _e('Выбор автотренинга', 'wpm'); ?></span><br>
                    </p>
                    <p>
                        <select name="auto_training_access">
                            <?php foreach ($autotrainings AS $autotraining) : ?>
                                <option value="<?php echo $autotraining->term_id; ?>"><?php echo $autotraining->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                </td>
                <td>
                    <p>
                        <span class="description"><?php _e('Введите порядковый номер урока', 'wpm'); ?></span><br>
                    </p>
                    <p>
                        <input type="text" style="width: 40px" name="auto_training_access_number">
                        <button type="button" class="button add-new-auto-training-access"><?php _e('Сохранить', 'wpm'); ?></button>
                        <span class="access-result"></span><br>
                        <span class="description"><?php _e('Работает только один раз!', 'wpm'); ?></span><br>
                    </p>
                </td>
            </tr>
        </table>
    <?php endif; ?>
<?php
}

add_action('personal_options_update', 'wpm_save_extra_profile_fields');
add_action('edit_user_profile_update', 'wpm_save_extra_profile_fields');

function wpm_save_extra_profile_fields($user_id)
{

    if (!current_user_can('edit_user', $user_id))
        return false;

    update_user_meta($user_id, 'surname', $_POST['surname']);
    update_user_meta($user_id, 'custom1', $_POST['custom1']);
    update_user_meta($user_id, 'custom2', $_POST['custom2']);
    update_user_meta($user_id, 'custom3', $_POST['custom3']);

    update_user_meta($user_id, 'avatar', $_POST['avatar']);

}

/**
 *
 */
function wpm_add_contact_methods($profile_fields)
{

    /* $user = wp_get_current_user();
     if (!in_array('customer', $user->roles)) return;*/
    // Add new fields
    $profile_fields['phone'] = 'Телефон';
    return $profile_fields;
}

add_filter('user_contactmethods', 'wpm_add_contact_methods');

/**
 *
 */
add_action('admin_init', 'wpm_user_profile');
function wpm_user_profile()
{
    $user = wp_get_current_user();
    if (!in_array('customer', $user->roles)) return;

    //removes the `profile.php` admin color scheme options
    remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
    add_action('admin_head', 'wpm_hide_personal_options');
}

function wpm_hide_personal_options()
{
    echo "\n" . '<script type="text/javascript">jQuery(document).ready(function($) { $(\'form#your-profile > h3:first\').hide(); $(\'form#your-profile > table:first\').hide(); $(\'form#your-profile\').show(); });</script>' . "\n";
}


/**
 *
 */

function wpm_add_key_to_user()
{
    $user_id = $_POST['user_id'];
    $code = trim($_POST['key']);

    $user = wp_get_current_user();

    $user_keys = get_user_meta($user_id, 'user_key', true);

    $user_keys = (empty($user_keys) && !is_array($user_keys)) ? array() : $user_keys;

    $result = array(
        'message' => '',
        'error' => false,
        'keys' => ''
    );

    $index = wpm_search_key_id($code);

    // check if user key exist
    if (empty($index)) {
        // if not exist
        $result['error'] = true;
        $result['message'] = __('Неверный ключ', 'wpm');
        //$result['message'] = $index;
    } else {
        // if exist
        if ($index['key_info']['status'] == 'new') {
            wpm_update_user_key_dates($user_id, $code);

            array_push($user_keys, $code);
            update_user_meta($user_id, 'user_key', $user_keys);
            $result['message'] = __('Ключ добавлен', 'wpm');

            $auto_subscription = new MemberLuxAutoSubscriptions();
            $auto_subscription->subscribe_user_by_key($user_id, $_POST['key']);

            //$result['keys'] = wpm_user_keys($user, $is_table = true);

        } elseif ($index['key_info']['status'] == 'used') {
            // if key is used
            $result['message'] = __('Этот ключ уже используется', 'wpm');
            $result['error'] = true;
        }
    }

    $result['keys'] = wpm_user_keys($user, $is_table = true, false);

    echo json_encode($result);
    die();
}

add_action('wp_ajax_wpm_add_key_to_user_action', 'wpm_add_key_to_user');

function wpm_add_auto_training_access() {
    $user_id = intval(trim($_POST['user_id']));
    $term_id = intval(trim($_POST['term_id']));
    $level = intval(trim($_POST['level']));

    $training_access = get_user_meta($user_id, 'training_access', true);
    $training_access = (empty($training_access) && !is_array($training_access)) ? array() : $training_access;

    $new_training_access = array();

    foreach ($training_access AS $training_access_item) {
        if($training_access_item['term_id'] != $term_id) {
            $new_training_access[] = $training_access_item;
        }
    }

    array_push($new_training_access, array('level' => $level, 'term_id' => $term_id));
    update_user_meta($user_id, 'training_access', $new_training_access);

    echo json_encode(array('result' =>  __('Уровень автотренинга обновлен', 'wpm')));
    die();
}


add_action('wp_ajax_wpm_add_auto_training_access', 'wpm_add_auto_training_access');

function wpm_get_user_keys_info($user_id)
{
    $result = array();

    $user_keys = get_user_meta($user_id, 'user_key', true);

    if(!empty($user_keys)){
        foreach ($user_keys as $key) {
            $result[] = wpm_search_key_id($key);
        }
    }

    return $result;
}

function wpm_update_user_key_dates($user_id, $code, $isBanned = false)
{
    $key = wpm_search_key_id($code);
    $term_id = $key['term_id'];
    $duration = $key['key_info']['duration'];
    $units = array_key_exists('units', $key['key_info']) ? $key['key_info']['units'] : 'months';

    if ($key['is_deleted']) {
        $key['key_info']['date_start'] = date('d-m-Y', time());
        $key['key_info']['date_end'] = date('d-m-Y', strtotime("+$duration " . $units));
        $key['key_info']['status'] = 'used';

        $deleted_keys = wpm_get_term_keys($term_id, 'wpm_keys_basket');

        $deleted_keys[$code] = $key['key_info'];
        $deleted_keys[$code]['key'] = $code;

        wpm_set_term_keys($term_id, $deleted_keys, 'wpm_keys_basket');
    } elseif($isBanned) {
        $term_keys = wpm_get_term_keys($term_id);
        $date_start = strtotime($key['key_info']['date_start']);

        foreach (wpm_get_user_keys_info($user_id) AS $user_key) {
            $start = strtotime($user_key['key_info']['date_start']);
            if ($user_key['term_id'] == $term_id && $start > $date_start) {
                $user_key['key_info']['date_start'] = date('d-m-Y', strtotime("-$duration " . $units, $start));
                $user_key['key_info']['date_end'] = date('d-m-Y', strtotime("-$duration " . $units, strtotime($user_key['key_info']['date_end'])));
                $term_keys[$user_key['item_id']] = $user_key['key_info'];
            }
        }

        wpm_set_term_keys($term_id, $term_keys);
    } else {
        $date_start = time();

        foreach (wpm_get_user_keys_info($user_id) AS $user_key) {
            if ($user_key['term_id'] == $term_id) {
                $date_start = max($date_start, strtotime($user_key['key_info']['date_end']));
            }
        }

        $key['key_info']['date_registered'] = date('d-m-Y', time());
        $key['key_info']['date_start'] = date('d-m-Y', $date_start);
        $key['key_info']['date_end'] = date('d-m-Y', strtotime("+$duration " . $units, $date_start));
        $key['key_info']['status'] = 'used';

        $term_keys = wpm_get_term_keys($term_id);

        $term_keys[$key['item_id']] = $key['key_info'];

        wpm_set_term_keys($term_id, $term_keys);
    }
}

function wpm_move_key_to_ban ()
{
    $result = array(
        'message' => '',
        'error' => false
    );

    $user_keys = get_user_meta($_GET['user'], 'user_key', true);

    foreach ($user_keys as $key => $code) {
        if ($code==$_GET['key']) {
            wpm_update_user_key_dates($_GET['user'], $_GET['key'], true);

            add_user_meta($_GET['user'], 'user_banned_key', $_GET['key']);
            unset($user_keys[$key]);

            update_user_meta($_GET['user'], 'user_key', $user_keys);
        }
    }

    echo json_encode($result);
    die();
}
add_action('wp_ajax_wpm_move_key_to_ban_action', 'wpm_move_key_to_ban');

/**
 *
 */
function wpm_search_key_id($user_key)
{
    $user_key = trim($user_key);

    $terms = get_terms('wpm-levels', array(
        'hide_empty' => 0
    ));

    if (is_array($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $term_id = $term->term_id;
            $term_keys = wpm_get_term_keys($term_id);
            foreach ($term_keys as $item_id => $item) {
                if ($item['key'] === $user_key) {
                    return array(
                        'term_id'    => $term_id,
                        'key_info'   => $term_keys[$item_id],
                        'item_id'    => $item_id,
                        'is_deleted' => false
                    );
                }
            }
        }

        foreach ($terms as $term) {
            $deleted_keys = wpm_get_term_keys($term->term_id, 'wpm_keys_basket');

            if ($deleted_keys && array_key_exists($user_key, $deleted_keys) && $user_key === $deleted_keys[$user_key]['key']) {
                return array(
                    'term_id'    => $term->term_id,
                    'key_info'   => $deleted_keys[$user_key],
                    'is_deleted' => true
                );
            }
        }
    }

    return null;
}





/*
* On login attempt check if user account is active
*/
function wpm_check_login($user, $username, $password)
{
    if(is_wp_error($user)) {
        return $user;
    }

    $meta = wpm_get_user_status($user->ID);

    if ($meta == 'inactive' && !in_array('administrator', $user->roles)) {
        return new WP_Error('wpm_inactive', 'Ваш аккаунт временно заблокирован.');
    } else {
        return $user;
    }
}
add_filter('authenticate', 'wpm_check_login', 100, 3);

/*
* Insert new items to the bulk actions dropdown on users.php
*/
function wpm_bulk_admin_footer()
{
    if(!current_user_can('activate_plugins')) {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('<option>').val('wpm_deactivate_account').text('Заблокировать').appendTo("select[name='action']");
            $('<option>').val('wpm_activate_account').text('Разблокировать').appendTo("select[name='action']");
        });
    </script>
<?php
}
add_action('admin_footer-users.php', 'wpm_bulk_admin_footer');

/*
* Perform bulk actions on form submit
*/
function wpm_users_bulk_action()
{
    if(!current_user_can('activate_plugins')) {
        return;
    }

    $wp_list_table = _get_list_table('WP_Users_List_Table');

    $action = $wp_list_table->current_action();

    switch($action) {
        case 'wpm_deactivate_account':
            $user_ids = $_GET['users'];
            $deactivated = 0;
            foreach( $user_ids as $user_id ) {
                if(get_current_user_id() != $user_id){
                    update_user_meta($user_id, 'wpm_status', 'inactive');
                    $deactivated++;
                }
            }
            $sendback = add_query_arg( array('deactivated' => $deactivated ), $sendback );
            break;
        case 'wpm_activate_account':
            $user_ids = $_GET['users'];
            $activated = 0;
            foreach( $user_ids as $user_id ) {
                update_user_meta($user_id, 'wpm_status', 'active');
                $activated++;
            }
            $sendback = add_query_arg( array('activated' => $activated ), $sendback );
            break;
        case 'wpm_activate_single_account':
            update_user_meta($_GET['user'], 'wpm_status', 'active');
            $sendback = add_query_arg( array('activated' => 1 ), $sendback );
            break;
        case 'wpm_deactivate_single_account':
            update_user_meta($_GET['user'], 'wpm_status', 'inactive');
            $sendback = add_query_arg( array('activated' => 1 ), $sendback );
            break;
        default: return;
    }
    wp_redirect($sendback);
    exit();
}
add_action('load-users.php', 'wpm_users_bulk_action');

/*
* Display admin notice on activation and deactivation of accounts
*/
function custom_bulk_admin_notices()
{
    global $pagenow;

    if ($pagenow == 'users.php') {
        if (isset($_REQUEST['deactivated']) && (int) $_REQUEST['deactivated']) {
            $message = sprintf( _n( 'Пользовательский аккаунт заблокирован.', '%s пользовательских аккаунтов заблокировано.', $_REQUEST['deactivated'] ), number_format_i18n( $_REQUEST['deactivated'] ) );
            echo "<div class=\"updated\"><p>$message</p></div>";
        } elseif(isset($_REQUEST['activated']) && (int) $_REQUEST['activated']) {
            $message = sprintf( _n( 'Пользовательский аккаунт разблокирован.', '%s пользовательских аккаунтов разблокировано.', $_REQUEST['activated'] ), number_format_i18n( $_REQUEST['activated'] ) );
            echo "<div class=\"updated\"><p>$message</p></div>";
        }
    }
}
add_action('admin_notices', 'custom_bulk_admin_notices');

/*
* Display status of each account in the WordPress users table
*/
function wpm_add_user_id_column($columns)
{
    $columns['wpm_status'] = 'Состояние аккаунта';

    return $columns;
}
add_filter('manage_users_columns', 'wpm_add_user_id_column');

//function wpm_activation_action_link($actions, $user_object)
//{
//    $actions['activation_link'] = "<a class='view_frontend_profile' href='#'>" . __( 'Активировать', 'wpm_activation_link' ) . "</a>";
//    return $actions;
//}
//add_filter('user_row_actions', 'wpm_activation_action_link', 11, 2);


function wpm_show_user_id_column_content($value, $column_name, $user_id)
{
    $account_status = wpm_get_user_status($user_id);
    $user           = get_user_by('id', $user_id );

    if (!in_array('administrator', $user->roles) && 'wpm_status' == $column_name ) {
        return $account_status=='active'
            ? '<b style="color: #008000;">Активен</b> <br /><a href="' . admin_url('/users.php?action=wpm_deactivate_single_account&user=' . $user_id) . '">Блокировать</a>'
            : '<b style="color: red;">Блокирован</b> <br /><a href="' . admin_url('/users.php?action=wpm_activate_single_account&user=' . $user_id) . '">Снять блок</a>';
    }

    return $value;
}
add_action('manage_users_custom_column',  'wpm_show_user_id_column_content', 10, 3);