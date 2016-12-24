<?php
@ini_set( ‘upload_max_size’ , ‘1024M’ );
@ini_set( ‘post_max_size’, ‘1024M’);
@ini_set( ‘max_execution_time’, ‘1000’ );
/**
 * Theme: tanhit
 */
  
/**
 * Define theme version
 */
define( 'TANHIT_VERSION', '1.0.0' );

define('TM_DIR', get_template_directory(__FILE__));
define('TM_URL', get_template_directory_uri(__FILE__));

/**
 * Define key to prevent open multiple current-webinar page by registered user
 */
define( 'TANHIT_PREVENT_OPEN_KEY', 'tanhit_prevent_open_key' );

/**
 * Define webinar page name
 */
define( 'TANHIT_WEBINAR_PAGE', 'current-webinar' );

/**
 * Define page to redirect from current webinar room
 */
define( 'TANHIT_WEBINAR_REDIRECT_PAGE', '/my-account' );


require_once TM_DIR . '/lib/Parser.php';
require_once TM_DIR . '/lib/wp_bootstrap_navwalker.php';

/*
 * Make theme available for translation.
 * Translations can be filed in the /languages/ directory.
 */
load_theme_textdomain( 'tanhit', get_template_directory() . '/languages' );


if ( is_admin() ) :
	/** */
else :
	require_once TM_DIR . '/includes/shortcodes.php';
endif;

/**
 * Remove standard WOO action
 * @see woocommerce\includes\wc-template-hooks.php
 */
if ( class_exists( 'WooCommerce' ) ) :

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

    add_action( 'after_setup_theme', 'woocommerce_support' );
    function woocommerce_support() {
        add_theme_support( 'woocommerce' );
    }
	
	/**
	 * Remove or overwrite product description heading in tab
	 */
	add_filter( 'woocommerce_product_description_heading', 'tanhit_product_description_heading' );
    function tanhit_product_description_heading( $heading ) {
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
	add_filter( 'comment_reply_link_args', 'tanhit_comment_reply_link_args', 10, 3 );
	function tanhit_comment_reply_link_args( $args, $comment, $post ) {
		/**
		 * Reset login text for unregistered users
		 *
		 * @see wp_list_comments() in tanhit\woocommerce\single-product-reviews.php
		 */
		$args[ 'login_text' ] = '';
		return $args;
	}	

	/**
	 * Add custom fields in user registration 
	 */
	require_once( 'woocommerce-registration-form.php' );
	
	/**
	 * Manipulation with the fields at checkout page
	 */	
	require_once( 'woocommerce-checkout.php' );

	/**
	 * Manipulation with the fields at my-account/edit-address/billing page
	 */	
	require_once( 'woocommerce-billing.php' );
	
	/**
	 * Adding new tab for product
	 */	
	require_once( 'woocommerce-product-tab.php' );	
	
	/**
	 * Adding single product summary
	 */		
	require_once( 'woocommerce-single_product_summary.php' );
	
	/**
	 * Init data for my account
	 */				
	require_once( 'includes/woocommerce-my-account-init.php' );
	
	/**
	 * Change my orders template
	 */			
	require_once( 'includes/woocommerce-my_orders.php' );	
	 
	/**
	 * Adding column for info about files in orders (my-account page)
	 */		
	require_once( 'includes/woocommerce-my_orders_download.php' );	

	/**
	 * Change my address template
	 */			
	require_once( 'includes/woocommerce-my-address.php' );	
	
	/**
	 * Change my downloads template
	 */			
	require_once( 'includes/woocommerce-my-downloads.php' );	
	
	/**
	 * Add current webinar
	 */
	require_once( 'includes/show-current-webinar.php' );
	
	/**
	 * Add theme specific my account extension
	 */	
	require_once( 'includes/tanhit-my-account.php' );
	
	/**
	 * @todo doc
	 */		
	require_once( 'includes/tanhit-ajax-actions.php' );
	Tanhit_Ajax::controller();
	
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		/** do nothing */
	} else {
		
		/**
		 * Add page 'архив-вебинаров-и-практик' specific code 
		 */	
		require_once( 'includes/tanhit-archive-webinars-practice.php' );
		
		/**
		 * Add various functions and filters
		 */
		require_once( 'includes/tanhit-functions.php' );

	}
	
