<?php
$user = wp_get_current_user();
?>

<div class="toggle-mobile-menu-wrap">
    <button class="menu-toggle pull-right"><span class="line-1"></span><span class="line-2"></span><span
            class="line-3"></span></button>

</div>
<div class="nav-menu-mobile">
    <?php

    if (current_user_can('manage_options')) {
        ?>
        <a class="link" href="<?php echo admin_url('edit.php?post_type=wpm-page'); ?>"
           title="<?php bloginfo('name'); ?>">Панель управления</a>
        <?php
    }

    if (is_single()) edit_post_link();

    if (is_user_logged_in()) {
        $redirect_url = get_permalink($main_options['home_id']);
        //$redirect_url = '';
        ?>
        <a class="link" href="<?php echo wp_logout_url($redirect_url); ?>"
           title="<?php _e('Выход', 'wpm'); ?>"><?php _e('Выход', 'wpm'); ?></a>
        <?php
        $user_profile_url = admin_url('/user-edit.php?user_id=' . $user->ID);
        echo '<a class="link" href="' . $user_profile_url . '">Ваш профиль</a>'; ?>
        <a class="link activation-link" href="#activation" data-toggle="modal"
           data-target="#activation_modal"><?php _e('Активация', 'wpm'); ?></a>
        <?php if (!isset($main_options['main']['hide_ask']) || $main_options['main']['hide_ask'] != 'hide') { ?>
            <a class="link login-link" href="#ask" data-toggle="modal"
               data-target="#ask_modal"><?php _e('Задать вопрос', 'wpm'); ?></a>
        <?php } ?>
    <?php } else { ?>
        <a class="link registration-link" href="#registration" data-toggle="modal"
           data-target="#registration_modal"><?php _e('Регистрация', 'wpm'); ?></a>
        <a class="link login-link" href="#login" data-toggle="modal"
           data-target="#login_modal"><?php _e('Вход', 'wpm'); ?></a>
        <?php
        $showAsk = (!isset($main_options['main']['hide_ask_for_not_registered']) || $main_options['main']['hide_ask_for_not_registered'] != 'on')
            && (!isset($main_options['main']['hide_ask']) || $main_options['main']['hide_ask'] != 'on');
        ?>
        <?php if ($showAsk) { ?>
            <a class="link login-link" href="#ask" data-toggle="modal"
               data-target="#ask_modal"><?php _e('Задать вопрос', 'wpm'); ?></a>
        <?php } ?>
    <?php } ?>
</div>


<script>
    jQuery(function ($) {
        $('.menu-toggle').on('click', function () {
            $(this).toggleClass('active');
            $('.nav-menu-mobile').toggleClass('opened');
        });
    });
</script>


