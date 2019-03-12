<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

add_action('init', 'myStartSession', 1);
function myStartSession() {
    if (isset($_POST['session_id']) && $_POST['session_id']){
		session_id($_POST['session_id']);
	}
	
	if( ! session_id()) {
        session_start();
    }
}

/*@ini_set('upload_max_size', '128M');
@ini_set('post_max_size', '128M');
@ini_set('max_execution_time', '60');*/
/**
 * Theme: tanhit
 */


if(current_user_can('administrator') && isset($_REQUEST['login_to_user']) && $_REQUEST['login_to_user']){
	$user_id = $_REQUEST['login_to_user'];
	
	$user = get_user_by( 'id', $user_id ); 
	if( $user ) {
		wp_set_current_user( $user_id, $user->user_login );
		wp_set_auth_cookie( $user_id );
		do_action( 'wp_login', $user->user_login );
	}
}

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

	/**
	 * Add various functions and filters
	 */
	require_once('includes/tanhit-functions.php');
	
    if (defined('DOING_AJAX') && DOING_AJAX) {
        /** do nothing */
    } else {

        /**
         * Add page 'архив-вебинаров-и-практик' specific code
         */
        require_once('includes/tanhit-archive-webinars-practice.php');
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
    
    //custom css
    wp_enqueue_style('custom', get_template_directory_uri() . '/css/custom.css', array(), filemtime(get_template_directory() . '/css/custom.css'));
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
        2,
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


add_action('woocommerce_before_cart', 'tanhit_cart_warning', 50);
function tanhit_cart_warning() {
    ?>
	<div class="cart-warning" style='font-weight:normal;'>
		<strong>Внимание. Покупка он-лайн на территории Украины не доступна</strong>, так как все денежные он-лайн переводы из Украины в Россию блокируются.
		<br /><br />
		Вы можете оплатить выбранный товар (семинар) через любой местный банк.
		<br />
		Запросить реквизиты для оплаты можно через e-mail <a href='mailto:info@tanhit.com'>info@tanhit.com</a>.
		<br /><br />
		Приносим свои извинения за доставленные неудобства
	</div>
	<?php
}


add_action( 'woocommerce_cart_collaterals', 'custom_cart_text', 9 );
function custom_cart_text(){?>
  
	<div class='cart-total-text'>
		Доступ ко всем приобретенным видеоматериалам и записям вебинаров осуществляется через Личный кабинет только он-лайн. Ссылки на скачивание не предоставляются.
	</div>
	
	<?php
}



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
		
		register_sidebar([
            'name'          => 'Футер код',
            'id'            => 'footer',
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

function isOrderVirtual($order){
	
	//$result = TRUE;
	
	if (is_numeric($order)){
		$order = new WC_Order($order);
    }

	//Товары в заказе
	$order_items = $order->get_items();
	foreach ($order_items as $item_id => $item_data) {
	
		$oProduct = new WC_Product($item_data['product_id']);//$order->get_product_from_item($item_data);

		if ( $oProduct){
			if ( ! $oProduct->is_virtual()) {
				return FALSE;
				break;
			}
		}
	}  
	
	return true;
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
		
        if (trim($aData['path'], '/') == 'current-webinar' && $_REQUEST['id'] && getWebinarId($_REQUEST['id'])) {
            exit(wp_redirect(get_post_permalink($_REQUEST['id'])));
        } 
		else if (trim($aData['path'], '/') == 'my-account') {
			$product_id = isset($_REQUEST['pid']) ? (int)$_REQUEST['pid'] : null;
			$order_id = isset($_REQUEST['oid']) ? (int)$_REQUEST['oid'] : null;
			
			if ($product_id || $order_id){
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
        }elseif('/' == $aData['path']){
			$pid_to_cart = isset($_REQUEST['p2c']) ? (int)$_REQUEST['p2c'] : null;
			
			if ($pid_to_cart){
				global $woocommerce;
				
				$woocommerce->cart->empty_cart();
				$woocommerce->cart->add_to_cart( $pid_to_cart );
				
				exit(wp_redirect( get_permalink( get_option( 'woocommerce_cart_page_id') ) ));
			}
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
        foreach ($downloads as $key => $download){
            $download_expiry = isset($download['access_expires']) ? " (Доступно до: ".date('d.m.Y', strtotime($download['access_expires'])).")" : '';
        ?>

          <li data-product="<?php echo $product->id; ?>">
            <span class="item-preview"style="display: inline-block; overflow: hidden">
				<?if(trim($download['img'])){?>
					<img src="<?=httpToHttps($download['img'])?>" />
				<?}else{?>
					<?php echo httpToHttps($product->get_image()); ?>
				<?}?>
			</span>
            <a href="<?php echo httpToHttps(get_the_permalink($product->id)); ?>"
               class="item-link vid-link" target="_blank"><?php echo $product->post->post_title; ?></a>
            <span class="file-name"><?php echo $download['name'] . $download_expiry; ?></span>
            
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
            
                    <div class="show-video btn-show" data-src="<?=getVideoFromLink(httpToHttps($link));?>" data-type="<?=getTypeFromVideoLink($link)?>">
                        <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
                    </div>

                    <?/*<div style="display:none;" class="show_vid" id="vid<?php echo $product->id . "-" . $key; ?>" data-src="<?= $link; ?>">
                        <div class="vid_player vid_player2">2
                            <?= (getPlayerForm($link)) ?>
                        </div>
                    </div>*/?>
                <?}else{?>
                    <a href="<?php echo home_url() . '/?tanhit_download=true&product=' . $product->id . '&key=' . $key; ?>"
                        class="btn-download"><?php pll_e('Скачать', 'tanhit'); ?>
                     </a>
                <?}?>
              <?}elseif ($youtube){?>
                <div class="show-video btn-show" data-src="<?=getVideoFromLink(httpToHttps($youtube));?>" data-type="<?=getTypeFromVideoLink($youtube)?>">
                    <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
                </div>

                <?/*<div style="display:none;" class="show_vid" id="vid<?php echo $product->id . "-" . $key; ?>"
                     data-src="<?= $youtube; ?>">
                  <div class="vid_player vid_player2">
                      <?= (getPlayerForm($youtube)) ?>
                  </div>
                </div>*/?>
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

    $download_expiry = isset($download['access_expires']) ? " (Доступно до: ".date('d.m.Y', strtotime($download['access_expires'])).")" : '';
    
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
	
	$img_url = trim($download['file']['img']);
    ?>

    <span class="item-preview" style="">
		<?if($img_url){?>
			<img src="<?=httpToHttps($img_url)?>" />
		<?}else{?>
			<?php echo httpToHttps(tanhit_get_product_thumbnail($download['product_id'])); ?>
		<?}?>
	</span>

    <a href="<?php echo httpToHttps($product[$download['product_id']]['permalink']); ?>" class="item-link vid-link" target="_blank">
        <?php echo $product[$download['product_id']]['product_name']; ?>
    </a>

    <span class="file-name"><?php /* pll_e( 'Файл:', 'tanhit' );*/echo $download['file']['name'] . $download_expiry; ?></span>

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
          <div class="show-video btn-show" data-src="<?=getVideoFromLink(httpToHttps($link));?>" data-type="<?=getTypeFromVideoLink($link)?>">
              <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
          </div>


          <?/*<div style="display:none;" class="show_vid" id="vid<?php echo md5($download['file']['file']); ?>" data-src="<?= $link; ?>">
            <div class="vid_player vid_player2">
                <?=(getPlayerForm($link))?>
            </div>
          </div>*/?>
        <?}else{?>
            <a href="<?php echo esc_url($download['download_url']); ?>"
                class="btn-download"><?php pll_e('Скачать', 'tanhit'); ?>
            </a>
        <?}
    }elseif ($youtube) {?>
        <div class="show-video btn-show" data-src="<?=getVideoFromLink(httpToHttps($youtube));?>" data-type="<?=getTypeFromVideoLink($youtube)?>">
            <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
        </div>

        <?/*<div style="display:none;" class="show_vid" id="vid<?php echo md5($download['file']['file']); ?>" data-src="<?= $youtube; ?>">
            <div class="vid_player vid_player2">
                <?=(getPlayerForm($youtube))?>
            </div>
        </div>*/?>
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

function getTypeFromVideoLink($file){
	$videoUrl = parse_url($file);
	
	$type = 0;
	if (strpos($videoUrl['host'], 'youtu') !== false) {
		$type = 1;
	}
	
	return$type;
}

function getVideoFromLink($file){
	$videoUrl = parse_url($file);
	
	$link = $file;
	if (strpos($videoUrl['host'], 'youtu') !== false) {
        $pattern = '#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=‌​(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#';
        preg_match($pattern, $file, $matches);
        if (isset($matches[0])) {
            $youtubeId = $matches[0];
        } else {
            parse_str(parse_url($file, PHP_URL_QUERY), $params);
            $youtubeId = isset($params['v']) ? $params['v'] : (isset($params['amp;v']) ? $params['amp;v'] : '0');
        }

        $link = $youtubeId;
    }
	
	return $link;
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

        $link = httpToHttps('http://www.youtube.com/watch?v=' . $youtubeId);
        $data = 'data-setup=\'{"techOrder": ["youtube"], "sources": [{ "type": "video/youtube", "src": "' . $link . '"}], "youtube": { "controls": 0 }}\'';
    } else {
        $link = $file;
        $source_html = '<source src="' . $link . '" type="video/mp4"></source>';
    }

    $videoId = 'vid_id_' . substr(md5($link . rand(0, 1000)), 0, 20);
    //$script = '<script>wpmVideo.initYT("%s",%s,"%s",%d);</script>';

    $video = '<video id="' . $videoId . '" class="video-js vjs-default-skin" controls preload="auto" ' . $data . ' width="490" height="275">' . $source_html . '  
      <p class="vjs-no-js">
        To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="https://videojs.com/html5-video-support/" target="_blank"> supports HTML5 video</a>
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

function getPageProtectShow($post_id){
	global $wpdb;
	
	$iShowPage = TRUE;
	
	if ( ! current_user_can('administrator')){
		$post_access_protect = get_field( "access_protect", $post_id );
		if ($post_access_protect){
			$iShowPage = FALSE;
			
			//Если страница доступна когда куплены товары
			$aProductIDs = get_field( "product_ids", $post_id );
			if ($aProductIDs){
				//Проверяем если заказы с товарами привязанными к странице
				$sQuery = "SELECT COUNT(P.ID) as cnt 
				FROM `{$wpdb->prefix}posts` P 
				INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.meta_key = '_customer_user' && PM.meta_value = '".get_current_user_id()."' && PM.post_id = P.ID) 
				INNER JOIN {$wpdb->prefix}woocommerce_order_items WOI ON (WOI.order_id = P.ID) 
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta WOM ON (WOM.order_item_id = WOI.order_item_id && WOM.meta_key = '_product_id') 
				WHERE P.post_type = 'shop_order' && P.post_status = 'wc-completed' && WOM.meta_value IN (".implode(',', $aProductIDs).")";
				if($wpdb->get_var( $sQuery)){
					$iShowPage = TRUE;
				}
			}
			
			if ( ! $iShowPage){
				//Если страница доступна когда имеются сертификаты
				$aSertificateIDs = get_field( "sertificate_ids", $post_id );
				if ($aSertificateIDs){
					//Проверяем если сертификаты у пользователя которые привязанные к странице
					$sQuery = "SELECT COUNT(P.ID) as cnt 
					FROM `{$wpdb->prefix}posts` P 
					INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.meta_key = 'cert_user' && PM.meta_value = '".get_current_user_id()."' && PM.post_id = P.ID) 
					INNER JOIN {$wpdb->prefix}term_relationships TR ON (TR.object_id = P.ID) 
					WHERE P.post_type = 'certificates' && P.post_status = 'publish' && TR.term_taxonomy_id IN (".implode(',', $aSertificateIDs).")";
					if($wpdb->get_var( $sQuery)){
						$iShowPage = TRUE;
					}
				}
			}
		}
	}
	
	return $iShowPage;
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
	
	//Сертификат выводим только если я его владелец либо он в состоянии custom_hidden = "Показывать"
	$oData = $wpdb->get_row( "
		SELECT P.*, TRIM(CONCAT(UM.meta_value,' ',UM2.meta_value)) as cert_user_name, PM2.meta_value as cert_location, PM3.meta_value as cert_date 
		FROM {$wpdb->prefix}posts P 
		INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user')
		INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value)
		INNER JOIN {$wpdb->prefix}usermeta UM ON (UM.user_id = U.ID && UM.meta_key = 'first_name')
		INNER JOIN {$wpdb->prefix}usermeta UM2 ON (UM2.user_id = U.ID && UM2.meta_key = 'last_name')
		INNER JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'cert_location') 
		LEFT JOIN {$wpdb->prefix}postmeta PM3 ON (PM3.post_id = P.ID && PM3.meta_key = 'cert_date') 
		LEFT JOIN {$wpdb->prefix}postmeta PM5 ON (PM5.post_id = P.ID && PM5.meta_key = 'custom_hidden') 
		WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' && P.ID = '{$post_id}' && (U.ID = '".get_current_user_id()."' || (PM5.meta_value IS NULL || PM5.meta_value = ''))
		LIMIT 1" 
	);
	
	if ($oData){
		$iCertificateNum = str_pad($oData->ID, 10, 0, STR_PAD_LEFT);
		
		$term_list = wp_get_post_terms($oData->ID, 'certificate_type', array("fields" => "all"));
		if ($term_list){
			$term = array_shift($term_list);
			
			$sHtmlContent = '';
			
			$tpl_img  = wp_get_terms_meta($term->term_id, 'tpl', true);
			$tpl_img .= '?'.time();
		
			if ($tpl_img){
				SWITCH($term->slug){
					case 'c1':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:390px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:868px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:950px;right:160px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c2':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:400px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:735px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:898px;right:150px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
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
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:470px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:770px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:900px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c6':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:460px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:735px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:900px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c7':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:423px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:810px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:935px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c8':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:456px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:780px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:926px;left:185px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c9':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:423px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:790px;right:180px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:936px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c10':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:468px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:770px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:900px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
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
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:465px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:735px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:900px;left:190px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c15':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:482px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:675px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:876px;right:155px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c16':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:515px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:750px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:875px;right:150px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c17':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:482px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:670px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:910px;right:155px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c18':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:470px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:740px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:875px;right:155px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c19':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:597px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:918px;right:150px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:993px;left:210px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c20':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:657px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:884px;right:155px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:992px;left:210px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c21':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:682px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:877px;right:155px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:971px;left:192px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c22':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:635px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:927px;right:155px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:985px;left:170px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c23':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:480px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:805px;right:190px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:642px;left:187px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c24':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:447px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:810px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:600px;left:187px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c25':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:535px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:730px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:865px;right:150px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c26':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:520px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:738px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:867px;right:150px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c27':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:573px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:805px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:845px;left:315px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c28':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:465px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:805px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:955px;left:180px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c29':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:470px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:820px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:975px;left:180px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c30':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:505px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:770px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:950px;left:260px;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c31':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:500px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:880px;right:160px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:1017px;width:100%;text-align:center;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c32':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:440px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:780px;width:100%;text-align:center;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:900px;left:190px;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c33':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:423px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:790px;right:180px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:938px;left:190px;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c34':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:465px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:860px;right:160px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:945px;left:290px;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
					break;
					
					case 'c35':
						//настроен
						$sHtmlContent = "<html><head></head><body style='position:relative;'><div style='position:absolute;top:425px;width:100%;text-align:center;font-size:24px;'>{$oData->cert_user_name}</div><div style='position:absolute;top:790px;right:190px;font-size:24px;'>{$iCertificateNum}</div><div style='position:absolute;top:697px;left:185px;font-size:18px;'>".date('d.m.Y', strtotime($oData->cert_date))."</div><img src='".$tpl_img."' /></body></html>";
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
		$iCertificateNum = $el->get('certificates');
		$post_id = (int)$iCertificateNum;
		
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
		add_filter('the_title','certificates_custom_title_in_list', 100, 2);
	}
	else
	if ('customusermap' == $_REQUEST['post_type']){
		add_filter('the_title','customusermap_custom_title_in_list', 100, 2);
	}
}

function certificates_custom_title_in_list( $title, $id ) {
    return 'Сертификат №  '.str_pad($id, 10, 0, STR_PAD_LEFT);
}

function customusermap_custom_title_in_list( $title, $id ) {

	$cert_user 		= get_field('cert_user', $id);
	
    return trim("{$cert_user['user_lastname']} {$cert_user['user_firstname']}");
}

add_filter( 'manage_edit-customusermap_columns', 'custom_customusermap_column', 11);
function custom_customusermap_column($columns){	
	unset($columns['profile']);

	$aResult = array();
	foreach($columns as $sKey => $sValue){
		$aResult[$sKey] = $sValue;
		if ($sKey == 'title'){
			$aResult['custom_user_location'] = 'Местоположение';
		} 
	}

	return $aResult;
}

add_action( 'manage_customusermap_posts_custom_column' , 'custom_customusermap_list_column_content', 10, 2 );
function custom_customusermap_list_column_content( $column, $post_id ){	
    switch ( $column )
    {
        case 'custom_user_location' :
			$cert_location 	= get_field('cert_location', $post_id);
			
			echo $cert_location['address'];
			
            break;
    }
}

//Фильтр в сертификатах по пользователю
add_action( 'restrict_manage_posts', 'certificates_manage_posts');
function certificates_manage_posts($post_type) {
	
	if ( 'certificates' == $post_type ) {
		certificates_filters();
	}
}

function certificates_filters(){
	global $wpdb;
	
	if (isset($_REQUEST['export_to_excel'])){
		
		$aInnerTable = array();
		$aWhere = array();
	
		$aInnerTable[] = "INNER JOIN {$wpdb->prefix}postmeta PM6 ON (PM6.post_id = P.ID && PM6.meta_key = 'cert_date')";
	
		if (isset($_GET['m']) && $_GET['m']){
			$sYear = substr($_GET['m'], 0, 4);
			$sMonth= substr($_GET['m'], 4, 2);
			
			$aWhere[] = "YEAR(P.post_date) = '{$sYear}'";
			$aWhere[] = "MONTH(P.post_date) = '{$sMonth}'";
		}
	
		if (isset($_GET['_customer_user']) && $_GET['_customer_user']){
			$aWhere[] = "U.ID = '{$_GET['_customer_user']}'";
		}
		
		if (isset($_GET['_custom_sertificate_practika']) && $_GET['_custom_sertificate_practika']){
			$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_PRACTIKA ON (UM_PRACTIKA.terms_id = TR.term_taxonomy_id && UM_PRACTIKA.meta_key = 'cert_practika')";
			$aWhere[] = "UM_PRACTIKA.meta_value = '{$_GET['_custom_sertificate_practika']}'";
		}

		if (isset($_GET['_custom_sertificate_status']) && $_GET['_custom_sertificate_status']){		
			$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_STATUS ON (UM_STATUS.terms_id = TR.term_taxonomy_id && UM_STATUS.meta_key = 'cert_status')";
			$aWhere[] = "UM_STATUS.meta_value  = '{$_GET['_custom_sertificate_status']}'";
		}
		
		if ( isset( $_GET['_custom_hidden']) && $_GET['_custom_hidden']) {
			
			$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}postmeta PM5 ON (PM5.post_id = P.ID && PM5.meta_key = 'custom_hidden')";
				
			if ($_GET['_custom_hidden'] == 1){
				//Скрыт
				$aWhere[] = "(PM5.meta_value IS NOT NULL && PM5.meta_value != '')";
			}else{
				//Показан
				$aWhere[] = "(PM5.meta_value IS NULL || PM5.meta_value = '')";
			}
		}
		
		if ( isset( $_GET['_custom_overdue']) && $_GET['_custom_overdue']) {
				
			if ($_GET['_custom_overdue'] == 1){
				//Просрочен 
				$aWhere[] = "(DATE_ADD(PM6.meta_value, INTERVAL 1 YEAR) < NOW())";
			}else{
				//Не просрочен
				$aWhere[] = "(DATE_ADD(PM6.meta_value, INTERVAL 1 YEAR) >= NOW())";
			}
		}
		
		$sQuery = "
			SELECT P.*, U.id as user_id, PM6.meta_value as cert_date 
			FROM {$wpdb->prefix}posts P 
			INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user')
			INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value)
			INNER JOIN {$wpdb->prefix}term_relationships TR ON (TR.object_id = P.ID)
			".($aInnerTable ? implode(' ',$aInnerTable) : '')."
			WHERE P.post_type = 'certificates' && P.`post_status` = 'publish'".($aWhere ? ' && ('.implode(' && ', $aWhere).')' : '')."  
			GROUP BY P.ID 
			ORDER BY PM6.meta_value DESC";

		$aDataT = $wpdb->get_results( $sQuery );
		
		$aDataExport = array();
		if ($aDataT){
			foreach($aDataT as $oItem){
				$aDataExport[$oItem->user_id][] = (array)$oItem;
			}
			
			if($aDataExport){
				ksort($aDataExport);
			}
		}
		
		ob_clean();
		
		$aExcelHeaders = array('ФИО','Email','Контакты','Номер сертификата','Дата сертификата');
		
		// Create new Spreadsheet object
		$spreadsheet = new Spreadsheet();
		$activesheet = $spreadsheet->setActiveSheetIndex(0);
		
		$activesheet->getColumnDimension('A')->setAutoSize(true);
		$activesheet->getColumnDimension('B')->setAutoSize(true);
		$activesheet->getColumnDimension('C')->setAutoSize(true);
		$activesheet->getColumnDimension('D')->setAutoSize(true);
		$activesheet->getColumnDimension('E')->setAutoSize(true);
		
		$iRow = 1;
	
		$activesheet->getRowDimension(1)->setRowHeight(30);
	
		// Add some data
		foreach($aExcelHeaders as $iCol => $sVal){
			$activesheet->setCellValueByColumnAndRow($iCol+1, $iRow, $sVal);
		}
		
		$activesheet->getStyle('A1:E1')->getAlignment()->setVertical('center');
		$activesheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');
		
		$styleArray = [
			'allBorders' => [
				'borderStyle' => 'thick',
				'color' => ['argb' => 'FF000000'],
			],
		];

		$activesheet->getStyle('A1:E1')->getBorders()->applyFromArray($styleArray);
		
		if ($aDataExport){
			foreach($aDataExport as $iUserid => $aItems){
				
				$oUserInfo = get_userdata($iUserid);

				$aUserExtra = get_user_meta($iUserid, 'user_extra', true);
				$aUserExtra = array_map('trim', $aUserExtra);
				
				$aContactData = array();
				if ($sVal = trim($aUserExtra['email'])){
					$aContactData[] = 'E-mail: ' . $sVal;
				}
				if ($sVal = trim($aUserExtra['phone'])){
					$aContactData[] = 'Телефон: ' . $sVal;
				}
				if ($sVal = trim($aUserExtra['site'])){
					$aContactData[] = 'Сайт: ' . $sVal;
				}
				
				$iRowStart = $iRowEnd = 0;
				foreach($aItems as $iKey => $aItem){
					++$iRow;
					
					if (0 == $iKey){
						$iRowStart = $iRow;
						
						$activesheet->setCellValueByColumnAndRow(1, $iRow, trim($oUserInfo->first_name.' '.$oUserInfo->last_name));
						$activesheet->setCellValueByColumnAndRow(2, $iRow, $oUserInfo->data->user_email);
						
						$activesheet->setCellValueByColumnAndRow(3, $iRow, implode("\n", $aContactData));
					}
					
					$activesheet->setCellValueByColumnAndRow(4, $iRow, str_pad($aItem['ID'], 10, 0, STR_PAD_LEFT));
					$activesheet->setCellValueByColumnAndRow(5, $iRow, date('d.m.Y', strtotime($aItem['cert_date'])));
					
					$iRowEnd = $iRow;
				}
				
				if ($iRowStart){
					$activesheet->getStyle("C{$iRowStart}")->getAlignment()->setWrapText(true);
					//$activesheet->getStyle("A{$iRowStart}:С{$iRowEnd}")->getAlignment()->setVertical('top');
					//$activesheet->getStyle("D{$iRowStart}:E{$iRowEnd}")->getAlignment()->setHorizontal('center');
					
					if ($iRowStart != $iRowEnd){
						$activesheet->mergeCells("A{$iRowStart}:A{$iRowEnd}");
						$activesheet->mergeCells("B{$iRowStart}:B{$iRowEnd}");
						$activesheet->mergeCells("C{$iRowStart}:C{$iRowEnd}");
					}
				}
			}
		}
		
		// Redirect output to a client’s web browser (Xls)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Список сертификатов_'.date('dmYHis').'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		$writer = IOFactory::createWriter($spreadsheet, 'Xls');
		$writer->save('php://output');
		exit;		
	}
	
	//Скрыть все просроченные
	if ((isset($_GET['hide_overdue']) && $_GET['hide_overdue']) || (isset($_GET['hide_selected_overdue']) && $_GET['hide_selected_overdue'])){
		
		$sQuery = "
		SELECT P.ID   
		FROM {$wpdb->prefix}posts P 
		INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_date') 
		LEFT JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'custom_hidden') 
		WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' && DATE_ADD(PM.meta_value, INTERVAL 1 YEAR) < NOW() && (PM2.meta_value IS NULL || PM2.meta_value = '')" . (isset($_REQUEST['post']) && $_REQUEST['post'] ? " && P.ID IN (".implode(',', $_REQUEST['post']).")" : '');
		
		$aResultsData = $wpdb->get_results( $sQuery );
		
		if ($aResultsData){
			foreach($aResultsData as $oItem){
				update_post_meta( $oItem->ID, 'custom_hidden', array(1));
			}
		}
		
		header('Location: /wp-admin/edit.php?post_type=certificates&overdue=1');
		exit;
	}
	
	if (isset($_GET['overdue']) && $_GET['overdue']){
		submit_button( 'Скрыть выделенное', 'apply', 'hide_selected_overdue', false, 'style="margin-right:5px;"' );
		submit_button( 'Скрыть все', 'apply', 'hide_overdue', false );
		
		?>
		<style>
		#filter-by-date,#post-query-submit{display:none;}
		</style>
		<?
	}else{
		$user_string = '';
		$user_id     = '';
		if ( ! empty( $_GET['_customer_user'] ) ) {
			$user_id     = absint( $_GET['_customer_user'] );
			$user        = get_user_by( 'id', $user_id );
			$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
		}
		
		$aSertificatePractika = get_terms(array(
			'taxonomy' 		=> 'certificate_practika',
			'orderby'       => 'name', 
			'order'         => 'ASC',
			'hide_empty' 	=> false,
		));

		$aSertificateStatus = get_terms(array(
			'taxonomy' 		=> 'certificate_status',
			'orderby'       => 'name', 
			'order'         => 'ASC',
			'hide_empty' 	=> false,
		));
		
		$aActiveData = array(
			1 => 'Скрыт',
			2 => 'Показан',
		);
		
		$aOverdueData = array(
			1 => 'Просрочен',
			2 => 'Активен',
		);
		?>
		
		<input type="hidden" class="wc-customer-search" name="_customer_user" data-placeholder="Пользователь" data-selected="<?php echo htmlspecialchars( $user_string ); ?>" value="<?php echo $user_id; ?>" data-allow_clear="true" />
		
		<select name="_custom_sertificate_practika" id="filter-by-certificate-practika">
			<option value="0">Все практики</option>
			<?if($aSertificatePractika){
				foreach($aSertificatePractika as $oItem){
					echo "<option value='{$oItem->term_id}'".(isset($_GET['_custom_sertificate_practika']) && $oItem->term_id == $_GET['_custom_sertificate_practika'] ? ' selected="selected"' : '').">{$oItem->name}</option>";
				}
			}?>
		</select>
		
		<select name="_custom_sertificate_status" id="filter-by-certificate-status">
			<option value="0">Все статусы</option>
			<?if($aSertificateStatus){
				foreach($aSertificateStatus as $oItem){
					echo "<option value='{$oItem->term_id}'".(isset($_GET['_custom_sertificate_status']) && $oItem->term_id == $_GET['_custom_sertificate_status'] ? ' selected="selected"' : '').">{$oItem->name}</option>";
				}
			}?>
		</select>
		
		<select name="_custom_overdue" id="filter-by-certificate-status">
			<option value="0">Срок действия</option>
			<?if($aOverdueData){
				foreach($aOverdueData as $iKey => $sItem){
					echo "<option value='{$iKey}'".(isset($_GET['_custom_overdue']) && $iKey == $_GET['_custom_overdue'] ? ' selected="selected"' : '').">{$sItem}</option>";
				}
			}?>
		</select>
		
		<select name="_custom_hidden" id="filter-by-certificate-status">
			<option value="0">Состояние</option>
			<?if($aActiveData){
				foreach($aActiveData as $iKey => $sItem){
					echo "<option value='{$iKey}'".(isset($_GET['_custom_hidden']) && $iKey == $_GET['_custom_hidden'] ? ' selected="selected"' : '').">{$sItem}</option>";
				}
			}?>
		</select>		

	<?php
	
		submit_button( 'В Excel', 'apply', 'export_to_excel', false, 'style="margin-right:5px;"' );
	}
}

add_filter('views_edit-certificates', 'views_edit_certificates');
function views_edit_certificates($views){
	
	global $wpdb;
	
	$sQuery = "
	SELECT COUNT(P.ID) as cnt  
	FROM {$wpdb->prefix}posts P 
	INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_date') 
	LEFT JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'custom_hidden') 
	WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' && DATE_ADD(PM.meta_value, INTERVAL 1 YEAR) < NOW() && (PM2.meta_value IS NULL || PM2.meta_value = '')";

	$oRow = $wpdb->get_row($sQuery);
	
	$views['overdue'] = '<a href="edit.php?post_type=certificates&overdue=1">Отображаемые просроченные<span class="count">('.$oRow->cnt.')</span></a>';
	
	return $views;
}

