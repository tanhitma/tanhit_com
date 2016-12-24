<?php

include_once(WP_PLUGIN_DIR . '/member-luxe/inc/post-type.php'); //
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/user.php'); //
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/page-functions.php'); //
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/comments.php'); //
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/wpm-metabox.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/wpm-options.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/wpm-user-levels.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/page_lessons.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/shortcodes/shortcodes.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/shortcodes/video/WPMVideoShortCode.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/shortcodes/shortcode-settings.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/shortcodes/shortcode-settings-js.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/updater/updater.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/duplicate-post/wpm_duplicate-post.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/order-terms/order-terms.php');
//include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/uppod/wpp_uppod.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/post-ordering/wpm-post-ordering.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/tinymce-description/tinymce-description.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/comment-images/comment-image.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/file-upload/file-upload.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/mandrill/mandrill.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/plugins/ses/ses.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/core-functions.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/auto-subscriptions.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/view-autotraining.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/auto-training.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/AutoTrainingView.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/MBLComment.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/send_mails.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/term_keys.php');
include_once(WP_PLUGIN_DIR . '/member-luxe/inc/import_users.php');

add_action('init', 'wpm_init_session', 1);
if (!function_exists('wpm_init_session')):
    function wpm_init_session()
    {
        session_start();
    }
endif;

/**
 * Install MemberLux
 */

function wpm_install()
{
    $user_key = get_option('wpm_key');


    if (empty($user_key) || !is_array($user_key)) {
        $key_args = array(
            'code'       => '',
            'status'     => '',
            'duration'   => '',
            'units'      => '',
            'time_start' => '',
            'time_end'   => ''
        );
        update_option('wpm_key', $key_args);
    }

    flush_rewrite_rules();

    if (get_option('wpm_version') == '0.1.0') {
        wpm_migrate_keys();
    }

    //----------- migrate headers
    migrate_to_new_header();

    //-------------------


    update_option('wpm_version', WP_MEMBERSHIP_VERSION);

    wpm_install_db();
    wpm_add_role(); // add new role "customer"

    $upload_dir = wp_upload_dir();
    $wpm_folder = $upload_dir['basedir'] . '/wpm';
    if (!file_exists($wpm_folder)) {
        mkdir($wpm_folder);
    }
    add_theme_support('post-thumbnails');
    wpm_set_default_options();


    // Create start page
    $main_options = get_option('wpm_main_options');
    if (empty($main_options['home_id'])) {
        $start_page_id = wp_insert_post(array(
            'post_title'   => __('Стартовая страница', 'wpm'),
            'post_name'    => 'start',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'wpm-page',
            'post_author'  => get_current_user_id()
        ));
        if (!is_wp_error($start_page_id)) {
            $main_options['home_id'] = $start_page_id;
            update_option('wpm_main_options', $main_options);
        }

    } else {
        $page_data = get_post($main_options['home_id']);
        if ($page_data->post_status != 'publish') {

            $start_page_id = wp_insert_post(array(
                'post_title'   => __('Стартовая страница', 'wpm'),
                'post_name'    => 'start',
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'wpm-page',
                'post_author'  => get_current_user_id()
            ));
            if (!is_wp_error($start_page_id)) {
                $main_options['home_id'] = $start_page_id;
                update_option('wpm_main_options', $main_options);
            }

        }
    }


    if (empty($main_options['schedule_id'])) {
        $schedule_page_id = wp_insert_post(array(
            'post_title'   => __('Расписание', 'wpm'),
            'post_name'    => 'schedule',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'wpm-page',
            'post_author'  => get_current_user_id()
        ));
        if (!is_wp_error($schedule_page_id)) {
            $main_options['schedule_id'] = $schedule_page_id;
            update_option('wpm_main_options', $main_options);
        }

    } else {
        $page_schedule_data = get_post($main_options['schedule_id']);
        if ($page_schedule_data->post_status != 'publish') {

            $schedule_page_id = wp_insert_post(array(
                'post_title'   => __('Расписание', 'wpm'),
                'post_name'    => 'schedule',
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'wpm-page',
                'post_author'  => get_current_user_id()
            ));
            if (!is_wp_error($schedule_page_id)) {
                $main_options['schedule_id'] = $schedule_page_id;
                update_option('wpm_main_options', $main_options);
            }

        }
    }

    wp_schedule_event(time(), 'daily', 'wpm_daily_schedule_hook');
}

/**
 *
 */


function wpm_page_post_type()
{
    $labels = array(
        'name'               => __('МemberLux', 'wpm'),
        'singular_name'      => __('Материал', 'wpm'),
        'all_items'          => __('Все материалы', 'wpm'),
        'add_new'            => __('Добавить материал', 'wpm'),
        'add_new_item'       => __('Добавить материал', 'wpm'),
        'edit_item'          => __('Редактировать', 'wpm'),
        'new_item'           => __('Новый материал', 'wpm'),
        'view_item'          => __('Смотреть', 'wpm'),
        'search_items'       => __('Поиск', 'wpm'),
        'not_found'          => __('Ничего не найдено', 'wpm'),
        'not_found_in_trash' => __('Ничего не найдено в корзине', 'wpm'),
        'parent_item_colon'  => ''
    );
    $args = array(
        'labels'               => $labels,
        'public'               => true,
        'publicly_queryable'   => true,
        'show_ui'              => true,
        'query_var'            => true,
        'rewrite'              => array(
            'slug'       => 'wpm',
            'with_front' => false,
        ),
        'capability_type'      => 'post',
        'hierarchical'         => true,
        'has_archive'          => true,
        'supports'             => array(
            'title',
            'thumbnail',
            'editor',
            'page-attributes',
            'comments',
            //'excerpt',
            'revisions'
        ),
        'menu_position'        => 2,
        'show_in_menu'         => true,
        'register_meta_box_cb' => 'add_wpm_page_metabox'
    );
    register_post_type('wpm-page', $args);
    flush_rewrite_rules();
}


function wpm_taxonomies()
{
    $labels = array(
        'name'              => _x('Рубрики материалов', 'taxonomy general name'),
        'singular_name'     => _x('Рубрики материалов', 'taxonomy singular name'),
        'search_items'      => __('Найти рубрику', 'wpm'),
        'all_items'         => __('Все рубрики', 'wpm'),
        'parent_item'       => __('Родительская рубрика', 'wpm'),
        'parent_item_colon' => __('Родительская рубрика:', 'wpm'),
        'edit_item'         => __('Редактировать рубрику', 'wpm'),
        'update_item'       => __('Обновить рубрику', 'wpm'),
        'add_new_item'      => __('Добавить новую рубрику', 'wpm'),
        'new_item_name'     => __('Название рубрики', 'wpm'),
        'menu_name'         => __('Рубрики', 'wpm'),
    );
    register_taxonomy('wpm-category', 'wpm-page',
        array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_in_nav_menus' => true,
            'show_ui'           => true,
            'query_var'         => true,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug'       => 'wpm-category',
                'with_front' => false,
            ),
        ));

    flush_rewrite_rules();
}

function wpm_user_level_taxonomies()
{
    $labels = array(
        'name'              => _x('Уровни доступа', 'taxonomy general name'),
        'singular_name'     => _x('Уровни доступа', 'taxonomy singular name'),
        'search_items'      => __('Найти уровень', 'wpm'),
        'all_items'         => __('Все уровни', 'wpm'),
        'parent_item'       => __('Родительский уровень'),
        'parent_item_colon' => __('Родительский уровень:', 'wpm'),
        'edit_item'         => __('Редактировать уровень', 'wpm'),
        'update_item'       => __('Обновить уровень', 'wpm'),
        'add_new_item'      => __('Добавить новый', 'wpm'),
        'new_item_name'     => __('Название рубрики', 'wpm'),
        'menu_name'         => __('Уровни доступа', 'wpm'),
        'description'       => __('Продажа доступа', 'wpm')
    );

    register_taxonomy('wpm-levels', 'wpm-page',
        array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_in_nav_menus' => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array(
                'slug'       => 'wpm-levels',
                'with_front' => false,
            ),

        ));
    flush_rewrite_rules();
}

function wpm_home_task_taxonomies()
{
    $labels = array(
        'name'          => _x('Домашние задания', 'taxonomy general name'),
        'singular_name' => _x('Домашние задания', 'taxonomy singular name'),
        'search_items'  => __('Найти домашнее задание', 'wpm'),
        'all_items'     => __('Все домашние задания', 'wpm'),
        'edit_item'     => __('Редактировать домашнее задание', 'wpm'),
        'update_item'   => __('Обновить домашнее задание', 'wpm'),
        'add_new_item'  => __('Добавить домашнее задание', 'wpm'),
        'new_item_name' => __('Название домашнего задания', 'wpm'),
        'menu_name'     => __('Домашние задания', 'wpm'),
        'description'   => __('Домашние задания', 'wpm')
    );

    register_taxonomy('wpm-levels', 'wpm-page',
        array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_in_nav_menus' => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array(
                'slug'       => 'wpm-home-tasks',
                'with_front' => false,
            ),

        ));
    flush_rewrite_rules();
}

function wpm_view_autotraining_taxonomies()
{
    $labels = array(
        'name'          => _x('Автотренинги', 'taxonomy general name'),
        'singular_name' => _x('Автотренинг', 'taxonomy singular name'),
        'search_items'  => __('Найти автотренинг', 'wpm'),
        'all_items'     => __('Все автотренинги', 'wpm'),
        'new_item_name' => __('Название автотренинга', 'wpm'),
        'menu_name'     => __('Автотренинги', 'wpm'),
    );
    register_taxonomy('wpm-view-autotraining', 'wpm-page',
        array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_in_nav_menus' => true,
            'show_ui'           => true,
            'query_var'         => true,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug'       => 'wpm-view-autotraining',
                'with_front' => false,
            ),
        ));

    flush_rewrite_rules();
}

/*
 *
 */
