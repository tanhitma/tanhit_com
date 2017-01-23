<?php
/**
 * @package Tanhit
 */

/** functions */

/**
 * Return product thumbnail by product id
 * @see https://docs.woothemes.com/wc-apidocs/source-function-woocommerce_get_product_thumbnail.html#709-726
 */
function tanhit_get_product_thumbnail( $product_id, $size = 'shop_catalog' ) {

	$thumb = get_the_post_thumbnail( $product_id, $size );
	
	if ( empty( $thumb ) && wc_placeholder_img_src() ) {
		$thumb = wc_placeholder_img( $size );
	}
	
	return $thumb;
	
}

/**
 * Download file like Woocommerce
 */
function tanhit_download_file( $file ) {

	//error_log( 'file path: '.$file );


	if (ob_get_level()) {
		ob_end_clean();
	}

	// заставляем браузер показать окно сохранения файла
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . basename( $file ) );
	//header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));

	// читаем файл и отправляем его пользователю
	wp_ob_end_flush_all();
	readfile( $file );

  
}


/**
 * Download file like Woocommerce
 */
function tanhit_download_file_OLD_VERSION( $path ) {
	/**
	 * @see http://www.web-development-blog.com/archives/php-download-file-script/
	 */
	$fullPath = $path;
	 
	if ($fd = fopen ($fullPath, "r")) {
		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);
		$ext = strtolower($path_parts["extension"]);
		switch ($ext) {
			case "pdf":
			header("Content-type: application/pdf");
			header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
			break;
			// add more headers for other content types here
			default;
			header("Content-type: application/octet-stream");
			header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
			break;
		}
		header("Content-length: $fsize");
		header("Cache-control: private"); //use this to open files directly
		while(!feof($fd)) {
			$buffer = fread($fd, 2048);
			echo $buffer;
		}
	}
	fclose ($fd);
}

/**
 *
 */ 
function tanhit_edit_address( $load_address = 'billing' ) {
	
	$hidden_fields = array( 
		'billing_first_name',
		'billing_last_name',
		'billing_email'
	);	
	
	/**
	 * @see edit_address() in woocommerce\includes\shortcodes\class-wc-shortcode-my-account.php
	 */
	$current_user = wp_get_current_user();
	$load_address = sanitize_key( $load_address );

	$address = WC()->countries->get_address_fields( get_user_meta( get_current_user_id(), $load_address . '_country', true ), $load_address . '_' );

	// Prepare values
	foreach ( $address as $key => $field ) {

		$value = get_user_meta( get_current_user_id(), $key, true );

		if ( ! $value ) {
			switch( $key ) {
				case 'billing_email' :
				case 'shipping_email' :
					$value = $current_user->user_email;
				break;
				case 'billing_country' :
				case 'shipping_country' :
					$value = WC()->countries->get_base_country();
				break;
				case 'billing_state' :
				case 'shipping_state' :
					$value = WC()->countries->get_base_state();
				break;
			}
		}
		
		if ( in_array( $key, $hidden_fields ) ) {
			$address[ $key ][ 'class_wrap' ][] = 'hidden';
		} else {
			$address[ $key ][ 'class_wrap' ][] = '';
		}	
		
		if ( ! empty( $field[ 'required' ] ) && $field[ 'required' ] ) {
			unset( $address[ $key ][ 'required' ] );	
		}	
		
		if ( 'billing_first_name' == $key ) {
			$address[ $key ][ 'source_field' ] = 'account_first_name';
			$address[ $key ][ 'class_wrap' ][] = 'tanhit-listen-change';
			$address[ $key ][ 'class' ][] = 'source-account_first_name';
		}
		
		if ( 'billing_last_name' == $key ) {
			$address[ $key ][ 'source_field' ] = 'account_last_name';
			$address[ $key ][ 'class_wrap' ][] = 'tanhit-listen-change';
			$address[ $key ][ 'class' ][] = 'source-account_last_name';
		}	
		
		if ( 'billing_email' == $key ) {
			$address[ $key ][ 'source_field' ] = 'account_email';
			$address[ $key ][ 'class_wrap' ][] = 'tanhit-listen-change';
			$address[ $key ][ 'class' ][] = 'source-account_email';
		}
		
		if ( 'billing_phone' == $key ) {
			unset( $address[ $key ][ 'class' ] );
			$address[ $key ][ 'class' ][] = 'form-row-wide';	
		}	
		
		$address[ $key ]['value'] = apply_filters( 'woocommerce_my_account_edit_address_field_value', $value, $key, $load_address );
	}

	return $address;
}	


/**
 * @return true if user bought product
 */
function tanhit_customer_bought_product( $product = null, $user = null ) {
	
	if( empty( $product ) ) {
		global $product;
	}	
	
	if ( empty( $user ) ) {

		global $current_user;
		$user = $current_user;	

		// get user attributes
		// $user = wp_get_current_user();
	}	
	
	$result = false;
	
	/**
	 * Fetch product attributes by ID
	 */
	if( empty( $product->id ) ){
		$wc_pf = new WC_Product_Factory();
		$product = $wc_pf->get_product( $product->ID );
	}
	
	/** 
	 * Determine if customer has bought product
	 */
	if( wc_customer_bought_product( $user->email, $user->ID, $product->id ) ){
		$result = true;
	}	
	
	return $result;
}	 
 
 
/** Actions and filters */