//Применение фильтра, изменение запроса
add_filter( 'request', 'certificates_request_query' );
function certificates_request_query( $query ) {
	global $wpdb, $wp_post_statuses;

	if ( 'certificates' == $query['post_type'] ) {
		
		if (isset($_GET['overdue']) && $_GET['overdue']){
			//Показать все просроченные		
			$sQuery = "
				SELECT P.ID  
				FROM {$wpdb->prefix}posts P 
				INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_date') 
				LEFT JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'custom_hidden') 
				WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' && DATE_ADD(PM.meta_value, INTERVAL 1 YEAR) < NOW() && (PM2.meta_value IS NULL || PM2.meta_value = '')";
			
			$aResultIDs = array(0);
			$aResultData = $wpdb->get_results($sQuery);
			if ($aResultData){
				foreach($aResultData as $oItem){
					$aResultIDs[] = $oItem->ID;
				}
			}
			
			$query['post__in'] = $aResultIDs;
		}else{
		
			// Filter the orders by the posted customer.
			if ( isset( $_GET['_customer_user'] ) && $_GET['_customer_user'] > 0 ) {
				$meta_query[] = 
				array(
					'key'   	=> 'cert_user',
					'value' 	=> (int) $_GET['_customer_user'],
					'compare' 	=> '='
				);
			}
			
			$_custom_sertificate_practika = isset( $_GET['_custom_sertificate_practika'] ) && $_GET['_custom_sertificate_practika'] > 0 ? $_GET['_custom_sertificate_practika'] : 0;
			$_custom_sertificate_status = isset( $_GET['_custom_sertificate_status'] ) && $_GET['_custom_sertificate_status'] > 0 ? $_GET['_custom_sertificate_status'] : 0;
			
			if ($_custom_sertificate_practika || $_custom_sertificate_status){
				$aInnerTable = array();
				$aWhere = array();
				
				if ($_custom_sertificate_practika){
					$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_PRACTIKA_F ON (UM_PRACTIKA_F.terms_id = TR.term_taxonomy_id && UM_PRACTIKA_F.meta_key = 'cert_practika')";
					$aWhere[] = "UM_PRACTIKA_F.meta_value = '{$_custom_sertificate_practika}'";
				}
				if ($_custom_sertificate_status){
					$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_STATUS_F ON (UM_STATUS_F.terms_id = TR.term_taxonomy_id && UM_STATUS_F.meta_key = 'cert_status')";
					$aWhere[] = "UM_STATUS_F.meta_value = '{$_custom_sertificate_status}'";
				}
				
				$sSql = "SELECT TR.term_taxonomy_id FROM {$wpdb->prefix}term_relationships TR ".implode(' ', $aInnerTable)." WHERE ".implode(' && ', $aWhere);
				
				$aDataTypeIDs = array(0);
				$aResultsTR = $wpdb->get_results($sSql);
				if ($aResultsTR){
					foreach ($aResultsTR as $oItem){
						$aDataTypeIDs[] = $oItem->term_taxonomy_id;
					}
				}
				
				$query['tax_query'] = array(
					array(
						'taxonomy' 	=> 'certificate_type',
						'field' 	=> 'term_id',
						'terms' 	=> $aDataTypeIDs
					)
				);
			}
			
		
			if ( (isset( $_GET['_custom_hidden']) && $_GET['_custom_hidden']) || (isset( $_GET['_custom_overdue']) && $_GET['_custom_overdue'])) {
				$aInnerTable = array();
				$aWhere = array();
				
				//Состояние
				if ( isset( $_GET['_custom_hidden']) && $_GET['_custom_hidden']) {
				
					$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}postmeta PM5 ON (PM5.post_id = P.ID && PM5.meta_key = 'custom_hidden')";
						
					if ($_GET['_custom_hidden'] == 1){
						//Скрыт
						$aWhere[] = "(PM5.meta_value IS NOT NULL && PM5.meta_value != '')";
					}else{
						//Показан
						$aWhere[] = "(PM5.meta_value IS NULL || PM5.meta_value = '')";
					}
				}
				
				//Срок действия
				if ( isset( $_GET['_custom_overdue']) && $_GET['_custom_overdue']) {
					
					$aInnerTable[] = "INNER JOIN {$wpdb->prefix}postmeta PM6 ON (PM6.post_id = P.ID && PM6.meta_key = 'cert_date')";
						
					if ($_GET['_custom_overdue'] == 1){
						//Просрочен 
						$aWhere[] = "(DATE_ADD(PM6.meta_value, INTERVAL 1 YEAR) < NOW())";
					}else{
						//Не просрочен
						$aWhere[] = "(DATE_ADD(PM6.meta_value, INTERVAL 1 YEAR) >= NOW())";
					}
				}
			
				$sQuery = "
					SELECT P.ID  
					FROM {$wpdb->prefix}posts P ".implode(' ', $aInnerTable)."
					WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' && ".implode(' && ', $aWhere);
			
				$aResultIDs = array(0);
				$aResultData = $wpdb->get_results($sQuery);
				if ($aResultData){
					foreach($aResultData as $oItem){
						$aResultIDs[] = $oItem->ID;
					}
				}
				
				$query['post__in'] = $aResultIDs;
			}
		}
		
		
		$query['meta_query'] = $meta_query;
	}
	
	return $query;
}


