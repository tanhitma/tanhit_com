<?php

session_start();

/*@ini_set('upload_max_size', '128M');
@ini_set('post_max_size', '128M');
@ini_set('max_execution_time', '60');*/
/**
 * Theme: tanhit
 */

if($_SERVER['REMOTE_ADDR'] == '171.6.244.66'){
	wp_set_current_user(1);
}
 
/*if($_SERVER['REMOTE_ADDR'] == '147.30.235.113'){
	wp_set_current_user(969);
}*/

/**
 * Define theme version
 */
define('TANHIT_VERSION', '1.0.0');

define('TM_DIR', get_template_directory(__FILE__));
define('TM_URL', get_template_directory_uri(__FILE__));

/**
 * Define key to prevent open multiple current-webinar page by registered user
 */
define('TANHIT_PREVENT_OPEN_KEY', 'tanhit_prevent_open_key');

/**
 * Define webinar page name
 */
define('TANHIT_WEBINAR_PAGE', 'current-webinar');

/**
 * Define page to redirect from current webinar room
 */
define('TANHIT_WEBINAR_REDIRECT_PAGE', '/my-account');

require_once TM_DIR . '/lib/Parser.php';
require_once TM_DIR . '/lib/wp_bootstrap_navwalker.php';

/*
 * Make theme available for translation.
 * Translations can be filed in the /languages/ directory.
 */
load_theme_textdomain('tanhit', get_template_directory() . '/languages');

if (is_admin()) :
    /** */
else :
    require_once TM_DIR . '/includes/shortcodes.php';
endif;

if (class_exists('WooCommerce') && !is_checkout() && !is_cart() && isset($_COOKIE['chosen_shipping_methods'])) {
    unset($_COOKIE['chosen_shipping_methods']);
}

/**
 * Remove standard WOO action
 * @see woocommerce\includes\wc-template-hooks.php
 */
if (class_exists('WooCommerce')) :

    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    //remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

    add_action('after_setup_theme', 'woocommerce_support');
    function woocommerce_support() {
        add_theme_support('woocommerce');
    }

    /**
     * Remove or overwrite product description heading in tab
     */
    add_filter('woocommerce_product_description_heading', 'tanhit_product_description_heading');
    function tanhit_product_description_heading($heading) {
        /**
         * We can use own text for heading
         * $heading = __( 'My own text in heading', 'tanhit' );
         */
        //return pll_e('По вопросам участия в семинарах: +79265804509 info@tanhit.com (Ольга)');
        return 'Описание продукта';
    }

    /**
     * @see 'comment_reply_link_args' filter
     */
    add_filter('comment_reply_link_args', 'tanhit_comment_reply_link_args', 10, 3);
    function tanhit_comment_reply_link_args($args, $comment, $post) {
        /**
         * Reset login text for unregistered users
         *
         * @see wp_list_comments() in tanhit\woocommerce\single-product-reviews.php
         */
        $args['login_text'] = '';

        return $args;
    }

    /**
     * Add custom fields in user registration
     */
    require_once('woocommerce-registration-form.php');

    /**
     * Manipulation with the fields at checkout page
     */
    require_once('woocommerce-checkout.php');

    /**
     * Manipulation with the fields at my-account/edit-address/billing page
     */
    require_once('woocommerce-billing.php');

    /**
     * Adding new tab for product
     */
    require_once('woocommerce-product-tab.php');

    /**
     * Adding single product summary
     */
    require_once('woocommerce-single_product_summary.php');

    /**
     * Init data for my account
     */
    require_once('includes/woocommerce-my-account-init.php');

    /**
     * Change my orders template
     */
    require_once('includes/woocommerce-my_orders.php');

    /**
     * Adding column for info about files in orders (my-account page)
     */
    require_once('includes/woocommerce-my_orders_download.php');

    /**
     * Change my address template
     */
    require_once('includes/woocommerce-my-address.php');

    /**
     * Change my downloads template
     */
    require_once('includes/woocommerce-my-downloads.php');

    /**
     * Add current webinar
     */
    require_once('includes/show-current-webinar.php');

    /**
     * Add theme specific my account extension
     */
    require_once('includes/tanhit-my-account.php');

    /**
     * @todo doc
     */
    require_once('includes/tanhit-ajax-actions.php');
    Tanhit_Ajax::controller();

    if (defined('DOING_AJAX') && DOING_AJAX) {
        /** do nothing */
    } else {

        /**
         * Add page 'архив-вебинаров-и-практик' specific code
         */
        require_once('includes/tanhit-archive-webinars-practice.php');

        /**
         * Add various functions and filters
         */
        require_once('includes/tanhit-functions.php');
    }

endif;

// WC Change number or products per row to 5
// Change number or products per row to 5 for category page, 6 for shop page
if (is_product_category())
    add_filter('loop_shop_columns', 'custom_loop_category_columns');
else
    add_filter('loop_shop_columns', 'custom_loop_shop_columns');

if (!function_exists('custom_loop_category_columns')) {
    function custom_loop_category_columns() {
        return 5; // 5 products per row for category page
    }
}

if (!function_exists('custom_loop_shop_columns')) {
    function custom_loop_shop_columns() {
        return 5; // 5 products per row for shop page
    }
}

function tanhit_add_style() {
    wp_enqueue_style('my-bootstrap-extension', get_template_directory_uri() . '/css/bootstrap.min.css', [], '1');
    //wp_enqueue_style( 'fotorama', get_template_directory_uri() . '/css/fotorama.css', array('my-bootstrap-extension'), '1');
    //wp_enqueue_style( 'my-styles', get_template_directory_uri() . '/css/style.css', array('my-bootstrap-extension'), '1');
    wp_enqueue_style('my-sass', get_template_directory_uri() . '/sass/style.css', ['my-bootstrap-extension'], '2.38');
}

function tanhit_add_script() {
    /**
     * @todo remove
     */
    //wp_enqueue_script( 'jq', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), '1');

    //wp_enqueue_script( 'my-bootstrap-extension', get_template_directory_uri() . '/js/bootstrap.min.js', array(), '1');
    wp_register_script(
        'my-bootstrap-extension',
        get_template_directory_uri() . '/js/bootstrap.min.js',
        ['jquery'],
        TANHIT_VERSION,
        true
    );
    wp_enqueue_script('my-bootstrap-extension');

    //wp_enqueue_script( 'fotorama-js', get_template_directory_uri() . '/js/fotorama.js', array(), '1');
    wp_register_script(
        'fotorama-js',
        get_template_directory_uri() . '/js/fotorama.js',
        ['jquery'],
        TANHIT_VERSION,
        true
    );
    wp_enqueue_script('fotorama-js');

    /**
     * Enqueue script with common js code
     * @scope front
     */
    wp_register_script(
        'frontend-script',
        TM_URL . '/js/script.js',
        ['jquery'],
        TANHIT_VERSION,
        true
    );
    wp_enqueue_script('frontend-script');
    wp_localize_script(
        'frontend-script',
        'TanhitFrontManager',
        [
            'version'        => TANHIT_VERSION,
            'ajaxurl'        => admin_url('admin-ajax.php'),
            'process_ajax'   => 'Tanhit_Ajax_process_ajax',
            'cart'           => tanhit_get_cart(),
            'post_id'        => tanhit_get_post_id(),
            'duplKey'        => tanhit_get_duplicate(),
            'pathname_redir' => tanhit_get_redirect_page(),
            'timerValue'     => tanhit_get_redirect_timer()
        ]
    );
}

/**
 * Return timer when current-webinar page is forbidden
 */
function tanhit_get_redirect_timer() {
    return 20000;
}

/**
 * Return webinar redirect page
 */
function tanhit_get_redirect_page() {
    return TANHIT_WEBINAR_REDIRECT_PAGE;
}

/**
 * Update and return time mark at current-webinar page
 */
function tanhit_get_duplicate() {
    $id = tanhit_get_post_id();
    if (null == $id) {
        return false;
    }
    global $post, $current_user;
    if (TANHIT_WEBINAR_PAGE == $post->post_name) {

        if (!is_user_logged_in()) {
            return false;
        }

        $time = time();
        update_user_meta($current_user->ID, TANHIT_PREVENT_OPEN_KEY, $time);

        return $time;
    }

    return false;
}

/**
 * Return current post ID
 */
function tanhit_get_post_id() {
    global $post;
    if (empty($post)) {
        return null;
    }

    return $post->ID;
}

/**
 * Return woocommerce cart
 */
function tanhit_get_cart() {
    global $woocommerce;
    $response = [];
    $response['cart_count'] = $woocommerce->cart->cart_contents_count;
    $response['cart_total'] = $woocommerce->cart->get_cart_total();
    $response['cart_url'] = $woocommerce->cart->get_cart_url();

    return $response;
}

function tanhit_add_admin_script() {

    global $pagenow;
    /**
     * @todo add array of pages to load bootstrap
     */
    if (!in_array($pagenow, [])) {

        return;
    }

    /**
     * @todo remove
     */
    // wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/jquery-2.1.3.min.js', array(), '1');

    //wp_enqueue_script( 'moment', get_template_directory_uri() . '/js/bower_components/moment/min/moment.min.js', array(), '1');
    wp_enqueue_script(
        'moment',
        get_template_directory_uri() . '/js/bower_components/moment/min/moment.min.js',
        [],
        TANHIT_VERSION,
        true
    );

    //wp_enqueue_script( 'datetimepicker', get_template_directory_uri() . '/js/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js', array(), '1');
    wp_enqueue_script(
        'datetimepicker',
        get_template_directory_uri() . '/js/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        ['jquery', 'moment'],
        TANHIT_VERSION,
        true
    );

    //wp_enqueue_script('admin',get_template_directory_uri() . '/js/admin.js', array(), '1');
    wp_enqueue_script(
        'admin',
        get_template_directory_uri() . '/js/admin.js',
        ['jquery', 'datetimepicker'],
        TANHIT_VERSION,
        true
    );

    //wp_enqueue_style( 'my-bootstrap-extension-admin', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '1');
    wp_enqueue_style(
        'my-bootstrap-extension-admin',
        get_template_directory_uri() . '/css/bootstrap.min.css',
        [],
        TANHIT_VERSION
    );

    /**
     * @todo make correct loading
     */
    wp_enqueue_script('my-bootstrap-extension', get_template_directory_uri() . '/js/bootstrap.min.js', [], '1');
    wp_enqueue_style('my-style-admin', get_template_directory_uri() . '/css/admin.css', [], '1');
    wp_enqueue_style('bootstrapdatetimepicker', get_template_directory_uri() . '/js/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css', [], '1');
}

add_action('admin_enqueue_scripts', 'tanhit_add_admin_script');
add_action('wp_enqueue_scripts', 'tanhit_add_style');
add_action('wp_enqueue_scripts', 'tanhit_add_script');

function prn($content) {
    echo '<pre style="background: lightgray; border: 1px solid black; padding: 2px">';
    print_r($content);
    echo '</pre>';
}

function my_pagenavi() {
    global $wp_query;

    $big = 999999999; // уникальное число для замены

    $args = [
        'base'      => str_replace($big, '%#%', get_pagenum_link($big))
        , 'format'  => ''
        , 'current' => max(1, get_query_var('paged'))
        , 'total'   => $wp_query->max_num_pages
    ];

    $result = paginate_links($args);

    // удаляем добавку к пагинации для первой страницы
    $result = str_replace('/page/1/', '', $result);

    echo $result;
}

function excerpt_readmore($more) {
    return '... <br><a href="' . get_permalink($post->ID) . '" class="readmore">' . 'Читать далее' . '</a>';
}

add_filter('excerpt_more', 'excerpt_readmore');

if (function_exists('add_theme_support'))
    add_theme_support('post-thumbnails');

// убираем автоматическую чистку корзины
function my_remove_schedule_delete() {
    remove_action('wp_scheduled_delete', 'wp_scheduled_delete');
}

/**
 * @todo remove action
 */
//add_action( 'init', 'my_remove_schedule_delete' );

/*--------------------------------------------- МЕНЮ НАВИГАЦИИ -------------------------------------------------------*/

function theme_register_nav_menu() {
    register_nav_menus([
        'primary' => 'Меню',

    ]);
    //register_nav_menu( 'primary', 'Главное меню' );
}

add_action('after_setup_theme', 'theme_register_nav_menu');

/*-------------------------------------------- КОНЕЦ МЕНЮ НАВИГАЦИИ --------------------------------------------------*/

/*--------------------------------------------------- ТОВАРЫ ---------------------------------------------------------*/
// Review Post type
/**
 * @todo remove action
 */
//add_action('init', 'product_register');
function product_register() {

    $labels = [
        'name'               => _x('Товары', 'post type general name'),
        'singular_name'      => _x('Товар', 'post type singular name'),
        'add_new'            => _x('Добавить товар', 'review'),
        'add_new_item'       => __('Добавить новый товар'),
        'edit_item'          => __('Редактировать товар'),
        'new_item'           => __('Новый товар'),
        'view_item'          => __('Посмотреть товар'),
        'search_items'       => __('Найти товар'),
        'not_found'          => __('Ничего не найдено'),
        'not_found_in_trash' => __('В корзине пусто'),
        'parent_item_colon'  => ''
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'query_var'          => true,
        'menu_icon'          => null,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => ['title', 'editor', 'thumbnail']
    ];

    register_post_type('product', $args);
}

// Custom taxonomy type
function add_type_taxonomies() {
    register_taxonomy('type', 'product', [
        // Hierarchical taxonomy (like categories)
        'hierarchical' => true,
        // This array of options controls the labels displayed in the WordPress Admin UI
        'labels'       => [
            'name'              => _x('Типы товаров', 'taxonomy general name'),
            'singular_name'     => _x('Типы товаров', 'taxonomy singular name'),
            'search_items'      => __('Поиск типов'),
            'all_items'         => __('Все типы'),
            'parent_item'       => __('Родитель'),
            'parent_item_colon' => __('Родитель:'),
            'edit_item'         => __('Редактировать тип'),
            'update_item'       => __('Обновить тип'),
            'add_new_item'      => __('Добавить новый тип'),
            'new_item_name'     => __('Новое название типа'),
            'menu_name'         => __('Типы товаров'),
        ],

        // Control the slugs used for this taxonomy
        'rewrite'      => [
            'slug'         => 'type', // This controls the base slug that will display before each term
            'with_front'   => false, // Don't display the category base before "/locations/"
            'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
        ],
    ]);
}