/**
 * @see action 'tanhit_free_download_products'
 */
add_action( 'init', 'tanhit_my_account_download', 99 );
function tanhit_my_account_download() {
	
	if ( empty( $_GET[ 'tanhit_download' ] ) ) {
		return;
	}
	
	if ( empty( $_GET[ 'product' ] ) ) {
		return;	
	}
	
	if ( empty( $_GET[ 'key' ] ) ) {
		return;
	}	
	
	$product = wc_get_product( absint( $_GET[ 'product' ] ) );

	if ( empty( $product ) ) {
		return;	
	}
	
	$downloads = $product->get_files();
	
	if ( empty( $downloads ) ) {
		return;	
	}	
	
	if ( ! array_key_exists( $_GET[ 'key' ], $downloads ) ) {
		return;	
	}	
	
	$parsed_file_path = WC_Download_Handler::parse_file_path( $downloads[ $_GET[ 'key' ] ][ 'file' ] );
	//error_log( 'parsed_file_path: '.print_r( $parsed_file_path, true ) );
	tanhit_download_file( $parsed_file_path[ 'file_path' ] );


}

/**
 * Set price format for product with zero price 
 * @see get_price_html() in abstract-wc-product.php
 */
add_filter( 'woocommerce_free_sale_price_html', 'tanhit_free_sale_price_html', 10, 2 );
function tanhit_free_sale_price_html( $price, $wc_product ) {
	return '<del><span class="amount">' . $wc_product->regular_price . '&nbsp;&#8381;</span></del> <ins>0 ₽ !!!</ins>';
}

/**
 * Enqueue script for account page
 */
add_action( 'wp_enqueue_scripts', 'tanhit_my_account_scripts', 99 );
function tanhit_my_account_scripts() {
	
	if (  ! is_account_page() ) {
		return;	
	}

	wp_register_script(
		'tanhit-my-account',
		TM_URL . '/js/tanhit-my-account.js',
		array( 'jquery' ),
		TANHIT_VERSION,
		true
	);
	wp_enqueue_script( 'tanhit-my-account' );
	wp_localize_script(
		'tanhit-my-account',
		'TanhitMyAccount',
		array(
			'version' => TANHIT_VERSION,
			#'editAddress' => tanhit_edit_address(),
			#'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			#'process_ajax' => 'Tanhit_Ajax_process_ajax',
		)
	);	
	
}

/**
 * @see save_account_details() in woocommerce\includes\class-wc-form-handler.php
 */
add_action( 'woocommerce_save_account_details', 'tanhit_save_account_details' );
function tanhit_save_account_details( $user_id  ) {

	$fields = array();
	
	$fields[ 'billing_first_name' ] = ! empty( $_POST[ 'account_first_name' ] ) ? wc_clean( $_POST[ 'account_first_name' ] ) : '';
    $fields[ 'billing_last_name' ]  = ! empty( $_POST[ 'account_last_name' ] ) ? wc_clean( $_POST[ 'account_last_name' ] ) : '';
    $fields[ 'billing_email' ]      = ! empty( $_POST[ 'account_email' ] ) ? sanitize_email( $_POST[ 'account_email' ] ) : '';	
	$fields[ 'billing_phone' ]		= ! empty( $_POST[ 'billing_phone' ] ) ? wc_clean( $_POST[ 'billing_phone' ] ) : '';	
	$fields[ 'billing_city' ]		= ! empty( $_POST[ 'billing_city' ] ) ? wc_clean( $_POST[ 'billing_city' ] ) : '';	
	
	foreach( $fields as $field=>$value ) {
		update_user_meta( $user_id, $field, $value );
	}	

}	
 

/**
 * @see themes\tanhit\woocommerce\myaccount\form-edit-account.php
 */
add_action( 'tanhit_edit_account_form', 'tanhit_edit_account_form_callback' );
function tanhit_edit_account_form_callback() {
	/**
	 * @see woocommerce\templates\myaccount\form-edit-address.php
	 */ 
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;	
	}	

	$address = tanhit_edit_address();
	
	foreach ( $address as $key => $field ) : 
	
		$class_wrap = implode( ' ', $field[ 'class_wrap' ] ); 
		$source_field = empty( $field[ 'source_field' ] ) ? '' : $field[ 'source_field' ];

		?>
		
		<div class="<?php echo $class_wrap; ?>" data-source="<?php echo $source_field; ?>" data-field="<?php echo $key; ?>">		<?php
			woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] );	?>
		</div>				<?php	
			
	endforeach;

}

/**
 * Redefine button for zero price product
 *
 * @see http://tanhit.local/архив-вебинаров-и-практик
 * @see filter 'woocommerce_loop_add_to_cart_link' in add-to-cart.php
 */
add_filter( 'woocommerce_loop_add_to_cart_link', 'tanhit_loop_add_to_cart_link', 10, 2 );
function tanhit_loop_add_to_cart_link( $link, $product ) {
	
	/**
	 * Check price 
	 */
	if ( $product->price == 0 ) {
	
		$link = sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
			get_the_permalink( $product->id ),
			esc_attr( isset( $quantity ) ? $quantity : 1 ),
			esc_attr( $product->id ),
			esc_attr( $product->get_sku() ),
			esc_attr( isset( $class ) ? $class : 'button add_to_cart_button' ),
			pll__( 'Просмотреть', 'tanhit' )
		);
		
	}	
	
	return $link;
	
}	
