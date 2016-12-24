<?php

define('TM_DIR', get_template_directory(__FILE__));
define('TM_URL', get_template_directory_uri(__FILE__));

require_once TM_DIR . '/lib/Parser.php';
require_once TM_DIR . '/lib/wp_bootstrap_navwalker.php';

function add_style(){
    wp_enqueue_style( 'my-bootstrap-extension', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '1');
    wp_enqueue_style( 'fotorama', get_template_directory_uri() . '/css/fotorama.css', array('my-bootstrap-extension'), '1');
    wp_enqueue_style( 'my-styles', get_template_directory_uri() . '/css/style.css', array('my-bootstrap-extension'), '1');
    wp_enqueue_style( 'my-sass', get_template_directory_uri() . '/sass/style.css', array('my-bootstrap-extension'), '1');
   
}

function add_script(){
    wp_enqueue_script( 'jq', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), '1');
    wp_enqueue_script( 'my-bootstrap-extension', get_template_directory_uri() . '/js/bootstrap.min.js', array(), '1');
    wp_enqueue_script( 'fotorama-js', get_template_directory_uri() . '/js/fotorama.js', array(), '1');
    wp_enqueue_script( 'my-script', get_template_directory_uri() . '/js/script.js', array(), '1');
    
}

function add_admin_script(){
    wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/jquery-2.1.3.min.js', array(), '1');
    wp_enqueue_script('admin',get_template_directory_uri() . '/js/admin.js', array(), '1');
    wp_enqueue_style( 'my-bootstrap-extension-admin', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '1');
    wp_enqueue_script( 'my-bootstrap-extension', get_template_directory_uri() . '/js/bootstrap.min.js', array(), '1');
    wp_enqueue_style( 'my-style-admin', get_template_directory_uri() . '/css/admin.css', array(), '1');
    wp_enqueue_script( 'moment', get_template_directory_uri() . '/js/bower_components/moment/min/moment.min.js', array(), '1');
    wp_enqueue_script( 'datetimepicker', get_template_directory_uri() . '/js/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js', array(), '1');
    wp_enqueue_style( 'bootstrapdatetimepicker', get_template_directory_uri() . '/js/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css', array(), '1');

}

add_action('admin_enqueue_scripts', 'add_admin_script');
add_action( 'wp_enqueue_scripts', 'add_style' );
add_action( 'wp_enqueue_scripts', 'add_script' );

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
add_action( 'init', 'my_remove_schedule_delete' );

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
add_action('init', 'product_register');
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
add_action( 'init', 'add_type_taxonomies', 0 );
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
add_action( 'init', 'add_section_taxonomies', 0 );
//product status custom field
add_action( 'init', 'custom_post_status' );
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
add_action('add_meta_boxes', 'registerCustomFields', 1);
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
add_action('save_post', 'updateCustomFields', 10, 1);
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