function wpm_install_db()
{
    global $wpdb;
    $response_table = $wpdb->prefix . "memberlux_responses";
    $response_review_table = $wpdb->prefix . "memberlux_response_review";
    $response_relationships_table = $wpdb->prefix . "memberlux_responses_relationships";
    $response_log_table = $wpdb->prefix . "memberlux_response_log";
    $login_log_table = $wpdb->prefix . "memberlux_login_log";

    $sql_response_table = "CREATE TABLE IF NOT EXISTS `" . $response_table . "` (
                              `id` bigint(20) NOT NULL AUTO_INCREMENT,
                              `response_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                              `approval_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                              `response_status` enum('opened','approved','rejected','accepted') NOT NULL DEFAULT 'opened',
                              `response_content` longtext NOT NULL,
                              `response_type` varchar(20) NOT NULL DEFAULT 'auto',
                              `post_id` bigint(11) NOT NULL,
                              `user_id` bigint(11) NOT NULL,
                              UNIQUE KEY `id` (`id`)
                            )
                            DEFAULT CHARACTER SET utf8
                            DEFAULT COLLATE utf8_general_ci;";

    $sql_response_review_table = "CREATE TABLE IF NOT EXISTS `" . $response_review_table . "` (
                              `id` bigint(20) NOT NULL AUTO_INCREMENT,
                              `response_id` bigint(20) NOT NULL,
                              `user_id` bigint(11) NOT NULL,
                              `review_content` longtext NOT NULL,
                              `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                              UNIQUE KEY `id` (`id`)
                            )
                            DEFAULT CHARACTER SET utf8
                            DEFAULT COLLATE utf8_general_ci;";


    $sql_response_relationships_table = "CREATE TABLE IF NOT EXISTS `" . $response_relationships_table . "` (
                                          `id` bigint(20) NOT NULL AUTO_INCREMENT,
                                          `response_id` bigint(20) NOT NULL,
                                          `term_taxonomy` longtext NOT NULL,
                                          `response_type` varchar(20) NOT NULL DEFAULT 'auto',
                                          `post_id` bigint(20) unsigned NOT NULL,
                                          `user_id` bigint(20) unsigned NOT NULL,
                                          UNIQUE KEY `id` (`id`),
                                          KEY `response_id` (`response_id`),
                                          KEY `post_id` (`post_id`),
                                          KEY `user_id` (`user_id`)
                                        )
                                        DEFAULT CHARACTER SET utf8
                                        DEFAULT COLLATE utf8_general_ci;";

    $sql_response_log_table = "CREATE TABLE IF NOT EXISTS `" . $response_log_table . "` (
                              `id` bigint(20) NOT NULL AUTO_INCREMENT,
                              `response_id` bigint(20) NOT NULL,
                              `event` varchar(255) NOT NULL,
                              `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                              UNIQUE KEY `id` (`id`)
                            )
                            DEFAULT CHARACTER SET utf8
                            DEFAULT COLLATE utf8_general_ci;";

    $sql_login_log_table = "CREATE TABLE IF NOT EXISTS `" . $login_log_table . "` (
                              `id` bigint(20) NOT NULL AUTO_INCREMENT,
                              `user_id` bigint(20) NOT NULL,
                              `ip` varchar(60) NOT NULL,
                              `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                              UNIQUE KEY `id` (`id`)
                            )
                            DEFAULT CHARACTER SET utf8
                            DEFAULT COLLATE utf8_general_ci;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql_response_table);
    dbDelta($sql_response_review_table);
    dbDelta($sql_response_relationships_table);
    dbDelta($sql_response_log_table);
    dbDelta($sql_login_log_table);

    add_option("memberlux_db_version", '0.1');
}


/**
 * Create user role
 */
function wpm_add_role()
{
    add_role(
        'customer',
        __('Клиент MemberLux', 'wpm'),
        array(
            'read'         => true, // true allows this capability
            'edit_posts'   => false,
            'delete_posts' => false, // Use false to explicitly deny
            'upload_files' => true,
            'edit_files'   => true,
            'level_2'      => true
        )
    );

    add_role(
        'coach',
        __('Тренер MemberLux', 'wpm'),
        array(
            'read'         => true, // true allows this capability
            'edit_posts'   => false,
            'delete_posts' => false, // Use false to explicitly deny
            'upload_files' => true,
            'edit_files'   => true,
            'level_2'      => true
        )
    );
}

function wpm_add_customer_caps()
{
    // gets the customer role
    $role = get_role('customer');

    $role->add_cap('upload_files');
    $role->add_cap('edit_files');
}

add_action('admin_init', 'wpm_add_customer_caps');

function wpm_add_coach_caps()
{
    // gets the coach role
    $coach_role = get_role('coach');

    if (is_object($coach_role) && method_exists($coach_role, 'add_cap')) {

        $coach_role->add_cap('upload_files');
        $coach_role->add_cap('edit_files');
        $coach_role->add_cap('review_homeworks');
    }

    $admin_role = get_role('administrator');
    $admin_role->add_cap('review_homeworks');
}

add_action('admin_init', 'wpm_add_coach_caps');

add_filter('ajax_query_attachments_args', 'wpm_show_users_own_attachments', 1, 1);
function wpm_show_users_own_attachments($query)
{
    $id = get_current_user_id();
    if (!current_user_can('manage_options'))
        $query['author'] = $id;
    return $query;
}

/**
 *
 */

function wpm_deactivate()
{
    wp_clear_scheduled_hook('wpm_hourly_event_test');
    wp_clear_scheduled_hook('wpm_hourly_event');
    wp_clear_scheduled_hook('wpm_daily_schedule_hook');
}

/**
 * Define post revisions
 */

add_filter('wp_revisions_to_keep', 'wpm_revisions', 10, 2);

function wpm_revisions($num, $post)
{
    if ('wpm-page' == $post->post_type) {
        $num = 3;
    }
    return $num;
}

/**
 *
 */

function add_wpm_page_metabox()
{
    add_meta_box('wpm_page_metabox', __('Дополнительные параметры страницы', 'wpm'), 'wpm_page_extra', 'wpm-page', 'normal');
}

/**
 *
 */


function wpm_admin_menu()
{
    add_submenu_page('edit.php?post_type=wpm-page', __('Автотренинги', 'wpm'), __('Автотренинги', 'wpm'), 'manage_options', 'wpm-view-autotraining', 'wpm_view_autotraining_page');
    add_submenu_page('edit.php?post_type=wpm-page', __('Домашние задания', 'wpm'), __('Домашние задания', 'wpm'), 'review_homeworks', 'wpm-autotraining', 'wpm_autotraining_page');
    add_submenu_page('edit.php?post_type=wpm-page', __('Рассылка', 'wpm'), __('Рассылка', 'wpm'), 'manage_options', 'wpm-send-mails', 'wpm_send_mails_page');
    add_submenu_page('edit.php?post_type=wpm-page', __('Активация', 'wpm'), __('Активация', 'wpm'), 'manage_options', 'wpm-activation', 'wpm_not_active_memberluxe_page');
   // add_submenu_page('edit.php?post_type=wpm-page', __('Безопасность', 'wpm'), __('Безопасность', 'wpm'), 'manage_options', 'wpm-security', 'wpm_security_page');
    add_submenu_page('edit.php?post_type=wpm-page', __('Параметры', 'wpm'), __('Настройки', 'wpm'), 'manage_options', 'wpm-options', 'wpm_options');
    add_submenu_page('edit.php?post_type=wpm-page', __('Обновление', 'wpm'), __('Обновление', 'wpm'), 'update_plugins', 'wpm-updater', 'wpm_updater');
    add_submenu_page('edit.php?post_type=wpm-page', __('Уроки', 'wpm'), __('Уроки', 'wpm'), 'manage_options', 'wpm-lessons', 'wpm_lessons_page');
}

/**
 * Default options
 */
add_action('wp_ajax_wpm_reset_options_to_default_action', 'wpm_reset_options_to_default'); // ajax for logged in users
function wpm_reset_options_to_default()
{

    $result = array(
        'message' => '',
        'error'   => false
    );
    $options = $_POST['option_type'];
    $default_main_options = get_option('wpm_main_options_default');
    $default_design_options = get_option('wpm_design_options_default');
    if ($options == 'all') {
        update_option('wpm_main_options', $default_main_options);
        update_option('wpm_design_options', $default_design_options);
        $result['message'] = 'Настройки сброшены';
    }
    if ($options == 'design') {
        update_option('wpm_design_options', $default_design_options);
        $result['message'] = 'Настройки дизайна сброшены';
    }

    echo json_encode($result);
    die();
}

function wpm_set_default_options()
{

    $default_main_options = array(
        'make_home_start'    => false,
        'home_id'            => '',
        'start_page'         => array(
            'make_home_start'         => false,
            'page_on_front'           => '',
            'page_for_posts'          => '',
            'page_on_front_original'  => '',
            'page_for_posts_original' => ''
        ),
        'protection'         => array(
            'youtube_protected' => 'on',
            'jwplayer_code'     => '',
            'text_protected'    => 'off',
            'one_session' => array(
                'status' => 'off',
                'interval' => '60'
            )
        ),
        'registration_form'  => array(
            'name'       => 'on',
            'surname'    => 'on',
            'patronymic' => 'on',
            'phone'      => 'on',
            'custom1'      => 'off',
            'custom2'      => 'off',
            'custom3'      => 'off'
        ),
        'header_scripts' => '',
        'schedule_id'        => '',
        'hide_schedule'      => 'off',
        'main'               => array(
            'posts_per_page' => '20',
            'opened'         => false
        ),
        'favicon'            => array(
            'url' => plugins_url('/member-luxe/i/wpm_favicon.png'),
        ),
        'logo'               => array(
            'url'     => plugins_url('/member-luxe/i/wpm_logo.png'),
            'width'   => '',
            'height'  => '',
            'visible' => 'visible'
        ),
        'login_content'      => array(
            'content'  => '',
            'visible'  => 'hidden',
            'position' => 'top'
        ),
        'header'             => array(
            'content' => '',
            'visible' => 'hidden'
        ),
        'footer'             => array(
            'content' => '',
            'visible' => 'hidden'
        ),
        'letters'            => array(
            'mandrill_is_on'        => 'off',
            'mandrill_api_key'      => '',
            'registration'          => array(
                'title'   => 'Спасибо за регистрацию!',
                'content' => 'Здравствуйте [user_name]!

Ваши данные для входа:

Страница входа: [start_page]
Логин: [user_login]
Пароль: [user_pass]

Приятной работы!'
            ),
            'comment_subscription'          => array(
                'title'   => 'Ответ на Ваш комментарий',
                'content' => 'Здравствуйте [user_name]!

На Ваш комментарий на странице "[page_title]" ([page_link]) был опубликован ответ.'
            ),
            'registration_to_admin' => array(
                'title'   => 'Зарегистрирован новый пользователь',
                'content' => ''
            )
        ),
        'social'             => array(
            'facebook'  => array(
                'app_id' => '',
                'admin'  => ''
            ),
            'vkontakte' => array(
                'id' => ''
            )
        ),
        'auto_subscriptions' => array(
            'justclick'      => array(
                'active'       => false,
                'user_id'      => '',
                'user_rps_key' => '',
                'rid'          => '',
                'doneurl2'     => ''
            ),
            'smartresponder' => array(
                'active'      => false,
                'api_key'     => '',
                'delivery_id' => '',
                'track_id'    => '',
                'group_id'    => ''
            ),
            'getresponce'    => array(
                'active'         => false,
                'api_key'        => '',
                'campaign_token' => ''
            ),
            'unisender'      => array(
                'active'  => false,
                'api_key' => '',
                'lists'   => '',
                'tags'    => ''
            )
        )
    );
    $default_design_options = array(
        'main'      => array(
            'background_color'            => 'f7f8f9',
            'background-attachment-fixed' => 'off',
            'background_image'            => array(
                'url'      => '',
                'repeat'   => 'repeat',
                'position' => 'center top'
            ),
            'height'                      => '',
            'visible'                     => 'visible',
            'hide_ask_for_not_registered' => false,
            'hide_ask'                    => false,
            'date_is_hidden'              => 'off',
            'comments_order'              => 'asc',
            'attachments_mode'            => 'allowed_to_all',
            'visibility'                  => 'to_all',
            'border-radius'               => 3
        ),
        'menu'      => array(
            'bold'             => 'off',
            'submenu_bold'     => 'off',
            'current_bold' => 'off',
            'border'           => array(
                'color' => 'd3d9df'
            ),
            'background_color' => 'ffffff',
            'a'                => array(
                'normal_color'        => '000000',
                'active_color'        => '2c8bb7',
                'selected_link_color' => '2b9973'
            ),
            'a_submenu'        => array(
                'normal_color'        => '919191',
                'active_color'        => '2c8bb7',
                'selected_link_color' => '2b9973'
            ),
            'font_size' => '14'
        ),
        'page'      => array(
            'background_color' => 'ffffff',
            'text_color' => '333',
            'link_color' => '428bca',
            'link_color_hover' => '2a6496',
            'border'           => array(
                'color' => 'd3d9df'
            ),
            'header'           => array(
                'background_color' => '2b9973',
                'text_color'       => 'ffffff'
            ),
            'row'              => array(
                'odd'  => array(
                    'background_color'       => 'f3f6f8',
                    'background_color_hover' => 'f3f6f8',
                    'text_color'             => '677c8a',
                    'text_color_hover'       => '343d43',
                ),
                'even' => array(
                    'background_color'       => 'ffffff',
                    'background_color_hover' => 'ffffff',
                    'text_color'             => '677c8a',
                    'text_color_hover'       => '343d43',
                )

            )
        ),
        'single'    => array(
            'header'           => array(
                'background_color' => 'fbfcfc',
                'border_color' => '2B9973',
                'title_text_color' => '000000',
                'desc_text_color' => '4a5363',
                'label_color' => '2b9973'
            )
        ),
        'buttons'   => array(
            'show'      => array(
                'background_color'       => 'f3f6f8',
                'background_color_hover' => '2c8bb7',
                'text_color'             => '2c8bb7',
                'text_color_hover'       => 'ffffff',
                'border_color'           => '2c8bb7',
                'border_color_hover'     => '2c8bb7',
                'text'                   => 'Показать'
            ),
            'no_access' => array(
                'background_color'       => 'f3f6f8',
                'background_color_hover' => '677c8a',
                'text_color'             => '677c8a',
                'text_color_hover'       => 'ffffff',
                'border_color'           => '677c8a',
                'border_color_hover'     => '677c8a',
                'text'                   => 'Нет доступа'
            ),
            'back'      => array(
                'background_color'       => 'f3f6f8',
                'background_color_hover' => '2c8bb7',
                'text_color'             => '2c8bb7',
                'text_color_hover'       => 'ffffff',
                'border_color'           => '2c8bb7',
                'border_color_hover'     => '2c8bb7',
                'text'                   => 'Вернуться к списку'
            ),
            'home_work_respond_on_page' => array(
                'background_color'       => 'f3f6f8',
                'background_color_hover' => '2c8bb7',
                'text_color'             => '2c8bb7',
                'text_color_hover'       => 'ffffff',
                'border_color'           => '2c8bb7',
                'border_color_hover'     => '2c8bb7',
                'text'                   => 'Ответить'
            ),
            'home_work_respond_on_popup' => array(
                'background_color'       => '2c8bb7',
                'background_color_hover' => '00608c',
                'text_color'             => 'ffffff',
                'text_color_hover'       => 'ffffff',
                'text'                   => 'Отправить'
            ),
            'home_work_edit' => array(
                'background_color'       => 'f3f6f8',
                'background_color_hover' => '2c8bb7',
                'text_color'             => '2c8bb7',
                'text_color_hover'       => 'ffffff',
                'border_color'           => '2c8bb7',
                'border_color_hover'     => '2c8bb7',
                'text'                   => 'Редактировать'
            ),
            'home_work_edit_on_popup' => array(
                'background_color'       => '2c8bb7',
                'background_color_hover' => '00608c',
                'text_color'             => 'ffffff',
                'text_color_hover'       => 'ffffff',
                'text'                   => 'Отправить'
            ),
            'home_work_edit_on_popup_add_file' => array(
                'background_color'       => 'fff',
                'background_color_hover' => '2C8BB7',
                'text_color'             => '2C8BB7',
                'text_color_hover'       => 'fff',
                'border_color'           => '2C8BB7',
                'border_color_hover'     => '2C8BB7',
                'text'                   => 'Добавить файлы'
            ),
            'home_work_edit_on_popup_upload' => array(
                'background_color'       => 'f9f9f9',
                'background_color_hover' => '2C8BB7',
                'text_color'             => '2C8BB7',
                'text_color_hover'       => 'fff',
                'border_color'           => '2C8BB7',
                'border_color_hover'     => '2C8BB7',
                'text'                   => 'Загрузить'
            ),
            'home_work_edit_on_popup_cancel' => array(
                'background_color'       => 'f9f9f9',
                'background_color_hover' => '2C8BB7',
                'text_color'             => '2C8BB7',
                'text_color_hover'       => 'fff',
                'border_color'           => '2C8BB7',
                'border_color_hover'     => '2C8BB7',
                'text'                   => 'Отмена'
            ),
            'home_work_edit_on_popup_delete' => array(
                'background_color'       => 'f9f9f9',
                'background_color_hover' => '2C8BB7',
                'text_color'             => '2C8BB7',
                'text_color_hover'       => 'fff',
                'border_color'           => '2C8BB7',
                'border_color_hover'     => '2C8BB7',
                'text'                   => 'Удалить'
            ),
            'refresh_comments' => array(
                'text_color'             => '428bca',
                'text_color_hover'       => '2f71a9',
                'text'                   => 'Обновить'
            ),
            'send_comment' => array(
                'background_color'       => 'f3f6f8',
                'background_color_hover' => '00608c',
                'text_color'             => '2c8bb7',
                'text_color_hover'       => 'ffffff',
                'border_color'           => '2c8bb7',
                'border_color_hover'     => '00608c',
                'text'                   => 'Отправить'
            ),
            'sign_in' => array(
                'background_color'       => '2c8bb7',
                'background_color_hover' => '00608c',
                'text_color'             => 'ffffff',
                'text_color_hover'       => 'ffffff',
                'text'                   => 'Войти'
            ),
            'register' => array(
                'background_color'       => '2c8bb7',
                'background_color_hover' => '00608c',
                'text_color'             => 'ffffff',
                'text_color_hover'       => 'ffffff',
                'text'                   => 'Зарегистрироваться'
            ),
            'activate_pin' => array(
                'background_color'       => '2c8bb7',
                'background_color_hover' => '00608c',
                'text_color'             => 'ffffff',
                'text_color_hover'       => 'ffffff',
                'text'                   => 'Добавить'
            ),
            'get_pin' => array(
                'background_color'       => '3f8bb9',
                'background_color_hover' => '00608c',
                'text_color'             => 'ffffff',
                'text_color_hover'       => 'ffffff',
                'text'                   => 'Получить пин-код'
            ),
            'copy_pin' => array(
                'background_color'       => 'ffffff',
                'background_color_hover' => '3f8bb9',
                'text_color'             => '3f8bb9',
                'text_color_hover'       => 'ffffff',
                'border_color'           => '3f8bb9',
                'border_color_hover'     => '3f8bb9',
                'text'                   => 'Скопировать пин-код'
            ),
            'register_on_pin' => array(
                'background_color'       => 'ffffff',
                'background_color_hover' => '3f8bb9',
                'text_color'             => '3f8bb9',
                'text_color_hover'       => 'ffffff',
                'border_color'           => '3f8bb9',
                'border_color_hover'     => '3f8bb9',
                'text'                   => 'Пройти регистрацию'
            ),
            'ask' => array(
                'background_color'       => '2c8bb7',
                'background_color_hover' => '00608c',
                'text_color'             => 'ffffff',
                'text_color_hover'       => 'ffffff',
                'text'                   => 'Отправить'
            ),
            'top_admin_bar' => array(
                'background_panel_color' => '222222',
                'background_color'       => '222222',
                'background_color_hover' => '333333',
                'text_color'             => 'ffffff',
                'text_color_hover'       => '2ea2cc'
            ),
            'welcome_tabs' => array(
                'text_color_login'             => '2b9973',
                'text_color_login_hover'       => '2c8bb7',
                'text_color_register'             => '2b9973',
                'text_color_register_hover'       => '2c8bb7',
                'text_login'             => 'Вход',
                'text_register'          => 'Регистрация'
            )
        ),
        'preloader' => array(
            'color_1' => '76d6b6'
        )
    );
    update_option('wpm_main_options_default', $default_main_options);
    update_option('wpm_design_options_default', $default_design_options);

    $main_options = get_option('wpm_main_options');
    $design_options = get_option('wpm_design_options');

    if (isset($main_options) && is_array($main_options)) {
        update_option('wpm_main_options', array_replace_recursive($default_main_options, $main_options));
    }else{
        update_option('wpm_main_options', $default_main_options);
    }

    if (isset($design_options) && is_array($design_options)){
        update_option('wpm_design_options', array_replace_recursive($default_design_options, $design_options));
    }else{
        update_option('wpm_design_options', $default_design_options);
    }
}

/**
 *
 */


function wpm_rewrite_init()
{
    flush_rewrite_rules();
}

/*
 *
 */

add_action("admin_head", "wpm_enqueue_admin_style_js", 9999);

function wpm_enqueue_admin_style_js()
{
    global $typenow;
    global $current_screen;

    if (is_admin()) {
        wp_enqueue_style('wpm-admin-style-all', plugins_url('/member-luxe/css/admin-style-all-pages.css'));
        wp_enqueue_style('wpm-editor-css', includes_url('/css/editor.min.css'));
        $isWPMpage = $current_screen->post_type == 'wpm-page'
            || $typenow == 'wpm-page'
            || (isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'wpm-levels')
            || (isset($_GET['post_type']) && $_GET['post_type'] == 'wpm-page');
        if ($isWPMpage) {
            wp_enqueue_style('wpm-admin-style', plugins_url('/member-luxe/css/admin-style.css'));

            // Thickbox
            wp_enqueue_style('thickbox');
            wp_enqueue_script('thickbox');

            // jQuery ui
            wp_enqueue_style('wpm-jquery-ui-wpm', plugins_url('/member-luxe/js/jquery/themes/wpm/jquery.ui.base.css'));
            // wp_enqueue_style('wpm-jquery-ui', plugins_url('/member-luxe/js/jquery/themes/wpm/jquery.ui.all.css'));

            wp_enqueue_style('wpm-mediaelement', plugins_url('/member-luxe/js/mediaelement/mediaelementplayer.min.css'));
            wp_enqueue_style('wpm-mediaelement-skins', plugins_url('/member-luxe/js/mediaelement/wpm-skins.css'));


            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('jquery-ui-slider');
            wp_enqueue_script('jquery-ui-datepicker');

            wp_enqueue_script('jquery-ui-datepicker-ru', plugins_url('/member-luxe/js/jquery/ui/i18n/jquery.ui.datepicker-ru.min.js'));
            wp_enqueue_script('zeroclipboard', plugins_url('/member-luxe/js/zeroclipboard/ZeroClipboard.min.js'));
            wp_enqueue_script('jquery-ui-timepicker', plugins_url('/member-luxe/js/time_picker/jquery.ui.timepicker.js'));
            wp_enqueue_script('js-color-picker', plugins_url('/member-luxe/js/jscolor/jscolor.js'));

            wp_enqueue_script('wpm-mediaelement', plugins_url('/member-luxe/js/mediaelement/mediaelement-and-player.min.js'));

            wp_enqueue_script('jquery-ui_cookie', plugins_url('/member-luxe/js/miscellaneous/jquery.cookie.js'));
        }
    }
}

/**
 *
 */

add_action('admin_head', 'wpm_admin_header_css');
function wpm_admin_header_css()
{
    $current_user = wp_get_current_user();
    $roles = $current_user->roles;
    if (in_array('customer', $roles)) {
        echo '<style type="text/css"> #menu-media, #wp-admin-bar-new-content{ display: none!important}</style>';
    }
    echo '<style type="text/css">  #menu-posts-wpm-page > a { background-color: #12527f!important; } </style>';

}


/**
 * @param $id
 * @param $path
 */

function wpm_enqueue_style($id, $path)
{
    echo '<link rel="stylesheet" type="text/css" media="all" id="' . $id . '-css" href="' . $path . '">' . "\n";
}

/**
 * @param $id
 * @param $path
 */
function wpm_enqueue_script($id, $path)
{
    echo '<script type="text/javascript" id="' . $id . '-js" src="' . $path . '"></script>' . "\n";
}

/**
 *
 */
add_action("wpm_head", "wpm_enqueue_styles", 900);
function wpm_enqueue_styles()
{
    echo "<!-- wpm_enqueue_styles --> \n";
    wpm_enqueue_style('wpm-bootstrap', plugins_url('/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'));
    wpm_enqueue_style('wpm-base-style', plugins_url('/member-luxe/templates/base/base-styles.css'));
    wpm_enqueue_style('wpm-protected-style', plugins_url('/member-luxe/templates/base/base-protected-page.css'));
    wpm_enqueue_style('wpm-countdown', plugins_url('/member-luxe/js/countdown/jquery.countdown.css'));

    wpm_enqueue_style('wpm-fancybox', plugins_url('/member-luxe/js/fancybox/jquery.fancybox.css'));

    wpm_enqueue_style('wpm-mediaelement', plugins_url('/member-luxe/js/mediaelement/mediaelementplayer.min.css'));
    wpm_enqueue_style('wpm-owl', plugins_url('/member-luxe/js/owl.carousel/assets/owl.carousel.css'));


    wpm_enqueue_style('wpm-mediaelement-skins', plugins_url('/member-luxe/js/mediaelement/wpm-skins.css'));



    echo "<!-- // wpm_enqueue_styles --> \n";
}

/**
 *
 */
add_action("wpm_head", "wpm_enqueue_scripts", 900);
function wpm_enqueue_scripts()
{
    echo "<!-- wpm_enqueue_scripts --> \n";
    wpm_enqueue_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js');

    wpm_enqueue_script('jquery-form', plugins_url('/member-luxe/js/jquery/jquery.form.js'));
    
    wpm_enqueue_script('jquery-ui-custom', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
    wpm_enqueue_script('jquery-ui-core', plugins_url('/member-luxe/js/jquery/ui/jquery.ui.core.min.js'));
    wpm_enqueue_script('jquery-ui-widget', plugins_url('/member-luxe/js/jquery/ui/jquery.ui.widget.min.js'));
    wpm_enqueue_script('jquery-ui-tabs', plugins_url('/member-luxe/js/jquery/ui/jquery.ui.tabs.min.js'));
    wpm_enqueue_script('jquery-ui-sortable', plugins_url('/member-luxe/js/jquery/ui/jquery.ui.sortable.min.js'));
    wpm_enqueue_script('jquery-ui-accordion', plugins_url('/member-luxe/js/jquery/ui/jquery.ui.accordion.min.js'));
    wpm_enqueue_script('jquery-ui-slider', plugins_url('/member-luxe/js/jquery/ui/jquery.ui.slider.min.js'));
    wpm_enqueue_script('jquery-ui-datepicker', plugins_url('/member-luxe/js/jquery/ui/jquery.ui.datepicker.min.js'));

    wpm_enqueue_script('wpm-bootstrap', plugins_url("/member-luxe/templates/base/bootstrap/js/bootstrap.min.js"));
    wpm_enqueue_script('jquery-ui-cookie', plugins_url('/member-luxe/js/miscellaneous/jquery.cookies.2.2.0.min.js'));
    wpm_enqueue_script('jquery-countdown-plugin', plugins_url('/member-luxe/js/countdown/jquery.plugin.min.js'));
    wpm_enqueue_script('jquery-countdown', plugins_url('/member-luxe/js/countdown/jquery.countdown.js'));
    wpm_enqueue_script('jquery-countdown-ru', plugins_url('/member-luxe/js/countdown/jquery.countdown-ru.js'));

    wpm_enqueue_script('jquery-fancybox', plugins_url('/member-luxe/js/fancybox/jquery.fancybox.js'));

    wpm_enqueue_script('wpm-comments-replay', includes_url('js/comment-reply.min.js'));

    wpm_enqueue_script('wpm-mediaelement', plugins_url('/member-luxe/js/mediaelement/mediaelement-and-player.min.js'));

    wpm_enqueue_script('wpm-owl', plugins_url('/member-luxe/js/owl.carousel/owl.carousel.min.js'));

    if (wpm_is_pin_code_page()) {
        wpm_enqueue_script('zeroclipboard', plugins_url('/member-luxe/js/zeroclipboard/ZeroClipboard.min.js'));
    }

    echo "<!-- // wpm_enqueue_scripts --> \n";
}

/**
 *
 */

function wpm_head()
{
    do_action('wpm_head');
}

function wpm_footer()
{

    do_action('wpm_footer');
}


/**
 * Remove all external plugins, languages from Tinymce on wppage editing page
 */


function wpm_remove_all_tinymce_ext_plugins()
{

    if (isset($_GET['post'])) {
        $post = get_post($_GET['post']);
        $post_type = get_post_type($post);
        if ($post_type == 'wpm-page') {

            remove_all_actions('mce_external_plugins', 9999);
            remove_all_actions('mce_buttons', 9999);
            remove_all_actions('mce_external_languages', 9999);
        }
    }
}

/**
 *
 */

function wpm_tinymce_config($init)
{

    global $typenow;
    global $current_screen;

    if ($current_screen->post_type != 'wpm-page' || $typenow != 'wpm-page') return $init;

    $upload_dir = wp_upload_dir();

    /*$init['force_p_newlines'] = 'true';
    $init['remove_linebreaks'] = true;
    $init['force_br_newlines'] = false;
    $init['remove_trailing_nbsp'] = true;
    $init['verify_html'] = true;*/


    $init['remove_linebreaks'] = 'false';
    $init['wpautop'] = 'false';
    $init['apply_source_formatting'] = 'true';
    $init['paste_auto_cleanup_on_paste'] = 'true';
    $init['paste_convert_headers_to_strong'] = 'false';
    $init['paste_strip_class_attributes'] = 'all';
    $init['paste_strip_class_attributes'] = 'false';
    $init['paste_remove_spans'] = 'true';
    $init['paste_remove_styles'] = 'true';

    if (!isset($init['content_css_force'])) {
        $init['content_css'] = includes_url("css/dashicons.min.css");
        $init['content_css'] .= ', ' . includes_url("js/mediaelement/mediaelementplayer.min.css");
        $init['content_css'] .= ', ' . includes_url("js/mediaelement/wp-mediaelement.css");
        $init['content_css'] .= ', ' . plugins_url() . '/member-luxe/css/editor-style-wpm-page.css?' . time();
    } else {
        $init['content_css'] = $init['content_css_force'];
    }


    if (version_compare(get_bloginfo('version'), '3.9', '>=')) {
        $init['toolbar1'] = 'bold italic underline strikethrough | bullist numlist  | blockquote hr | alignleft aligncenter alignright | outdent indent | anchor link unlink anchor fullscreen wp_adv';
        $init['toolbar2'] = 'fontselect fontsizeselect formatselect forecolor backcolor | table pastetext removeformat | undo redo ';
        $init['toolbar3'] = '';
        $init['fontsize_formats'] = '10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 21pt 22pt 23pt 24pt 25pt 26pt 27pt 28pt 29pt 30pt 32pt 42pt 48pt 52pt';
    } else {
        $init['theme_advanced_font_sizes'] = '10pt,11pt,12pt,13pt,14pt,15pt,16pt,17pt,18pt,19pt,20pt,21pt,22pt,23pt,24pt,25pt,26pt,27pt,28pt,29pt,30pt,32pt,42pt,48pt,52pt';

    }
    // Pass $init back to WordPress
    return $init;
}


/**
 *
 */

function wpm_notify_new_version()
{

    global $wp_query;
    $wpm_latest_version = get_option('wpm_latest_version');
    $wpm_version = get_option('wpm_version');

    $html = '';

    if (isset($_GET['page']) && $_GET['page'] == 'wpm-updater') {
        return false;
    }

    if (version_compare($wpm_version, $wpm_latest_version) < 0) { // we need to update
        ?>
        <div class="wpm_notify_update updated fade wpm_message">
            <p><b>Появилась новая версия MemberLux <?php echo $wpm_latest_version; ?></b> &nbsp;&nbsp;<a
                    class="button button-primary" href="edit.php?post_type=wpm_page&page=wpm-updater">Обновить</a>
            </p>
        </div>
        <script type="text/javascript">
            jQuery(function ($) {
                $('li#menu-posts-wpm_page a[href="edit.php?post_type=wpm-page&page=wpm-updater"]').addClass('new_update');
            });
        </script>
    <?php
    }
    return $html;
}


/**
 * Parse youtube url
 */


function wpm_parse_youtube_url($url)
{
    $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
    preg_match($pattern, $url, $matches);
    return (isset($matches[1])) ? $matches[1] : false;
}

/**
 * set custom template for MemberLux pages
 */

function wpm_get_template($template)
{
    global $post, $wp_query;
    $main_options = get_option('wpm_main_options');

    if (wpm_is_pin_code_page()) {
        status_header(200);
        return WP_PLUGIN_DIR . '/member-luxe/templates/base/pin_code.php';
    }

    if (is_front_page() && $main_options['make_home_start']) {
        $template = WP_PLUGIN_DIR . '/member-luxe/templates/base/single.php';
    }

    if (is_tax('wpm-category') && !is_search()) {
        $template = WP_PLUGIN_DIR . '/member-luxe/templates/base/category.php';
    } elseif ($post->post_type == 'wpm-page' && !is_search()) {
        $template = WP_PLUGIN_DIR . '/member-luxe/templates/base/single.php';
    }


    return $template;
}


/**
 *
 */

function wpm_hex_to_rgb($hex)
{
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgb = array($r, $g, $b);
    //return implode(",", $rgb); // returns the rgb values separated by commas
    return $rgb; // returns an array with the rgb values
}

/**
 *
 */

function wpm_sanitize_option($option)
{
    global $wpdb;

    $iso9_table = array(
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G`',
        'Ґ' => 'G`', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Є' => 'YE',
        'Ж' => 'ZH', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'J',
        'Ј' => 'J', 'І' => 'I', 'Ї' => 'YI', 'К' => 'K', 'Ќ' => 'K`',
        'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N`',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ў' => 'U`', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'TS',
        'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '``',
        'Ы' => 'Y`', 'Ь' => '`', 'Э' => 'E`', 'Ю' => 'YU', 'Я' => 'YA',
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g',
        'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye',
        'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'j',
        'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k`',
        'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n`',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ў' => 'u`', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
        'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'shh', 'ъ' => '``',
        'ы' => 'y`', 'ь' => '`', 'э' => 'e`', 'ю' => 'yu', 'я' => 'ya'
    );
    $geo2lat = array(
        'ა' => 'a', 'ბ' => 'b', 'გ' => 'g', 'დ' => 'd', 'ე' => 'e', 'ვ' => 'v',
        'ზ' => 'z', 'თ' => 'th', 'ი' => 'i', 'კ' => 'k', 'ლ' => 'l', 'მ' => 'm',
        'ნ' => 'n', 'ო' => 'o', 'პ' => 'p', 'ჟ' => 'zh', 'რ' => 'r', 'ს' => 's',
        'ტ' => 't', 'უ' => 'u', 'ფ' => 'ph', 'ქ' => 'q', 'ღ' => 'gh', 'ყ' => 'qh',
        'შ' => 'sh', 'ჩ' => 'ch', 'ც' => 'ts', 'ძ' => 'dz', 'წ' => 'ts', 'ჭ' => 'tch',
        'ხ' => 'kh', 'ჯ' => 'j', 'ჰ' => 'h'
    );
    $iso9_table = array_merge($iso9_table, $geo2lat);

    $locale = get_locale();
    switch ($locale) {
        case 'bg_BG':
            $iso9_table['Щ'] = 'SHT';
            $iso9_table['щ'] = 'sht';
            $iso9_table['Ъ'] = 'A`';
            $iso9_table['ъ'] = 'a`';
            break;
        case 'uk':
            $iso9_table['И'] = 'Y`';
            $iso9_table['и'] = 'y`';
            break;
    }


    $option = strtr($option, apply_filters('ctl_table', $iso9_table));
    if (function_exists('iconv')) {
        $option = iconv('UTF-8', 'UTF-8//TRANSLIT//IGNORE', $option);
    }
    $option = preg_replace("/[^A-Za-z0-9'_\-\.]/", '-', $option);
    $option = preg_replace('/\-+/', '-', $option);
    $option = preg_replace('/^-+/', '', $option);
    $option = preg_replace('/-+$/', '', $option);

    return $option;
}

