<?php

// don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	exit( "Hello! I freelance as a plugin, you can't call me directly. :/" );
}


// load plugin
// NOTE: plugins_url( '', __FILE__ ) not working properly with symlink folder
require dirname( __FILE__ ) . '/code/class-order-terms.php';
$GLOBALS['wpm_order_terms'] = new WPM_Order_Terms( dirname( __FILE__ ), plugins_url( '', 'member-luxe/plugins/order-terms/order-terms.php' ) );

// plugin activation (NOTE: must be inside main file)
//register_activation_hook( __FILE__, array( 'WPM_Order_Terms', 'activate' ) );