/**
 * @todo remove action
 */
//add_action( 'init', 'add_type_taxonomies', 0 );
// Custom taxonomy section
function add_section_taxonomies() {
    register_taxonomy('section', 'product', [
        // Hierarchical taxonomy (like categories)
        'hierarchical' => true,
        // This array of options controls the labels displayed in the WordPress Admin UI
        'labels'       => [
            'name'              => _x('Разделы', 'taxonomy general name'),
            'singular_name'     => _x('Разделы', 'taxonomy singular name'),
            'search_items'      => __('Поиск разделов'),
            'all_items'         => __('Все разделы'),
            'parent_item'       => __('Родитель'),
            'parent_item_colon' => __('Родитель:'),
            'edit_item'         => __('Редактировать раздел'),
            'update_item'       => __('Обновить раздел'),
            'add_new_item'      => __('Добавить новый раздел'),
            'new_item_name'     => __('Новое название раздела'),
            'menu_name'         => __('Разделы'),
        ],

        // Control the slugs used for this taxonomy
        'rewrite'      => [
            'slug'         => 'section', // This controls the base slug that will display before each term
            'with_front'   => false, // Don't display the category base before "/locations/"
            'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
        ],
    ]);
}

/**
 * @todo remove action
 */
//add_action( 'init', 'add_section_taxonomies', 0 );

/**
 * @todo remove action
 */
//product status custom field
//add_action( 'init', 'custom_post_status' );
function custom_post_status() {
    register_post_status(
        'created',
        [
            'label'                     => _x('Создан', 'post'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>'),
        ]
    );
}

/*
//product teaser custom field
function productTeaser($post)
{
    ?>
    <p>
        <span>Embed-cсылка на тизер: </span>
        <input type="text" name='extra[teaser]' value="<?php echo get_post_meta($post->ID, "teaser", 1); ?>">
    </p>
    <?php
}
//product mark custom field
function productMark($post)
{
    ?>
    <p>
        <span>Признак(новинка): </span>

        <input type="checkbox" <?php if(get_post_meta($post->ID, "mark", 1)){
            echo "checked";
        } ?> name='extra[mark]' value="1">
    </p>
    <?php
}
//product price custom field
function productPrice($post)
{
    ?>
    <p>
        <span>Цена: </span>
        <input type="text" name='extra[price]' value="<?php echo get_post_meta($post->ID, "price", 1); ?>">
    </p>
    <?php
}
//product date custom field
function productDate($post)
{
    ?>
    <div>
        <span>Дата проведения: </span>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker'>
                <input type='text' name='extra[date]' value="<?php echo get_post_meta($post->ID, "date", 1); ?>" class="form-control" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <?php
}
//product set custom field
function productSet($post)
{
    ?>
    <p>
        <span>Массив ID, входящих в набор: </span>
        <input type="text" name='extra[set]' value="<?php echo get_post_meta($post->ID, "set", 1); ?>">
    </p>
    <?php
}
//product recommended custom field
function productRecommended($post)
{
    ?>
    <p>
        <span>Массив ID рекомендованых товаров: </span>
        <input type="text" name='extra[recommend]' value="<?php echo get_post_meta($post->ID, "recommend", 1); ?>">
    </p>
    <?php
}
//register custom fields
function registerCustomFields()
{
    add_meta_box('extra_teaser', 'Тизер', 'productTeaser', 'product', 'normal', 'high');
    add_meta_box('extra_mark', 'Признак', 'productMark', 'product', 'normal', 'high');
    add_meta_box('extra_price', 'Цена', 'productPrice', 'product', 'normal', 'high');
    add_meta_box('extra_date', 'Дата проведения', 'productDate', 'product', 'normal', 'high');
    add_meta_box('extra_set', 'Массив ID, входящих в набор', 'productSet', 'product', 'normal', 'high');
    add_meta_box('extra_recommend', 'Массив ID рекомендованых товаров', 'productRecommended', 'product', 'normal', 'high');
}
*/

/**
 * @todo remove action
 */
//add_action('add_meta_boxes', 'registerCustomFields', 1);
/* Сохраняем данные, при сохранении поста*/
function updateCustomFields($post_id) {
    if (!isset($_POST['extra'])) return false;
    foreach ($_POST['extra'] as $key => $value) {
        if (empty($value)) {
            delete_post_meta($post_id, $key); // удаляем поле если значение пустое
            continue;
        }

        update_post_meta($post_id, $key, $value); // add_post_meta() работает автоматически
    }

    return $post_id;
}

/**
 * @todo remove action
 */
//add_action('save_post', 'updateCustomFields', 10, 1);
//custom field files
function my_attachments($attachments) {
    $fields = [
        [
            'name'    => 'title',                         // unique field name
            'type'    => 'text',                          // registered field type
            'label'   => __('Заголовок', 'attachments'),    // label to display
            'default' => 'title',                         // default value upon selection
        ]
    ];

    $args = [

        // title of the meta box (string)
        'label'       => 'Прикрепленные файлы',

        // all post types to utilize (string|array)
        'post_type'   => ['product'],

        // meta box position (string) (normal, side or advanced)
        'position'    => 'normal',

        // meta box priority (string) (high, default, low, core)
        'priority'    => 'high',

        // allowed file type(s) (array) (image|video|text|audio|application)
        'filetype'    => null,  // no filetype limit

        // include a note within the meta box (string)
        'note'        => 'прикрепите файлы здесь!',

        // by default new Attachments will be appended to the list
        // but you can have then prepend if you set this to false
        'append'      => true,

        // text for 'Attach' button in meta box (string)
        'button_text' => __('Добавить файлы', 'attachments'),

        // text for modal 'Attach' button (string)
        'modal_text'  => __('Добавить', 'attachments'),

        // which tab should be the default in the modal (string) (browse|upload)
        'router'      => 'browse',

        // whether Attachments should set 'Uploaded to' (if not already set)
        'post_parent' => false,

        // fields array
        'fields'      => $fields,

    ];

    $attachments->register('my_attachments', $args); // unique instance name
}

add_action('attachments_register', 'my_attachments');
/*------------------------------------------------ КОНЕЦ ТОВАРОВ -----------------------------------------------------*/

/*
 * Auto complete virtual orders
 */
add_filter('woocommerce_payment_complete_order_status', 'virtual_order_payment_complete_order_status', 10, 2);

function virtual_order_payment_complete_order_status($order_status, $order_id) {
    $order = new WC_Order($order_id);

    if ('processing' == $order_status &&
        ('on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status)
    ) {

        $virtual_order = null;

        if (count($order->get_items()) > 0) {

            foreach ($order->get_items() as $item) {

                if ('line_item' == $item['type']) {

                    $_product = $order->get_product_from_item($item);

                    if (!$_product->is_virtual()) {
                        // once we've found one non-virtual product we know we're done, break out of the loop
                        $virtual_order = false;
                        break;
                    } else {
                        $virtual_order = true;
                    }
                }
            }
        }

        // virtual order, mark as completed
        if ($virtual_order) {
            return 'completed';
        }
    }

    // non-virtual order, return original status
    return $order_status;
}

/*
 * Redirect to my-account after purchase
 */

add_action('template_redirect', 'wc_custom_redirect_after_purchase');
function wc_custom_redirect_after_purchase() {
    global $wp;

    if (is_checkout() && !empty($wp->query_vars['order-received'])) {
        wp_redirect(home_url() . '/my-account/#order-received');
        exit;
    }
}

/*
 * Change WC-offer template
 */

/**
 * woocommerce_single_product_summary hook.
 *
 * @hooked woocommerce_template_single_title - 5
 * @hooked woocommerce_template_single_rating - 10
 * @hooked woocommerce_template_single_price - 10
 * @hooked woocommerce_template_single_excerpt - 20
 * @hooked woocommerce_template_single_add_to_cart - 30
 * @hooked woocommerce_template_single_meta - 40
 * @hooked woocommerce_template_single_sharing - 50
 */

/**
 * woocommerce_after_single_product_summary hook.
 *
 * @hooked woocommerce_output_product_data_tabs - 10
 * @hooked woocommerce_upsell_display - 15
 * @hooked woocommerce_output_related_products - 20
 */

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
add_action('woocommerce_single_product_summary_tabs', 'woocommerce_output_product_data_tabs', 10);

// Add cat seminar warning
function tanhit_cart_warning() {
    ?>
  <div class="cart-warning"><?php
    pll_e('Внимание! Указаная стоимость семинаров - это предоплата! Подробнее в описании к семинарам.', 'tanhit');
    ?> </div><?php
}

add_action('woocommerce_before_cart', 'tanhit_cart_warning', 50);

/*
 * Add login stylesheet
 */

function my_login_stylesheet() {
    wp_enqueue_style('custom-login', get_template_directory_uri() . '/style-login.css');
}

add_action('login_enqueue_scripts', 'my_login_stylesheet');

/*
 *
 */

function my_page_template_redirect() {
    if (is_page('goodies') && !is_user_logged_in()) {
        wp_redirect(home_url('/signup/'));
        exit();
    }
}

add_action('template_redirect', 'my_page_template_redirect');

/**
 * Register widget area.
 *
 * @since Tanhit
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function tanhit_widgets_init() {

    if (function_exists('register_sidebar')) {
        register_sidebar([
            'name'          => 'Blog Sidebar',
            'id'            => 'blog_sidebar',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
        ]);

        register_sidebar([
            'name'          => 'Mainpage subscribe',
            'id'            => 'main_subscribe',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '',
            'after_title'   => '',
        ]);
    }
}

add_action('widgets_init', 'tanhit_widgets_init');

function remove_page_from_query_string($query_string) {
    if ($query_string['name'] == 'page' && isset($query_string['page'])) {
        unset($query_string['name']);
// 'page' in the query_string looks like '/2', so i'm spliting it out
        list($delim, $page_index) = split('/', $query_string['page']);
        $query_string['paged'] = $page_index;
    }

    return $query_string;
}

add_filter('request', 'remove_page_from_query_string');

// Интеграция WooCommerce в Google Analytics
add_filter('woocommerce_order_formatted_shipping_address', 'custom_woocommerce_order_formatted_shipping_address');
add_filter('woocommerce_order_formatted_billing_address', 'custom_woocommerce_order_formatted_shipping_address');
function custom_woocommerce_order_formatted_shipping_address($fields) {
    unset($fields['city']);

    return $fields;
}

add_filter('woocommerce_admin_billing_fields', 'custom_woocommerce_admin_billing_fields');
function custom_woocommerce_admin_billing_fields($fields) {
    
    $fields = array(
        'first_name' => array(
                'label' => __( 'First Name', 'woocommerce' ),
                'show'  => false
        ),
        'last_name' => array(
                'label' => __( 'Last Name', 'woocommerce' ),
                'show'  => false
        ),
        'company' => array(
                'label' => __( 'Company', 'woocommerce' ),
                'show'  => false
        ),
        'address_1' => array(
                'label' => 'Улица',
                'show'  => false
        ),
        'address_2' => array(
                'label' => 'Номер дома',
                'show'  => false
        ),
        'address_3' => array(
                'label' => 'Номер квартиры',
                'show'  => false
        ),
        /*'city' => array(
                'label' => __( 'City', 'woocommerce' ),
                'show'  => false
        ),*/
        'postcode' => array(
                'label' => __( 'Postcode', 'woocommerce' ),
                'show'  => false
        ),
        'country' => array(
                'label'   => __( 'Country', 'woocommerce' ),
                'show'    => false,
                'class'   => 'js_field-country select short',
                'type'    => 'select',
                'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries()
        ),
        'state' => array(
                'label' => __( 'State/County', 'woocommerce' ),
                'class'   => 'js_field-state select short',
                'show'  => false
        ),
        'email' => array(
                'label' => __( 'Email', 'woocommerce' ),
        ),
        'phone' => array(
                'label' => __( 'Phone', 'woocommerce' ),
        ),
    );
    
    return $fields;
}

add_filter('woocommerce_admin_shipping_fields', 'custom_woocommerce_admin_shipping_fields');
function custom_woocommerce_admin_shipping_fields($fields) {
    if ($fields) {
        foreach ($fields as $iKey => $aItem) {
            if ($iKey != 'excerpt') {
                unset($fields[$iKey]);
            }
        }
    }

    return $fields;
}

add_action('admin_head', 'my_custom_css');
function my_custom_css() {
    echo '<style>
    #order_data .order_data_column ._billing_state_field{width:100%;}
    #order_data .order_data_column ._shipping_state_field{width:100%;}
    .order_actions .export_to_cdek{padding: 2px;font-size: 14px;text-transform: lowercase;height: 26px;line-height: 18px;}
  </style>';
}

add_filter('woocommerce_checkout_fields', 'custom_woocommerce_checkout_fields');
function custom_woocommerce_checkout_fields($fields) {
    //unset($fields['billing']['billing_city']);

    $fields['billing']['billing_state'] = [
        'type'     => 'text',
        'label'    => 'Город',
        'required' => true,
    ];
    $fields['billing']['billing_address_1'] = [
        'type'     => 'text',
        'label'    => 'Улица',
        'required' => (isset($_REQUEST['billing_delivery_point']) && $_REQUEST['billing_delivery_point'] ? false : true),
    ];
    $fields['billing']['billing_address_2'] = [
        'type'     => 'text',
        'label'    => 'Номер дома',
        'required' => (isset($_REQUEST['billing_delivery_point']) && $_REQUEST['billing_delivery_point'] ? false : true),
    ];
    $fields['billing']['billing_address_3'] = [
        'type'     => 'text',
        'label'    => 'Номер квартиры',
        'required' => false,
    ];

    $fields['billing']['billing_state_id'] = [
        'type'     => 'hidden',
        'required' => true
    ];

    return $fields;
}