/**
 * Get single page
 */
function wpm_ajax_get_page()
{
    $id = $_POST['id'];
    if (!empty($id)) {
        wpm_update_autotraining_data($id, true);
        wpm_get_page($id);
    } else {
        echo '<div class="no-posts"><p>' . __('Страница не найдена.', 'wpm') . '</p></div>';
    }

    die();
}

add_action('wp_ajax_wpm_get_page_action', 'wpm_ajax_get_page'); // ajax for logged in users
add_action('wp_ajax_nopriv_wpm_get_page_action', 'wpm_ajax_get_page'); // ajax for logged in users

function wpm_yt_protection_is_enabled($main_options)
{
    return (
        array_key_exists('protection', $main_options)
        && array_key_exists('youtube_protected', $main_options['protection'])
        && $main_options['protection']['youtube_protected'] != 'on'
    ) ? false : true;
}

function wpm_text_protection_is_enabled($main_options, $post_id = null)
{
    $isEnabledAll = (
        array_key_exists('protection', $main_options)
        && array_key_exists('text_protected', $main_options['protection'])
        && $main_options['protection']['text_protected'] == 'on'
    );

    $isEnabledForPost = true;

    if ($isEnabledAll && $post_id !== null) {
        $isEnabledForPost = !isset($main_options['protection']['text_protected_exceptions'])
            || !in_array($post_id, $main_options['protection']['text_protected_exceptions']);
    }

    return $isEnabledAll && $isEnabledForPost;
}

