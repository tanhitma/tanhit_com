<?php
/*
WP-Cache Config Sample File

See wp-cache.php for author details.
*/

$wp_cache_preload_on = 0; //Added by WP-Cache Manager
$wp_cache_preload_taxonomies = 0; //Added by WP-Cache Manager
$wp_cache_preload_email_volume = 'many'; //Added by WP-Cache Manager
$wp_cache_preload_email_me = 0; //Added by WP-Cache Manager
$wp_cache_preload_interval = 0; //Added by WP-Cache Manager
$wp_cache_preload_posts = 'all'; //Added by WP-Cache Manager
$ossdlcdn = 0; //Added by WP-Cache Manager
$cache_schedule_interval = 'twicedaily'; //Added by WP-Cache Manager
$cache_gc_email_me = 0; //Added by WP-Cache Manager
$cache_time_interval = '3600'; //Added by WP-Cache Manager
$cache_max_time = 3600; //Added by WP-Cache Manager
$cache_scheduled_time = '00:00'; //Added by WP-Cache Manager
$cache_schedule_type = 'time'; //Added by WP-Cache Manager
$wp_cache_slash_check = 0; //Added by WP-Cache Manager
$cache_compression = 1; //Added by WP-Cache Manager
$wp_cache_object_cache = 0; //Added by WP-Cache Manager
$wp_cache_not_logged_in = 1; //Added by WP-Cache Manager
$wp_cache_clear_on_post_edit = 0; //Added by WP-Cache Manager
$wp_cache_hello_world = 0; //Added by WP-Cache Manager
$super_cache_enabled = true; //Added by WP-Cache Manager
$cache_enabled = false; //Added by WP-Cache Manager
$wp_supercache_cache_list = 1; //Added by WP-Cache Manager
$wp_cache_front_page_checks = 1; //Added by WP-Cache Manager
$wp_cache_mobile_enabled = 1; //Added by WP-Cache Manager
$wp_cache_mfunc_enabled = 0; //Added by WP-Cache Manager
$wp_supercache_304 = 1; //Added by WP-Cache Manager
$wp_cache_no_cache_for_get = 1; //Added by WP-Cache Manager
$wp_cache_disable_utf8 = 0; //Added by WP-Cache Manager
$cache_path = '/var/www/vhosts/64/149880/webspace/httpdocs/tanhit.pa.infobox.ru/wp-content/cache'; //Added by WP-Cache Manager
$cache_page_secret = '4c047e6be1df8ff9bb1dc4b8d134e900'; //Added by WP-Cache Manager
$wp_cache_mobile_browsers = '2.0 MMP, 240x320, 400X240, AvantGo, BlackBerry, Blazer, Cellphone, Danger, DoCoMo, Elaine/3.0, EudoraWeb, Googlebot-Mobile, hiptop, IEMobile, KYOCERA/WX310K, LG/U990, MIDP-2., MMEF20, MOT-V, NetFront, Newt, Nintendo Wii, Nitro, Nokia, Opera Mini, Palm, PlayStation Portable, portalmmm, Proxinet, ProxiNet, SHARP-TQ-GX10, SHG-i900, Small, SonyEricsson, Symbian OS, SymbianOS, TS21i-10, UP.Browser, UP.Link, webOS, Windows CE, WinWAP, YahooSeeker/M1A1-R2D2, iPhone, iPod, Android, BlackBerry9530, LG-TU915 Obigo, LGE VX, webOS, Nokia5800'; //Added by WP-Cache Manager
$dismiss_readable_warning = 1; //Added by WP-Cache Manager
$wp_cache_mobile_groups = ''; //Added by WP-Cache Manager
$wp_cache_mobile_prefixes = 'w3c , w3c-, acs-, alav, alca, amoi, audi, avan, benq, bird, blac, blaz, brew, cell, cldc, cmd-, dang, doco, eric, hipt, htc_, inno, ipaq, ipod, jigs, kddi, keji, leno, lg-c, lg-d, lg-g, lge-, lg/u, maui, maxo, midp, mits, mmef, mobi, mot-, moto, mwbp, nec-, newt, noki, palm, pana, pant, phil, play, port, prox, qwap, sage, sams, sany, sch-, sec-, send, seri, sgh-, shar, sie-, siem, smal, smar, sony, sph-, symb, t-mo, teli, tim-, tosh, tsm-, upg1, upsi, vk-v, voda, wap-, wapa, wapi, wapp, wapr, webc, winw, winw, xda , xda-'; //Added by WP-Cache Manager
$wp_cache_refresh_single_only = '0'; //Added by WP-Cache Manager
$wp_cache_make_known_anon = 0; //Added by WP-Cache Manager
$wp_cache_mod_rewrite = 0; //Added by WP-Cache Manager
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       $cache_acceptable_files = array( 'wp-comments-popup.php', 'wp-links-opml.php', 'wp-locations.php' );

$cache_rejected_uri = array ( 0 => 'wp-.*\\.php', 1 => 'index\\.php', 2 => 'my-account', ); //Added by WP-Cache Manager
$cache_rejected_user_agent = array ( 0 => 'bot', 1 => 'ia_archive', 2 => 'slurp', 3 => 'crawl', 4 => 'spider', 5 => 'Yandex' );

$cache_rebuild_files = 0; //Added by WP-Cache Manager

// Disable the file locking system.
// If you are experiencing problems with clearing or creating cache files
// uncommenting this may help.
$wp_cache_mutex_disabled = 0; //Added by WP-Cache Manager

// Just modify it if you have conflicts with semaphores
$sem_id = 336460515; //Added by WP-Cache Manager

if ( '/' != substr($cache_path, -1)) {
	$cache_path .= '/';
}

$wp_cache_mobile = 0;
$wp_cache_mobile_whitelist = 'Stand Alone/QNws';
$wp_cache_mobile_browsers = '2.0 MMP, 240x320, 400X240, AvantGo, BlackBerry, Blazer, Cellphone, Danger, DoCoMo, Elaine/3.0, EudoraWeb, Googlebot-Mobile, hiptop, IEMobile, KYOCERA/WX310K, LG/U990, MIDP-2., MMEF20, MOT-V, NetFront, Newt, Nintendo Wii, Nitro, Nokia, Opera Mini, Palm, PlayStation Portable, portalmmm, Proxinet, ProxiNet, SHARP-TQ-GX10, SHG-i900, Small, SonyEricsson, Symbian OS, SymbianOS, TS21i-10, UP.Browser, UP.Link, webOS, Windows CE, WinWAP, YahooSeeker/M1A1-R2D2, iPhone, iPod, Android, BlackBerry9530, LG-TU915 Obigo, LGE VX, webOS, Nokia5800'; //Added by WP-Cache Manager

// change to relocate the supercache plugins directory
$wp_cache_plugins_dir = WPCACHEHOME . 'plugins';
// set to 1 to do garbage collection during normal process shutdown instead of wp-cron
$wp_cache_shutdown_gc = 0;
$wp_super_cache_late_init = 0; //Added by WP-Cache Manager

