<?php
/**
 * @package Tanhit
 */
add_filter( 'woocommerce_my_account_my_downloads_title', 'tanhit_my_account_my_downloads_title' );
function tanhit_my_account_my_downloads_title( $title ) {
	return pll__( 'Мои вебинары и практики', 'tanhit' );
}

add_filter( 'woocommerce_available_download_link', 'tanhit_available_download_link', 10, 2 );
function tanhit_available_download_link( $link, $download ) {
	/**
	 * Reset default download link
	 */
	return '';	
}	

/**
 * @see tanhit\woocommerce\myaccount\my-downloads.php
 */ 
add_action( 'woocommerce_available_download_start', 'tanhit_available_download_start' );
function tanhit_available_download_start( $download ) {

	global $tanhit_customer_products;	

        $download_expiry = isset($download['access_expires']) ? " (Доступно до: ".date('d.m.Y', strtotime($download['access_expires'])).")" : '';
        
	$product = array();
	
	foreach( $tanhit_customer_products as $pr ) :
		if ( $pr[ 'product_id' ] == $download[ 'product_id' ] ) {
			$product[ $download[ 'product_id' ] ] = $pr;
			break;
		}	
	endforeach;

	$disable_file_online_show = array( '.zip', '.rar' );

	?>
	

	<span class="item-preview" style=""><?php echo httpToHttps(tanhit_get_product_thumbnail( $download[ 'product_id' ] ));?></span>
	
	<a href="<?php echo httpToHttps($product[ $download[ 'product_id' ] ][ 'permalink' ]); ?>"
		class="item-link vid-link" target="_blank"><?php echo $product[ $download[ 'product_id' ] ][ 'product_name' ]; ?></a>
	
	<span class="file-name"><?php	/* pll_e( 'Файл:', 'tanhit' );*/ echo $download[ 'file' ][ 'name' ] . $download_expiry; ?></span>



	<a href="<?php echo httpToHttps(esc_url( $download['download_url'] )); ?>" class="btn-download"><?php pll_e( 'Скачать', 'tanhit' ); ?></a>
	
	<?php
	if ( empty( $download[ 'file' ][ 'file' ] ) ) :	?>
		<?php /* <a href="#" class="btn-show"><?php pll_e( 'Онлайн-просмотр', 'tanhit' ); ?></a>	*/ ?> <?php
	else:
		/**
		 * @todo remove line below after real video will be loaded to server for check
		 */
		//$download[ 'file' ][ 'file' ] = 'http://media.jilion.com/videos/demo/midnight_sun_sv1_720p.mp4';

		/**
		 * Check for disabled file for online show
		 */
		$disabled = false;
		foreach( $disable_file_online_show as $piece ) {
			if ( false !== strpos( $download[ 'file' ][ 'file' ], $piece ) ) {
				$disabled = true;
				break;
			}	
		}	
		if ( ! $disabled ) {
			?>
            <a href="#vid<?php echo md5($download['download_url']); ?>" class="show-video btn-show">
                <?php pll_e( 'Онлайн-просмотр', 'tanhit' ); ?>
            </a>

            <?/*<div style="display:none;" class="show_vid" id="vid<?php echo md5($download['download_url']); ?>">
                <div class="vid_player">
                    <?php  echo do_shortcode('[evp_embed_video url="'.$download['download_url']. '" width="800" ratio="0.7"]'); ?>
                </div>
            </div>*/?>
			555

        <?php
		}	?>



		<?php
	endif;
}

/**
 * @see tanhit\woocommerce\myaccount\my-downloads.php
 */ 
add_action( 'tanhit_free_download_products', '_tanhit_free_download_products' );
function _tanhit_free_download_products() {	

	global $tanhit_customer_products;	

	$disable_file_online_show = array( '.zip', '.rar' );	
	
	$args = array(
		'post_type' 	 => 'product',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		#'meta_key'   	 => 'product_date_start', // @TODO remove after test
		#'orderby'    	 => 'meta_value_num',
		#'order'      	 => 'ASC',
		#'meta_query'	 => array(
		#	array(
		#		'key'     => 'product_date_start',
		#		'value'   => '\d*',
		#		'compare' => 'REGEXP',
		#	)
		#),
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( 'webinar', 'practice' ),
			)
		),
	);

    $events = new WP_Query( $args );

	$now = date( 'Ymd', time() );

	$free_products = array();
	foreach( $events->posts as $key => $product ) :

		$product_date_start = strtotime( get_post_meta( $product->ID, 'product_date_start', true ) );

		//if ( $product_date_start >= strtotime( $now ) ) { // @TODO remove after test
			/**
			 * Don't add future product
			 */
			//continue;
		//}

		$pr = wc_get_product( $product->ID );
		if ( $pr->get_price() > 0 ) {
			/**
			 * Don't add product with price > 0
			 */			
			continue;
		}	
		
		/**
		 * Don't add a product that was bought
		 */
		$product_bought = false; 
		foreach( $tanhit_customer_products as $customer_product ) {  
			if (  $customer_product[ 'order' ]->post_status == 'wc-completed' && $product->ID == $customer_product[ 'product_id' ] ) {
				$product_bought = true;
				break;
			}	
		
		}
		if ( $product_bought ) {
			continue;	
		}	
		
		$free_products[] = $pr;
		
	endforeach;	
	
	foreach( $free_products as $product ) :
		
		$downloads = $product->get_files();

		/**
		 * for download @see 'init' action in tanhit-functions.php
		 */
		foreach( $downloads as $key => $download ) :
                    $download_expiry = isset($download['access_expires']) ? " (Доступно до: ".date('d.m.Y', strtotime($download['access_expires'])).")" : '';
                ?>
			<li data-product="<?php echo $product->id; ?>">
				<span class="item-preview" style="display: inline-block; overflow: hidden"><?php echo $product->get_image(); ?></span>
				<a href="<?php echo httpToHttps(get_the_permalink( $product->id )); ?>"
					class="item-link vid-link" target="_blank"><?php echo $product->post->post_title; ?></a>
				<span class="file-name"><?php echo $download[ 'name' ] . $download_expiry; ?></span>
				<?php
				/**
				 * @see class-wc-download-handler.php for query string handle
				 */
				?>



				<a href="<?php echo home_url() . '/?tanhit_download=true&product=' . $product->id . '&key=' . $key; ?>"
					class="btn-download"><?php pll_e( 'Скачать', 'tanhit' ); ?></a>
					
				<?php
				if ( ! empty( $download[ 'file' ] ) ) :
					/**
					 * Check for disabled file for online show
					 */
					$disabled = false;
					foreach( $disable_file_online_show as $piece ) {
						if ( false !== strpos( $download[ 'file' ], $piece ) ) {
							$disabled = true;
							break;
						}	
					}	
					if ( ! $disabled ) {
						?>
						<a href="#vid<?php echo $product->id."-".$key; ?>" class="show-video btn-show">
							<?php pll_e( 'Онлайн-просмотр', 'tanhit' ); ?>
						</a>

                        <?/*<div style="display:none;" class="show_vid" id="vid<?php echo $product->id."-".$key; ?>">
                            <div class="vid_player">
                                <?php  echo do_shortcode('[evp_embed_video url="'.home_url() . '?tanhit_download=true&product=' . $product->id . '&key=' . $key.'" width="800" ratio="0.7"]'); ?>
                            </div>
                        </div>*/?>
						666

                    <?php
					}	?>




					<?php
				endif;		?>			
					
			</li>		
			<?php
		endforeach;
		
	endforeach;	
}	