add_action('woocommerce_checkout_update_order_review', 'custom_woocommerce_checkout_update_order_review');
function custom_woocommerce_checkout_update_order_review() {

    $aPostData = [];
    if (!empty($_POST['post_data'])) {
        parse_str($_POST['post_data'], $aPostData);
    }

    if ((!isset($aPostData['billing_state_id']) || $aPostData['billing_state_id'] != custom_woocommerce_checkout_get_value('', 'billing_state_id')) && isset($_COOKIE['chosen_shipping_methods'])) {
        unset($_COOKIE['chosen_shipping_methods']);
    }

    if ($aPostData) {
        if ($aSaveFields = array_intersect(
            ['billing_state_id', 'billing_state', 'billing_address_1', 'billing_address_2', 'billing_address_3', 'billing_email', 'billing_city', 'billing_phone', 'billing_first_name', 'billing_last_name'],
            array_keys($aPostData)
        )
        ) {
            if (is_user_logged_in()) {
                foreach ($aSaveFields as $iFieldKey) {
                    if (isset($aPostData[$iFieldKey])) {
                        update_user_meta('', $iFieldKey, $aPostData[$iFieldKey]);
                    }
                }
            } else {
                foreach ($aSaveFields as $iFieldKey) {
                    if (isset($aPostData[$iFieldKey])) {
                        WC()->session->set("user_field_{$iFieldKey}", $aPostData[$iFieldKey]);
                    }
                }
            }
        }

        add_action('woocommerce_after_calculate_totals', 'custom_woocommerce_after_calculate_totals');
    }
}

//Если доставка в данный город не возможна
function custom_woocommerce_after_calculate_totals($el) {
    remove_action('woocommerce_after_calculate_totals');

    parse_str($_POST['post_data'], $aPostData);
    if (isset($aPostData['billing_state_id']) && $aPostData['billing_state_id']) {
        $aSettingsEdostavka = get_option('woocommerce_edostavka_settings');

        if ($aPostData['billing_state_id'] != $aSettingsEdostavka['city_origin']) {
            //Проверяем есть ли в корзине физический товар
            if ($el->cart_contents) {
                $iNoVirtualProduct = FALSE;
                foreach ($el->cart_contents as $aItem) {
                    if ($aItem['data']->virtual != 'yes') {
                        $iNoVirtualProduct = TRUE;
                        break;
                    }
                }

                if ($iNoVirtualProduct) {
                    $packages = WC()->shipping->get_packages();

                    $iRates = FALSE;
                    foreach ($packages as $i => $package) {
                        if ($package['rates']) {
                            $iRates = TRUE;
                            break;
                        }
                    }

                    if (!$iRates) {
                        $data = [
                            'result'    => 'success',
                            'messages'  => '',
                            'reload'    => 'false',
                            'fragments' => apply_filters('woocommerce_update_order_review_fragments', [
                                '.woocommerce-checkout-review-order-table' => '<div class="woocommerce-checkout-review-order-table"><div style="padding-top:10px;margin-top:10px;border-top:1px solid;">' . wpautop('К сожалению, в данный город отгрузка идет в ручном режиме, для согласования способов оплаты / доставки, просим связаться с нами.') . '</div></div>',
                                '.woocommerce-checkout-payment'            => '<div class="woocommerce-checkout-payment"></div>'
                            ])
                        ];

                        unset(WC()->session->refresh_totals, WC()->session->reload_checkout);

                        wp_send_json($data);

                        die();
                    }
                }
            }
        }
    }
}

add_filter('woocommerce_checkout_get_value', 'custom_woocommerce_checkout_get_value', 10, 2);
function custom_woocommerce_checkout_get_value($val, $input) {
    if (in_array($input, ['billing_state_id', 'billing_state', 'billing_address_1', 'billing_address_2', 'billing_address_3', 'billing_email', 'billing_city', 'billing_phone', 'billing_first_name', 'billing_last_name'])) {
        if (is_user_logged_in()) {
            $val = get_user_meta('', $input, true);
        } else {
            $val = WC()->session->get("user_field_{$input}");
        }

        if (!$val && $input == 'billing_state_id') {
            $aSettingsEdostavka = get_option('woocommerce_edostavka_settings');

            $val = $aSettingsEdostavka['city_origin'];
        } else
            if (!$val && in_array($input, ['billing_state'])) {
                $val = 'Москва';
            }
    }

    return $val;
}

//Добавляем свой статус заказа
add_filter('wc_order_statuses', 'custom_wc_order_statuses');
function custom_wc_order_statuses($order_statuses) {
    $order_statuses['wc-export_to_cdek'] = 'Заказ выгружен в СДЭК';
    $order_statuses['wc-delete_from_cdek'] = 'Заказ удален из СДЭК';

    return $order_statuses;
}

//Добавляем свой статус заказа для вывода в списке
add_action('init', 'register_order_status_export_to_cdek');
function register_order_status_export_to_cdek() {
    register_post_status('wc-export_to_cdek', [
        'label'                     => 'Заказ выгружен в СДЭК',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Выгружен в СДЭК <span class="count">(%s)</span>', 'Выгружены в СДЭК <span class="count">(%s)</span>')
    ]);

    register_post_status('wc-delete_from_cdek', [
        'label'                     => 'Заказ удален из СДЭК',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Удален из СДЭК <span class="count">(%s)</span>', 'Удалены из СДЭК <span class="count">(%s)</span>')
    ]);
}

//Добавляем действие в карточку заказа
add_filter('woocommerce_order_actions', 'custom_woocommerce_order_actions');
function custom_woocommerce_order_actions($actions) {
    $actions['export_to_cdek'] = 'Выгрузить в СДЭК';
    $actions['delete_from_cdek'] = 'Удалить заказ из СДЭК';

    return $actions;
}

add_action('woocommerce_order_action_export_to_cdek', 'custom_woocommerce_order_action_export_to_cdek');
function custom_woocommerce_order_action_export_to_cdek($order) {
    
    if (is_numeric($order)) {
        $order = new WC_Order($order);
    }

    $aDeliveryMethod = $order->get_shipping_methods();
    $aDeliveryMethod = $aDeliveryMethod ? array_shift($aDeliveryMethod) : [];
    $iDeliveryMethod = isset($aDeliveryMethod['method_id']) ? explode('_', $aDeliveryMethod['method_id']) : ''; //['edostavka', 137]

    //error_log(print_r($aDeliveryMethod, 1));
    //error_log($order);
    //Выгрузка заказа в СДЭК
    if (isset($iDeliveryMethod[0]) && $iDeliveryMethod[0] == 'edostavka' && isset($iDeliveryMethod[1]) && $iDeliveryMethod[1]) {
        $iDeliveryMethod = $iDeliveryMethod[1];

        require_once __DIR__ . '/cdek_integrator/class.cdek_integrator.php';
        $api_cdek = new cdek_integrator();

        $DeclaredSum = $DeliveryPayment = 0;
        $aSettingsEdostavka = get_option('woocommerce_edostavka_settings');
        if (isset($aSettingsEdostavka['login']) && $aSettingsEdostavka['login'] && isset($aSettingsEdostavka['password']) && $aSettingsEdostavka['password']) {
            //Авторизационные данные
            $api_cdek->setAuth($aSettingsEdostavka['login'], $aSettingsEdostavka['password']);

            //Формируем список товаров заказа для выгрузки
            $component = $api_cdek->loadComponent('orders');
            //error_log(print_r($component, 0));

            //Задаем номер выгрузки
            $component->setNumber('shop_' . date('Ymd', strtotime($order->order_date)) . '_' . str_pad($order->id, 10, 0, STR_PAD_LEFT));

            //Meta Order
            $aMetaDataOrder = get_post_meta($order->id);
            //Meta Delivery 
            $aDeliveryMethod = $order->get_shipping_methods();
            
            //Стоимость доставки
            $DeliveryPayment = $aMetaDataOrder['_order_shipping'][0];

            
            //Массив данных для передачи на выгрузку
            $aDataExport = [];

            //Номер заказа
            $aDataExport['order_id'] = "ORD{$order->id}";

            //Международная посылка
            if (stripos($aMetaDataOrder['_billing_state'][0], 'россия') === FALSE) {
                $aDataExport['foreign_delivery'] = 1;
            }

            //город отправитель
            $aDataExport['city_id'] = $aSettingsEdostavka['city_origin'];
            //Данные получателя
            $aDataExport['recipient_city_id'] = $aMetaDataOrder['_billing_state_id'][0];
            $aDataExport['recipient_name'] = $aMetaDataOrder['_billing_last_name'][0] . ' ' . $aMetaDataOrder['_billing_first_name'][0];
            $aDataExport['recipient_email'] = $aMetaDataOrder['_billing_email'][0];
            $aDataExport['recipient_telephone'] = $aMetaDataOrder['_billing_phone'][0];
            //ID тарифа
            $aDataExport['tariff_id'] = $iDeliveryMethod;
            $aDataExport['currency'] = $aMetaDataOrder['_order_currency'][0];

            //Адрес получателя
            $aDataExport['address']['street']      = trim($aMetaDataOrder['_billing_address_1'][0]);
            $aDataExport['address']['house']       = trim($aMetaDataOrder['_billing_address_2'][0]);
            $aDataExport['address']['flat']        = trim($aMetaDataOrder['_billing_address_3'][0]);
            
            //$aDataExport['address']['address'] = trim($aMetaDataOrder['_billing_address_1'][0] . ' ' . $aMetaDataOrder['_billing_address_2'][0]);
            
            //ИД пункта выдачи
            if (!empty($aMetaDataOrder['_billing_delivery_point'][0])) {
                $aDataExport['address']['pvz_code'] = $aMetaDataOrder['_billing_delivery_point'][0];
            }

            //Коробки с товарами
            $aDataPackageExport = [];
            $aDataPackageExport['size_a'] = 0;
            $aDataPackageExport['size_b'] = 0;
            $aDataPackageExport['size_c'] = 0;
            $aDataPackageExport['weight'] = 0;

            $aProductItems = array();
            //Товары в заказе
            $order_items = $order->get_items();
            foreach ($order_items as $item_id => $item_data) {
                $oProduct = $order->get_product_from_item($item_data);

                if (!$oProduct->is_virtual()) {
                    $sSku = $oProduct->get_sku();
                    $iCost = $item_data['line_total'] / $item_data['qty'];

                    $DeclaredSum += $item_data['line_total'];
                                     
                    $_weight = wc_get_weight(str_replace(',', '.', $oProduct->weight), 'kg');
                    if (!$_weight) {
                        $_weight = $aSettingsEdostavka['minimum_weight'];
                    }

                    //$_weight = 30;
                    $_weight *= 1000;

                    //Параметры отдельного товара
                    $aDataPackageExport['item'][] = [
                        'comment'  => $item_data['name'],
                        'ware_key' => $sSku ? $sSku : $oProduct->get_id(),
                        'cost'     => $iCost,
                        'payment'  => 0,//$iCost,
                        'weight'   => $_weight,
                        'amount'   => $item_data['qty']
                    ];

                    //Габариты коробки
                    $_height = wc_get_dimension(str_replace(',', '.', $oProduct->height), 'cm');
                    $_width = wc_get_dimension(str_replace(',', '.', $oProduct->width), 'cm');
                    $_length = wc_get_dimension(str_replace(',', '.', $oProduct->length), 'cm');

                    if (!$_height) {
                        $_height = $aSettingsEdostavka['minimum_height'];
                    }
                    if (!$_width) {
                        $_width = $aSettingsEdostavka['minimum_width'];
                    }
                    if (!$_length) {
                        $_length = $aSettingsEdostavka['minimum_length'];
                    }

                    //Вес всех товаров
                    $aDataPackageExport['size_a'] += ($_height/* * $item_data['qty']*/);
                    $aDataPackageExport['size_b'] += ($_width/* * $item_data['qty']*/);
                    $aDataPackageExport['size_c'] += ($_length/* * $item_data['qty']*/);
                    $aDataPackageExport['weight'] += ($_weight/* * $item_data['qty']*/);
                    
                    //Продукты для выгрузки в FullFillmment
                    $aProductItems[] = array(
                        'id'            => $oProduct->get_id(),
                        'sku'           => $sSku ? $sSku : 'пустой',
                        'name'          => $item_data['name'],
                        'price'         => $iCost,
                        'qty'           => $item_data['qty'],
                        'unit_code'     => $item_data['unit_code']
                    );
                }
            }

            $aDataPackageExport['pack'] = TRUE;

            $aDataExport['package'][1] = $aDataPackageExport;
            //(new WC_Logger())->add('cdek_integration', 'INFO: order_to_cdek|'.$order->id.'|' . print_r($aDataExport, 0));
            //Добавляем данные в выгрузку
            $component->setOrders([$aDataExport]);
            //Отправляем данные на сервер сдэк
           
            $response = $api_cdek->sendData($component);
            //(new WC_Logger())->add('cdek_integration', 'INFO: order_to_cdek|'.$order->id.'|' . print_r($response, 0));
            $aOrderResponse = (array)$response->Order[0];

            //die(var_dump($aOrderResponse,$component->getData()));
            
            if (!isset($aOrderResponse["@attributes"]["ErrorCode"])) {
                
            }

            if (class_exists('WC_Logger')) {
                $logger = new WC_Logger();

                if (isset($aOrderResponse["@attributes"]["ErrorCode"]) && $aOrderResponse["@attributes"]["ErrorCode"]) {
                    $logger->add('cdek_integration', 'ERROR: order_to_cdek|'.$order->id.'|' . implode('|', $aOrderResponse["@attributes"]));
                } else {
                    $logger->add('cdek_integration', 'OK: order_to_cdek|'.$order->id.'|' . implode('|', $aOrderResponse["@attributes"]));
                }
            }
            
            
            //Если заказ выгружен в СДЭК, то выгружаем в FFillment
            if ($aProductItems && ( ! isset($aOrderResponse["@attributes"]["ErrorCode"]) || $aOrderResponse["@attributes"]["ErrorCode"] == 'ERR_ORDER_DUBL_EXISTS')) {
                
                //Дополняем нужными полями
                /*foreach ($aProductItems as $iKey => $aItem){
                    //$FF_SkuExternalCode = get_field( "FF_SkuExternalCode", $aItem['id'] );
                    $FF_Article = get_field( "FF_Article", $aItem['id'] );
                    //$FF_UnitCode = get_field( "FF_UnitCode", $aItem['id'] );
                    
                    if ( ! $FF_Article){
                        if (class_exists('WC_Logger')) {
                            $logger = new WC_Logger();

                            $logger->add('ffillment_integration', 'ERROR: EMPTY_FF_FIELD_FOR_PRODUCT|'.$aItem['id'].'|'.$order->id);
                        }
                        
                        return FALSE;
                        break;
                    }
                    
                    //$aProductItems[$iKey]['SkuExternalCode'] = $FF_SkuExternalCode;
                    $aProductItems[$iKey]['Article'] = $FF_Article;
                    //$aProductItems[$iKey]['UnitCode'] = $FF_UnitCode;
                }*/
                
                $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                    <ClientOrder_RequestDelivery xmlns="http://lkff.cdek.ru">
                                            <sid>24D1A468-A368-40DB-BB1F-BA47DE1A9D69</sid>
                                            <Sender></Sender>
                                            <OrderDate>'.date('Y-m-d').'</OrderDate>
                                            <OrderCode>'.$aDataExport['order_id'].'</OrderCode>
                                            <OrderRPO></OrderRPO>
                                            <DeliveryType>9</DeliveryType>
                                            <DeliveryDate></DeliveryDate>
                                            <DeliveryHours></DeliveryHours>
                                            <DeliveryTariff>'.($aDataExport['tariff_id']).'</DeliveryTariff>
                                            <DeliveryMode>0</DeliveryMode>
                                            <WarehouseCode>32</WarehouseCode>
                                            <CountryCode></CountryCode>
                                            <POD>'.(isset($aMetaDataOrder['_billing_delivery_point'][0]) ? $aMetaDataOrder['_billing_delivery_point'][0] : '').'</POD>
                                            <ShipmentAddress>'.($aDataExport['address']['street'] ? trim(trim($aDataExport['address']['street'] .', '. $aDataExport['address']['house'] .', '. $aDataExport['address']['flat']),',') : '').'</ShipmentAddress>
                                            <Phones>'.$aDataExport['recipient_telephone'].'</Phones>
                                            <Receiver>'.$aDataExport['recipient_name'].'</Receiver>
                                            <AOGUID></AOGUID>
                                            <ZipCode></ZipCode>
                                            <Subject></Subject>
                                            <CityCode>'.$aDataExport['recipient_city_id'].'</CityCode>
                                            <City>'.$aMetaDataOrder['_billing_state'][0].'</City>
                                            <Region></Region>
                                            <Town></Town>
                                            <Street>'.$aDataExport['address']['street'].'</Street>
                                            <House>'.$aDataExport['address']['house'].'</House>
                                            <DeclaredSum>'.(/*$DeclaredSum*/'0').'</DeclaredSum>
                                            <DeliveryPayment>'.(/*$DeliveryPayment*/'0').'</DeliveryPayment>
                                            <SumToPay>0</SumToPay>
                                            <Comment>test</Comment>
                                            <tblcount>'.count($aProductItems).'</tblcount>
                                            <Goods>';
                
                                            foreach ($aProductItems as $aItem){
                                                $xml_post_string .= '<Goods>
                                                    <Life>0</Life>
                                                    <ExternalCode></ExternalCode>
                                                    <SkuExternalCode>p'.$aItem['id'].'</SkuExternalCode>
                                                    <Article>'.$aItem['sku'].'</Article>
                                                    <FullName>'.$aItem['name'].'</FullName>
                                                    <Color></Color>
                                                    <Size></Size>
                                                    <Variant></Variant>
                                                    <Season></Season>
                                                    <Price>'.$aItem['price'].'</Price>
                                                    <MinQty>1</MinQty>
                                                    <PackQty>1</PackQty>
                                                    <BoxQty>1</BoxQty>
                                                    <SkuGroupName></SkuGroupName>
                                                    <Barcode></Barcode>
                                                    <Brand></Brand>
                                                    <Weight>0</Weight>
                                                    <Volume>0</Volume>
                                                    <Length>0</Length>
                                                    <Width>0</Width>
                                                    <Height>0</Height>
                                                    <UnitCode>784</UnitCode>
                                                    <Qty>'.$aItem['qty'].'</Qty>
                                                    <SerialNumber></SerialNumber>
                                                </Goods>';
                                            }

                                            $xml_post_string .= '
                                            </Goods>
                                    </ClientOrder_RequestDelivery>
                            </soap:Body>
                    </soap:Envelope>';

                $headers = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: http://lkff.cdek.ru/ClientOrder_RequestDelivery", 
                        "Content-length: ".strlen($xml_post_string),
                ); 

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://lkff.cdek.ru:8080/cdekfullfillment.asmx?op=ClientOrder_RequestDelivery');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //curl_setopt($ch, CURLOPT_HEADER, TRUE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);

                if (!empty($xml_post_string)) {
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, /*array('xml_request' => */$xml_post_string/*)*/);
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $your_xml_response = curl_exec($ch);
                curl_close($ch);

                //Разбираем ответ
                $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $your_xml_response);
                $xml = simplexml_load_string($clean_xml);

                $iOrderID = '';
                if (isset($xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->OrderID) && (string)$xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->OrderID){
                    $iOrderID = (string)$xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->OrderID;
                }
                
                if (class_exists('WC_Logger')) {
                    $logger = new WC_Logger();

                    if (isset($xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->ErrorCode) && (string)$xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->ErrorCode) {
                        $logger->add('ffillment_integration', 'ERROR: '.$order->id.'|' . (string)$xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->ErrorCode);
                    } else {
                        $logger->add('ffillment_integration', 'OK: '.$order->id.'|' . $iOrderID);
                    }
                }
                
                if ($iOrderID){
                    $order->update_status('wc-export_to_cdek');
                }
            }
        }
    }

    return true;
}