/* Display custom column */
// adding the data for each orders by column (example)
add_action( 'manage_certificates_posts_custom_column' , 'custom_certificates_list_column_content', 10, 2 );
function custom_certificates_list_column_content( $column )
{
    global $post;
	
	$post_id = $post->ID;
	
	
    switch ( $column ){
		
        case 'overdue' :
			$cert_date = get_post_meta( $post_id, 'cert_date', true);

			$iDateCert = strtotime('+1 years', strtotime($cert_date));
			
			if ($iDateCert < time()){
				echo "<span style='color:red;'>Просрочен</span>";
			}else{
				echo "<span style='color:green;'>До ".date('d.m.Y', $iDateCert)."</span>";
			}			
        break;
		
		case 'custom_hidden' :
			$custom_hidden = get_post_meta( $post_id, 'custom_hidden', true);
		
			if (isset($custom_hidden[0]) && $custom_hidden[0]){
				echo "<span style='color:red;'>Скрыт</span>";
			}else{
				echo "<span style='color:green;'>Показан</span>";
			}
        break;
    }
}

add_filter( 'manage_edit-certificates_columns', 'custom_certificates_column',11);
function custom_certificates_column($columns)
{
	$columns = array();
	
	$columns["cb"] 							= '<input type="checkbox" />';
	$columns["title"]						= 'Заголовок';
	$columns["taxonomy-certificate_type"]	= 'Заказ';
	$columns["profile"]						= 'Profile';
	$columns["overdue"]						= 'Срок действия';
	$columns["custom_hidden"]				= 'Состояние';
	$columns["date"]						= 'Дата';

	return $columns;
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
								'cert_user' 		=> $user->ID,
								'cert_date'			=> date('Y-m-d', $sDate),
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
	</div>
	
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


//Добавляем страницу массового добавления/исключения БК
add_action('admin_menu', 'register_users_generation_submenu_page');
function register_users_generation_submenu_page() {
    add_submenu_page('users.php', 'Массовое добавление/исключение БК', 'Массовое добавление/исключение БК', 'manage_options', 'wp_users_generation_page', 'wp_users_generation_page_callback');
}

function wp_users_generation_page_callback() {

    $sMessage = '';

    //Обновляем записи
    if (isset($_POST['submit'])) {
		$sEmails 	= trim(isset($_POST['emails']) ? $_POST['emails'] : '');
		$iActionId 	= (int)(isset($_POST['action_id']) ? $_POST['action_id'] : '');
		
		if($sEmails && $iActionId){
			$sEmails = trim($sEmails, "\r\n");
			$sEmails = trim($sEmails);
			
			$iAddTotal = 0;
			
			$aEmails = explode("\r\n", $sEmails);
			if($aEmails){
				foreach ($aEmails as $sEmail){
					$user = get_user_by( 'email', $sEmail );
					if ( ! empty( $user ) ) {
						//Добавление
						if ($iActionId==1){
							$user->add_role( 'vip' );
						}
						//Исключение
						else{
							$user->remove_role( 'vip' );
							$user->add_role( 'customer' );
						}
						
						$iAddTotal++;
					}
				}
			}
			
			$_SESSION['sMessage'] = "<div style='padding:5px;background:#bdfe82;'><strong>Обработано пользователей: {$iAddTotal}</strong></div>";
		}else{
			$_SESSION['sMessage'] = "<div style='padding:5px;background:#fe8282;'><strong>Заполнены не все поля</strong></div>";
		}
		
		header('Location: /wp-admin/users.php?page=wp_users_generation_page');
		exit;
	}
	?>

    <div class="wrap">
		<h2>Массовая добавление/исключение БК</h2>

		<?if(isset($_SESSION['sMessage'])){
			echo $_SESSION['sMessage'];
			unset($_SESSION['sMessage']);
		}?>
		
		<form action="" method="POST">
			<table class="form-table">
				<tr>
					<th><label for="emails">Список E-mail</label></th>

					<td>
						<textarea style='width:100%;height:200px;resize:vertical;' name='emails' required='true'></textarea>
						<p>каждый адрес на отдельной строке</p>
					</td>
				</tr>
				<tr>
					<th><label for="action_id">Действие</label></th>

					<td>
						<select id="action_id" style='width:100%;' name="action_id" required='true'>
							<option value='1'>Добавление в БК</option>
							<option value='2'>Исключение из БК</option>
						</select>
					</td>
				</tr>
			</table>

			<?=submit_button('Выполнить');?>
		</form>
	</div>
	<?
}

//[cert_list statuses="220" column_location_title="Место прохождения практики"]
add_shortcode( 'cert_list', 'cert_list_shortcode' );
function cert_list_shortcode( $atts ) {
	global $wpdb;
	
	$atts = shortcode_atts( array(
		'id_list'				=> '',
		'my' 					=> 0, 	//Выводить только мои сертификаты
		'user_id' 				=> 0, 	//Выводить сертификаты пользователя
		'manager' 				=> 0, 	//Сертификаты подопечных
		'full' 					=> 0,	//Краткие столбцы или подробные
		'filter'				=> 0,	//Выводить фильтр
		'sort'					=> 0,	//Выводить сортировку
		'practika'				=> '',	//Сертификаты для каких типов выводить через запятую 1,2,3
		'statuses'				=> '',	//Сертификаты для каких статусов выводить через запятую 1,2,3
		'practika_statuses' 	=> '',	//Сертификаты типов с определенными статусами 1:2,3|2:3,
		'column_location_title'	=> '',
		'practika_double'		=> '',
		'contact_no_empty'		=> '',
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
		'user_id' 			=> 0, 	//Выводить сертификаты пользователя
		'manager' 			=> 0, 	//Сертификаты подопечных
		'practika'			=> '',	//Сертификаты для каких типов выводить через запятую 1,2,3
		'statuses'			=> '',	//Сертификаты для каких статусов выводить через запятую 1,2,3
		'practika_statuses' => '',	//Сертификаты типов с определенными статусами 1:2,3|2:3
		'icon_img'			=> '',
		'practika_double'	=> '',
		'contact_no_empty'	=> '',
	), $atts, 'cert_map' );
	
	ob_start();
	include (__DIR__ . '/certificate/cert_map.php' );
	return ob_get_clean();
}

add_shortcode( 'custommap', 'custommap_shortcode' );
function custommap_shortcode( $atts ) {
	global $wpdb;
	
	$atts = shortcode_atts( array(
		'post_type'			=> 'customusermap'
	), $atts, 'custommap' );
	
	ob_start();
	include (__DIR__ . '/certificate/custommap.php' );
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
	
	//if (isset($_POST['user_extra']) && $_POST['user_extra']){
		update_user_meta($user_id, 'user_extra', $_POST['user_extra']);
	//}
	
	//if (isset($_POST['user_extra_adress1']) && $_POST['user_extra_adress1']){
		update_user_meta($user_id, 'user_extra_adress1', $_POST['user_extra_adress1']);
	//}
	//if (isset($_POST['user_extra_adress2']) && $_POST['user_extra_adress2']){
		update_user_meta($user_id, 'user_extra_adress2', $_POST['user_extra_adress2']);
	//}
	
	//if (isset($_POST['user_social']) && $_POST['user_social']){
		$aUserSocial = array_filter($_POST['user_social'], function($var){return $var!='';});
		update_user_meta($user_id, 'user_social', $aUserSocial);
	//}
	
	//Рассылка
	mailchimp_updated_user_meta($user_id, 'subscribe_all', (int)$_POST['subscribe_all']);
	mailchimp_updated_user_meta($user_id, 'subscribe_vip', (int)$_POST['subscribe_vip']);
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

//Получаем максимальный статус имеющегося сертификата у пользователя
function getUserStatus($user_id = 0){
	global $wpdb;
	
	if ( ! $user_id){
		$user_id = get_current_user_id();
	}
	
	$sQuery = "SELECT MAX(UM_STATUS.meta_value) as `status`
	FROM {$wpdb->prefix}posts P 
	INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user') 
	INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value && U.ID = '{$user_id}')  
	INNER JOIN {$wpdb->prefix}term_relationships TR ON (TR.object_id = P.ID) 
	INNER JOIN {$wpdb->prefix}termsmeta UM_STATUS ON (UM_STATUS.terms_id = TR.term_taxonomy_id && UM_STATUS.meta_key = 'cert_status') 
	WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' && (UM_STATUS.meta_value IN (220,221,222,223))";
	
	return (int)$wpdb->get_var( $sQuery );
}

add_action('init', 'do_rewrite');
function do_rewrite(){
	// Правило перезаписи
	add_rewrite_rule( 'users$', 'index.php?pagename=404', 'top' );
	add_rewrite_rule( 'users/$', 'index.php?pagename=404', 'top' );
	add_rewrite_rule( 'users/([0-9]+)$', 'index.php?pagename=users&user_id=$matches[1]', 'top' );
	add_rewrite_rule( 'users/([0-9]+)/$', 'index.php?pagename=users&user_id=$matches[1]', 'top' );

	// скажем WP, что есть новые параметры запроса
	add_filter( 'query_vars', function( $vars ){
		$vars[] = 'user_id';
		return $vars;
	} );
}

function force404(){
	status_header( 404 );
	nocache_headers();
	include( get_query_template( '404' ) );
	exit; 
}

/*
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
                    $aDataPackageExport['size_a'] += ($_height);
                    $aDataPackageExport['size_b'] += ($_width);
                    $aDataPackageExport['size_c'] += ($_length);
                    $aDataPackageExport['weight'] += ($_weight);
                    
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
                                            <DeclaredSum>'.('0').'</DeclaredSum>
                                            <DeliveryPayment>'.('0').'</DeliveryPayment>
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
					curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
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
}*/

function export_to_cdek($order) {
    
	$result = '';
	
    if (is_numeric($order)) {
        $order = new WC_Order($order);
    }

    $aDeliveryMethod = $order->get_shipping_methods();
    $aDeliveryMethod = $aDeliveryMethod ? array_shift($aDeliveryMethod) : [];
    $iDeliveryMethod = isset($aDeliveryMethod['method_id']) ? explode('_', $aDeliveryMethod['method_id']) : '';

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

                if ( ! $oProduct->is_virtual()) {
                    $sSku = $oProduct->get_sku();
                    $iCost = $item_data['line_total'] / $item_data['qty'];

                    $DeclaredSum += $item_data['line_total'];
                                     
                    $_weight = wc_get_weight(str_replace(',', '.', $oProduct->weight), 'kg');
                    if ( ! $_weight) {
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

                    if ( ! $_height) {
                        $_height = $aSettingsEdostavka['minimum_height'];
                    }
                    if ( ! $_width) {
                        $_width = $aSettingsEdostavka['minimum_width'];
                    }
                    if ( ! $_length) {
                        $_length = $aSettingsEdostavka['minimum_length'];
                    }

                    //Вес всех товаров
                    $aDataPackageExport['size_a'] += ($_height);
                    $aDataPackageExport['size_b'] += ($_width);
                    $aDataPackageExport['size_c'] += ($_length);
                    $aDataPackageExport['weight'] += ($_weight);
                    
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
            //Добавляем данные в выгрузку
            $component->setOrders([$aDataExport]);
            //Отправляем данные на сервер сдэк
           
            $response = $api_cdek->sendData($component);
            $aOrderResponse = (array)$response->Order[0];

			//Если существует ошибка и это не дубль
            if (isset($aOrderResponse["@attributes"]["ErrorCode"]) && 'ERR_ORDER_DUBL_EXISTS' != $aOrderResponse["@attributes"]["ErrorCode"]) {
                $result = $aOrderResponse["@attributes"]["Msg"];
            }
			
			/*if ( ! $result && $aOrderResponse["@attributes"]["DispatchNumber"]){
				$result = (string)$aOrderResponse["@attributes"]["DispatchNumber"];
			}*/
			
			if ( ! $result && $aOrderResponse["@attributes"]["Number"]){
				$result = (string)$aOrderResponse["@attributes"]["Number"];
			}
			
			if ( ! $result){
				$aDebug = array(
					'ответ' 		=> $aOrderResponse
				);
				
				echo '<pre>';
				die(var_dump($aDebug));
			}
        }
    }

    return $result;
}

function export_to_fullfilment($order) {
    
	$result = '';
	
    if (is_numeric($order)) {
        $order = new WC_Order($order);
    }

    $aDeliveryMethod = $order->get_shipping_methods();
    $aDeliveryMethod = $aDeliveryMethod ? array_shift($aDeliveryMethod) : [];
    $iDeliveryMethod = isset($aDeliveryMethod['method_id']) ? explode('_', $aDeliveryMethod['method_id']) : ''; //['edostavka', 137]

    //Выгрузка заказа в СДЭК
    if (isset($iDeliveryMethod[0]) && $iDeliveryMethod[0] == 'edostavka' && isset($iDeliveryMethod[1]) && $iDeliveryMethod[1]) {
        $iDeliveryMethod = $iDeliveryMethod[1];

		//Meta Order
		$aMetaDataOrder = get_post_meta($order->id);
		//Meta Delivery 
		$aDeliveryMethod = $order->get_shipping_methods();
		
		//Массив данных для передачи на выгрузку
		$aDataExport = [];

		//Номер заказа
		$aDataExport['order_id'] = "ORD{$order->id}";
		//Данные получателя
		$aDataExport['recipient_city_id'] = $aMetaDataOrder['_billing_state_id'][0];
		$aDataExport['recipient_name'] = $aMetaDataOrder['_billing_last_name'][0] . ' ' . $aMetaDataOrder['_billing_first_name'][0];
		$aDataExport['recipient_telephone'] = $aMetaDataOrder['_billing_phone'][0];
		//ID тарифа
		$aDataExport['tariff_id'] = $iDeliveryMethod;
		$aDataExport['currency'] = $aMetaDataOrder['_order_currency'][0];

		//Адрес получателя
		$aDataExport['address']['street']      = trim($aMetaDataOrder['_billing_address_1'][0]);
		$aDataExport['address']['house']       = trim($aMetaDataOrder['_billing_address_2'][0]);
		$aDataExport['address']['flat']        = trim($aMetaDataOrder['_billing_address_3'][0]);
		
		$aProductItems = array();
		//Товары в заказе
		$order_items = $order->get_items();
		foreach ($order_items as $item_id => $item_data) {
			$oProduct = $order->get_product_from_item($item_data);

			if ( ! $oProduct->is_virtual()) {
				$sSku = $oProduct->get_sku();
				$iCost = $item_data['line_total'] / $item_data['qty'];

				//Продукты для выгрузки в FullFillmment
				$aProductItems[] = array(
					'id'            => $oProduct->get_id(),
					'sku'           => $sSku ? $sSku : 'пустой',
					'name'          => $item_data['name'],
					'price'         => $iCost,
					'qty'           => $item_data['qty']
				);
			}
		}            
		
		//Если заказ выгружен в СДЭК, то выгружаем в FFillment
		if ($aProductItems) {
		  
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
										<DeclaredSum>'.('0').'</DeclaredSum>
										<DeliveryPayment>'.('0').'</DeliveryPayment>
										<SumToPay>0</SumToPay>
										<Comment></Comment>
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

			if ( ! empty($xml_post_string)) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$your_xml_response = curl_exec($ch);
			curl_close($ch);

			//Разбираем ответ
			$clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $your_xml_response);
			$xml = simplexml_load_string($clean_xml);

			//Если существует ошибка и это не дубль
			if (isset($xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->ErrorCode) && (string)$xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->ErrorCode) {
				$result = (string)$xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->ErrorCode;
			}
			
			if ( ! $result && isset($xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->OrderID) && (string)$xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->OrderID){
				$result = (string)$xml->Body->ClientOrder_RequestDeliveryResponse->ClientOrder_RequestDeliveryResult->OrderID;
			}
			
			if ( ! $result){
				$aDebug = array(
					'запрос' => array(
						'адрес'		=> 'http://lkff.cdek.ru:8080/cdekfullfillment.asmx?op=ClientOrder_RequestDelivery',
						'заголовки' => $headers,
						'тело' 		=> $xml_post_string,
					),
					'ответ' 		=> $your_xml_response,
					'ответ xml'		=> $xml
				);
				
				echo '<pre>';
				die(var_dump($aDebug));
			}
		}
    }

    return $result;
}

