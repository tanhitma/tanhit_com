<?php if ($main_options['main']['opened'] && !is_user_logged_in()) { ?>
    <div class="modal fade" id="login_modal" tabindex="-1" role="dialog" aria-labelledby="login_label"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h4 class="modal-title" id="login_label">Вход в систему</h4>
                </div>
                <div class="modal-body">
                    <?php wpm_ajax_login_form(); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registration_modal" tabindex="-1" role="dialog" aria-labelledby="registration_label"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h4 class="modal-title" id="registration_label">Регистрация в системе</h4>
                </div>
                <div class="modal-body">
                    <?php wpm_register_form(); ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($main_options['main']['opened'] || is_user_logged_in()) { ?>
    <div class="modal fade" id="activation_modal" tabindex="-1" role="dialog" aria-labelledby="activation_label"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h4 class="modal-title" id="activation_label">Активация</h4>
                </div>
                <div class="modal-body">
                    <form class="activation-form" action="" method="post">
                        <p>
                            <input type="text" id="user_key" value="" placeholder="Ваш ключ" class="regular-text">
                        </p>
                        <p>
                            <input type="submit" class="button add-new-key wpm-activate-pin-button" value="<?php echo $design_options['buttons']['activate_pin']['text']; ?>">
                        </p>
                        <br>

                        <div class="result"></div>
                    </form>
                    <?php if (is_user_logged_in()): ?>
                        <?php $user = wp_get_current_user(); ?>
                        <?php $user_keys = wpm_user_keys($user, true, false); ?>
                        <div class="user-keys-wrap <?php echo empty($user_keys) ? 'hidden' : ''; ?>">
                            <h5 class="user-keys-title">Ваши ключи:</h5>
                            <table class="table table-striped user-keys">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ключ</th>
                                </tr>
                                </thead>
                                <tbody id="user-keys">
                                <?php echo $user_keys; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php if (is_user_logged_in() && $main_options['protection']['one_session']['status'] == 'on') { ?>

        <div class="modal fade" id="user-auth-fail" tabindex="-1" role="dialog" aria-labelledby="user_auth_fail"
             data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="activation_label">Предупреждение о выходе из системы</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="padding-row">
                                    <p>Кто-то вошел в систему под вашим логином.</p>

                                    <p>Вы будете перенаправлены на главную страницу через 7 секунд</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <img src="<?php echo plugins_url('/member-luxe/templates/base/img/hacker.png'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <script>
        jQuery(function ($) {

            $('.activation-form').on('submit', function (e) {
                var result = $(this).find('.result');
                result.hide();
                result.html('<p class="alert alert-info">Загрузка...</p>').fadeIn();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        action: "wpm_add_key_to_user_action",
                        key: $('#user_key').val(),
                        user_id: "<?php echo get_current_user_id(); ?>"
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.error) {
                            result.fadeOut('slow', function () {
                                result.html('<p class="alert alert-warning">' + data.message + '</p>').fadeIn();
                                setTimeout(function () {
                                    result.fadeOut('slow', function () {
                                        result.html('');
                                        $('#user_key').val('');
                                    });
                                }, 2000);
                            });
                        } else {
                            result.fadeOut('slow', function () {
                                result.html('<p class="alert alert-success">' + data.message + '</p>').fadeIn();
                                $('#user-keys').html(data.keys);
                                $('.user-keys-wrap').removeClass('hidden');
                            });
                            setTimeout(function () {
                                result.fadeOut('slow', function () {
                                    result.html('');
                                    $('#user_key').val('');
                                });
                            }, 2000);
                        }
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });
                e.preventDefault();
            });
        });
    </script>


    <div class="modal fade" id="ask_modal" tabindex="-1" role="dialog" aria-labelledby="ask_label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h4 class="modal-title" id="registration_label">Задать вопрос</h4>
                </div>
                <div class="modal-body">
                    <form name="wpm-ajax-ask-form" class="ask-form" method="post" action="">
                        <?php
                        global $current_user;
                        get_currentuserinfo();
                        ?>

                        <p>
                            <input type="text" name="user_name" value="<?php echo $current_user->user_firstname; ?>"
                                   placeholder="Ваше имя" required="">
                        </p>

                        <p>
                            <input type="email" name="user_email" value="<?php echo $current_user->user_email; ?>"
                                   placeholder="Ваш email" required="">
                        </p>

                        <p><input name="title" type="text" placeholder="Заголовок вопроса" value="" required></p>

                        <p><textarea name="message" placeholder="Ваш вопрос" required></textarea></p>

                        <p><input type="submit" class="button wpm-button-ask" value="<?php echo $design_options['buttons']['ask']['text']; ?>"></p>

                        <div class="wpm-ask-result"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(function ($) {
            $('form[name=wpm-ajax-ask-form]').on('submit', function (e) {
                var result = $('.wpm-ask-result');
                result.html('<div class="preloader"></div>');

                var name = $('form[name=wpm-ajax-ask-form] input[name=user_name]');
                var email = $('form[name=wpm-ajax-ask-form] input[name=user_email]');
                var title = $('form[name=wpm-ajax-ask-form] input[name=title]');
                var message = $('form[name=wpm-ajax-ask-form] textarea');

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: "send_wpm_ask_form",
                        name: name.val(),
                        email: email.val(),
                        title: title.val(),
                        message: message.val()
                    },
                    success: function (data) {
                        if (data == 'yes') {
                            result.html('<p class="success">Ваше сообщение отправлено!</p>').show();

                            setTimeout(function () {
                                $('#ask_modal').find('.close').click();
                                title.val('');
                                message.val('');
                                result.hide();
                            }, 1000);
                        }
                        if (data == 'no') {
                            result.html('<p class="danger">Произошла ошибка! Сообщение не отправлено.</p>').show();
                        }
                    },
                    error: function (errorThrown) {
                        // alert(errorThrown);
                    }
                });

                e.preventDefault();
            });
        });

    </script>
<?php }