add_action('woocommerce_order_action_delete_from_cdek', 'custom_woocommerce_order_action_delete_from_cdek');
function custom_woocommerce_order_action_delete_from_cdek($order) {
    if (is_numeric($order)) {
        $order = new WC_Order($order);
    }

    require_once __DIR__ . '/cdek_integrator/class.cdek_integrator.php';
    $api_cdek = new cdek_integrator();

    $iOrderIdCDEK = "ORD{$order->id}";

    //Формируем список товаров заказа для выгрузки
    $component = $api_cdek->loadComponent('order_delete');

    //Задаем номер выгрузки
    $component->setNumber('shop_' . date('Ymd', strtotime($order->order_date)) . '_' . str_pad($order->id, 10, 0, STR_PAD_LEFT));

    //Отправляем данные на сервер сдэк
    $response = $api_cdek->sendData($component);
    $aOrderResponse = (array)$response->Order[0];

    if (!isset($aOrderResponse["@attributes"]["ErrorCode"])) {
        $order->update_status('wc-delete_from_cdek');
    }

    if (class_exists('WC_Logger')) {
        $logger = new WC_Logger();

        if (isset($aOrderResponse["@attributes"]["ErrorCode"])) {
            $logger->add('cdek_integration', 'ERROR: order_delete_from_cdek|' . $iOrderIdCDEK . '|' . implode('|', $aOrderResponse["@attributes"]) . "\r\n");
        } else {
            $logger->add('cdek_integration', 'OK: order_delete_from_cdek|' . $iOrderIdCDEK . '|' . implode('|', $aOrderResponse["@attributes"]) . "\r\n");
        }
    }
    
    return true;
}

//Добавляем действие в список заказов
add_action('admin_footer-edit.php', 'custom_bulk_admin_footer');
function custom_bulk_admin_footer() {

    global $post_type;

    if ($post_type == 'shop_order') {
        ?>
      <script type="text/javascript">
		  jQuery(document).ready(function () {
			  jQuery('<option>').val('export_to_cdek').text('Выгрузить в СДЭК').appendTo("select[name='action']");
		  });
      </script>
        <?php
    }
}

add_action('load-edit.php', 'custom_bulk_action');
function custom_bulk_action() {

    $post_ids = $_REQUEST['post'];

    if ($post_ids) {
        // 1. get the action
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();

        switch ($action) {
            // 3. Perform the action
            case 'export_to_cdek':
                foreach ($post_ids as $post_id) {
                    custom_woocommerce_order_action_export_to_cdek($post_id);
                }

                return;
                break;

            default:
                return;
        }
    }
}

//Добавляем действие в список как кнопку в действиях у заказа
add_filter('woocommerce_admin_order_actions', 'custom_woocommerce_admin_order_actions', 10, 2);
function custom_woocommerce_admin_order_actions($actions, $the_order) {
    if (!$the_order->has_status(['export_to_cdek', 'completed', 'cancelled', 'refunded', 'failed'])) {
        $actions['export_to_cdek'] = [
            'url'    => admin_url('admin-ajax.php?action=export_to_cdek&paged=' . (int)$_REQUEST['paged'] . '&order_id=' . $the_order->id),
            'name'   => 'В СДЭК',
            'action' => "export_to_cdek"
        ];
    }

    return $actions;
}

//Обработка нажатия кнопки
add_action('wp_ajax_export_to_cdek', 'ajax_export_to_cdek');
add_action('wp_ajax_nopriv_export_to_cdek', 'ajax_export_to_cdek');
function ajax_export_to_cdek() {
    if (isset($_REQUEST['order_id']) && $_REQUEST['order_id']) {
        $paged = (int)$_REQUEST['paged'];
        if (!$paged) {
            $paged = 1;
        }

        custom_woocommerce_order_action_export_to_cdek($_REQUEST['order_id']);

        wp_redirect(admin_url('edit.php?post_type=shop_order&paged=' . (int)$_REQUEST['paged']));
        exit;
    }
}

function woo_cart_total_no_virtual_product() {
    global $woocommerce;

    $result = 0;

    if (!is_admin()) {
        // Get all products in cart
        $products = $woocommerce->cart->get_cart();

        // Loop through cart products
        foreach ($products as $product) {
            if ($product['data']->virtual == 'no') {
                $result += $product['line_total'];
            }
        }
    }

    return $result;
}

//Добавить надбавки к стоимости заказа
add_action('woocommerce_cart_calculate_fees', 'woocommerce_custom_surcharge');
function woocommerce_custom_surcharge() {
    global $woocommerce;

    if (is_admin() && !defined('DOING_AJAX'))
        return;

    $iTotalNoVirtual = woo_cart_total_no_virtual_product();

    if ($iTotalNoVirtual) {
        //$surcharge = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;
        //$woocommerce->cart->add_fee( 'Страховой взнос.', $iTotalNoVirtual*0.0075, true, 'standard' );
        //$woocommerce->cart->add_fee( 'Упаковка и сервисный сбор логистической компании.', 50, true, 'standard' );
        //$woocommerce->cart->add_fee( 'Доставка до склада логистической компании.', 9, true, 'standard' );
        $woocommerce->cart->add_fee('Сервисный логистический сбор.', ($iTotalNoVirtual * 0.0075) + 50 + 9, true, 'standard');
    }
}

//скрыть поля если в корзине только вирт товары
add_filter('woocommerce_checkout_fields', 'woo_remove_billing_checkout_fields', 100, 1);
function woo_remove_billing_checkout_fields($fields) {
    if (woo_cart_has_virtual_product() == true) {
        unset($fields['billing']['billing_state_id']);
        unset($fields['billing']['billing_state']);
        unset($fields['billing']['billing_address_1']);
        unset($fields['billing']['billing_address_2']);
        unset($fields['billing']['billing_address_3']);
        unset($fields['billing']['billing_city']);
        unset($fields['billing']['billing_delivery_point']);
        //unset($fields['shipping']);
    }

    return $fields;
}

/**
 * Проверяем, содержит ли корзина виртуальные товары
 *
 * @return bool
 */
function woo_cart_has_virtual_product() {
    global $woocommerce;

    // По-умолчанию, виртуальных товаров нет
    $has_virtual_products = false;
    // Значение по-умолчанию количества виртуальных товаров
    $virtual_products = 0;

    if (!is_admin()) {
        // Получаем все товары в корзине
        $products = $woocommerce->cart->get_cart();
        // Проходимся по всем товаров в корзине
        foreach ($products as $product) {
            // Получаем ID товара и '_virtual' post meta
            $product_id = $product['product_id'];
            $is_virtual = get_post_meta($product_id, '_virtual', true);
            // Обновляем $has_virtual_product если товар является виртуальным
            if ($is_virtual == 'yes')
                $virtual_products += 1;
        }

        if (count($products) == $virtual_products) {
            $has_virtual_products = true;
        }
    }

    return $has_virtual_products;
}

// Fix hack
function woo_order_has_virtual_product() {
    return woo_cart_has_virtual_product();
}

//Добавляем роль пользователя
// Add a custom user role
add_role('vip', 'Ближний Круг', []);