add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column',11);
function custom_shop_order_column($columns)
{
	$columns = array();
	
	$columns["cb"] 						= '<input type="checkbox" />';
	$columns["order_status"]			= '<span class="status_head tips" data-tip="Статус">Статус</span>';
	$columns["order_title"]				= 'Заказ';
	$columns["order_items"]				= 'Товары';
	
	//$columns["billing_address"]		= 'Биллинг';
	//$columns["shipping_address"]		= 'Ship to';
	//$columns["customer_message"]		= '<span class="notes_head tips" data-tip="Customer Message">Customer Message</span>';
	//$columns["order_notes"]			= '<span class="order-notes_head tips" data-tip="Заметки к заказу">Заметки к заказу</span>';
	
	$columns['export-to-cdek'] 			= 'Выгрузка в СДЭК';
    $columns['export-to-fullfilment'] 	= 'Выгрузка в ФуллФилмент';
	
	$columns["order_date"]				= 'Дата';
	$columns["order_total"]				= 'Итого';
	$columns["order_actions"]			= 'Действия';

	return $columns;
}

// adding the data for each orders by column (example)
add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 10, 2 );
function custom_orders_list_column_content( $column )
{
    global $post, $woocommerce, $the_order;
    $order_id = $the_order->id;

	$bVirtualOrder = isOrderVirtual($the_order);
	
	if ( ! $bVirtualOrder){
		$aDeliveryMethod = $the_order->get_shipping_methods();
		$aDeliveryMethod = $aDeliveryMethod ? array_shift($aDeliveryMethod) : [];
		$iDeliveryMethod = isset($aDeliveryMethod['method_id']) ? explode('_', $aDeliveryMethod['method_id']) : '';
	}
	
    switch ( $column )
    {
        case 'export-to-cdek' :
			//Выгрузка заказов возможна только с определенным способом доставки
			if ( ! $bVirtualOrder && isset($iDeliveryMethod[0]) && $iDeliveryMethod[0] == 'edostavka' && isset($iDeliveryMethod[1]) && $iDeliveryMethod[1]) {
				$val = wc_get_order_item_meta( $order_id, '_export_to_cdek', true );
				
				if ( ! $val){
					echo "<div style='color:red;'>".wc_get_order_item_meta( $order_id, '_export_to_cdek_error', true )."</div>";
					echo "<div><a class='button' href='".wp_nonce_url( admin_url( 'admin-ajax.php?action=export_order_to_cdek&order_id=' . $order_id ), 'export_order_to_cdek' )."'>Выгрузить в CDEK</a></div>";
				}else{
					echo "<span style='color:green;'>Успешно - {$val}</span>";
				}
			}
			
            break;
		
		case 'export-to-fullfilment' :
			//Выгрузка заказов возможна только с определенным способом доставки
			if ( ! $bVirtualOrder && isset($iDeliveryMethod[0]) && $iDeliveryMethod[0] == 'edostavka' && isset($iDeliveryMethod[1]) && $iDeliveryMethod[1]) {
				$val = wc_get_order_item_meta( $order_id, '_export_to_fullfilment', true );
				
				if ( ! $val){
					echo "<div style='color:red;'>".wc_get_order_item_meta( $order_id, '_export_to_fullfilment_error', true )."</div>";
					echo "<div><a class='button' href='".wp_nonce_url( admin_url( 'admin-ajax.php?action=export_order_to_fullfilment&order_id=' . $order_id ), 'export_order_to_fullfilment' )."'>Выгрузить в FullFilment</a></div>";
				}else{
					echo "<span style='color:green;'>Успешно - {$val}</span>";
				}
			}
			
            break;
    }
}

