<?php
/**
 * Plugin Name: Tanhit Site Manager
 * Plugin URI: 
 * Description: Manager for tanhit site.
 * Version: 1.0.0
 * Author: alexgff
 * Author URI: 
 * Network: false
 * License: GPL2
 * Credits: Alex Gor (alexgff)
 * Copyright 2016 alexgff
 */ 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TANHIT_SITE_MANAGER_VERSION', '1.0.0' );

require_once( 'class-tanhit-site-manager.php' );
Tanhit_Site_Manager::controller();