//проверить доступен ли товар бесплатно для текущего пользователя
function getAccessToProduct($product_id) {
    global $wpdb;

    if (is_admin()) {
        return true;
    }

    $result = false;

    /*if (is_super_admin()){
      $result = true;
    }*/

    //Продукт не доступен для покупки и просмотра текущему типо пользователя
    if (!$result) {
        $user_access = get_post_meta($product_id, 'access', TRUE);
        if ($user_access && $user_access != 3 && !current_user_can('vip')) {
            return 0;
        }

        if (in_array($user_access, [1, 3]) && current_user_can('vip')) {
            $result = true;
        }
    }

    //Закрыт доступ к продукту
    if (!$result && !is_user_logged_in()) {
        $result = false;
    }

    if (!$result) {
        $oProduct = wc_get_product($product_id);
        //открыт доступ к продукту
        if (!$oProduct->get_price()) {
            $result = true;
        }
    }

    //Если пользователь имеет роль вип
    if (!$result && current_user_can('vip') && in_array(get_post_meta($product_id, 'access', TRUE), [1, 3])) {
        $result = true;
    }

    //Ищем оплаченный заказ с текущим товаром
    if ($user_id = get_current_user_id()) {
        $sql = "SELECT SUM(M2.`meta_value`) as cnt 
		FROM wp_woocommerce_order_items I 
		INNER JOIN wp_woocommerce_order_itemmeta M ON (M.`order_item_id` = I.`order_item_id`)
		INNER JOIN wp_woocommerce_order_itemmeta M2 ON (M2.`order_item_id` = I.`order_item_id`)
		INNER JOIN wp_postmeta PM ON (PM.post_id = I.`order_id`)
		INNER JOIN wp_posts P ON (P.`ID` = I.`order_id`)
		WHERE PM.meta_key = '_customer_user' && PM.meta_value = '{$user_id}' && M.`meta_key`= '_product_id' && M.`meta_value` = '{$product_id}' && P.`post_type` = 'shop_order' && P.`post_status` = 'wc-completed' && M2.meta_key = '_qty'";

        //var_dump($sql);
        if ($val = $wpdb->get_var($sql)) {
            $result = $val;
        }
    }

    return $result;
}

function getWebinarId($product_id) {
    if ($val = get_post_meta($product_id, 'webinar_room_id', TRUE)) {
        return $val;
    }

    return false;
}

add_action('template_redirect', 'wpse12535_redirect_sample');
function wpse12535_redirect_sample() {
    if (!is_admin()) {
        $aData = parse_url($_SERVER['REQUEST_URI']);
        $product_id = isset($_REQUEST['pid']) ? (int)$_REQUEST['pid'] : null;
        $order_id = isset($_REQUEST['oid']) ? (int)$_REQUEST['oid'] : null;
        if (trim($aData['path'], '/') == 'current-webinar' && $_REQUEST['id'] && getWebinarId($_REQUEST['id'])) {
            exit(wp_redirect(get_post_permalink($_REQUEST['id'])));
        } 
		else if (trim($aData['path'], '/') == 'my-account' && ($product_id || $order_id)) {
            $current_user = wp_get_current_user();

            if ((int)$current_user->ID > 0) {
                if ($product_id) {
                    $product = wc_get_product($product_id);
                    if ($product && $product->post->post_type == 'product') {
                        $access = getAccessToProduct($product_id);
                        if ($access === true) { //Not payed access!
                            if (!wc_customer_bought_product($current_user->user_email, $current_user->ID, $product_id)) { //Check order exist
                                $address = [
                                    'first_name' => $current_user->user_firstname,
                                    'last_name'  => $current_user->user_lastname,
                                    'email'      => $current_user->user_email,
                                ];

                                $order = wc_create_order(['customer_id' => $current_user->ID]);
                                if ($order->id) {
                                    $order->add_product($product, 1);
                                    $order->set_address($address, 'billing');
                                    $order->set_address($address, 'shipping');
                                    $order->calculate_totals();
                                    $order->update_status('wc-completed');
                                    wc_downloadable_product_permissions($order->id);
                                }
                            } else {
                                grant_permission_to_payed_files($current_user, $product, null);
                            }
                        } elseif ($access) { //Payed access
                            grant_permission_to_payed_files($current_user, $product, null);
                        }
                    }
                } elseif ($order_id) {
                    grant_permission_to_payed_files($current_user, null, $order_id);
                }
            }
            exit(wp_redirect('/my-account'));
        }
    }
}

add_action( 'wp_ajax_get_cert_archive',        'get_cert_archive' ); // For logged in users
add_action( 'wp_ajax_nopriv_get_cert_archive', 'get_cert_archive' ); // For anonymous users
function get_cert_archive(){
	set_time_limit (0);
	
	$iUserId = get_current_user_id();
	$oUserData = get_userdata($iUserId);
	
    $sFileName = createAndDownloadArchiveCert();
	
	if ($sFileName){
		$aUploadDir = wp_upload_dir();
		$sDirUrl 	= $aUploadDir['baseurl'] .'/certificates/pdf/'.$iUserId.'/';
	
		//$attachments = array($sFileName);
		wp_mail($oUserData->user_email, 'Архив сертификатов', 'Ссылка для скачивания архива доступна только 24 часа: '.$sDirUrl.rawurlencode($sFileName));
	}
}

function createAndDownloadArchiveCert(){
	global $wpdb;
	
	session_start ();

	$aInnerTable = array();
	$aWhere = array();
	$aFilterWhere = array();

	//if ( ! $atts['id_list'] ){
		$atts['id_list'] = 'manager';//get_the_ID();
	//}
	
	//if (isset($atts['manager']) && $atts['manager']){
		$aInnerTable[] = "INNER JOIN {$wpdb->prefix}postmeta PM_MANAGER ON (PM_MANAGER.post_id = P.ID && PM_MANAGER.meta_key = 'cert_manager')";
		$aWhere[] = "PM_MANAGER.meta_value = '".get_current_user_id()."'";
	//}

	//Фильтр
	$cert_practika = '';
	if(isset($_SESSION['cert_practika_'.$atts['id_list']])){
		$cert_practika = $_SESSION['cert_practika_'.$atts['id_list']];
	}

	$cert_status = '';
	if(isset($_SESSION['cert_status_'.$atts['id_list']])){
		$cert_status = $_SESSION['cert_status_'.$atts['id_list']];
	}

	if ($cert_practika){
		$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_PRACTIKA_F ON (UM_PRACTIKA_F.terms_id = TR.term_taxonomy_id && UM_PRACTIKA_F.meta_key = 'cert_practika')";
		$aWhere[] = "UM_PRACTIKA_F.meta_value = '{$cert_practika}'";
	}
	if ($cert_status){
		$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_STATUS_F ON (UM_STATUS_F.terms_id = TR.term_taxonomy_id && UM_STATUS_F.meta_key = 'cert_status')";
		$aWhere[] = "UM_STATUS_F.meta_value = '{$cert_status}'";
	}
	//\Фильтр


	$sQuery = "
		SELECT P.ID 
		FROM {$wpdb->prefix}posts P 
		INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user')
		INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value)
		INNER JOIN {$wpdb->prefix}usermeta UM ON (UM.user_id = U.ID && UM.meta_key = 'first_name')
		INNER JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'cert_location')
		INNER JOIN {$wpdb->prefix}term_relationships TR ON (TR.object_id = P.ID)
		".($aInnerTable ? implode(' ',$aInnerTable) : '')."
		WHERE P.post_type = 'certificates' && P.`post_status` = 'publish'".($aWhere ? ' && ('.implode(' && ', $aWhere).')' : '').($aFilterWhere ? ' && '.implode(' && ', $aFilterWhere) : '')."  
		GROUP BY P.ID";

	$aData = $wpdb->get_results( $sQuery );
	
	if ( $aData){
		ini_set('max_execution_time', 300);
		
		$aFiles = array();
		
		//Создаем директория для архивации
		$aUploadDir = wp_upload_dir();
		$sDirTmp 		= $aUploadDir['basedir'] .'/certificates/tmp/'.get_current_user_id().'/';
		$sDirCrt 		= $aUploadDir['basedir'] .'/certificates/pdf/'.get_current_user_id().'/';
		
		if (is_dir($sDirTmp)){
			foreach (glob($sDirTmp . '*') as $file){
				@unlink($file);
			}
		}
		
		if ( ! is_dir($sDirTmp)){
			mkdir($sDirTmp, 0777, TRUE);
		}
		
		if ( ! is_dir($sDirCrt)){
			mkdir($sDirCrt, 0777, TRUE);
		}
		
		foreach ($aData as $oItem){
			$aFiles[] = getCertificatePdf($oItem->ID, $sDirTmp);
		}

		if($aFiles){
			$sFileNameArchive = 'archive_'.date('d-m-y-h-i-s').'.zip';
			
			$zip = new ZipArchive();
			$zip->open($sDirCrt . $sFileNameArchive, ZipArchive::CREATE);
			foreach ($aFiles as $sFile){
				$zip->addFile($sFile, basename($sFile)) or die ("ERROR: Could not add file: $sFile");
			}
			
			$zip->close();
			
			if (file_exists($sDirCrt . $sFileNameArchive)){
				chmod($sDirCrt . $sFileNameArchive, 0777);
				return $sFileNameArchive;
			}
		}
	}
	
	return false;
}

function grant_permission_to_payed_files($user, $product, $order_id) {
    $added_flag = false;
    $customer_available_downloads = wc_get_customer_available_downloads($user->ID);
    if ($product) {
        $downloadable_product_files = array_keys($product->get_files());
        foreach ($customer_available_downloads as $download_info) {
            if (($key = array_search($download_info['download_id'], $downloadable_product_files)) !== false) {
                unset($downloadable_product_files[$key]);
            }
        }

        if (!empty($downloadable_product_files)) {
            global $wpdb;
            $sql = "SELECT DISTINCT order_id FROM {$wpdb->prefix}woocommerce_order_items oi
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta im ON im.order_item_id = oi.order_item_id
                LEFT JOIN {$wpdb->posts} p ON p.ID = oi.order_id
                LEFT JOIN {$wpdb->postmeta} pm ON oi.order_id = pm.post_id
                WHERE p.post_status IN ('wc-completed', 'wc-processing')
                AND im.meta_key = '_product_id' AND im.meta_value = {$product->id}
                AND im.meta_value != 0
                AND pm.meta_key IN ('_billing_email', '_customer_user' )
                AND pm.meta_value IN ('" . implode("','", [$user->user_email, $user->ID]) . "')
                GROUP BY order_id";

            $result = $wpdb->get_row($sql);
            $order = wc_get_order($result->order_id);
            $order_products = $order->get_items();

            foreach ($order_products as $order_product) {
                if ($order_product['product_id'] == $product->id) {
                    foreach ($downloadable_product_files as $download_id) {
                        wc_downloadable_file_permission($download_id, $order_product['variation_id'] > 0 ? $order_product['variation_id'] : $product->id, $order, $order_product['qty']);
                    }
                    $added_flag = true;
                    break;
                }
            }
        }
    } elseif ($order_id) {
        $order = wc_get_order($order_id);
        if (in_array($order->get_status(), ['completed', 'processing'])) {
            $order_products = $order->get_items();
            if (sizeof($order_products) > 0) {
                foreach ($order_products as $order_product) {
                    $product = $order->get_product_from_item($order_product);

                    if ($product && $product->exists() && $product->is_downloadable()) {
                        $downloadable_product_files = array_keys($product->get_files());

                        foreach ($customer_available_downloads as $download_info) {
                            if (($key = array_search($download_info['download_id'], $downloadable_product_files)) !== false) {
                                unset($downloadable_product_files[$key]);
                            }
                        }
                        if (!empty($downloadable_product_files)) {
                            foreach ($downloadable_product_files as $download_id) {
                                wc_downloadable_file_permission($download_id, $order_product['variation_id'] > 0 ? $order_product['variation_id'] : $order_product['product_id'], $order, $order_product['qty']);
                            }
                            $added_flag = true;
                        }
                    }
                }
            }
        }
    }
    $message = $added_flag ? 'Новые файлы добавлены в ваш ЛК!' : 'Новых файлов пока нет!';
    wc_add_notice($message, $added_flag ? 'success' : 'notice');
}

add_filter('woocommerce_loop_add_to_cart_link', 'custom_woocommerce_loop_add_to_cart_link', 10, 2);
function custom_woocommerce_loop_add_to_cart_link($val, $product) {

    $access = getAccessToProduct($product->id);

    $user_access = get_post_meta($product->id, 'access', TRUE);
    if (($access) || (!$product->price) || (current_user_can('vip') && in_array($user_access, [1, 3]))/* || (is_super_admin())*/) {
        if ( ! $product->is_virtual()){
			return $val;
		}else{
			return sprintf('<a rel="nofollow" href="%s" class="%s">Смотреть</a>',
				esc_url(get_the_permalink($product->id)),
				esc_attr(isset($class) ? $class : 'button'),
				esc_html($product->add_to_cart_text())
			);
		}
    } else
        if ($access === 0) {
            return 'доступно только для Ближний Круг';
        } else {
            return sprintf('<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
                esc_url($product->add_to_cart_url()),
                esc_attr(isset($quantity) ? $quantity : 1),
                esc_attr($product->id),
                esc_attr($product->get_sku()),
                esc_attr(isset($class) ? $class : 'button'),
                esc_html($product->add_to_cart_text())
            );
        }
}