//Действие Выгрузка в CDEK
add_action('wp_ajax_export_order_to_cdek', 'ajax_export_order_to_cdek');
function ajax_export_order_to_cdek() {

	if ( ! is_admin() ) die;
	if ( ! current_user_can('edit_shop_orders') ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce' ) );
	if ( ! check_admin_referer('export_order_to_cdek')) wp_die( __( 'You have taken too long. Please go back and retry.', 'woocommerce' ) );

	$order_id = isset($_GET['order_id']) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : '';
	if ( ! $order_id) die;

	$result = export_to_cdek($order_id);
	
	if ("ORD{$order_id}" == $result){
		wc_update_order_item_meta( $order_id, '_export_to_cdek', $result, true );
	}else{
		wc_update_order_item_meta( $order_id, '_export_to_cdek_error', $result, true );
	}
	
	wp_safe_redirect( wp_get_referer() );
}

//Действие Выгрузка в FullFillment
add_action('wp_ajax_export_order_to_fullfilment', 'ajax_export_order_to_fullfilment');
function ajax_export_order_to_fullfilment() {

	if ( ! is_admin() ) die;
	if ( ! current_user_can('edit_shop_orders') ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce' ) );
	if ( ! check_admin_referer('export_order_to_fullfilment')) wp_die( __( 'You have taken too long. Please go back and retry.', 'woocommerce' ) );

	$order_id = isset($_GET['order_id']) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : '';
	if ( ! $order_id) die;

	$result = export_to_fullfilment($order_id);
	
	if (is_numeric($result)){
		wc_update_order_item_meta( $order_id, '_export_to_fullfilment', $result, true );
	}else{
		wc_update_order_item_meta( $order_id, '_export_to_fullfilment_error', $result, true );
	}
	
	wp_safe_redirect( wp_get_referer() );
}

/*
add_filter( "manage_edit-shop_order_sortable_columns", 'MY_COLUMNS_SORT_FUNCTION' );
function MY_COLUMNS_SORT_FUNCTION( $columns ) {
    $custom = array(
        //start editing

        'export-to-cdek'    	=> 'MY_COLUMN_1_POST_META_ID',
        'export-to-fullfilment' => 'MY_COLUMN_2_POST_META_ID'

        //stop editing
    );
    return wp_parse_args( $custom, $columns );
}*/


//Подписка при регистрации пользователя
add_action('user_register', 'mailchimp_user_register');
function mailchimp_user_register($user_id){
	
	//Подписываем на рассылку
	mailchimp_updated_user_meta($user_id, 'subscribe_all', 1);
}

//Добавление пользователя в вип
add_action('add_user_role','mailchimp_add_role',10,2);
function mailchimp_add_role($user_id, $role){
	
	//Подписываем на рассылку
	mailchimp_updated_user_meta($user_id, 'subscribe_vip', 1);
}

//Удаление пользователя из вип
add_action('remove_user_role','mailchimp_remove_role',10,2);
function mailchimp_remove_role($user_id, $role){
		
	//Отписываем от рассылки
	mailchimp_updated_user_meta($user_id, 'subscribe_vip', 0);
}

//При изменении данных
add_action( 'updated_user_meta', 'mailchimp_update_user_meta', 10, 4 );
function mailchimp_update_user_meta($meta_id, $user_id, $meta_key, $_meta_value) {
	
	if ($_meta_value && in_array($meta_key, array('first_name','last_name'))){
		//Подписываем на рассылку
		mailchimp_updated_user_meta($user_id, 'subscribe_all', 1);
	}
}

use \DrewM\MailChimp\MailChimp;	

//Изменение флага рассылки
function mailchimp_updated_user_meta($user_id, $meta_key, $_meta_value){
	
	$user = get_userdata($user_id);

	//Подключаем MailChimp
	include('includes/mailchimp-api/MailChimp.php'); 
	
	//use \DrewM\MailChimp\MailChimp;
	$MailChimp = new MailChimp('41cce6d337dd08ca80df2842f604bf6a-us14');
	
	
	SWITCH($meta_key){
		case 'subscribe_all':
			//Общий список рассылки
			$list_id = 'be2d256a25';
	
			//Подписка
			if($_meta_value){
				if ($user->first_name && $user->last_name){
					$result = $MailChimp->post("lists/{$list_id}/members", [
						'email_address' => $user->user_email,
						'status'        => 'subscribed',
						'merge_fields'  => array('FNAME' => $user->first_name,'LNAME' => $user->last_name)
					]);
				
					if ( ! $MailChimp->success()) {
						$subscriber_hash = $MailChimp->subscriberHash($user->user_email);
						$result = $MailChimp->patch("lists/{$list_id}/members/$subscriber_hash", [
							'status' 		=> 'subscribed',
							'merge_fields'  => array('FNAME' => $user->first_name,'LNAME' => $user->last_name)
						]);
					}
				}
			}
			//отписка
			else{
				$subscriber_hash = $MailChimp->subscriberHash($user->user_email);
				$result = $MailChimp->patch("lists/{$list_id}/members/$subscriber_hash", [
					'status' => 'unsubscribed'
				]);
			}
		break;
		
		case 'subscribe_vip':
			//VIP список рассылки
			$list_id = 'e11dd4d4b6';
	
			//Подписка
			if($_meta_value){
				if ($user->first_name && $user->last_name){
					$result = $MailChimp->post("lists/{$list_id}/members", [
						'email_address' => $user->user_email,
						'status'        => 'subscribed',
						'merge_fields'  => array('FNAME' => $user->first_name,'LNAME' => $user->last_name)
					]);
					
					if ( ! $MailChimp->success()) {
						$subscriber_hash = $MailChimp->subscriberHash($user->user_email);
						$result = $MailChimp->patch("lists/{$list_id}/members/$subscriber_hash", [
							'status' 		=> 'subscribed',
							'merge_fields'  => array('FNAME' => $user->first_name,'LNAME' => $user->last_name)
						]);
					}
				}
			}
			//отписка
			else{
				$subscriber_hash = $MailChimp->subscriberHash($user->user_email);
				$result = $MailChimp->patch("lists/{$list_id}/members/$subscriber_hash", [
					'status' => 'unsubscribed'
				]);
			}
		break;
	}
	
	//die(var_dump($result));
}

//Изменение email у пользователя
add_action( 'profile_update', 'mailchimp_profile_update', 10, 2 );
function mailchimp_profile_update($user_id, $old_user_data){
	
	$aListIds = array(
		//Общий список рассылки
		'be2d256a25',
		//VIP список рассылки
		'e11dd4d4b6',
	);

	$user = get_userdata($user_id);
	
	//Подключаем MailChimp
	include('includes/mailchimp-api/MailChimp.php'); 
	
	//use \DrewM\MailChimp\MailChimp;*
	$MailChimp = new MailChimp('41cce6d337dd08ca80df2842f604bf6a-us14');
	
	
	//изменяем email в списках подписки
	foreach ($aListIds as $list_id){
		$subscriber_hash = $MailChimp->subscriberHash($old_user_data->user_email);
		$result = $MailChimp->patch("lists/{$list_id}/members/$subscriber_hash", [
			'email_address' => $user->user_email
		]);
	}
}

function mailchimp_exists($user_email, $list_id){
    //Подключаем MailChimp
	include('includes/mailchimp-api/MailChimp.php'); 
	
	//use \DrewM\MailChimp\MailChimp;*
	$MailChimp = new MailChimp('41cce6d337dd08ca80df2842f604bf6a-us14');
    $subscriber_hash = $MailChimp->subscriberHash($user_email);
	
    $result = $MailChimp->get("lists/$list_id/members/$subscriber_hash");
	
    if ($result['status'] != 'subscribed') return false;
    return true;
}

//Подгрузка вкладок личного кабинета по требованию
add_action( 'wp_ajax_load_user_tab', 'load_user_tab' ); // For logged in users
add_action( 'wp_ajax_nopriv_load_user_tab', 'load_user_tab' ); // For anonymous users
function load_user_tab(){
	set_time_limit (0);
	
	$sTab = isset($_POST['tab_name']) ? $_POST['tab_name'] : '';
	
	do_action( 'woocommerce_my_account' );
	
	SWITCH($sTab){
		case 'home':
			do_action( 'woocommerce_before_my_account' );
			do_action( 'tanhit_my_account' );
			do_action( 'woocommerce_after_my_account' );
		break;
		
		case 'profile':
			wc_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) );
		break;
		
		case 'certificates':
			wc_get_template( 'myaccount/my-user-certificates.php' );
		break;
		
		case 'webinars':
			wc_get_template( 'myaccount/my-downloads.php' );
		break;
		
		case 'orders':
			wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) );
		break;
		
		case 'pins':
			do_action('display_pincodes');
		break;
		
		case 'docs':
			wc_get_template( 'myaccount/my-docs.php' );
		break;
		
		case 'manager':
			wc_get_template( 'myaccount/my-manager-certificates.php' );
		break;
	}
	
	exit;
}