endif;

// WC Change number or products per row to 5
// Change number or products per row to 5 for category page, 6 for shop page
if(is_product_category())
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


function tanhit_add_style(){
    wp_enqueue_style( 'my-bootstrap-extension', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '1');
    //wp_enqueue_style( 'fotorama', get_template_directory_uri() . '/css/fotorama.css', array('my-bootstrap-extension'), '1');
    //wp_enqueue_style( 'my-styles', get_template_directory_uri() . '/css/style.css', array('my-bootstrap-extension'), '1');
    wp_enqueue_style( 'my-sass', get_template_directory_uri() . '/sass/style.css', array('my-bootstrap-extension'), '2.33');
   
}

function tanhit_add_script(){
	/**
	 * @todo remove
	 */
    //wp_enqueue_script( 'jq', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), '1');
    
	//wp_enqueue_script( 'my-bootstrap-extension', get_template_directory_uri() . '/js/bootstrap.min.js', array(), '1');
	wp_register_script(
		'my-bootstrap-extension',
		get_template_directory_uri() . '/js/bootstrap.min.js',
		array( 'jquery' ),
		TANHIT_VERSION,
		true
	);
	wp_enqueue_script( 'my-bootstrap-extension' );	
	
    //wp_enqueue_script( 'fotorama-js', get_template_directory_uri() . '/js/fotorama.js', array(), '1');
	wp_register_script(
		'fotorama-js',
		get_template_directory_uri() . '/js/fotorama.js',
		array( 'jquery' ),
		TANHIT_VERSION,
		true
	);
	wp_enqueue_script( 'fotorama-js' );		
	
	/**
	 * Enqueue script with common js code
	 * @scope front
	 */
	wp_register_script(
		'frontend-script',
		TM_URL . '/js/script.js',
		array( 'jquery' ),
		TANHIT_VERSION,
		true
	);
	wp_enqueue_script( 'frontend-script' );		
	wp_localize_script(
		'frontend-script',
		'TanhitFrontManager',
		array(
			'version' => TANHIT_VERSION,
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'process_ajax' => 'Tanhit_Ajax_process_ajax',
			'cart'	  => tanhit_get_cart(),
			'post_id' => tanhit_get_post_id(),	
			'duplKey' => tanhit_get_duplicate(),
			'pathname_redir' => tanhit_get_redirect_page(),
			'timerValue' 	 => tanhit_get_redirect_timer()
		)
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
	if ( null == $id ) {
		return false;	
	}	
	global $post, $current_user;
	if ( TANHIT_WEBINAR_PAGE == $post->post_name ) {
		
		if (  ! is_user_logged_in() ) {
			return false; 
		}

		$time = time();
		update_user_meta( $current_user->ID, TANHIT_PREVENT_OPEN_KEY, $time );
		return $time;
	}
	return false;
}

/**
 * Return current post ID
 */
function tanhit_get_post_id() {
	global $post;
	if ( empty( $post ) ) {
		return null;	
	}	
	return $post->ID;	
}	

/**
 * Return woocommerce cart
 */
function tanhit_get_cart(){
	global $woocommerce;
	$response = array();
	$response[ 'cart_count' ] 	= $woocommerce->cart->cart_contents_count;
	$response[ 'cart_total' ] 	= $woocommerce->cart->get_cart_total();
	$response[ 'cart_url' ] 	= $woocommerce->cart->get_cart_url();
	return $response;
}
	
function tanhit_add_admin_script(){
	
	global $pagenow;	
	/**
	 * @todo add array of pages to load bootstrap
	 */
	if ( ! in_array( $pagenow, array() ) ) {

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
		array(), 
		TANHIT_VERSION,
		true
	);

    //wp_enqueue_script( 'datetimepicker', get_template_directory_uri() . '/js/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js', array(), '1');
    wp_enqueue_script( 
		'datetimepicker',
		get_template_directory_uri() . '/js/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
		array( 'jquery', 'moment' ),
		TANHIT_VERSION,
		true
	);
    
	//wp_enqueue_script('admin',get_template_directory_uri() . '/js/admin.js', array(), '1');
	wp_enqueue_script(
		'admin',
		get_template_directory_uri() . '/js/admin.js',
		array( 'jquery', 'datetimepicker' ),
		TANHIT_VERSION,
		true
	);
    
	
	//wp_enqueue_style( 'my-bootstrap-extension-admin', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '1');
	wp_enqueue_style( 
		'my-bootstrap-extension-admin', 
		get_template_directory_uri() . '/css/bootstrap.min.css',
		array(), 
		TANHIT_VERSION
	);
    
	
	/**
	 * @todo make correct loading
	 */
	wp_enqueue_script( 'my-bootstrap-extension', get_template_directory_uri() . '/js/bootstrap.min.js', array(), '1');
    wp_enqueue_style( 'my-style-admin', get_template_directory_uri() . '/css/admin.css', array(), '1');
    wp_enqueue_style( 'bootstrapdatetimepicker', get_template_directory_uri() . '/js/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css', array(), '1');

}

add_action( 'admin_enqueue_scripts', 'tanhit_add_admin_script');
add_action( 'wp_enqueue_scripts', 'tanhit_add_style' );
add_action( 'wp_enqueue_scripts', 'tanhit_add_script' );

function prn($content) {
    echo '<pre style="background: lightgray; border: 1px solid black; padding: 2px">';
    print_r ( $content );
    echo '</pre>';
}

function my_pagenavi() {
    global $wp_query;

    $big = 999999999; // уникальное число для замены

    $args = array(
        'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) )
    ,'format' => ''
    ,'current' => max( 1, get_query_var('paged') )
    ,'total' => $wp_query->max_num_pages
    );

    $result = paginate_links( $args );

    // удаляем добавку к пагинации для первой страницы
    $result = str_replace( '/page/1/', '', $result );

    echo $result;
}

function excerpt_readmore($more) {
    return '... <br><a href="'. get_permalink($post->ID) . '" class="readmore">' . 'Читать далее' . '</a>';
}
add_filter('excerpt_more', 'excerpt_readmore');

if ( function_exists( 'add_theme_support' ) )
    add_theme_support( 'post-thumbnails' );

// убираем автоматическую чистку корзины
function my_remove_schedule_delete() {
    remove_action( 'wp_scheduled_delete', 'wp_scheduled_delete' );
}
/**
 * @todo remove action 
 */
//add_action( 'init', 'my_remove_schedule_delete' );

/*--------------------------------------------- МЕНЮ НАВИГАЦИИ -------------------------------------------------------*/

function theme_register_nav_menu() {
    register_nav_menus( array(
        'primary' => 'Меню',

    ) );
    //register_nav_menu( 'primary', 'Главное меню' );
}
add_action( 'after_setup_theme', 'theme_register_nav_menu' );

/*-------------------------------------------- КОНЕЦ МЕНЮ НАВИГАЦИИ --------------------------------------------------*/

/*--------------------------------------------------- ТОВАРЫ ---------------------------------------------------------*/
// Review Post type
/**
 * @todo remove action 
 */
//add_action('init', 'product_register');
function product_register() {

    $labels = array(
        'name' => _x('Товары', 'post type general name'),
        'singular_name' => _x('Товар', 'post type singular name'),
        'add_new' => _x('Добавить товар', 'review'),
        'add_new_item' => __('Добавить новый товар'),
        'edit_item' => __('Редактировать товар'),
        'new_item' => __('Новый товар'),
        'view_item' => __('Посмотреть товар'),
        'search_items' => __('Найти товар'),
        'not_found' =>  __('Ничего не найдено'),
        'not_found_in_trash' => __('В корзине пусто'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => null,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','editor','thumbnail')
    );

    register_post_type( 'product' , $args );
}
// Custom taxonomy type
function add_type_taxonomies() {
    register_taxonomy('type', 'product', array(
        // Hierarchical taxonomy (like categories)
        'hierarchical' => true,
        // This array of options controls the labels displayed in the WordPress Admin UI
        'labels' => array(
            'name' => _x( 'Типы товаров', 'taxonomy general name' ),
            'singular_name' => _x( 'Типы товаров', 'taxonomy singular name' ),
            'search_items' =>  __( 'Поиск типов' ),
            'all_items' => __( 'Все типы' ),
            'parent_item' => __( 'Родитель' ),
            'parent_item_colon' => __( 'Родитель:' ),
            'edit_item' => __( 'Редактировать тип' ),
            'update_item' => __( 'Обновить тип' ),
            'add_new_item' => __( 'Добавить новый тип' ),
            'new_item_name' => __( 'Новое название типа' ),
            'menu_name' => __( 'Типы товаров' ),
        ),

        // Control the slugs used for this taxonomy
        'rewrite' => array(
            'slug' => 'type', // This controls the base slug that will display before each term
            'with_front' => false, // Don't display the category base before "/locations/"
            'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
        ),
    ));
}

/**
 * @todo remove action 
 */
//add_action( 'init', 'add_type_taxonomies', 0 );
// Custom taxonomy section
function add_section_taxonomies() {
    register_taxonomy('section', 'product', array(
        // Hierarchical taxonomy (like categories)
        'hierarchical' => true,
        // This array of options controls the labels displayed in the WordPress Admin UI
        'labels' => array(
            'name' => _x( 'Разделы', 'taxonomy general name' ),
            'singular_name' => _x( 'Разделы', 'taxonomy singular name' ),
            'search_items' =>  __( 'Поиск разделов' ),
            'all_items' => __( 'Все разделы' ),
            'parent_item' => __( 'Родитель' ),
            'parent_item_colon' => __( 'Родитель:' ),
            'edit_item' => __( 'Редактировать раздел' ),
            'update_item' => __( 'Обновить раздел' ),
            'add_new_item' => __( 'Добавить новый раздел' ),
            'new_item_name' => __( 'Новое название раздела' ),
            'menu_name' => __( 'Разделы' ),
        ),

        // Control the slugs used for this taxonomy
        'rewrite' => array(
            'slug' => 'section', // This controls the base slug that will display before each term
            'with_front' => false, // Don't display the category base before "/locations/"
            'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
        ),
    ));
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
function custom_post_status(){
    register_post_status(
        'created',
        array(
            'label'                     => _x( 'Создан', 'post' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>' ),
        )
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
function updateCustomFields($post_id)
{
    if (!isset($_POST['extra'])) return false;
    foreach ($_POST['extra'] as $key => $value) {
        if (empty($value)) {
            delete_post_meta($post_id, $key); // удаляем поле если значение пустое
            continue;
        }

        update_post_meta($post_id, $key,$value); // add_post_meta() работает автоматически
    }
    return $post_id;
}

/**
 * @todo remove action 
 */
//add_action('save_post', 'updateCustomFields', 10, 1);
//custom field files
function my_attachments( $attachments )
{
    $fields         = array(
        array(
            'name'      => 'title',                         // unique field name
            'type'      => 'text',                          // registered field type
            'label'     => __( 'Заголовок', 'attachments' ),    // label to display
            'default'   => 'title',                         // default value upon selection
        )
    );

    $args = array(

        // title of the meta box (string)
        'label'         => 'Прикрепленные файлы',

        // all post types to utilize (string|array)
        'post_type'     => array( 'product' ),

        // meta box position (string) (normal, side or advanced)
        'position'      => 'normal',

        // meta box priority (string) (high, default, low, core)
        'priority'      => 'high',

        // allowed file type(s) (array) (image|video|text|audio|application)
        'filetype'      => null,  // no filetype limit

        // include a note within the meta box (string)
        'note'          => 'прикрепите файлы здесь!',

        // by default new Attachments will be appended to the list
        // but you can have then prepend if you set this to false
        'append'        => true,

        // text for 'Attach' button in meta box (string)
        'button_text'   => __( 'Добавить файлы', 'attachments' ),

        // text for modal 'Attach' button (string)
        'modal_text'    => __( 'Добавить', 'attachments' ),

        // which tab should be the default in the modal (string) (browse|upload)
        'router'        => 'browse',

        // whether Attachments should set 'Uploaded to' (if not already set)
        'post_parent'   => false,

        // fields array
        'fields'        => $fields,

    );

    $attachments->register( 'my_attachments', $args ); // unique instance name
}
add_action( 'attachments_register', 'my_attachments' );
/*------------------------------------------------ КОНЕЦ ТОВАРОВ -----------------------------------------------------*/


/*
 * Auto complete virtual orders
 */
add_filter( 'woocommerce_payment_complete_order_status', 'virtual_order_payment_complete_order_status', 10, 2 );

function virtual_order_payment_complete_order_status( $order_status, $order_id ) {
    $order = new WC_Order( $order_id );

    if ( 'processing' == $order_status &&
        ( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status ) ) {

        $virtual_order = null;

        if ( count( $order->get_items() ) > 0 ) {

            foreach( $order->get_items() as $item ) {

                if ( 'line_item' == $item['type'] ) {

                    $_product = $order->get_product_from_item( $item );

                    if ( ! $_product->is_virtual() ) {
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
        if ( $virtual_order ) {
            return 'completed';
        }
    }

    // non-virtual order, return original status
    return $order_status;
}

/*
 * Redirect to my-account after purchase
 */

add_action( 'template_redirect', 'wc_custom_redirect_after_purchase' );
function wc_custom_redirect_after_purchase() {
    global $wp;

    if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
        wp_redirect( home_url() . '/my-account/#order-received' );
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

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'woocommerce_single_product_summary_tabs', 'woocommerce_output_product_data_tabs', 10 );


// Add cat seminar warning
function tanhit_cart_warning()
{
    ?> <div class="cart-warning"><?php
    pll_e( 'Внимание! Указаная стоимость семинаров - это предоплата! Подробнее в описании к семинарам.', 'tanhit' );
    ?> </div><?php
}
add_action( 'woocommerce_before_cart', 'tanhit_cart_warning', 50 );

/*
 * Add login stylesheet
 */

function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_template_directory_uri() . '/style-login.css' );
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );


/*
 *
 */

function my_page_template_redirect()
{
    if( is_page( 'goodies' ) && ! is_user_logged_in() )
    {
        wp_redirect( home_url( '/signup/' ) );
        exit();
    }
}
add_action( 'template_redirect', 'my_page_template_redirect' );



/**
 * Register widget area.
 *
 * @since Tanhit
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function tanhit_widgets_init() {

    if (function_exists('register_sidebar')) {
        register_sidebar(array(
            'name'=> 'Blog Sidebar',
            'id' => 'blog_sidebar',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
        ));

        register_sidebar(array(
            'name'=> 'Mainpage subscribe',
            'id' => 'main_subscribe',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '',
            'after_title' => '',
        ));
    }
}
add_action( 'widgets_init', 'tanhit_widgets_init' );


function remove_page_from_query_string($query_string)
{
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