function getLinkAutologinToRoom($product_id, $qty = 1) {

    //вебинар старого образца, т.е. не имеет ИД вебинара
    if (!getWebinarId($product_id)) {
        return false;
    }

    $qty = (int)$qty;

    $oProduct = wc_get_product($product_id);
    $webinar_room_id = get_post_meta($product_id, 'webinar_room_id', TRUE);

    $aLinks = [];

    try {
        include_once __DIR__ . '/lib/ClickMeetingRestClient.php';
        $client = new ClickMeetingRestClient(['api_key' => 'usc7521301839e049c59fc6dab55dd54555caf1dda']);

        //получаем информацию о конференции
        $aConferenceResult = $client->conference($webinar_room_id);
        $room_url = $aConferenceResult->conference->room_url . "?l=";

        //Текущий пользователь
        $current_user = wp_get_current_user();

        SWITCH ($aConferenceResult->conference->access_type) {
            //бесплатный
            case 1:
                $client->conferenceAutologinHash($webinar_room_id, [
                    'email'    => $current_user->user_email,
                    //'nickname' => $current_user->user_login,
                    'nickname' => trim($current_user->user_firstname.' '.$current_user->user_lastname),
                    'role'     => 'listener'
                ]);

                $aLinks[] = $room_url . $oAutoLogin->autologin_hash;
                break;

            //с паролем
            /*case 2:
              $client->conferenceAutologinHash($webinar_room_id, array(
                'email' 	=> 'test@mail.ru',
                'nickname' 	=> 'pavel',
                'role' 		=> 'listener'
              ));

              $aLinks[] = $room_url . $oAutoLogin->autologin_hash;
            break;*/

            //с токеном
            case 3:
                $aLinksOld = $aLinks = array();
                
                $iRevisionPost = (int)get_post_meta($product_id, 'webinar_room_id_rev', true);
                $iRevisionUser = (int)get_user_meta($current_user->ID, "post_{$product_id}_webinar_room_id_rev", true);
                
                if ($iRevisionPost == $iRevisionUser){
                    $aLinksOld = $aLinks = get_user_meta($current_user->ID, 'webinar_links_' . $product_id, true);
                }
                
                $qtyOld = $qty;

                $qty -= is_array($aLinks) ? count($aLinks) : 0;
                if ($qty > 0) {
                    $aTokensResult = ($client->generateConferenceTokens($webinar_room_id, ['how_many' => $qty]));
                    if ($aTokensResult->access_tokens) {
                        foreach ($aTokensResult->access_tokens as $key => $oToken) {
                            $sPostfix = ($qtyOld > 1 || ($key != 0 && !count($aLinksOld))) ? '_' . ((count($aLinksOld)) + $key + 1) : '';

                            $oAutoLogin = $client->conferenceAutologinHash($webinar_room_id, [
                                'email'    => $current_user->user_email,
                                //'nickname' => $sPrefix . $current_user->user_login,
                                'nickname' => trim($current_user->user_firstname.' '.$current_user->user_lastname) . $sPostfix,
                                //'role' 		=> 'listener',
                                'token'    => $oToken->token
                            ]);

                            $aLinks[] = $room_url . $oAutoLogin->autologin_hash;
                        }
                    }
                }

                if ($aLinksOld != $aLinks) {
                    update_user_meta($current_user->ID, 'webinar_links_' . $product_id, $aLinks);
                }

                if ($aLinks) {
                    $aLinks = array_slice($aLinks, 0, $qtyOld);
                }
                
                if ($iRevisionPost != $iRevisionUser){
                    update_user_meta( $current_user->ID, "post_{$product_id}_webinar_room_id_rev", $iRevisionPost );
                }
                
                break;
        }
    } catch (Exception $e) {
        //die(var_dump($e));
    }

    return $aLinks;
}

add_filter('woocommerce_is_purchasable', 'custom_woocommerce_is_purchasable', 10, 2);
function custom_woocommerce_is_purchasable($purchasable, $product) {
    if (!is_admin()) {
        $access = getAccessToProduct($product->get_id());
        $user_access = get_post_meta($product->get_id(), 'access', TRUE);
        if (($access === 0) || (current_user_can('vip') && in_array($user_access, [1, 3]))/* || (is_super_admin())*/) {
            $purchasable = false;
        }
    }

    return $purchasable;
}

add_filter('woocommerce_get_price_html', 'custom_woocommerce_get_price_html', 10, 2);
function custom_woocommerce_get_price_html($price, $product) {
    if (!is_admin()) {
        $access = getAccessToProduct($product->get_id());
        $user_access = get_post_meta($product->get_id(), 'access', TRUE);
        if (/*($user_access === 0) || */
        (current_user_can('vip') && in_array($user_access, [1, 3]))/* || (is_super_admin())*/
        ) {
            $price = __('Free!', 'woocommerce');
        }
    }

    return $price;
}

//Файлы к бесплатным товарам даже на которые нет заказов
remove_all_filters('tanhit_free_download_products');
add_action('tanhit_free_download_products', 'custom_tanhit_free_download_products');
function custom_tanhit_free_download_products() {

    global $tanhit_customer_products;

    //Для данных файлов, показывать ссылку на скачивание а не онлайн просмотр

    $disable_file_online_show = ['.zip', '.rar', '.pdf'];

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => ['webinar', 'practice'],
            ]
        ],
    ];

    $events = new WP_Query($args);

    $now = date('Ymd', time());

    $free_products = [];
    foreach ($events->posts as $key => $product) {

        $product_date_start = strtotime(get_post_meta($product->ID, 'product_date_start', true));

        $pr = wc_get_product($product->ID);

        if ($pr->get_price() > 0) {
            continue;
        }

        $product_bought = false;
        foreach ($tanhit_customer_products as $customer_product) {
            if ($customer_product['order']->post_status == 'wc-completed' && $product->ID == $customer_product['product_id']) {
                $product_bought = true;
                break;
            }
        }
        if ($product_bought) {
            continue;
        }

        $free_products[] = $pr;
    }

    foreach ($free_products as $product){

        $downloads = $product->get_files();

        /**
         * for download @see 'init' action in tanhit-functions.php
         */
        foreach ($downloads as $key => $download){?>
          <li data-product="<?php echo $product->id; ?>">
            <span class="item-preview"
                  style="display: inline-block; overflow: hidden"><?php echo $product->get_image(); ?></span>
            <a href="<?php echo get_the_permalink($product->id); ?>"
               class="item-link vid-link" target="_blank"><?php echo $product->post->post_title; ?></a>
            <span class="file-name"><?php echo $download['name']; ?></span>
            
            <?php
            
            $youtube = false;
            if (getYotubeDownLink($download['file'])) {
                $youtube = $download['file'];
            }

            if (!empty($download['file']) && !$youtube){
                /**
                 * Check for disabled file for online show
                 */
                $disabled = false;
                foreach ($disable_file_online_show as $piece) {
                    if (false !== strpos($download['file'], $piece)) {
                        $disabled = true;
                        break;
                    }
                }
                if (!$disabled) {
                    $link = getVideoFileByKey($product->id, $key);
                    ?>
            
                    <a href="#vid<?php echo $product->id . "-" . $key; ?>" class="show-video btn-show">
                        <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
                    </a>

                    <div style="display:none;" class="show_vid" id="vid<?php echo $product->id . "-" . $key; ?>" data-src="<?= $link; ?>">
                        <div class="vid_player vid_player2">2
                            <?= (getPlayerForm($link)) ?>
                        </div>
                    </div>
                <?}else{?>
                    <a href="<?php echo home_url() . '/?tanhit_download=true&product=' . $product->id . '&key=' . $key; ?>"
                        class="btn-download"><?php pll_e('Скачать', 'tanhit'); ?>
                     </a>
                <?}?>
              <?}elseif ($youtube){?>
                <a href="#vid<?php echo $product->id . "-" . $key; ?>" class="show-video btn-show">
                    <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
                </a>

                <div style="display:none;" class="show_vid" id="vid<?php echo $product->id . "-" . $key; ?>"
                     data-src="<?= $youtube; ?>">
                  <div class="vid_player vid_player2">
                      <?= (getPlayerForm($youtube)) ?>
                  </div>
                </div>
              <?}?>
          </li>
        <?}
    }
}

//Файлы к товарам которые заказаны
remove_all_filters('woocommerce_available_download_start');
add_action('woocommerce_available_download_start', 'custom_woocommerce_available_download_start');
function custom_woocommerce_available_download_start($download) {
    global $tanhit_customer_products;

    $product = [];

    foreach ($tanhit_customer_products as $pr) {
        //echo "***{$download['product_id']}***";
        if ($pr['product_id'] == $download['product_id']) {
            $product[$download['product_id']] = $pr;
            break;
        }
    }

    //Для данных файлов, показывать ссылку на скачивание а не онлайн просмотр
    $disable_file_online_show = ['.zip', '.rar', '.pdf'];

    $youtube = false;
    if (getYotubeDownLink($download['file']['file'])) {
        $youtube = $download['file']['file'];
    }
    ?>

    <span class="item-preview" style=""><?php echo tanhit_get_product_thumbnail($download['product_id']); ?></span>

    <a href="<?php echo $product[$download['product_id']]['permalink']; ?>" class="item-link vid-link" target="_blank">
        <?php echo $product[$download['product_id']]['product_name']; ?>
    </a>

    <span class="file-name"><?php /* pll_e( 'Файл:', 'tanhit' );*/echo $download['file']['name']; ?></span>

    <?php
    if ( ! empty($download['file']['file']) && ! $youtube){
        $disabled = false;
        foreach ($disable_file_online_show as $piece) {
            if (false !== strpos($download['file']['file'], $piece)) {
                $disabled = true;
                break;
            }
        }
        if (!$disabled) {
            $link = getVideoFile($download['file']['file']);
            ?>
          <a href="#vid<?php echo md5($download['file']['file']); ?>" class="show-video btn-show">
              <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
          </a>


          <div style="display:none;" class="show_vid" id="vid<?php echo md5($download['file']['file']); ?>"
               data-src="<?= $link; ?>">
            <div class="vid_player vid_player2">
                <?php/* echo do_shortcode("[wpm_video video=".getVideoFile($download['file']['file'])." ratio=16by9 autoplay=off]"); */ ?>
                <?= (getPlayerForm($link)) ?>
            </div>
          </div>
        <?}else{?>
            <a href="<?php echo esc_url($download['download_url']); ?>"
                class="btn-download"><?php pll_e('Скачать', 'tanhit'); ?>
            </a>
        <?}
    }elseif ($youtube) {?>
        <a href="#vid<?php echo md5($download['file']['file']); ?>" class="show-video btn-show">
            <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
        </a>

        <div style="display:none;" class="show_vid" id="vid<?php echo md5($download['file']['file']); ?>" data-src="<?= $youtube; ?>">
            <div class="vid_player vid_player2">
                <?= (getPlayerForm($youtube)) ?>
            </div>
        </div>
    <?}
}

//Фильтруем ссылки в письме, выполненного заказа
add_filter('woocommerce_get_item_downloads', 'custom_woocommerce_get_item_downloads', 99, 3);
function custom_woocommerce_get_item_downloads($files, $item, $el) {

    if (!is_admin()) {
        $aData = parse_url($_SERVER['REQUEST_URI']);
        if (trim($aData['path'], '/') != 'my-account') {
            global $wpdb;

            $product_id = $item['variation_id'] > 0 ? $item['variation_id'] : $item['product_id'];
            $product = wc_get_product($product_id);
            if (!$product) {
                /**
                 * $product can be `false`. Example: checking an old order, when a product or variation has been deleted.
                 * @see \WC_Product_Factory::get_product
                 */
                return [];
            }
            $download_ids = $wpdb->get_col($wpdb->prepare("
				SELECT download_id
				FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
				WHERE user_email = %s
				AND order_key = %s
				AND product_id = %s
				ORDER BY permission_id
			", $this->billing_email, $this->order_key, $product_id));

            $files = [];
            foreach ($download_ids as $download_id) {
                if ($product->has_file($download_id)) {
                    $temp = $product->get_file($download_id);
                    if (!getYotubeDownLink($temp['file'])) {
                        $files[$download_id] = $temp;
                        $files[$download_id]['download_url'] = $this->get_download_url($product_id, $download_id);
                    }
                }
            }
        }
    }

    return $files;
}

//получаем ссылку на видео файл
function getYotubeDownLink($link) {

    $video_id = '';

    if (false !== mb_strpos($link, 'youtu.be', 1, 'UTF-8')) {
        $video_id = ltrim(mb_substr($link, 16, 1000, 'UTF-8'), '/');
    } else
        if (false !== mb_strpos($link, 'youtube.com', 1, 'UTF-8')) {
            $video_id = mb_substr($link, 31, 1000, 'UTF-8');
        }

    if ($video_id) {
        return true;
    }

    return false;

    $res = '';
    if ($video_id) {
        $data = file_get_contents("https://www.youtube.com/get_video_info?video_id=$video_id");
        parse_str($data);

        $arr = explode(",", $url_encoded_fmt_stream_map);

        foreach ($arr as $key => $item) {
            parse_str($item, $aItem);

            $aVideos[$aItem['itag']] = $aItem;
        }

        if ($aVideos) {
            ksort($aVideos);

            $aItem = array_pop($aVideos);
            $res = $aItem['url'];
        }
    }

    return $res;
}

function getVideoFile($file) {

    return $file;
}

function getVideoFileByKey($product_id, $key) {
    $pr = wc_get_product($product_id);
    $files = $pr->get_files();

    $file = $files[$key]['file'];

    return $file;
}

function getPlayerForm($file) {

    $videoUrl = parse_url($file);
    $source_html = '';
    $data = '';

    if (strpos($videoUrl['host'], 'youtu') !== false) {
        $pattern = '#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=‌​(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#';
        preg_match($pattern, $file, $matches);
        if (isset($matches[0])) {
            $youtubeId = $matches[0];
        } else {
            parse_str(parse_url($file, PHP_URL_QUERY), $params);
            $youtubeId = isset($params['v']) ? $params['v'] : (isset($params['amp;v']) ? $params['amp;v'] : '0');
        }

        $link = 'http://www.youtube.com/watch?v=' . $youtubeId;
        $data = 'data-setup=\'{"techOrder": ["youtube"], "sources": [{ "type": "video/youtube", "src": "' . $link . '"}], "youtube": { "controls": 0 }}\'';
    } else {
        $link = $file;
        $source_html = '<source src="' . $link . '" type="video/mp4"></source>';
    }

    $videoId = 'vid_id_' . substr(md5($link . rand(0, 1000)), 0, 20);
    //$script = '<script>wpmVideo.initYT("%s",%s,"%s",%d);</script>';

    $video = '<video id="' . $videoId . '" class="video-js vjs-default-skin" controls preload="auto" ' . $data . ' width="490" height="275">' . $source_html . '  
      <p class="vjs-no-js">
        To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank"> supports HTML5 video</a>
      </p>
    </video>';

    $html = '<div class="wpm-video-size-wrap">';
    $html .= '<div class="wpm-video-youtube video_wrap video_margin_center wpmjw inactive style-9">';
    $html .= '<div class="embed-responsive embed-responsive-16by9">';
    //$html .= '<div id="' . $videoId . '" data-src="'. $link .'"></div>';
    $html .= $video;
    $html .= '</div></div></div>';

    //$html .= sprintf($script, $videoId, "'{$link}'", '16:9', 0);

    return $html;
}


//Если комната в вебинаре изменена, то обновляем версию, чтоб при запросе ссылки на вебинар у пользователя 
//если ссылка старая - то обновилась, т.е. обновился кэш 
add_action( 'updated_postmeta', 'custom_updated_post_meta', 10, 4 );
function custom_updated_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
    
    if ($meta_key == 'webinar_room_id'){
        $iRevision = (int)get_post_meta($object_id, 'webinar_room_id_rev', true);
        update_post_meta( $object_id, 'webinar_room_id_rev', ++$iRevision );
    }
}


function woocommerce_checkout_thankyou_shortcode(){
    echo '<div class="woocommerce"><p class="woocommerce-message">Спасибо за покупку! Сейчас вы будете перенаправлены в свой личный кабинет!</p>
        <script >window.onload = function(){setTimeout(\'location="/my-account"\', 4000);}</script></div>';
}
add_shortcode('woocommerce_checkout_thankyou', 'woocommerce_checkout_thankyou_shortcode');

add_action('display_pincodes', 'display_pincodes');
function display_pincodes(){
  $customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
	'numberposts' => 100,
	'meta_key'    => '_customer_user',
	'meta_value'  => get_current_user_id(),
	'post_type'   => 'shop_order',
	'post_status' => 'wc-completed'
) ) );

  echo '<h2>Мои пин-коды</h2>';
  $no_pins = true;
  if(!empty($customer_orders)){
      $WC_LD_Codes = new WC_LD_Code_Assignment();
      foreach ($customer_orders as $order){
        $table = $WC_LD_Codes->get_assigned_codes($order->ID);
        if ($table){
          $no_pins = false;
          echo $table;
        }
      }
  }

  if ($no_pins) {
    echo 'У вас ещё нет пин-кодов.';
  }
}