/*=========================ОГРАНИЧЕНИЕ КУПОНОВ ДЛЯ ПОЛЬЗОВАТЕЛЕЙ===============================*/
add_filter( 'woocommerce_coupon_data_tabs', array('Custom_Coupons', 'admin_coupon_options_tabs'), 20, 1);
add_action( 'woocommerce_coupon_data_panels', array( 'Custom_Coupons', 'admin_coupon_options_panels' ), 10, 0 );
add_action( 'wjecf_coupon_metabox_customer', array( 'Custom_Coupons', 'admin_coupon_metabox_customer' ), 10, 2 );
add_action( 'woocommerce_process_shop_coupon_meta', array( 'Custom_Coupons', 'process_shop_coupon_meta' ), 10, 2 ); 
add_filter('woocommerce_coupon_is_valid', array( 'Custom_Coupons', 'coupon_is_valid' ), 10, 2 );

Class Custom_Coupons{
	public function admin_coupon_options_tabs( $tabs ) {
				
		$tabs['extended_features_customers'] = array(
			'label'  => __( 'Customers', 'woocommerce-jos-autocoupon' ),
			'target' => 'wjecf_coupondata_customers',
			'class'  => 'wjecf_coupondata_customers',
		);

		return $tabs;
	} 
	
	public function admin_coupon_options_panels() {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		?>
			<div id="wjecf_coupondata_customers" class="panel woocommerce_options_panel">
				<?php
					do_action( 'wjecf_coupon_metabox_customer', $thepostid, $post );
				?>
			</div>
		<?php        
	}
	
	public function admin_coupon_metabox_customer( $thepostid, $post ) {
		
		$sUsers = get_post_meta($thepostid, 'customer_ids', true);
		$aUsers = ($sUsers ? $sUsers : array());

		$aData = array();
		if ($aUsers){
			$aData = get_users( array('include' => $aUsers, 'fields' => array('user_email')) );
		}
		?>
		
		<div style='padding:10px;'>
			<h3>Customers</h3>
			<textarea style='width:100%;height:200px;resize:vertical;' name='custom_emails'><?if($aData){foreach($aData as $oItem){?><?=($oItem->user_email."\r\n")?><?}}?></textarea>
			<p>каждый e-mail на отдельной строке</p>
		</div>
		
		<?php
	}
	
	function process_shop_coupon_meta( $post_id, $post ) {
		$sEmails = isset($_POST['custom_emails']) ? $_POST['custom_emails'] : '';
		
		$aSave = array();
		if($sEmails){
			$sEmails = trim($sEmails, "\r\n");
			$sEmails = trim($sEmails);
			
			$aEmails = explode("\r\n", $sEmails);
			if($aEmails){
				foreach ($aEmails as $sEmail){
					if ($sEmail){
						$user = get_user_by( 'email', $sEmail );
						if ( ! empty( $user ) && $user->ID) {
							$aSave[] = $user->ID;
						}
					}
				}
			}
		}
		

		if($aSave){
			update_post_meta( $post_id, 'customer_ids', $aSave );
		}else{
			delete_post_meta( $post_id, 'customer_ids' );
		}
	}
	
	public function coupon_is_valid ( $valid, $coupon ) {
        //Not valid? Then it will never validate, so get out of here
        if ( ! $valid ) {
            return FALSE;
        }
		
		$aUsers = get_post_meta($coupon->id, 'customer_ids', true);
		
        if ($aUsers) {        
            $user = wp_get_current_user();

            //If both fail we invalidate. Otherwise it's ok
            if ( ! in_array( $user->ID, $aUsers )) {
                return FALSE;
            }
        }
		
		return TRUE;
    } 
}

