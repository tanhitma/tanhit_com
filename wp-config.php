<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/var/www/vhosts/64/149880/webspace/httpdocs/tanhit.pa.infobox.ru/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'db1100785_tanhit');

/** Имя пользователя MySQL */
define('DB_USER', 'u1100785_tanhit');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '046461410_tanhit');

/** Имя сервера MySQL */
define('DB_HOST', '192.168.137.106');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'впишите сюда уникальную фразу11');
define('SECURE_AUTH_KEY',  'впишите сюда уникальную фразу22');
define('LOGGED_IN_KEY',    'впишите сюда уникальную фразу33');
define('NONCE_KEY',        'впишите сюда уникальную фразу44');
define('AUTH_SALT',        'впишите сюда уникальную фразу55');
define('SECURE_AUTH_SALT', 'впишите сюда уникальную фразу66');
define('LOGGED_IN_SALT',   'впишите сюда уникальную фразу77');
define('NONCE_SALT',       'впишите сюда уникальную фразу88');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 * 
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);
//define('WP_DEBUG', true);
define('SCRIPT_DEBUG', true);
// Tell WordPress to log everything to /wp-content/debug.log
define('WP_DEBUG_LOG', true);
// Turn off the display of error messages on your site
define('WP_DEBUG_DISPLAY', false);

define('SAVEQUERIES', true);

define( 'NGG_DISABLE_FILTER_THE_CONTENT', true );

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');

/** Memory Limit */
define('WP_MEMORY_LIMIT', '1280M');