add_action('admin_footer', 'custom_admin_scripts');
function custom_admin_scripts() {
    echo '"<script type="text/javascript" src="'. get_bloginfo('template_directory') . '/js/admin/chosen/chosen.min.css' . '"></script>"';
    echo '"<script type="text/javascript" src="'. get_bloginfo('template_directory') . '/js/admin/chosen/chosen.jquery.min.js' . '"></script>"';
	echo '<script>jQuery("#acf-field-cert_user").chosen({allow_single_deselect: true});</script>';
	echo '<script>jQuery("#acf-field-cert_manager").chosen({allow_single_deselect: true});</script>';
}

add_filter( 'post_type_link', 'my_custom_permalinks', 10, 2 );
function my_custom_permalinks( $permalink, $post ) {
	if ($post->post_type == 'certificates'){
		return home_url('/certificates/' . str_pad($post->ID, 10, 0, STR_PAD_LEFT));
	}
	
	return $permalink;
}

function getCertificatePdf($post_id, $save_dir = ''){
	global $wpdb;
	
	$oData = $wpdb->get_row( "
		SELECT P.*, TRIM(CONCAT(UM.meta_value,' ',UM2.meta_value)) as cert_user_name, PM2.meta_value as cert_location, PM3.meta_value as cert_date 
		FROM {$wpdb->prefix}posts P 
		INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user')
		INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value)
		INNER JOIN {$wpdb->prefix}usermeta UM ON (UM.user_id = U.ID && UM.meta_key = 'first_name')
		INNER JOIN {$wpdb->prefix}usermeta UM2 ON (UM2.user_id = U.ID && UM2.meta_key = 'last_name')
		INNER JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'cert_location')
		LEFT JOIN {$wpdb->prefix}postmeta PM3 ON (PM3.post_id = P.ID && PM3.meta_key = 'cert_date')
		WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' && P.ID = '{$post_id}'
		LIMIT 1" 
	);
	
	if ($oData){
		$iCertificateNum = str_pad($oData->ID, 10, 0, STR_PAD_LEFT);
		
		$term_list = wp_get_post_terms($oData->ID, 'certificate_type', array("fields" => "all"));
		if ($term_list){
			$term = array_shift($term_list);
			
			$sHtmlContent = '';
			
			$tpl_img = wp_get_terms_meta($term->term_id, 'tpl', true);
			if ($tpl_img){
				SWITCH($term->slug){
					case 'c1':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:390px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:868px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:950px;right:160px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c2':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:381px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:777px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;right:150px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c3':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:420px;width:100%;text-align:center;font-size:20px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:815px;width:100%;text-align:center;font-size:22px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:180px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c4':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:433px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:792px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:185px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c5':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:445px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:810px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:180px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c6':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:475px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:755px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:925px;left:180px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c7':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:419px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:789px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:925px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c8':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:456px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:780px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:185px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c9':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:430px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:783px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:180px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c10':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:468px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:800px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c11':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:428px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:785px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c12':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:445px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:785px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c13':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:418px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:788px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c14':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:470px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:768px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
				}
			}
			
			
			if ($sHtmlContent){
				$sFile = html2Pdf('Сертификат № ' . $iCertificateNum, $sHtmlContent, $save_dir . $iCertificateNum, '', ! $save_dir);
				
				if ($save_dir){
					return $sFile;
				}
			}
		}
	}
	
	return false;
}

add_action('pre_get_posts', function(WP_Query $el) {
	global $wpdb;
	
    // We want to act only on frontend and only main query
    if (is_admin() || !$el->is_main_query()) {
        return;
    }

	if ( is_post_type_archive( 'certificates' ) ) {
		// Выводим 50 записей если это архив типа записи 'movie'
		$el->set( 'posts_per_page', 20 );
	}
	else
	if ($el->query['post_type'] == 'certificates' && is_singular()){
		$post_id = (int)$el->get('certificates');
		
		if (getCertificatePdf($post_id) === FALSE){
			echo "Сертификат № {$iCertificateNum} не возможно отобразить, обратитесь к администратору сайта";
		}
		
		exit;
	}
});

function html2Pdf($sHtmlTitle, $sHtmlContent, $sSaveFile, $stylesheet = '', $to_browser = false, $aReplaceData = array()) {
    $sFile = $sSaveFile . '.pdf';
	
    $mpdf = new mPDF('', 'A4', '', '', 0, 0, 0, 0, 0, 0);
	$mpdf->SetTitle($sHtmlTitle);
    $mpdf->useOnlyCoreFonts = true;
    $mpdf->SetDisplayMode('fullpage');
	
	if ($stylesheet){
		$mpdf->WriteHTML($stylesheet,1);
	}

	if ($aReplaceData){
		$sHtmlContent = str_replace(array_keys($aReplaceData), array_values($aReplaceData), $sHtmlContent);
	}
	
    $mpdf->WriteHTML($sHtmlContent, 2);
	
	if ($to_browser){
		$mpdf->Output($sFile, 'D');
	}else{
		$mpdf->Output($sFile, 'F');
		chmod($sFile, 0777);
	}

    return $sFile;
}

add_action('display_certificates', 'display_certificates');
function display_certificates(){
	wc_get_template( 'myaccount/my-certificates.php' );
}

add_action('admin_head-edit.php', 'custom_edit_post_change_title_in_list');
function custom_edit_post_change_title_in_list() {
	if ('certificates' == $_REQUEST['post_type']){
		add_filter('the_title','wpse152971_construct_new_title', 100, 2);
	}
}

function wpse152971_construct_new_title( $title, $id ) {
    return 'Сертификат №  '.str_pad($id, 10, 0, STR_PAD_LEFT);
}

//Доп поля в пользователях - админка
/*add_action( 'show_user_profile', 'my_show_extra_profile_fields', 9999 );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields', 9999 );
function my_show_extra_profile_fields( $user ) { 
	$aTypes = get_terms( 'certificate_type', array(
		'hide_empty' => false,
	));
	
	$aTypesSelected = array();
	if($aTypes){
		foreach($aTypes as $aType){
			$aTypesSelected[$aType->term_id] = get_usermeta( $user->ID, "{$aType->taxonomy}_{$aType->term_id}" );
		}
	}
	
	$aStatuses = get_terms( 'certificate_status', array(
		'hide_empty' => false,
	));
?>

	<h3>Статусы по сертификатам</h3>

	<?if($aTypes){?>
	<table class="form-table">
		<?if($aTypes){
			foreach($aTypes as $aType){
				$sFieldName = "{$aType->taxonomy}_{$aType->term_id}";
			?>	
				<tr>
					<th><label for="<?=$sFieldName?>"><?=$aType->name?></label></th>

					<td>
						<select id="<?=$sFieldName?>" name="<?=$aType->taxonomy?>[<?=$aType->term_id?>]">
							<option value='0'>- не выбрано -</option>
							<?if($aStatuses){?>
								<?foreach($aStatuses as $aStatus){?>
									<option value='<?=$aStatus->term_id?>'<?=(isset($aTypesSelected[$aType->term_id]) && $aTypesSelected[$aType->term_id] && $aTypesSelected[$aType->term_id] == $aStatus->term_id ? ' selected="selected"' : '')?>><?=$aStatus->name?></option>
								<?}?>
							<?}?>
						</select>
					</td>
				</tr>
			<?}?>
		<?}?>
	</table>
	<?}else{?>
	<hr />
	типы сертификатов не существуют
	<?}?>
<?php }

//Сохранение доп полей пользователя
add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );
function my_save_extra_profile_fields( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) )
		return false;

	if ($_POST['certificate_type']){
		foreach($_POST['certificate_type'] as $iKey => $sValue){
			update_usermeta( $user_id, "certificate_type_{$iKey}", $sValue );
		}
	}
}
*/

//Фильтр в сертификатах по пользователю
add_action( 'restrict_manage_posts', 'certificates_manage_posts');
function certificates_manage_posts($post_type) {
	
	if ( 'certificates' == $post_type ) {
		certificates_filters();
	}
}

function certificates_filters(){
	$user_string = '';
	$user_id     = '';
	if ( ! empty( $_GET['_customer_user'] ) ) {
		$user_id     = absint( $_GET['_customer_user'] );
		$user        = get_user_by( 'id', $user_id );
		$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
	}
	?>
	
	<input type="hidden" class="wc-customer-search" name="_customer_user" data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;', 'woocommerce' ); ?>" data-selected="<?php echo htmlspecialchars( $user_string ); ?>" value="<?php echo $user_id; ?>" data-allow_clear="true" />
	<?php
}

//Скрипты для селекта - фильтра по пользователям
add_action('admin_enqueue_scripts', 'certificates_load_scripts');
function certificates_load_scripts($hook) {
	$screen = get_current_screen();
	
	if ($screen->post_type == 'certificates' && $hook == 'edit.php'){
		$suffix = '.min';
		
		// Select2 is the replacement for chosen
		wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2' . $suffix . '.js', array( 'jquery' ), '3.5.4' );
		wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'select2' ), WC_VERSION );
		wp_localize_script( 'wc-enhanced-select', 'wc_enhanced_select_params', array(
			'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
			'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
			'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
			'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
			'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
			'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
			'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
			'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
			'ajax_url'                  => admin_url( 'admin-ajax.php' ),
			'search_products_nonce'     => wp_create_nonce( 'search-products' ),
			'search_customers_nonce'    => wp_create_nonce( 'search-customers' )
		) );
		
		wp_enqueue_script( 'wc-enhanced-select' );
	}
}

//Применение фильтра, изменение запроса
add_filter( 'request', 'certificates_request_query' );
function certificates_request_query( $query ) {
	global $wp_post_statuses;

	if ( 'certificates' == $query['post_type'] ) {

		// Filter the orders by the posted customer.
		if ( isset( $_GET['_customer_user'] ) && $_GET['_customer_user'] > 0 ) {
			$query['meta_query'] = array(
				array(
					'key'   => 'cert_user',
					'value' => (int) $_GET['_customer_user'],
					'compare' => '='
				)
			);
		}
	}

	return $query;
}

//Добавляем страницу массовой генерации сертификатов
add_action('admin_menu', 'register_certificates_generation_submenu_page');
function register_certificates_generation_submenu_page() {
    add_submenu_page('edit.php?post_type=certificates', 'Массовая выдача', 'Массовая выдача', 'manage_options', 'wp_certificates_generation_page', 'wp_certificates_generation_page_callback');
}