add_filter('woocommerce_email_get_option', 'custom_woocommerce_email_get_option', 99, 5);
function custom_woocommerce_email_get_option($result, $object, $value, $key, $empty_value){
	if ($object -> id == 'customer_completed_order'){
		SWITCH($key){
			case 'heading_downloadable':
				$result = __( 'Your order is complete', 'woocommerce' );
			break;
			
			case 'subject_downloadable':
				$result = __( 'Your {site_title} order from {order_date} is complete', 'woocommerce' );
			break;
		}
	}
	
	return $result;
}
/*
add_filter('woocommerce_email_subject_customer_completed_order', 'custom_woocommerce_email_subject_customer_completed_order', 2, 10);
function custom_woocommerce_email_subject_customer_completed_order($subject, $object){
	return __( 'Your {site_title} order from {order_date} is complete', 'woocommerce' );
}

add_filter('woocommerce_email_heading_customer_completed_order', 'custom_woocommerce_email_heading_customer_completed_order', 2, 10);
function custom_woocommerce_email_heading_customer_completed_order($heading, $object){
	return __( 'Your order is complete', 'woocommerce' );
}*/

add_filter('woocommerce_continue_shopping_redirect', 'custom_woocommerce_continue_shopping_redirect');
function custom_woocommerce_continue_shopping_redirect($val){
	if (get_site_url() === $val){
		$val = get_site_url(null, 'eshop');
	}
	
	return $val;
}

function custom_checkbox_private_police(){
	$attr = '';
	
	$html = '<input class="required" type="checkbox" name="private_police" value="1" />';
	
	$customer_id = get_current_user_id();
	if ($customer_id){
		$private_police = get_user_meta( $customer_id, 'private_police', true);
		if ($private_police){
			$html = '<input type="hidden" name="private_police" value="1" /><input type="checkbox" value="1" checked="checked" disabled="disabled" />';
		}
	}
	
	return '<label>'.$html.' Я соглашаюсь с условиями пользовательского соглашения <a href="/oferta" target="_blank">подробнее</a></label>';
}

//Добавляем галку на пользовательское соглашение
add_action('register_form', 'custom_add_fields', 1);
add_action('woocommerce_checkout_after_customer_details', 'custom_add_fields', 1);
function custom_add_fields(){
	echo '<div class="register-section private_police" id="profile-details-section-wysija">
		<div class="editfield">
			'.custom_checkbox_private_police().'
		</div>
	</div>';
}

//Регистрация
add_action( 'woocommerce_register_post', 'custom_validate_extra_fields_register', 9999, 3 ); 
function custom_validate_extra_fields_register( $username, $email, $validation_errors ) {
	if ( ! isset( $_POST['private_police'] ) ) {
		$validation_errors->add( 'private_police_error', 'Пожалуйста, согласитесь с условиями пользовательского соглашения для регистрации на сайте');
	}
}

add_action( 'woocommerce_created_customer', 'custom_save_extra_fields_register' );
function custom_save_extra_fields_register( $customer_id ) {	
	//if ( isset( $_POST['private_police'] ) ) {
		// WooCommerce billing phone
		update_user_meta( $customer_id, 'private_police', isset($_POST['private_police']) ? 1 : 0);
	//}
}

//Оформление заказа
add_action( 'woocommerce_after_checkout_validation', 'custom_after_checkout_validation', 10 );
function custom_after_checkout_validation( $data ){
	if ( ! isset( $_POST['private_police'] ) ) {
		wc_add_notice( 'Пожалуйста, согласитесь с условиями пользовательского соглашения для проведения оплаты', 'error' );
	}
}

add_action( 'woocommerce_checkout_order_processed', 'custom_checkout_order_processed', 10);
function custom_checkout_order_processed(){
	$customer_id = get_current_user_id();
	if ($customer_id){
		update_user_meta( $customer_id, 'private_police', isset($_POST['private_police']) ? 1 : 0);
	}
}


//Поле для подписки
/*add_shortcode( 'private_potice', 'custom_private_potice' );*/
function custom_private_potice(){
	return '<div class="mc-field-group">'.custom_checkbox_private_police().'</div>';
}

remove_shortcode('shortcode','odras_content_func');
add_shortcode('shortcode','custom_odras_content_func');
function custom_odras_content_func($atts){
	extract( shortcode_atts( array(
		'id' => null,
	), $atts ) );
	
	$post = get_post($id);
	$content = $post->post_content;
	if (strpos($content, '<' . '?') !== false) {
		ob_start();
		eval('?' . '>' . $content);
		$content = ob_get_clean();
	}
	
	$content = str_replace('[private_potice]', custom_private_potice(), $content);
	
	return $content;
}

function httpToHttps($link){
	return str_replace('http://', 'https://', $link);
}

add_filter('wp_get_attachment_image_attributes', 'custom_wp_get_attachment_image_attributes', 10, 3);
function custom_wp_get_attachment_image_attributes($attr, $attachment, $size){
	$attr['src'] = httpToHttps($attr['src']);
	
	return $attr;
}

