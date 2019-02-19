<?php
/**
 * @package Tanhit
 */
 
/**
 * @see wc_get_order_statuses()
 */ 
add_filter( 'wc_order_statuses', 'tanhit_wc_order_statuses' ) ; 
function tanhit_wc_order_statuses( $order_statuses ) {
	
    $statuses[ 'wc-pending' ] 		= pll__( 'Ожидание платежа', 'tanhit' );
    $statuses[ 'wc-processing' ] 	= pll__( 'В обработке', 'tanhit' );
    $statuses[ 'wc-on-hold' ] 		= pll__( 'Зарезервирован', 'tanhit' );
    $statuses[ 'wc-completed' ] 	= pll__( 'Выполнен', 'tanhit' );
    $statuses[ 'wc-cancelled' ] 	= pll__( 'Отменён', 'tanhit' );
    $statuses[ 'wc-refunded' ] 		= pll__( 'Возврат', 'tanhit' );
    $statuses[ 'wc-failed' ] 		= pll__( 'Неудавшийся', 'tanhit' );
	
	return $statuses;
}
 
add_filter( 'woocommerce_my_account_my_orders_columns', 'tanhit_my_account_my_orders_columns' );
function tanhit_my_account_my_orders_columns( $columns ) {
	unset( $columns[ 'order-actions' ] );
	$columns[ 'order_downloadable_files' ] = pll__( 'Файлы', 'tanhit' ); 
	$columns[ 'order-actions' ] = '&nbsp;';
	return $columns;
}

add_action( 'woocommerce_my_account_my_orders_column_order-actions', 'tanhit_my_account_my_orders_column_order_actions' );
function tanhit_my_account_my_orders_column_order_actions( $order ) {
	
	$actions = array(
		'pay'    => array(
			'url'  => $order->get_checkout_payment_url(),
			'name' => __( 'Pay', 'woocommerce' )
		),
		'view'   => array(
			'url'  => $order->get_view_order_url(),
			'name' => __( 'View', 'woocommerce' )
		),
		'cancel' => array(
			'url'  => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
			'name' => __( 'Cancel', 'woocommerce' )
		),
    'reload_permission' => array(
        'url'  => wc_get_page_permalink( 'myaccount' ) . "?oid={$order->get_order_number()}",
        'name' => __( 'Проверить файлы', 'woocommerce' )
    )
	);

	if ( ! $order->needs_payment() ) {
		unset( $actions['pay'] );
	}

	if ( ! in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) ) ) {
		unset( $actions['cancel'] );
	}

	if ( $actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order ) ) {
		foreach ( $actions as $key => $action ) {
			if ( 'view' == $key ) {
				echo '<a href="#' . $order->id .'" class="show-order-items button ' . sanitize_html_class( $key ) . '" data-order-id="' . $order->id . '">' . esc_html( $action['name'] ) . '</a>';
			} else {	
				echo '<a href="' . esc_url( $action['url'] ) . '" class="show-order-items button ' . sanitize_html_class( $key ) . '" data-order-id="' . $order->id . '">' . esc_html( $action['name'] ) . '</a>';
			}	
		}
	}
	
}
	
add_action( 'woocommerce_my_account_my_orders_column_order-number', 'tanhit_my_account_my_orders_column_order_number' );
function tanhit_my_account_my_orders_column_order_number( $order ) {
	
	echo _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number();

	/**
	 * @see woocommerce-my-account-init.php
	 */ 
	global $tanhit_customer_orders;
	global $tanhit_customer_products;

	$disable_file_online_show = array( '.zip' );
	
	$products = array();
	foreach( $tanhit_customer_products as $product ) :
		if ( $product[ 'order_id' ] == $order->id ) {
			$products[ $product[ 'product_id' ] ] = $product;
		}	
	endforeach;
					
	if ( empty( $products ) ) {
		return;	
	}	
	
	?>
	<td colspan="6" id="table-order-<?php echo $order->id; ?>" class="order-details">
		<table class="my_account_order-items">
			<thead>
				<tr>
					<th class=""><span class="nobr"><?php echo pll_e( 'Товар', 'tanhit' ); ?></span></th>
					<th class=""><span class="nobr"><?php echo pll_e( 'Итого', 'tanhit' ); ?></span></th>
					<?php /*<th class=""><span class="nobr"><?php echo pll_e( 'Файлы', 'tanhit' ); ?></span></th> */ ?>
				</tr>
			</thead>		
			<tbody>
				<?php foreach( $products as $p ) { 	

					?>
					<tr class="order-item" data-product-id="<?php echo $p[ 'product_id' ]; ?>">
						<td class="" data-title="">
							<a href="<?php echo $p[ 'permalink' ]; ?>" target="_blank"><?php echo $p[ 'product_name' ]; ?></a>
						</td>
						<td class="" data-title="">	
							<?php echo $order->get_formatted_line_subtotal( $p[ 'item' ] ); ?>
						</td>
                        <?php /*
						<td class="" data-title="">	
							<?php

                                $files = array();
                                $file_message = '';
                                if ( empty( $p[ '_downloadable_files' ] ) ) {
                                    $file_message = pll__( 'Нет файлов', 'tanhit' );
                                } else {
                                    $file_message = pll__( 'Доступен', 'tanhit' );
                                    foreach( $p[ '_downloadable_files' ] as $id=>$attrs ) {
                                        $files[$id]['name'] = $attrs[ 'name' ];
                                        $files[$id]['file'] = $attrs[ 'file' ];
                                    }
                                }


								 // Order statuses [ wc-completed, wc-processing, wc-cancelled ]
								if ( 'wc-completed' == $p[ 'order' ]->post_status ) :
									if ( empty( $files ) ) { 
										echo $file_message;
									} else {
										echo '<ul>';	
										foreach( $files as $file ) {
											
											echo '<li>';
												echo $file['name']; 
											echo '</li>';
										}
										echo '</ul>';	
									}	
								endif;	
							?>
						</td>
                        */ ?>

					</tr>
				<?php }	?>	
			</tbody>
		</table>
	</td>
	<?php
	
}	

add_action( 'woocommerce_my_account_my_orders_column_order_downloadable_files', 'tanhit_my_account_my_orders_column_download' );
function tanhit_my_account_my_orders_column_download( $order ) {
	
	$products = $order->get_items();
	$has_files=false;
	foreach( $products as $product ) {

		$_downloadable_files = get_post_meta( $product[ 'product_id' ], '_downloadable_files', true );
		
		if ( ! empty( $_downloadable_files ) ) {
			$has_files=true;
		}	
	
	}

	if ( $has_files ) {
		pll_e( 'Доступны', 'tanhit' );
	} else {
		pll_e( 'Нет файлов', 'tanhit' );

	}
	
}	 