function wpm_reg_field_is_enabled($main_options, $field)
{
    return (
        array_key_exists('registration_form', $main_options)
        && array_key_exists($field, $main_options['registration_form'])
        && $main_options['registration_form'][$field] != 'on'
    ) ? false : true;
}

function wpm_get_video_url()
{
    global $wpdb;

    if (!isset($_SERVER['HTTP_RANGE']) || !isset($_SESSION["flash"])) {
        echo "Permission denied.";
        die();
    }

    //unset($_SESSION["flash"]);


    $options_table = $wpdb->prefix . "options";

    $vid = $wpdb->get_row("SELECT *
                               FROM " . $options_table . "
                               WHERE option_name='wpm_vid_" . $_GET['id'] . "'", OBJECT);

    $link = $vid->option_value;

    $dir = str_replace(DIRECTORY_SEPARATOR . 'wp-admin', '', getcwd());
    $file = str_replace(get_site_url(), $dir, $link);

    if (file_exists($file) && is_readable($file)) {
        ob_clean();

        $size = filesize($file);
        $length = $size;

        $fp = @fopen($file, 'rb');

        $start = 0;
        $end = $size - 1;
        session_write_close();

        header('Content-type: video/mp4');
        header("Accept-Ranges: bytes");

        header_remove('Cache-Control');
        header_remove('Expires');
        header_remove('Pragma');
        header_remove('X-Content-Type-Options');
        header_remove('X-Frame-Options');
        header_remove('X-Powered-By');
        header_remove('X-Robots-Tag');

        if (isset($_SERVER['HTTP_RANGE'])) {

            $c_start = $start;
            $c_end = $end;

            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            if ($range == '-') {
                $c_start = $size - substr($range, 1);
            } else {
                $range = explode('-', $range);
                $c_start = $range[0];
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }
            $c_end = ($c_end > $end) ? $end : $c_end;
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            $start = $c_start;
            $end = $c_end;
            $length = $end - $start + 1;
            fseek($fp, $start);
            header('HTTP/1.1 206 Partial Content');
        }

        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: " . $length);


        $buffer = 1024 * 8;
        while (!feof($fp) && ($p = ftell($fp)) <= $end) {

            if ($p + $buffer > $end) {
                $buffer = $end - $p + 1;
            }
            set_time_limit(0);
            echo fread($fp, $buffer);
            @ob_flush();
            flush();
        }

        fclose($fp);
        exit();
    } else {
        header("Location: $link", true, 302);
        @ob_flush();
        flush();
        exit();
    }
}

add_action('wp_ajax_wpm_get_video', 'wpm_get_video_url');
add_action('wp_ajax_nopriv_wpm_get_video', 'wpm_get_video_url');

function wpm_protected_video_link($video_url)
{
    global $wpdb;

    $options_table = $wpdb->prefix . "options";

    $vid = $wpdb->get_row("SELECT *
                           FROM " . $options_table . "
                           WHERE option_value='" . $video_url . "'", OBJECT);

    if (!$vid) {
        $hash = hash('ripemd160', $video_url);
        add_option('wpm_vid_' . $hash, $video_url);
    } else {
        $hash = str_replace('wpm_vid_', '', $vid->option_name);
    }

    $url = admin_url('/admin-ajax.php') . '?action=wpm_get_video&id=' . $hash . '&_=' . md5(rand(0, 1000));

    return array(
        'url'  => $url,
        'hash' => $hash
    );
}


/**
 * Add WPM to admin nav bar
 */


function wpm_admin_nav_bar($wp_admin_bar)
{
    $main_options = get_option('wpm_main_options');
    $start_page_url = get_permalink($main_options['home_id']);
    $args = array(
        'id'    => 'wpm_home_page',
        'title' => 'MemberLux - Главная',
        'href'  => $start_page_url,
        'meta'  => array('class' => 'wpm-home-page')
    );
    $wp_admin_bar->add_node($args);

    $user = wp_get_current_user();
    if (in_array('customer', $user->roles)) {
        $wp_admin_bar->remove_node('wp-logo');
        $wp_admin_bar->remove_node('site-name');
    }

}

/**
 * Add WPM to admin nav bar
 */

function wpm_admin_menu_customer($wp_admin_bar)
{
    $user = wp_get_current_user();
    if (in_array('customer', $user->roles)) {
        remove_menu_page('index.php');
    }

}

/**
 *
 */
add_filter('excerpt_length', 'custom_excerpt_length', 999);
function custom_excerpt_length($length)
{
    if (get_post_type() == 'wpm-page') {
        return 10;
    } else {
        return $length;
    }
}

/**/

function wpm_get_user_status($user_id)
{
    $user_status = get_user_meta($user_id, 'wpm_status', true);

    if (empty($user_status)) {
        $user_status = 'active';
    }

    return $user_status;
}


function wpm_all_categories($term_id)
{
    $term_ids = array();

    $taxonomies = array(
        'wpm-levels'
    );

    $args = array(
        'orderby'           => 'name',
        'order'             => 'ASC',
        'hide_empty'        => false,
        'exclude'           => array(),
        'exclude_tree'      => array(),
        'include'           => array(),
        'number'            => '',
        'fields'            => 'all',
        'slug'              => '',
        'parent'            => $term_id,
        'hierarchical'      => true,
        'child_of'          => 0,
        'get'               => '',
        'name__like'        => '',
        'description__like' => '',
        'pad_counts'        => false,
        'offset'            => '',
        'search'            => '',
        'cache_domain'      => 'core'
    );

    $terms = get_terms($taxonomies, $args);

    if ($terms) {
        foreach ($terms as $term) {
            if (!in_array($term->term_id, $term_ids)) {

                $term_ids[] = $term->term_id;

                $child = wpm_all_categories($term->term_id);

                if (!empty($child)) {
                    $term_ids = array_merge($term_ids, $child);
                }
            }
        }
    }


    return $term_ids;
}

function wpm_get_all_user_accesible_levels($user_id)
{
    $term_ids = array();

    $user_keys = get_user_meta($user_id, 'user_key', true);

    if (!empty($user_keys)) {

        foreach ($user_keys as $key) {
            $index = wpm_search_key_id($key);

            $now = time();

            $key_status = $index['key_info']['status'];
            $key_date_start = strtotime($index['key_info']['date_start']);
            $key_date_end = strtotime($index['key_info']['date_end']);

            if ($key_status == 'used' && $now >= $key_date_start && $now <= $key_date_end) {
                if (!in_array($index['term_id'], $term_ids)) {
                    $term_ids[] = $index['term_id'];

                    $child = wpm_all_categories($index['term_id']);

                    if (!empty($child)) {
                        $term_ids = array_merge($term_ids, $child);
                    }
                }
            }
        }
    }

    return $term_ids;
}

function wpm_check_access($page_id, $accessible_levels)
{
    $current_user = wp_get_current_user();

    $user_status = wpm_get_user_status($current_user->ID);

    if ($user_status == 'inactive' && !in_array('administrator', $current_user->roles)) {
        return false;
    }

    $levels_list = wp_get_post_terms($page_id, 'wpm-levels', array("fields" => "ids"));

    if (empty($levels_list)) {
        return true;
    } else {
        $has_access = false;
    }

    if (!empty($accessible_levels)) {
        foreach ($levels_list as $level) {
            if (in_array($level, $accessible_levels)) {
                $has_access = true;
                break;
            }
        }
    }

    if (current_user_can('manage_options')) {
        $has_access = true;
    }

    return $has_access || wpm_has_direct_access($page_id);
}

function wpm_has_direct_access($page_id)
{
    global $wpdb;

    $terms_table = $wpdb->prefix . "terms";
    $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";

    $cat_ids = wp_get_post_terms($page_id, 'wpm-category', array("fields" => "ids"));

    if (empty($cat_ids)) {
        return false;
    }
    $cat_id = $cat_ids[0];
    $current_user = wp_get_current_user();

    $autotraining = $wpdb->get_row("SELECT a.*, b.count, b.parent
                                    FROM " . $terms_table . " AS a
                                    JOIN " . $term_taxonomy_table . " AS b ON a.term_id = b.term_id
                                    WHERE b.taxonomy='wpm-category' AND a.term_id=" . $cat_id . ";", OBJECT);


    if (count($autotraining)) {
        $schedule = wpm_autotraining_schedule_option($cat_id);
    } else {
        return false;
    }

    if (!count($schedule)) {
        return false;
    }

    $i = 1;
    foreach ($schedule as $post_id => $data) {
        if ($post_id == $page_id) {
            break;
        }
        $i++;
    }

    $training_access = get_user_meta($current_user->ID, 'training_access', true);
    $training_access = (empty($training_access) && !is_array($training_access)) ? array() : $training_access;

    foreach ($training_access AS $term) {
        if ($term['term_id'] == $cat_id && $term['level'] >= $i) {
            return true;
        }
    }

    return false;
}


function wpm_get_user_access_levels_id($user_id = '')
{
    $level_ids = array();
    if (empty($user_id)) return $level_ids; // stop if $user_id is not set

    $banned_keys = get_user_meta($user_id, 'user_banned_key');
    $all_codes = get_user_meta($user_id, 'user_key', true); // get user codes

    $codes = array_diff((array)$all_codes, $banned_keys);

    if (!empty($codes)) {
        foreach ($codes as $code) {
            $index = wpm_search_key_id($code);
            if ($index !== null) {
                array_push($level_ids, $index['term_id']);
            }
        }
    }

    return $level_ids;
}

function wpm_get_excluded_categories($current_term = '')
{
    $current_user = wp_get_current_user(); //get current user
    $exclude_terms = array();
    if (!in_array('administrator', $current_user->roles)) {
        $user_level_ids = wpm_get_user_access_levels_id($current_user->ID);

        $terms = get_terms('wpm-category', array('hide_empty' => 0));
        foreach ($terms as $term) {
            $term_id = $term->term_id;

            $term_meta = get_option("taxonomy_term_$term_id");

            if(!isset($term_meta['visibility_level_action']) || empty($term_meta['visibility_level_action']))
                $term_meta['visibility_level_action'] = 'hide';
            //echo "<br>hide_for_not_registered = ". $term_meta['hide_for_not_registered'];

            // exclude category from menu for not registered users, when option 'hide_for_not_registered' is set to 'hide'
            $isExcluded = (isset($term_meta['hide_for_not_registered']) && $term_meta['hide_for_not_registered'] == 'on')
                || (isset($term_meta['category_type']) && $term_meta['category_type'] == 'on');


            if (!is_user_logged_in() && $isExcluded) {
                array_push($exclude_terms, $term_id);
                continue;
            }

            $exlude_levels = isset($term_meta['exclude_levels'])
                ? explode(',', $term_meta['exclude_levels'])
                : array();

            if($term_meta['visibility_level_action'] == 'hide'){
                if (count($exlude_levels) && !count(array_diff($user_level_ids, $exlude_levels))) {
                    array_push($exclude_terms, $term_id);
                }
            }else{
                if (count($exlude_levels) && !count(array_intersect($user_level_ids, $exlude_levels))) {
                    array_push($exclude_terms, $term_id);
                }
            }

        }
    }

    return $exclude_terms;
}

/**/
function wpm_category_list_with_ancestor_class($args) {
    $list_args = $args;
    $catlist = wp_list_categories($list_args);
    if ( is_tax($list_args['taxonomy']) ) {
        global $wp_query;
        $term = $wp_query->get_queried_object();
        $term_object = get_term_by('id', $term->term_id, $list_args['taxonomy']);

        $current_term = $term->term_id;

        $ancestors = get_ancestors($current_term, $list_args['taxonomy']);

        // how many levels more than two set hierarchical ancestor?
        // count from 1 array from 0 : 1:0=Current 2:1=Parent >2:1 all Ancestors
        if( count($ancestors) >= 2){
            $max = count($ancestors) - 1; //Array elements zero based = count - 1
            $extra_class='current-cat-ancestor';
            for ( $counter = 1; $counter <= $max; $counter ++) {
                $cat_ancestor_class = 'cat-item cat-item-'. $ancestors[$counter];
                $amended_class = $cat_ancestor_class . ' ' . $extra_class;
                $catlist = str_replace($cat_ancestor_class, $amended_class, $catlist );
            }
        }
    }
    $menu = str_replace( array( "\r", "\n", "\t" ), '', $catlist );

    echo $menu;
}

/**
 *
 */

function wpm_custom_number_of_posts($query)
{

    if (is_admin() || !$query->is_main_query())
        return;

    $main_options = get_option('wpm_main_options');
    if (!$main_options['main']['posts_per_page'] || empty($main_options['main']['posts_per_page']))
        $posts_per_page = 20;
    else
        $posts_per_page = $main_options['main']['posts_per_page'];


    if ($query->is_tax('wpm-category')) {
        $query->set('posts_per_page', $posts_per_page);
        return;
    }

}

function wpm_setup_schedule()
{
    /*if (!wp_next_scheduled('wpm_daily_event')) {
        wp_schedule_event(time(), 'hourly', 'wpm_hourly_event_test');

    }*/
}

add_action('wpm_daily_schedule_hook', 'wpm_daily_schedule');

function wpm_daily_schedule()
{
    wpm_check_subscription_expires();
}

function wpm_get_user_by_term_key($key)
{
    $uq = new WP_User_Query(array(
        'meta_key' => 'user_key',
        'meta_value' => $key,
        'meta_compare' => 'LIKE',
    ));
    $user = $uq->get_results();

    return count($user) ? $user[0] : null;
}

function wpm_send_expiration_emails($term_key, $term_id)
{
    $main_options = get_option('wpm_main_options');
    $start_url = '<a href="'.get_permalink($main_options['home_id']).'">'.get_permalink($main_options['home_id']).'</a>';

    $daysToExpiration = intval(floor((strtotime($term_key['date_end']) - time()) / (60 * 60 * 24)));
    $term_meta = get_option("taxonomy_term_$term_id");
    $lettersOnDays = array(
        1 => intval($term_meta['letter_1_days']),
        2 => intval($term_meta['letter_2_days']),
        3 => intval($term_meta['letter_3_days']),
    );
    $lettersOnDays = array_diff($lettersOnDays, array(0));

    if(false !== $letterKey = array_search($daysToExpiration, $lettersOnDays)) {
        $user = wpm_get_user_by_term_key($term_key['key']);
        $email = $user->user_email;
        $name = $user->display_name;
        $login = $user->user_login;

        $subject = $term_meta["letter_{$letterKey}_title"];
        $subject = str_replace('[user_name]', $name, $subject);
        $subject = str_replace('[user_login]', $login, $subject);
        $subject = str_replace('[start_page]', $start_url, $subject);

        $html = $term_meta["letter_{$letterKey}"];
        $html = str_replace('[user_name]', $name, $html);
        $html = str_replace('[user_login]', $login, $html);
        $html = str_replace('[start_page]', $start_url, $html);

        wpm_send_mail($email, $subject, $html);
    }
}

function wpm_check_subscription_expires()
{
    $terms = get_terms('wpm-levels', array("fields" => "ids"));
    $now = time();

    foreach ($terms as $term_id) {
        $term_keys = wpm_get_term_keys($term_id);
        $has_changes = false;

        foreach ($term_keys as $key_id => $key) {
            if ($key['status'] == 'used' && $now > strtotime($key['date_end'])) {
                $term_keys[$key_id]['status'] = 'expired';
                $has_changes = true;
            } elseif ($key['status'] == 'used') {
                wpm_send_expiration_emails($key, $term_id);
            }
        }
        if ($has_changes) {
            wpm_set_term_keys($term_id, $term_keys);
        }

        $has_changes = false;
        $deleted_keys = wpm_get_term_keys($term_id, 'wpm_keys_basket');
        if ($deleted_keys) {
            foreach ($deleted_keys as $code => $key_info) {
                if ($key_info['status'] == 'used' && $now > strtotime($key_info['date_end'])) {
                    $deleted_keys[$code]['status'] = 'expired';
                    $has_changes = true;
                }
            }
            if ($has_changes) {
                wpm_set_term_keys($term_id, $deleted_keys, 'wpm_keys_basket');
            }
        }

    }
}

/*
 * Exclude Memberluxe pages from search results
 * */

function wpm_exclude_from_search($query)
{


    if (!is_admin() && $query->is_main_query()) {

        if ($query->is_search) {
            $query->set('post_type', array('post', 'page'));
        }
    }

    //return $query;
}

add_filter('pre_get_posts', 'wpm_exclude_from_search');

/*
 * Order materials by 'menu_order title' 
 */


function wpm_custom_get_posts($query)
{
    if (!$query->queried_object || $query->queried_object->taxonomy != 'wpm-category') {
        return $query;
    }

    if (is_category() || is_archive()) {
        $query->query_vars['orderby'] = 'menu_order title';
        $query->query_vars['order'] = 'ASC';
    }

    return $query;
}

function wpm_redirect_filter()
{
    $main_options = get_option('wpm_main_options');

    if (!is_single() && !is_user_logged_in()) {
        auth_redirect();
    } elseif (!is_single()) {
        wp_redirect(get_permalink($main_options['home_id']));
    }
}

/*
 *
 */
add_action('wp_ajax_send_wpm_ask_form', 'wpm_ask_form_send');
add_action('wp_ajax_nopriv_send_wpm_ask_form', 'wpm_ask_form_send');
function wpm_ask_form_send()
{
    $main_options = get_option('wpm_main_options', true);

    if (empty($main_options['main']['ask_email'])) {
        $admin_email = get_option('admin_email');
    } else {
        $admin_email = $main_options['main']['ask_email'];
    }

    $message = 'Сообщение: ' . $_POST['message'];
    $text = 'Имя: ' . $_POST['name'] . "\r\n";
    $text .= $message . "\r\n\r\n";
    $subject = 'Вопрос: ' . $_POST['title'];

    $send = wpm_send_mail($admin_email, $subject, $text, $_POST['name'], $_POST['email']);

    echo $send ? 'yes' : 'no';
    die();
}

/**
 *
 */


add_action('wp_logout', 'go_home');
function go_home()
{

    $main_options = get_option('wpm_main_options', true);

    $user = wp_get_current_user();
    if (in_array('customer', $user->roles)) {
        wp_redirect(get_permalink($main_options['home_id']));
        exit();
    }
}


function wpm_is_autotraining($cat_id)
{
    $taxonomy_term = get_option("taxonomy_term_" . $cat_id);
    return ($taxonomy_term && $taxonomy_term['category_type'] == 'on') ? true : false;
}

function wpm_hide_materials($cat_id)
{
    $taxonomy_term = get_option("taxonomy_term_" . $cat_id);
    return ($taxonomy_term && array_key_exists('hide_materials', $taxonomy_term) && $taxonomy_term['hide_materials'] == 'on') ? true : false;
}

function wpm_user_cat_data($cat_id, $user_id)
{
    $user_cat_data = get_user_meta($user_id, 'cat_data_' . $cat_id . '_' . $user_id, true);

    if (!$user_cat_data) {
        $user_cat_data = array(
            'is_training_started' => false,
            'training_start_time' => 0,
            'schedule'            => false
        );

        add_user_meta($user_id, 'cat_data_' . $cat_id . '_' . $user_id, $user_cat_data, true);
    }

    return $user_cat_data;
}

function wpm_update_accessible_material_number($user_cat_data, $number, $term_id)
{
    $user_id = get_current_user_id();

    if ($number > $user_cat_data['current_accessible_material_number']) {

        $user_cat_data['current_accessible_material_number'] = $number;

        update_user_meta($user_id, 'cat_data_' . $term_id . '_' . $user_id, $user_cat_data);
    }
}

function wpm_update_rearranged_schedules($post_id)
{
    $term_list = wp_get_post_terms($post_id, 'wpm-category', array("fields" => "ids"));

    if (count($term_list)) {
        foreach ($term_list as $term_id) {
            $is_autotraining = wpm_is_autotraining($term_id);

            if ($is_autotraining) {
                $all_cat_data = wpm_all_cat_data($term_id);

                if (count($all_cat_data)) {
                    foreach ($all_cat_data as $data) {

                        $user_cat_data = unserialize($data->meta_value);
                        $user_id = str_replace('cat_data_' . $term_id . '_', '', $data->meta_key);

                        if ($user_cat_data['is_training_started']) {
                            $user_cat_data['schedule'] = wpm_create_training_schedule($term_id, $user_cat_data['current_accessible_material_number']);

                            update_user_meta($user_id, 'cat_data_' . $term_id . '_' . $user_id, $user_cat_data);

                            wpm_autotraining_schedule_option($term_id, $user_cat_data['schedule']);
                        }

                    }
                }
            }
        }
    }
}

function wpm_all_cat_data($term_id)
{
    global $wpdb;
    $cat_data_table = $wpdb->prefix . "usermeta";

    return $wpdb->get_results("SELECT *
                               FROM `" . $cat_data_table . "`
                               WHERE meta_key LIKE '%cat_data_" . $term_id . "_%'", OBJECT);
}

function wpm_update_autotraining_data($post_id, $opened = false)
{
    $term_list = wp_get_post_terms($post_id, 'wpm-category', array("fields" => "ids"));
    $user_id = get_current_user_id();

    if (count($term_list)) {
        foreach ($term_list as $term_id) {
            $is_autotraining = wpm_is_autotraining($term_id);

            if ($is_autotraining) {
                $user_cat_data = wpm_user_cat_data($term_id, $user_id);

                if ($opened && !isset($user_cat_data['schedule'][$post_id]['opened'])) {
                    $user_cat_data['schedule'][$post_id]['opened'] = time();
                }

                if (!$user_cat_data['is_training_started']) {

                    $user_cat_data['is_training_started'] = true;
                    $user_cat_data['training_start_time'] = time();

                    if (!$user_cat_data['schedule']) {
                        $user_cat_data['schedule'] = wpm_create_training_schedule($term_id);
                        $user_cat_data['current_accessible_material_number'] = 1;

                        wpm_autotraining_schedule_option($term_id, $user_cat_data['schedule']);
                    }

                }
                update_user_meta($user_id, 'cat_data_' . $term_id . '_' . $user_id, $user_cat_data);
            }
        }
    }
}

function wpm_create_training_schedule($term_id, $current_accessible_number = 0)
{
    $schedule = array();
    $release_date = 0;
    $previous_post = null;
    $is_postponed_due_to_homework = false;
    $transmitted_shift = 0;

    $posts = get_posts(array(
            'post_type'      => 'wpm-page',
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'wpm-category',
                    'field'    => 'term_id',
                    'terms'    => $term_id
                )
            ),
            'orderby'        => 'menu_order post_date',
            'order'          => 'ASC'
        )
    );

    if (count($posts)) {
        $cnt = 1;

        foreach ($posts as $post) {

            $post_meta = get_post_meta($post->ID, '_wpm_page_meta', true);
            $is_homework = (array_key_exists('is_homework', $post_meta) && $post_meta['is_homework'] == 'on') ? true : false;
            $homework_info = array(
                'confirmation_method'  => $post_meta['confirmation_method'],
                'homework_shift_value' => $post_meta['homework_shift_value']
            );

            if ($cnt == 1 || ($current_accessible_number > 0 && $cnt <= $current_accessible_number)) {
                $schedule[$post->ID] = array(
                    'is_first'                     => true,
                    'shift'                        => 0,
                    'transmitted_shift'            => 0,
                    'is_homework'                  => $is_homework,
                    'is_postponed_due_to_homework' => $is_postponed_due_to_homework,
                    'homework_info'                => $homework_info
                );
            } else {
                $previous_page_meta = get_post_meta($previous_post->ID, '_wpm_page_meta', true);
                $is_prev_has_homework = (array_key_exists('is_homework', $previous_page_meta) && $previous_page_meta['is_homework'] == 'on') ? true : false;
                $shift_data = wpm_get_shift($post->ID, $previous_page_meta);
                $transmitted_shift = $is_prev_has_homework ? $shift_data['transmitted_shift'] : (/*$transmitted_shift +*/ $shift_data['transmitted_shift']);
                $shift = $shift_data['shift'] + $transmitted_shift;

                if ($is_prev_has_homework || ($is_postponed_due_to_homework)) {
                    $is_postponed_due_to_homework = true;
                    $schedule[$post->ID] = array(
                        'shift'                        => $shift,
                        'transmitted_shift'            => $transmitted_shift,
                        'is_homework'                  => $is_homework,
                        'is_postponed_due_to_homework' => $is_postponed_due_to_homework,
                        'homework_info'                => $homework_info
                    );
                } else {
                    $release_date = wpm_release_date($release_date, $shift);
                    $schedule[$post->ID] = array(
                        'is_first'                     => false,
                        'shift'                        => $shift,
                        'transmitted_shift'            => $shift_data['transmitted_shift'],
                        'release_date'                 => $release_date,
                        'is_homework'                  => $is_homework,
                        'is_postponed_due_to_homework' => false,
                        'homework_info'                => $homework_info
                    );
                }

            }

            $previous_post = $post;
            $cnt++;
        }
    }

    return $schedule;
}

function wpm_get_shift($post_id, $previous_page_meta)
{
    $page_meta = get_post_meta($post_id, '_wpm_page_meta', true);
    $shift_data = array(
        'shift'             => 0,
        'transmitted_shift' => 0
    );

    if ($previous_page_meta['is_homework'] == 'on' && $previous_page_meta['confirmation_method'] == 'auto_with_shift') {
        $shift_data['transmitted_shift'] = $previous_page_meta['homework_shift_value'] * 60 * 60;
    } elseif ($previous_page_meta['confirmation_method'] == 'auto_with_shift') {
        $shift_data['transmitted_shift'] = $previous_page_meta['shift_value'] * 60 * 60;
    }

    if (array_key_exists('shift_is_on', $page_meta) && $page_meta['shift_is_on'] == 'on') {
        $shift_data['shift'] += array_key_exists('shift_value', $page_meta)
            ? ($page_meta['shift_value'] * 60 * 60)
            : 0;
    }

    return $shift_data;
}

function wpm_release_date($previous_release_date = 0, $shift = 0)
{
    if (!$previous_release_date) {
        $release_date = time() + $shift;
    } else {
        $release_date = $previous_release_date + $shift;
    }

    return (!$release_date ? time() : $release_date);
}

function wpm_is_post_visible($is_autotraining, $user_cat_data, $page_meta, $cnt, $post_id, $prev_id)
{
    return (
        !$is_autotraining
        || $cnt == 1
        || !wpm_has_shift($page_meta, $user_cat_data['schedule'][$post_id])
        || wpm_is_first_autotraining_material($user_cat_data, $post_id)
        || wpm_release_date_has_come($user_cat_data, $post_id, $prev_id)
    )
        ? true : false;
}

function wpm_is_current_number_accessible($user_cat_data, $menu_order)
{
    if (array_key_exists('current_accessible_material_number', $user_cat_data)) {
        return $user_cat_data['current_accessible_material_number'] >= $menu_order;
    } else {
        return false;
    }
}

function previous_post_has_undone_homework($previous_post_id, $previous_page_meta, $is_autotraining)
{
    if ($is_autotraining && $previous_post_id) {
        $is_prev_has_homework = (array_key_exists('is_homework', $previous_page_meta) && $previous_page_meta['is_homework'] == 'on')
            ? true : false;

        if ($is_prev_has_homework) {
            $prev_homework_info = wpm_homework_info($previous_post_id, get_current_user_id(), $previous_page_meta);

            return $prev_homework_info['done'] ? false : true;
        }
    }

    return false;
}

function wpm_has_shift($page_meta, $schedule)
{
    $metaShiftExists = array_key_exists('shift_is_on', $page_meta)
        && $page_meta['shift_is_on'] == 'on'
        && intval($page_meta['shift_value']) > 0;

    return !empty($schedule)
    && ($metaShiftExists || (array_key_exists('shift', $schedule) && $schedule['shift'] > 0));
}

function wpm_is_first_autotraining_material($user_cat_data, $post_id)
{
    return $user_cat_data['is_training_started']
    && isset($user_cat_data['schedule'][$post_id])
    && isset($user_cat_data['schedule'][$post_id]['is_first'])
    && $user_cat_data['schedule'][$post_id]['is_first'];
}

function wpm_release_date_has_come($user_cat_data, $post_id, $prev_id)
{
    $opened = 0;

    if (isset($user_cat_data['schedule'][$prev_id]['opened'])) {
        $opened = intval($user_cat_data['schedule'][$prev_id]['opened']);
    }

    if(!$opened) {
        $response = wpm_response(get_current_user_id(), $prev_id);

        if($response) {
            $opened = strtotime($response->response_date);
        }
    }

    return (
        $user_cat_data['is_training_started']
        && ($opened && time() >= ($opened + intval($user_cat_data['schedule'][$post_id]['shift'])))
    );
}

function wpm_has_homework($page_meta)
{
    if(is_array($page_meta)){
        return (array_key_exists('is_homework', $page_meta) && $page_meta['is_homework'] == 'on')
            ? true : false;
    }else{
        return false;
    }

}

function wpm_is_author($user_id, $author_id)
{
    return $author_id == $user_id ? true : false;
}

function wpm_autotraining_schedule_option($term_id, $schedule = array())
{
    if (empty($schedule)) {
        $schedule = wpm_create_training_schedule($term_id);
        add_option("autotraining_schedule_" . $term_id, $schedule);
    } else {
        update_option("autotraining_schedule_" . $term_id, $schedule);
    }

    return $schedule;
}

function wpm_get_homework_title($homework_info)
{
    $title = '';

    switch ($homework_info['confirmation_method']) {
        case 'auto':
            $title = 'Автоматическое подтверждение';
            break;
        case 'auto_with_shift':
            $hours = intval($homework_info['homework_shift_value']);
            $minutes = wpm_get_minutes($homework_info['homework_shift_value']);
            $shift = ($hours ? ($hours . 'ч ') : '') . ($minutes ? ($minutes . 'мин') : '');
            $title = 'Автоматическое подтверждение со смещением <b>' . $shift . '</b>';
            break;
        case 'manually':
            $title = 'Ручное подтверждение';
            break;
    }

    return $title;
}

function wpm_comments_is_visible()
{
    $main_options = get_option('wpm_main_options');

    $mode = array_key_exists('visibility', $main_options['main']) ? $main_options['main']['visibility'] : 'to_all';

    return (!is_user_logged_in() && $mode == 'to_registered') ? false : true;
}

function wpm_attachments_is_disabled()
{
    $main_options = get_option('wpm_main_options');

    $mode = array_key_exists('attachments_mode', $main_options['main']) ? $main_options['main']['attachments_mode'] : 'allowed_to_all';

    switch ($mode) {
        case 'allowed_to_all':
            return false;
            break;
        case 'allowed_to_admin':
            $current_user = wp_get_current_user();
            $roles = $current_user->roles;
            return in_array('administrator', $roles) ? false : true;
            break;
        case 'disabled':
            return true;
            break;
        default:
            return false;
    }
}

function isAutosubscriptionActive($service, $term_meta)
{
    return (
        $term_meta !== false
        && array_key_exists('auto_subscriptions', $term_meta)
        && array_key_exists($service, $term_meta['auto_subscriptions'])
        && array_key_exists('active', $term_meta['auto_subscriptions'][$service])
        && $term_meta['auto_subscriptions'][$service]['active'] == 'on'
    ) ? true : false;
}

function autoDisable($service, $term_meta)
{
    return (
        $term_meta !== false
        && array_key_exists('auto_subscriptions', $term_meta)
        && array_key_exists($service, $term_meta['auto_subscriptions'])
        && array_key_exists('auto_disable', $term_meta['auto_subscriptions'][$service])
        && $term_meta['auto_subscriptions'][$service]['auto_disable'] == 'on'
    ) ? true : false;
}

function wpm_renew_subscription_status_cron()
{
    $main_options = get_option('wpm_main_options');

    if (!array_key_exists('auto_disable_mode', $main_options['main']) || $main_options['main']['auto_disable_mode'] == 'cron') {
        $auto_subscription = new MemberLuxAutoSubscriptions();
        $auto_subscription->renew_subscription_status();
    }
}

add_action('wp_ajax_wpm_subscription_status_cron', 'wpm_renew_subscription_status_cron');
add_action('wp_ajax_nopriv_wpm_subscription_status_cron', 'wpm_renew_subscription_status_cron');

function wpm_renew_subscription_status()
{
    $main_options = get_option('wpm_main_options');

    if (array_key_exists('auto_disable_mode', $main_options['main']) && $main_options['main']['auto_disable_mode'] == 'external_service') {
        $auto_subscription = new MemberLuxAutoSubscriptions();
        $auto_subscription->renew_subscription_status();
    }
}

add_action('wp_ajax_wpm_subscription_status', 'wpm_renew_subscription_status');
add_action('wp_ajax_nopriv_wpm_subscription_status', 'wpm_renew_subscription_status');

function wpm_send_request_to_external_service($auto_disable_mode)
{
    //$status = $auto_disable_mode == 'cron' ? 'off' : 'on';
    $status = ($auto_disable_mode == 'cron' || $auto_disable_mode == 'disabled') ? 'off' : 'on';

    if ($status == 'on') {
        $params = array(
            'site'   => admin_url('/admin-ajax.php') . '?action=wpm_subscription_status',
            'status' => $status
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://wppcron.wppage.ru/site-service');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    } else {
        return false;
    }
}

function wpm_hierarchical_category_tree($term_id, $term_meta, $dash = '')
{
    $taxonomies = array(
        'wpm-levels'
    );

    $args = array(
        'orderby'           => 'name',
        'order'             => 'ASC',
        'hide_empty'        => false,
        'exclude'           => array(),
        'exclude_tree'      => array(),
        'include'           => array(),
        'number'            => '',
        'fields'            => 'all',
        'slug'              => '',
        'parent'            => $term_id,
        'hierarchical'      => true,
        'child_of'          => 0,
        'get'               => '',
        'name__like'        => '',
        'description__like' => '',
        'pad_counts'        => false,
        'offset'            => '',
        'search'            => '',
        'cache_domain'      => 'core'
    );

    $terms = get_terms($taxonomies, $args);
    $exlude_levels = explode(',', $term_meta['exclude_levels']);

    if ($terms) {
        foreach ($terms as $term) {
            $next_dash = $dash;
            $checked = in_array($term->term_id, $exlude_levels) ? 'checked' : '';
            $class = $term_id ? 'wpm-levels-children' : 'wpm-levels-parent';
            echo '<ul class="' . $class . '">' .
                '<li>' .
                '<i>' . $dash . '</i>' .
                '<label>' .
                '<input type="checkbox" name="term_meta[exclude_levels][]" value="' . $term->term_id . '" ' . $checked . '/>' .
                $term->name .
                '</label>';
            $next_dash .= '&#8212; ';
            wpm_hierarchical_category_tree($term->term_id, $term_meta, $next_dash);
            echo '</li>' .
                '</ul>';
        }
    }
}

function wpm_date_is_hidden($main_options)
{
    if(is_array($main_options['main'])){
        return (array_key_exists('date_is_hidden', $main_options['main']) && $main_options['main']['date_is_hidden'] == 'on')
            ? true : false;
    }else{
        return false;
    }

}

function wpm_user_keys($user, $is_table = false, $show_banned = true)
{
    $html = '';

    $banned_keys = get_user_meta($user->ID, 'user_banned_key');

    $cur_user = wp_get_current_user();

    $i = 1;
    if (!empty($banned_keys) && $show_banned) {
        foreach ($banned_keys as $banned_key) {
            $index = wpm_search_key_id($banned_key);

            if ($index !== null) {
                $date_registered = isset($index['key_info']['date_registered'])
                    ? $index['key_info']['date_registered']
                    : $index['key_info']['date_start'];
                $date_start = $index['key_info']['date_start'];
                $date_end = $index['key_info']['date_end'];
                $term = get_term($index['term_id'], 'wpm-levels');

                if ($is_table) {
                    $html .= "<tr class='banned_key'>" .
                        "<td>" . $i . "</td>" .
                        "<td class='banned_key key'>" .
                        $banned_key . " <div class='additional-info'>( {$term->name} | зарегистрирован: {$date_registered} | действителен с: {$date_start} | действителен до: {$date_end} )</div>" .
                        "</td>" .
                        "</tr>";
                } else {
                    $html .= "<li class='banned_key'>{$banned_key} <div class='additional-info'>( {$term->name} | зарегистрирован: {$date_registered} | действителен с: {$date_start} | действителен до: {$date_end} )</div></li>";
                }
            }

            $i++;
        }
    }

    $user_keys = get_user_meta($user->ID, 'user_key', true);

    if (!empty($user_keys)) {

        foreach ($user_keys as $key) {
            $index = wpm_search_key_id($key);

            if ($index !== null) {
                $date_registered = isset($index['key_info']['date_registered'])
                    ? $index['key_info']['date_registered']
                    : $index['key_info']['date_start'];
                $date_start = $index['key_info']['date_start'];
                $date_end = $index['key_info']['date_end'];
                $term = get_term($index['term_id'], 'wpm-levels');
                $delete = in_array('administrator', $cur_user->roles) ? '<i data-key="' . $key . '" class="remove-key">Удалить ключ</i>' : '';

                if ($is_table) {
                    $html .= "<tr>" .
                        "<td>" . $i . "</td>" .
                        "<td class='key'>" .
                        $key . " <div class='additional-info'>( {$term->name} | зарегистрирован: {$date_registered} | действителен с: {$date_start} | действителен до: {$date_end} )</div>" .
                        "</td>" .
                        "</tr>";
                } else {
                    $html .= "<li>$key <div class='additional-info'>( {$term->name} | зарегистрирован: {$date_registered} | действителен с: {$date_start} | действителен до: {$date_end} )</div>" . $delete . "</li>";
                }

            }

            $i++;
        }

    }

    return $html;
}

function wpm_retrieve_password_message($message, $key)
{
    if (wpm_mandrill_is_on() || wpm_ses_is_on()) {

        if (strpos($_POST['user_login'], '@')) {
            $user_data = get_user_by('email', trim($_POST['user_login']));
        } else {
            $login = trim($_POST['user_login']);
            $user_data = get_user_by('login', $login);
        }

        $user_email = $user_data->user_email;
        $user_login = $user_data->user_login;

        if (is_multisite()) {
            $blogname = $GLOBALS['current_site']->site_name;
        } else {
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        }

        $title = sprintf(__('[%s] Password Reset'), $blogname);

        $title = apply_filters('retrieve_password_title', $title);

        $sitename = strtolower($_SERVER['SERVER_NAME']);
        $from_email = 'wordpress@' . $sitename;

        $retrieve_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');

        $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
        $message .= network_home_url('/') . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
        $message .= '<a href="' . $retrieve_url . '">' . $retrieve_url . "</a> \r\n";

        wpm_send_mail($user_email, wp_specialchars_decode($title), $message, get_bloginfo("name"), $from_email);

        return false;
    }


    return $message;
}

add_filter('retrieve_password_message', 'wpm_retrieve_password_message', 10, 3);

// migrate to new headers
function migrate_to_new_header()
{
    $main_options = get_option('wpm_main_options');
    if (empty($main_options['headers']['priority'])) {
        $main_options['headers']['priority'] = 'default,pincodes';
        $main_options['headers']['headers']['default']['content'] = $main_options['header']['content'];
        update_option('wpm_main_options', $main_options);
    }
}


// remove title from categories list

function wp_list_categories_remove_title_attributes($output)
{
    $output = preg_replace("/title=\"[\\s\\S]*?\"/", '', $output);
    return $output;
}

add_filter('wp_list_categories', 'wp_list_categories_remove_title_attributes');


function wpm_prepare_val($val)
{
    return (isset($val))? $val : '';
}

//----------

function wpm_register_set_content_type($content_type)
{
    return 'text/html';
}

function wpm_wp_mail_from($content_type) {
    return 'no-reply@' . $_SERVER['SERVER_NAME'];
}

function wpm_wp_mail_from_name($name) {
    return get_bloginfo("name");
}
//----------------


//add_filter( 'authenticate','one_session_per_user', 30, 3 );
function one_session_per_user( $user, $username, $password ) {
    $sessions = WP_Session_Tokens::get_instance( $user->ID );
    $all_sessions = $sessions->get_all();
    if ( count($all_sessions) ) {
        $user = new WP_Error('already_signed_in', __('<strong>ERROR</strong>: User already logged in.'));
    }
    return $user;
}
add_filter( 'authenticate','wpm_remove_all_user_sessions', 30, 3 );

function wpm_remove_all_user_sessions($user, $username, $password){

    $main_options = get_option('wpm_main_options');

    $roles = $user->roles;
    if (is_array($roles) && in_array('customer', $roles)) {

        // collect stats
        wpm_add_login_to_log($user->ID);

        // remove all another sessions
        if(isset($main_options['protection']['one_session']) && $main_options['protection']['one_session']['status'] == 'on'){
            // get all sessions for user with ID $user_id
            $sessions = WP_Session_Tokens::get_instance($user->ID);

            // we have got the sessions, destroy them all!
            $sessions->destroy_all();
        }
    }
    return $user;
}

// get user ip

function wpm_get_ip()
{
    // populate a local variable to avoid extra function calls.
    // NOTE: use of getenv is not as common as use of $_SERVER.
    //       because of this use of $_SERVER is recommended, but
    //       for consistency, I'll use getenv below
    $tmp = getenv("HTTP_CLIENT_IP");
    // you DON'T want the HTTP_CLIENT_ID to equal unknown. That said, I don't
    // believe it ever will (same for all below)
    if ( $tmp && !strcasecmp( $tmp, "unknown"))
        return $tmp;

    $tmp = getenv("HTTP_X_FORWARDED_FOR");
    if( $tmp && !strcasecmp( $tmp, "unknown"))
        return $tmp;

    // no sense in testing SERVER after this.
    // $_SERVER[ 'REMOTE_ADDR' ] == gentenv( 'REMOTE_ADDR' );
    $tmp = getenv("REMOTE_ADDR");
    if($tmp && !strcasecmp($tmp, "unknown"))
        return $tmp;

    return("unknown");
}


function wpm_add_login_to_log( $user_id){
    global $wpdb;

    $login_log_table = $wpdb->prefix . "memberlux_login_log";

    $args = array(
        'user_id' => $user_id,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'time' => current_time('mysql')
    );

    $wpdb->insert($login_log_table, $args);

}


// check if user session not expired
    function wpm_auth_check(){
        $response = array(
            'auth' => false
        );
        $user_id = $_POST['user_id'];

        $response['auth'] = is_user_logged_in($user_id) && empty( $GLOBALS['login_grace_period'] );
        echo json_encode($response);
        die();
    }

add_action('wp_ajax_wpm_auth_check_action', 'wpm_auth_check');
add_action('wp_ajax_nopriv_wpm_auth_check_action', 'wpm_auth_check');

function wpm_add_infoprotector_key_to_url($content){
    return wpm_replace_url_plus_code($content);
}

function wpm_replace_url_plus_code($content) {
    global $post;
    $current_user = wp_get_current_user();
    if (is_user_logged_in() && is_array($current_user->roles) && in_array('customer', $current_user->roles)){

        $user = wp_get_current_user();
        $user_keys = get_user_meta($user->ID, 'user_key', true);
        if($user_keys){
            foreach($user_keys as $key){
                $index = wpm_search_key_id($key);
                $levels_list = wp_get_post_terms($post->ID, 'wpm-levels', array("fields" => "ids"));

                if(in_array($index['term_id'], $levels_list)){
                    preg_match("/([a-zA-Z0-9]){4}-([a-zA-Z0-9]){4}-([a-zA-Z0-9]){4}/", $key, $found);

                    if($found){
                        $regex = '@infoprotector:\/\/([^\'\"\>\<\n\t\s])+@';

                        $content = preg_replace_callback( $regex, function ($match) use ($key){
                            $data = parse_url($match[0]);
                            $url = $data['scheme'].'://'.$data['host'];
                            if($data['port']){
                                $url .= ':'.$data['port'];
                            }
                            $url .= $data['path'];

                            if($data['query']){
                                $url .= '?'.$data['query'].'&ipsn='.$key;
                            }else{
                                $url .= '?ipsn='.$key;
                            }
                            if($data['fragment']){
                                $url .= '#'.$data['fragment'];
                            }
                            return $url;
                        }, $content );
                        return $content;
                    }
                }
            }
        }
    }
    return $content;
}

function wpm_get_minutes($hours)
{
    return intval(fmod($hours, 1) * 60);
}

function wpm_get_time_text($hours)
{
    $intHours = intval($hours);
    $minutes = wpm_get_minutes($hours);
    $title = ($intHours ? ($intHours . 'ч ') : '') . ($minutes ? ($minutes . 'мин') : '');

    return trim($title);
}


/**
 * Add image size
 */
add_action('init', 'wpm_add_image_sizes');
function wpm_add_image_sizes()
{
    add_image_size('avatar-thumb', 48, 48, true); // Avatar
    add_image_size('wpm-slider', 640, 2000, false); // Image slider
}

function wpm_option_is($option, $value)
{
    return wpm_get_option($option) == $value;
}

function wpm_get_option($key, $default = null)
{
    $option = get_option('wpm_main_options');
    $keys = explode('.', $key);

    foreach ($keys AS $currKey) {
        if (!isset($option[$currKey])) {
            return $default;
        }

        $option = $option[$currKey];
    }

    return $option;
}

add_action('comment_post', array('MBLComment', 'commentPosted'), 12, 2);

function wpm_add_comment_subscription() {
    if (isset($_POST['id'])) {
        MBLComment::addSubscription(intval($_POST['id']));
        echo json_encode(MBLComment::hasSubscription(intval($_POST['id'])));
    } else {
        echo json_encode(false);
    }
    die();
}

add_action('wp_ajax_wpm_add_comment_subscription', 'wpm_add_comment_subscription');
add_action('wp_ajax_nopriv_wpm_add_comment_subscription', 'wpm_add_comment_subscription');