add_filter('woocommerce_customer_get_downloadable_products', 'custom_woocommerce_customer_get_downloadable_products', 99);
function custom_woocommerce_customer_get_downloadable_products($downloads){
	global $wpdb;

	$customer_id = get_current_user_id();
	
	$downloads   = array();
	$_product    = null;
	$order       = null;
	$file_number = 0;

	// Get results from valid orders only
	$results = apply_filters( 'woocommerce_permission_list', $wpdb->get_results( $wpdb->prepare( "
		SELECT permissions.*
		FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions as permissions
		WHERE user_id = %d
		AND permissions.order_id > 0
		AND
			(
				permissions.downloads_remaining > 0
				OR
				permissions.downloads_remaining = ''
			)
		AND
			(
				permissions.access_expires IS NULL
				OR
				permissions.access_expires >= %s
				OR
				permissions.access_expires = '0000-00-00 00:00:00'
			)
		ORDER BY permissions.order_id, permissions.product_id, permissions.permission_id;
		", $customer_id, date( 'Y-m-d', current_time( 'timestamp' ) ) ) ), $customer_id );

	if ($results) {
		$looped_downloads = array();
		foreach ($results as $result) {
			if (!$order || $order->id != $result->order_id ) {
				// new order
				$order    = wc_get_order( $result->order_id );
				$_product = null;
			}

			// Make sure the order exists for this download
			if (!$order) {
				continue;
			}

			// Downloads permitted?
			if (!$order->is_download_permitted() ) {
				continue;
			}

			$product_id = intval( $result->product_id );

			if ( ! $_product || $_product->id != $product_id ) {
				// new product
				$file_number = 0;
				$_product    = wc_get_product( $product_id );
			}
			
			// Check product exists and has the file
			if ( ! $_product || ! $_product->exists() || ! $_product->has_file( $result->download_id ) ) {
				continue;
			}

			$download_file = $_product->get_file( $result->download_id );

			// Check if the file has been already added to the downloads list
			if ( isset($looped_downloads[$product_id]) && in_array( $download_file, $looped_downloads[$product_id] ) ) {
				continue;
			}

			$looped_downloads[$product_id][] = $download_file;

			// Download name will be 'Product Name' for products with a single downloadable file, and 'Product Name - File X' for products with multiple files
			$download_name = apply_filters(
				'woocommerce_downloadable_product_name',
				$_product->get_title() . ' &ndash; ' . $download_file['name'],
				$_product,
				$result->download_id,
				$file_number
			);

			$downloads[] = array(
				'download_url'        => add_query_arg(
					array(
						'download_file' => $product_id,
						'order'         => $result->order_key,
						'email'         => $result->user_email,
						'key'           => $result->download_id
					),
					home_url( '/' )
				),
				'download_id'         => $result->download_id,
				'product_id'          => $product_id,
				'download_name'       => $download_name,
				'order_id'            => $order->id,
				'order_key'           => $order->order_key,
				'downloads_remaining' => $result->downloads_remaining,
				'access_expires' 	  => $result->access_expires,
				'file'                => $download_file
			);

			$file_number++;
		}
	}
	
	return $downloads;
}


//Social thumb
remove_action('wp_head', 'fbfixhead');
add_action('wp_head', 'custom_fbfixhead');
function custom_fbfixhead() {

	 // Required for is_plugin_active to work.
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	 // If BuddyPress is active
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {

		// If not on a BuddyPress members page
		if (!bp_current_component('members')) {

			 // If not the homepage
			if ( !is_home() ) {

				// If there is a post image...
				if (has_post_thumbnail()) {
				// Set '$featuredimg' variable for the featured image.
				$featuredimg = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), "Full");
				$ftf_description = get_the_excerpt($post->ID);
				global $post;
				$ot = get_post_meta($post->ID, 'ftf_open_type', true);
				if($ot == "") { $default = "article"; } else $default = get_post_meta($post->ID, 'ftf_open_type', true);
				$ftf_head = '
				<!--/ Facebook Thumb Fixer Open Graph /-->
				<meta property="og:type" content="'. $default . '" />
				<meta property="og:url" content="' . get_permalink() . '" />
				<meta property="og:title" content="' . wp_kses_data(get_the_title($post->ID)) . '" />
				<meta property="og:description" content="' . wp_kses($ftf_description, array ()) . '" />
				<meta property="og:site_name" content="' . wp_kses_data(get_bloginfo('name')) . '" />
				<meta property="og:image" content="' . $featuredimg[0] . '" />
                                <link rel="image_src" href="' . $featuredimg[0] . '" />

				<meta itemscope itemtype="'. $default . '" />
				<meta itemprop="description" content="' . wp_kses($ftf_description, array ()) . '" />
				<meta itemprop="image" content="' . $featuredimg[0] . '" />
				';
				} //...otherwise, if there is no post image.
				else {
				$ftf_description = get_the_excerpt($post->ID);
				global $post;
				$ot = get_post_meta($post->ID, 'ftf_open_type', true);
				if($ot == "") { $default = "article"; } else $default = get_post_meta($post->ID, 'ftf_open_type', true);
				$ftf_head = '
				<!--/ Facebook Thumb Fixer Open Graph /-->
				<meta property="og:type" content="'. $default . '" />
				<meta property="og:url" content="' . get_permalink() . '" />
				<meta property="og:title" content="' . wp_kses_data(get_the_title($post->ID)) . '" />
				<meta property="og:description" content="' . wp_kses($ftf_description, array ()) . '" />
				<meta property="og:site_name" content="' . wp_kses_data(get_bloginfo('name')) . '" />
				<meta property="og:image" content="' . get_option('default_fb_thumb') . '" />
                                <link rel="image_src" href="' . get_option('default_fb_thumb') . '" />
                                
				<meta itemscope itemtype="'. $default . '" />
				<meta itemprop="description" content="' . wp_kses($ftf_description, array ()) . '" />
				<meta itemprop="image" content="' . get_option('default_fb_thumb') . '" />
				';
				}
				} //...otherwise, it must be the homepage so do this:
				else {
				$ftf_name = get_bloginfo('name');
				$ftf_description = get_bloginfo('description');
				$ot = get_option( 'homepage_object_type', '');
				if($ot == "") { $default = "website"; } else $default = get_option( 'homepage_object_type', '');
				$ftf_head = '
				<!--/ Facebook Thumb Fixer Open Graph /-->
				<meta property="og:type" content="' . $default . '" />
				<meta property="og:url" content="' . get_option('home') . '" />
				<meta property="og:title" content="' . wp_kses($ftf_name, array ()) . '" />
				<meta property="og:description" content="' . wp_kses_data($ftf_description, array ()) . '" />
				<meta property="og:site_name" content="' . wp_kses($ftf_name, array ()) . '" />
				<meta property="og:image" content="' . get_option('default_fb_thumb') . '" />
                                <link rel="image_src" href="' . get_option('default_fb_thumb') . '" />
                                
				<meta itemscope itemtype="'. $default . '" />
				<meta itemprop="description" content="' . wp_kses($ftf_description, array ()) . '" />
				<meta itemprop="image" content="' . get_option('default_fb_thumb') . '" />
				';
			}
		}
  	} // Otherwie, if BuddyPress is NOT active...
	else if ( !is_plugin_active( 'buddypress/bp-loader.php' ) ) {

		// If not the homepage
		global $post;
		if ( !is_home() ) {

			// If there is a post image...
			if (has_post_thumbnail()) {
			// Set '$featuredimg' variable for the featured image.
			$featuredimg = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), "Full");
			$ftf_description = get_the_excerpt($post->ID);
			global $post;
			$ot = get_post_meta($post->ID, 'ftf_open_type', true);
			if($ot == "") { $default = "article"; } else $default = get_post_meta($post->ID, 'ftf_open_type', true);
			$ftf_head = '
			<!--/ Facebook Thumb Fixer Open Graph /-->
			<meta property="og:type" content="'. $default . '" />
			<meta property="og:url" content="' . get_permalink() . '" />
			<meta property="og:title" content="' . wp_kses_data(get_the_title($post->ID)) . '" />
			<meta property="og:description" content="' . wp_kses($ftf_description, array ()) . '" />
			<meta property="og:site_name" content="' . wp_kses_data(get_bloginfo('name')) . '" />
			<meta property="og:image" content="' . $featuredimg[0] . '" />
                        <link rel="image_src" href="' . $featuredimg[0] . '" />
                                
			<meta itemscope itemtype="'. $default . '" />
			<meta itemprop="description" content="' . wp_kses($ftf_description, array ()) . '" />
			<meta itemprop="image" content="' . $featuredimg[0] . '" />
			';
			} //...otherwise, if there is no post image.
			else {
			$ftf_description = get_the_excerpt($post->ID);
			global $post;
			$ot = get_post_meta($post->ID, 'ftf_open_type', true);
			if($ot == "") { $default = "article"; } else $default = get_post_meta($post->ID, 'ftf_open_type', true);
			$ftf_head = '
			<!--/ Facebook Thumb Fixer Open Graph /-->
			<meta property="og:type" content="'. $default . '" />
			<meta property="og:url" content="' . get_permalink() . '" />
			<meta property="og:title" content="' . wp_kses_data(get_the_title($post->ID)) . '" />
			<meta property="og:description" content="' . wp_kses($ftf_description, array ()) . '" />
			<meta property="og:site_name" content="' . wp_kses_data(get_bloginfo('name')) . '" />
			<meta property="og:image" content="' . get_option('default_fb_thumb') . '" />
                        <link rel="image_src" href="' . get_option('default_fb_thumb') . '" />
                                
			<meta itemscope itemtype="'. $default . '" />
			<meta itemprop="description" content="' . wp_kses($ftf_description, array ()) . '" />
			<meta itemprop="image" content="' . get_option('default_fb_thumb') . '" />
			';
			}
			} //...otherwise, it must be the homepage so do this:
			else {
			$ftf_name = get_bloginfo('name');
			$ftf_description = get_bloginfo('description');
			$ot = get_option( 'homepage_object_type', '');
			if($ot == "") { $default = "website"; } else $default = get_option( 'homepage_object_type', '');
			$ftf_head = '
			<!--/ Facebook Thumb Fixer Open Graph /-->
			<meta property="og:type" content="' . $default . '" />
			<meta property="og:url" content="' . get_option('home') . '" />
			<meta property="og:title" content="' . wp_kses($ftf_name, array ()) . '" />
			<meta property="og:description" content="' . wp_kses_data($ftf_description, array ()) . '" />
			<meta property="og:site_name" content="' . wp_kses($ftf_name, array ()) . '" />
			<meta property="og:image" content="' . get_option('default_fb_thumb') . '" />
                        <link rel="image_src" href="' . get_option('default_fb_thumb') . '" />
                                
			<meta itemscope itemtype="'. $default . '" />
			<meta itemprop="description" content="' . wp_kses($ftf_description, array ()) . '" />
			<meta itemprop="image" content="' . get_option('default_fb_thumb') . '" />
			';
		}
	}
  echo $ftf_head;
  print "\n";
}