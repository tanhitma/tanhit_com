<?php
/*
Plugin Name: Elfsight InstaShow
Description: Instagram feed for WordPress. Create unique galleries of Instagram photos. User friendly, flexible and fully responsive. Amazing look for stunning images.
Plugin URI: https://elfsight.com/instagram-feed-instashow/wordpress/
Version: 2.3.1
Author: Elfsight
Author URI: https://elfsight.com/
*/

if (!defined('ABSPATH')) exit;


define('ELFSIGHT_INSTASHOW_SLUG', 'instashow');
define('ELFSIGHT_INSTASHOW_VERSION', '2.3.1');
define('ELFSIGHT_INSTASHOW_FILE', __FILE__);
define('ELFSIGHT_INSTASHOW_PATH', plugin_dir_path(__FILE__));
define('ELFSIGHT_INSTASHOW_URL', plugin_dir_url( __FILE__ ));
define('ELFSIGHT_INSTASHOW_PLUGIN_SLUG', plugin_basename( __FILE__ ));
define('ELFSIGHT_INSTASHOW_TEXTDOMAIN', 'instashow');
define('ELFSIGHT_INSTASHOW_API_URL', ELFSIGHT_INSTASHOW_URL . 'api/');

function instashow_activation(){
    update_option('elfsight_instashow_activated', 1);
}
register_activation_hook(ELFSIGHT_INSTASHOW_FILE, 'instashow_activation');

require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-defaults.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-widgets-api.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'admin', 'instashow-admin.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-shortcode.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-widget.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-vc.php')));
require_once(ELFSIGHT_INSTASHOW_PATH . implode(DIRECTORY_SEPARATOR, array('includes', 'instashow-lib.php')));