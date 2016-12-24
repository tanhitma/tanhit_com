<?php
@ini_set('upload_max_size', '1024M');
@ini_set('post_max_size', '1024M');
@ini_set('max_execution_time', '1000');
/**
 * Theme: tanhit
 */

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
    wp_enqueue_style('my-sass', get_template_directory_uri() . '/sass/style.css', ['my-bootstrap-extension'], '2.33');
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
    unset($fields['city']);

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
        'label'    => 'Адрес (обязательно укажите улицу, дом, квартиру)',
        'required' => (isset($_REQUEST['billing_delivery_point']) && $_REQUEST['billing_delivery_point'] ? false : true),
    ];
    $fields['billing']['billing_address_2'] = [
        'type'     => 'text',
        'label'    => 'Адрес (продолжение)',
        'required' => false //(isset($_REQUEST['billing_delivery_point']) && $_REQUEST['billing_delivery_point'] ? false : true),
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
            ['billing_state_id', 'billing_state', 'billing_address_1', 'billing_address_2', 'billing_email', 'billing_city', 'billing_phone', 'billing_first_name', 'billing_last_name'],
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
    if (in_array($input, ['billing_state_id', 'billing_state', 'billing_address_1', 'billing_address_2', 'billing_email', 'billing_city', 'billing_phone', 'billing_first_name', 'billing_last_name'])) {
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

        $aSettingsEdostavka = get_option('woocommerce_edostavka_settings');
        if (isset($aSettingsEdostavka['login']) && $aSettingsEdostavka['login'] && isset($aSettingsEdostavka['password']) && $aSettingsEdostavka['password']) {
            //Авторизационные данные
            $api_cdek->setAuth($aSettingsEdostavka['login'], $aSettingsEdostavka['password']);

            //Формируем список товаров заказа для выгрузки
            $component = $api_cdek->loadComponent('orders');
            error_log(print_r($component, 0));

            //Задаем номер выгрузки
            $component->setNumber('shop_' . date('Ymd', strtotime($order->order_date)) . '_' . str_pad($order->id, 10, 0, STR_PAD_LEFT));

            //Meta Order
            $aMetaDataOrder = get_post_meta($order->id);
            //Meta Delivery 
            $aDeliveryMethod = $order->get_shipping_methods();

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
            //$aDataExport['address']['street']       = $aMetaDataOrder['_billing_address_1'][0];
            //$aDataExport['address']['house']        = $aMetaDataOrder['_billing_address_2'][0];

            $aDataExport['address']['address'] = $aMetaDataOrder['_billing_address_1'][0] . ' ' . $aMetaDataOrder['_billing_address_2'][0];

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

            //Товары в заказе
            $order_items = $order->get_items();
            foreach ($order_items as $item_id => $item_data) {
                $oProduct = $order->get_product_from_item($item_data);

                if (!$oProduct->is_virtual()) {
                    $sSku = $oProduct->get_sku();
                    $iCost = $item_data['line_total'] / $item_data['qty'];

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
                    $aDataPackageExport['size_a'] += $_height * $item_data['qty'];
                    $aDataPackageExport['size_b'] += $_width * $item_data['qty'];
                    $aDataPackageExport['size_c'] += $_length * $item_data['qty'];
                    $aDataPackageExport['weight'] += $_weight * $item_data['qty'];
                }
            }

            $aDataPackageExport['pack'] = TRUE;

            $aDataExport['package'][1] = $aDataPackageExport;
            (new WC_Logger())->add('cdek_integration', 'INFO: order_to_cdek|' . print_r($aDataExport, 0));
            //Добавляем данные в выгрузку
            $component->setOrders([$aDataExport]);
            //Отправляем данные на сервер сдэк
            $response = $api_cdek->sendData($component);
            (new WC_Logger())->add('cdek_integration', 'INFO: order_to_cdek|' . print_r($response, 0));
            $aOrderResponse = (array)$response->Order[0];

            if (!isset($aOrderResponse["@attributes"]["ErrorCode"])) {
                $order->update_status('wc-export_to_cdek');
            }

            if (class_exists('WC_Logger')) {
                $logger = new WC_Logger();

                if (isset($aOrderResponse["@attributes"]["ErrorCode"])) {
                    $logger->add('cdek_integration', 'ERROR: order_to_cdek|' . implode('|', $aOrderResponse["@attributes"]));
                } else {
                    $logger->add('cdek_integration', 'OK: order_to_cdek|' . implode('|', $aOrderResponse["@attributes"]));
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
        if (trim($aData['path'], '/') == 'current-webinar' && $_REQUEST['id'] && getWebinarId($_REQUEST['id'])) {
            exit(wp_redirect(get_post_permalink($_REQUEST['id'])));
        } else
            if (trim($aData['path'], '/') == 'my-account' && $_REQUEST['pid']) {
                global $wpdb;

                if ($user_id = get_current_user_id()) {
                    if (getAccessToProduct($_REQUEST['pid']) === true) {
                        //Ищем оплаченный заказ с текущим товаром
                        $sql = "SELECT SUM(M2.`meta_value`) as cnt 
					FROM wp_woocommerce_order_items I 
					INNER JOIN wp_woocommerce_order_itemmeta M ON (M.`order_item_id` = I.`order_item_id`)
					INNER JOIN wp_woocommerce_order_itemmeta M2 ON (M2.`order_item_id` = I.`order_item_id`)
					INNER JOIN wp_postmeta PM ON (PM.post_id = I.`order_id`)
					INNER JOIN wp_posts P ON (P.`ID` = I.`order_id`)
					WHERE PM.meta_key = '_customer_user' && PM.meta_value = '{$user_id}' && M.`meta_key`= '_product_id' && M.`meta_value` = '{$_REQUEST['pid']}' && P.`post_type` = 'shop_order' && P.`post_status` = 'wc-completed' && M2.meta_key = '_qty'";

                        if (!$wpdb->get_var($sql) && get_product($_REQUEST['pid'])) {
                            $current_user = wp_get_current_user();

                            $address = [
                                'first_name' => $current_user->user_firstname,
                                'last_name'  => $current_user->user_lastname,
                                'email'      => $current_user->user_email,
                            ];

                            $order = wc_create_order(['customer_id' => $current_user->ID]);
                            $order->add_product(get_product($_REQUEST['pid']), 1); //(get_product with id and next is for quantity)
                            $order->set_address($address, 'billing');
                            $order->set_address($address, 'shipping');
                            $order->calculate_totals();
                            $order->update_status('wc-completed');
                        }
                    }
                }

                exit(wp_redirect('/my-account'));
            }
    }
}

add_filter('woocommerce_loop_add_to_cart_link', 'custom_woocommerce_loop_add_to_cart_link', 10, 2);
function custom_woocommerce_loop_add_to_cart_link($val, $product) {

    $access = getAccessToProduct($product->id);

    $user_access = get_post_meta($product->id, 'access', TRUE);
    if (($access) || (!$product->price) || (current_user_can('vip') && in_array($user_access, [1, 3]))/* || (is_super_admin())*/) {
        return sprintf('<a rel="nofollow" href="%s" class="%s">Смотреть</a>',
            esc_url(get_the_permalink($product->id)),
            esc_attr(isset($class) ? $class : 'button'),
            esc_html($product->add_to_cart_text())
        );
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
                    'nickname' => $current_user->user_login,
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
                $aLinksOld = $aLinks = get_user_meta($current_user->ID, 'webinar_links_' . $product_id, true);

                $qtyOld = $qty;

                $qty -= is_array($aLinks) ? count($aLinks) : 0;
                if ($qty > 0) {
                    $aTokensResult = ($client->generateConferenceTokens($webinar_room_id, ['how_many' => $qty]));
                    if ($aTokensResult->access_tokens) {
                        foreach ($aTokensResult->access_tokens as $key => $oToken) {
                            $sPrefix = ($qtyOld > 1 || ($key != 0 && !count($aLinksOld))) ? ((count($aLinksOld)) + $key + 1) . '_' : '';

                            $oAutoLogin = $client->conferenceAutologinHash($webinar_room_id, [
                                'email'    => $current_user->user_email,
                                'nickname' => $sPrefix . $current_user->user_login,
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

remove_all_filters('tanhit_free_download_products');
add_action('tanhit_free_download_products', 'custom_tanhit_free_download_products');
function custom_tanhit_free_download_products() {

    global $tanhit_customer_products;

    $disable_file_online_show = ['.zip', '.rar'];

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

        //if ( $product_date_start >= strtotime( $now ) ) { // @TODO remove after test
        /**
         * Don't add future product
         */
        //continue;
        //}

        $pr = wc_get_product($product->ID);

        if ($pr->get_price() > 0) {
            /**
             * Don't add product with price > 0
             */
            continue;
        }

        /**
         * Don't add a product that was bought
         */
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

    foreach ($free_products as $product) :

        $downloads = $product->get_files();

        /**
         * for download @see 'init' action in tanhit-functions.php
         */
        foreach ($downloads as $key => $download) :
            ?>
          <li data-product="<?php echo $product->id; ?>">
            <span class="item-preview"
                  style="display: inline-block; overflow: hidden"><?php echo $product->get_image(); ?></span>
            <a href="<?php echo get_the_permalink($product->id); ?>"
               class="item-link vid-link" target="_blank"><?php echo $product->post->post_title; ?></a>
            <span class="file-name"><?php echo $download['name']; ?></span>
              <?php
              /**
               * @see class-wc-download-handler.php for query string handle
               */

              $youtube = false;
              if (getYotubeDownLink($download['file'])) {
                  $youtube = $download['file'];
              }

              if (!$youtube) {
                  ?>
                <a href="<?php echo home_url() . '/?tanhit_download=true&product=' . $product->id . '&key=' . $key; ?>"
                   class="btn-download"><?php pll_e('Скачать', 'tanhit'); ?>
                </a>
              <?
              } ?>

              <?php
              if (!empty($download['file']) && !$youtube) :
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

                    <div style="display:none;" class="show_vid" id="vid<?php echo $product->id . "-" . $key; ?>"
                         data-src="<?= $link; ?>">
                      <div class="vid_player vid_player2">2
                          <?php /* echo do_shortcode("[wpm_video video=".getVideoFileByKey($product->id, $key)." ratio=16by9 autoplay=off]"); */ ?>
                          <?= (getPlayerForm($link)) ?>
                      </div>
                    </div>

                      <?php
                  } ?>

              <?php elseif ($youtube): ?>
                <a href="#vid<?php echo $product->id . "-" . $key; ?>" class="show-video btn-show">
                    <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
                </a>

                <div style="display:none;" class="show_vid" id="vid<?php echo $product->id . "-" . $key; ?>"
                     data-src="<?= $youtube; ?>">
                  <div class="vid_player vid_player2">
                      <?= (getPlayerForm($youtube)) ?>
                  </div>
                </div>
              <?php endif; ?>
          </li>
            <?php
        endforeach;

    endforeach;
}

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

    $disable_file_online_show = ['.zip', '.rar'];

    $youtube = false;
    if (getYotubeDownLink($download['file']['file'])) {
        $youtube = $download['file']['file'];
    }
    ?>

  <span class="item-preview" style=""><?php echo tanhit_get_product_thumbnail($download['product_id']); ?></span>

  <a href="<?php echo $product[$download['product_id']]['permalink']; ?>"
     class="item-link vid-link" target="_blank"><?php echo $product[$download['product_id']]['product_name']; ?></a>

  <span class="file-name"><?php /* pll_e( 'Файл:', 'tanhit' );*/
      echo $download['file']['name']; ?></span>

    <? if (!$youtube) {
        ?>
    <a href="<?php echo esc_url($download['download_url']); ?>"
       class="btn-download"><?php pll_e('Скачать', 'tanhit'); ?></a>
    <?
    } ?>

    <?php

    if (empty($download['file']['file'])) : ?>
        <?php /* <a href="#" class="btn-show"><?php pll_e( 'Онлайн-просмотр', 'tanhit' ); ?></a>	*/ ?><?php
    else:
        if ($youtube) {
            ?>
          <a href="#vid<?php echo md5($download['file']['file']); ?>" class="show-video btn-show">
              <?php pll_e('Онлайн-просмотр', 'tanhit'); ?>
          </a>

          <div style="display:none;" class="show_vid" id="vid<?php echo md5($download['file']['file']); ?>"
               data-src="<?= $youtube; ?>">
            <div class="vid_player vid_player2">
                <?= (getPlayerForm($youtube)) ?>
            </div>
          </div>
        <?
        } else {
            /**
             * @todo remove line below after real video will be loaded to server for check
             */
            //$download[ 'file' ][ 'file' ] = 'http://media.jilion.com/videos/demo/midnight_sun_sv1_720p.mp4';

            /**
             * Check for disabled file for online show
             */
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

            <?
            } ?>
        <?
        } ?>
        <?php
    endif;
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