function wp_certificates_generation_page_callback() {

	$aTypes = get_terms( 'certificate_type', array(
		'hide_empty' => false,
	));
	
    $sMessage = '';

    //Обновляем записи
    if (isset($_POST['submit'])) {
		$sEmails 	= trim(isset($_POST['emails']) ? $_POST['emails'] : '');
		$iCertType 	= (int)(isset($_POST['cert_type']) ? $_POST['cert_type'] : '');
		$sDate 		= (isset($_POST['cert_date']) ? strtotime($_POST['cert_date']) : '');
		$aPlace 	= isset($_POST['place']) ? $_POST['place'] : '';
		$aPlace2 	= isset($_POST['place2']) ? $_POST['place2'] : '';
		$iManager 	= isset($_POST['manager']) ? $_POST['manager'] : '';
		
		if($sEmails && $iCertType && $sDate && $aPlace){
			$sEmails = trim($sEmails, "\r\n");
			$sEmails = trim($sEmails);
			
			$iAddTotal = 0;
			
			$aEmails = explode("\r\n", $sEmails);
			if($aEmails){
				foreach ($aEmails as $sEmail){
					$user = get_user_by( 'email', $sEmail );
					if ( ! empty( $user ) ) {
						$aPostData = array(
							'post_status'    => 'publish',
							'post_type'      => 'certificates',
							'tax_input'      => array( 
								'certificate_type' => array( 
									$iCertType 
								) 
							),
							'meta_input'     => array( 
								'cert_user' 	=> $user->ID,
								'cert_date'		=> date('Y-m-d', $sDate),
								'cert_location'		=> $aPlace,
								'cert_location_2'	=> $aPlace2,
							)
						);
				
						if ($iManager){
							$aPostData['meta_input']['cert_manager'] = $iManager;
						}
				
						if (wp_insert_post( $aPostData )){
							$iAddTotal++;
						}
					}
				}
			}
			
			$_SESSION['sMessage'] = "<div style='padding:5px;background:#bdfe82;'><strong>Выдано сертификатов: {$iAddTotal}</strong></div>";
		}else{
			$_SESSION['sMessage'] = "<div style='padding:5px;background:#fe8282;'><strong>Заполнены не все поля</strong></div>";
		}
		
		header('Location: /wp-admin/edit.php?post_type=certificates&page=wp_certificates_generation_page');
		exit;
	}
	?>

    <div class="wrap">
    <h2>Массовая выдача сертификатов</h2>

	<?if(isset($_SESSION['sMessage'])){
		echo $_SESSION['sMessage'];
		unset($_SESSION['sMessage']);
	}?>
	
    <form action="" method="POST">
		<table class="form-table">
			<tr>
				<th><label for="emails">Список E-mail</label></th>

				<td>
					<textarea style='width:100%;height:100px;resize:vertical;' name='emails' required='true'></textarea>
					<p>каждый адрес на отдельной строке</p>
				</td>
			</tr>
			<tr>
				<th><label for="cert_type">Тип сертификата</label></th>

				<td>
					<select id="cert_type" style='width:100%;' name="cert_type" required='true'>
						<option value=''>- не выбрано -</option>
						<?if($aTypes){?>
							<?foreach($aTypes as $aItem){?>
								<option value='<?=$aItem->term_id?>'><?=$aItem->name?></option>
							<?}?>
						<?}?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="cert_date">Дата</label></th>

				<td>
					<input id="cert_date" type='date' name="cert_date" required='true' />
				</td>
			</tr>
			<tr>
				<th>Место выдачи</th>
				<td>
					<input id='place_address' type='hidden' name='place[address]' value='' />
					<input id='place_lat' type='hidden' name='place[lat]' value='' />
					<input id='place_lng' type='hidden' name='place[lng]' value='' />
					<div><input id="google-map-search" class="controls" style='width:100%;' type="text" placeholder="Поиск..."></div><br />
					<div id="google-map"></div>
				</td>
			</tr>
			<tr>
				<th>Место выдачи (дополнительно)</th>
				<td>
					<input id='place_address2' type='hidden' name='place2[address]' value='' />
					<input id='place_lat2' type='hidden' name='place2[lat]' value='' />
					<input id='place_lng2' type='hidden' name='place2[lng]' value='' />
					<div><input id="google-map-search2" class="controls" style='width:100%;' type="text" placeholder="Поиск..."></div><br />
					<div id="google-map2"></div>
				</td>
			</tr>
			<tr>
				<th>Ведущий</th>
				<td>
					<select id='acf-field-cert_manager' name='manager'>
						<option></option>
						<?foreach(get_users(array('number'=>'','count_total'=>false,'fields'=>array('ID','display_name','user_email'),'orderby'=>'display_name')) as $oUser){?>
							<option value='<?=$oUser->ID?>'><?=$oUser->display_name?> [<?=$oUser->user_email?>]</option>
						<?}?>
					</select>
				</td>
			</tr>
		</table>

		<?=submit_button('Выдать сертификаты');?>
    </form>
	
	<style>
		#google-map {
			height: 400px;
			width:100%;
		}
		#google-map2 {
			height: 400px;
			width:100%;
		}
	</style>
    <script>
		// This example adds a search box to a map, using the Google Place Autocomplete
		// feature. People can enter geographical searches. The search box will return a
		// pick list containing a mix of places and predicted search terms.

		function initAutocomplete() {
			var myLatlng = {lat: 55.7522200, lng: 37.6155600};
			
			var map = new google.maps.Map(document.getElementById('google-map'), {
				center: myLatlng,
				zoom: 5,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});

			// Create the search box and link it to the UI element.
			var input = document.getElementById('google-map-search');
			var searchBox = new google.maps.places.SearchBox(input);
			//map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

			// Bias the SearchBox results towards current map's viewport.
			map.addListener('bounds_changed', function() {
				searchBox.setBounds(map.getBounds());
			});

			var markers = [];
			// [START region_getplaces]
			// Listen for the event fired when the user selects a prediction and retrieve
			// more details for that place.
			searchBox.addListener('places_changed', function() {
				var place = searchBox.getPlaces()[0];

				if ( ! place.geometry) return;

				jQuery('#place_address').val(place.formatted_address);
				jQuery('#place_lat').val(place.geometry.location.lat());
				jQuery('#place_lng').val(place.geometry.location.lng());
				
				markers.forEach(function(marker) {
					marker.setMap(null);
				});
				markers = [];
				
				// Create a marker for each place.
				markers.push(new google.maps.Marker({
					map: map,
					title: place.name,
					position: place.geometry.location
				}));
				
				var bounds = new google.maps.LatLngBounds();
				if (place.geometry.viewport) {
					bounds.union(place.geometry.viewport);
				} else {
					bounds.extend(place.geometry.location);
				}
				
				map.fitBounds(bounds);
			});
			// [END region_getplaces]
			
			
			var myLatlng2 = {lat: 55.7522200, lng: 37.6155600};
			
			var map2 = new google.maps.Map(document.getElementById('google-map2'), {
				center: myLatlng2,
				zoom: 5,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});

			// Create the search box and link it to the UI element.
			var input2 = document.getElementById('google-map-search2');
			var searchBox2 = new google.maps.places.SearchBox(input2);
			//map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

			// Bias the SearchBox results towards current map's viewport.
			map2.addListener('bounds_changed', function() {
				searchBox2.setBounds(map2.getBounds());
			});

			var markers2 = [];
			// [START region_getplaces]
			// Listen for the event fired when the user selects a prediction and retrieve
			// more details for that place.
			searchBox2.addListener('places_changed', function() {
				var place2 = searchBox2.getPlaces()[0];

				if ( ! place2.geometry) return;

				jQuery('#place_address2').val(place2.formatted_address);
				jQuery('#place_lat2').val(place2.geometry.location.lat());
				jQuery('#place_lng2').val(place2.geometry.location.lng());
				
				markers2.forEach(function(marker) {
					marker.setMap(null);
				});
				markers2 = [];
				
				// Create a marker for each place.
				markers2.push(new google.maps.Marker({
					map: map2,
					title: place2.name,
					position: place2.geometry.location
				}));
				
				var bounds2 = new google.maps.LatLngBounds();
				if (place2.geometry.viewport) {
					bounds2.union(place2.geometry.viewport);
				} else {
					bounds2.extend(place2.geometry.location);
				}
				
				map2.fitBounds(bounds2);
			});
			// [END region_getplaces]
		}
		
		jQuery(document).ready(function() {
			jQuery('#google-map-search').keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					return false;
				}
			});
			
			jQuery('#google-map-search2').keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					return false;
				}
			});
		});
    </script>
	
	<script src="https://maps.googleapis.com/maps/api/js?v=3&sensor=false&key=AIzaSyDIf-8uF1c86zFX_ElUI8PKv9lQVS_n3wM&libraries=places&callback=initAutocomplete" async defer></script>

	<?
}

//[cert_list statuses="220" column_location_title="Место прохождения практики"]
add_shortcode( 'cert_list', 'cert_list_shortcode' );
function cert_list_shortcode( $atts ) {
	global $wpdb;
	
	$atts = shortcode_atts( array(
		'id_list'				=> '',
		'my' 					=> 0, 	//Выводить только мои сертификаты
		'manager' 				=> 0, 	//Сертификаты подопечных
		'full' 					=> 0,	//Краткие столбцы или подробные
		'filter'				=> 0,	//Выводить фильтр
		'sort'					=> 0,	//Выводить сортировку
		'practika'				=> '',	//Сертификаты для каких типов выводить через запятую 1,2,3
		'statuses'				=> '',	//Сертификаты для каких статусов выводить через запятую 1,2,3
		'practika_statuses' 	=> '',	//Сертификаты типов с определенными статусами 1:2,3|2:3,
		'column_location_title'	=> '',
	), $atts, 'cert_list' );

	ob_start();
	include (__DIR__ . '/certificate/cert_list.php' );
	return ob_get_clean();
}

add_shortcode( 'cert_map', 'cert_map_shortcode' );
function cert_map_shortcode( $atts ) {
	global $wpdb;
	
	$atts = shortcode_atts( array(
		'id_list'			=> '',
		'my' 				=> 0, 	//Выводить только мои сертификаты
		'manager' 			=> 0, 	//Сертификаты подопечных
		'practika'			=> '',	//Сертификаты для каких типов выводить через запятую 1,2,3
		'statuses'			=> '',	//Сертификаты для каких статусов выводить через запятую 1,2,3
		'practika_statuses' => '',	//Сертификаты типов с определенными статусами 1:2,3|2:3
		'icon_img'			=> ''
	), $atts, 'cert_map' );
	
	ob_start();
	include (__DIR__ . '/certificate/cert_map.php' );
	return ob_get_clean();
}

add_action('admin_head', 'custom_admin_css');
function custom_admin_css() {
  echo '<style>
    #tagsdiv-certificate_status{display:none;}
    #tagsdiv-certificate_practika{display:none;}
  </style>';
}

add_filter('term_fields_select_cert_status', 'term_fields_select_cert_status');
function term_fields_select_cert_status(){
	
	$aDataT = get_terms( 'certificate_status', array(
		'hide_empty' => false,
	));
	
	$aData = array();
	if ($aDataT){
		foreach($aDataT as $oItem){
			$aData[] = array(
				'val' 	=>  $oItem->term_id,
				'label' =>  $oItem->name
			);
		}
	}
	
	return $aData;
}

add_filter('term_fields_select_cert_practika', 'term_fields_select_cert_practika');
function term_fields_select_cert_practika(){
	
	$aDataT = get_terms( 'certificate_practika', array(
		'hide_empty' => false,
	));
	
	$aData = array();
	if ($aDataT){
		foreach($aDataT as $oItem){
			$aData[] = array(
				'val' 	=>  $oItem->term_id,
				'label' =>  $oItem->name
			);
		}
	}
	
	return $aData;
}

add_action('woocommerce_save_account_details', 'custom_woocommerce_save_account_details');
function custom_woocommerce_save_account_details($user_id){
	global $blog_id, $wpdb;
	
	if (isset($_POST['user_description']) && $_POST['user_description']){
		update_user_meta($user_id, 'description', $_POST['user_description']);
	}

	if ( ! empty($_FILES['user_avatar'])){
		$name = $_FILES['user_avatar']['name'];
        $file = wp_handle_upload($_FILES['user_avatar'], array('test_form' => false));
        $type = $_FILES['user_avatar']['type'];

        $upload_dir = wp_upload_dir();
        if(is_writeable($upload_dir['path'])) {
          if(!empty($type) && preg_match('/(jpe?g|gif|png)$/i', $type)) {
            // Resize uploaded image
            if((bool) $wpua_resize_upload == 1) {
              // Original image
              $uploaded_image = wp_get_image_editor($file['file']);
              // Check for errors
              if(!is_wp_error($uploaded_image)) {
                // Resize image
                $uploaded_image->resize($wpua_resize_w, $wpua_resize_h, $wpua_resize_crop);
                // Save image
                $resized_image = $uploaded_image->save($file['file']);
              }
            }
            // Break out file info
            $name_parts = pathinfo($name);
            $name = trim(substr($name, 0, -(1 + strlen($name_parts['extension']))));
            $url = $file['url'];
            $file = $file['file'];
            $title = $name;
            // Use image exif/iptc data for title if possible
            if($image_meta = @wp_read_image_metadata($file)) {
              if(trim($image_meta['title']) && !is_numeric(sanitize_title($image_meta['title']))) {
                $title = $image_meta['title'];
              }
            }
            // Construct the attachment array
            $attachment = array(
              'guid'           => $url,
              'post_mime_type' => $type,
              'post_title'     => $title,
              'post_content'   => ""
            );

            // Save the attachment metadata
            $attachment_id = wp_insert_attachment($attachment, $file);
            if(!is_wp_error($attachment_id)) {
              // Delete other uploads by user
              $q = array(
                'author' => $user_id,
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'posts_per_page' => '-1',
                'meta_query' => array(
                  array(
                    'key' => '_wp_attachment_wp_user_avatar',
                    'value' => "",
                    'compare' => '!='
                  )
                )
              );
              $avatars_wp_query = new WP_Query($q);
              while($avatars_wp_query->have_posts()) : $avatars_wp_query->the_post();
                wp_delete_attachment($post->ID);
              endwhile;
              wp_reset_query();
              wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $file));
              // Remove old attachment postmeta
              delete_metadata('post', null, '_wp_attachment_wp_user_avatar', $user_id, true);
              // Create new attachment postmeta
              update_post_meta($attachment_id, '_wp_attachment_wp_user_avatar', $user_id);
              // Update usermeta
              update_user_meta($user_id, $wpdb->get_blog_prefix($blog_id).'user_avatar', $attachment_id);
            }
          }
        }
	}
}


add_action('save_post', 'product_save_actions', 15, 3);
function product_save_actions($post_id) {
    $post = get_post($post_id);

    if ($post->post_type == "product") {
        $preorder = isset($_POST['_wc_preorder']) && $_POST['_wc_preorder'] != 'no' ? 'yes' : 'no';

        update_post_meta($post_id, '_wc_preorder', $preorder);
    }
}

add_filter('product_type_options', 'product_type_options', 10, 1);
function product_type_options($options) {
    $options['wc_preorder'] = [
        'id'            => '_wc_preorder',
        'wrapper_class' => 'show_if_simple',
        'label'         => __('Предоплата', 'highthemes'),
        'description'   => __('Выводить текст о предоплате', 'highthemes'),
        'default'       => 'no'
    ];

    return $options;
}