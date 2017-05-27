<?php

//Добавляем кнопку отменить заказ в админке
function woocommerce_admin_order_actions($actions, $the_order){
	global $post;
	
	if ( in_array( $the_order->status, array( 'pending', 'on-hold', 'cancel' ) ) )
		$actions['cancel'] = array(
			'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce-mark-order-cancel&order_id=' . $post->ID ), 'woocommerce-mark-order-cancel' ),
			'name' 		=> __( 'Cancel', 'woocommerce' ),
			'action' 	=> "cancel"
		);
		
	return $actions;
}


/*Google Analytics Ecommerce*/
function cancelGoogleTransaction($order){
	if (@intval($_SESSION['del_order'])!=$order->id){
		// Transaction Data
		$trans = array(
			'id'			=>	date('Y.m.d', strtotime($order->order_date)) .' / '. $order->billing_phone, 
			'affiliation'	=>	$order->billing_phone,
			'revenue'		=>	-($order->order_total), 
			'shipping'		=>	-($order->order_shipping), 
			'tax'			=>	-($order->order_tax),
			'currency'		=> 'RUB'
		);

		$aItems = $order->get_items();
		
		$items = array();
		foreach($aItems as $iItemId => $aItem){
			// List of Items Purchased.
			$items[] = array(
				'sku'		=> get_post_meta( $aItem['product_id'],'_sku',true), 
				'name'		=> $aItem['name'], 
				'category'	=> '', 
				'price'		=> -(floatval($aItem['line_total'])/intval($aItem['qty'])), 
				'quantity'	=> -($aItem['qty'])
			);
		}
		
		$_SESSION['del_order']=$order->id;
	}
	
	if ( ! $items ) return FALSE;

	
	$sHTML = <<<HTML
<html>
<head>
<script language="javascript">
HTML;

$sHTML .= <<<HTML
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-11908455-42', 'pizzalarenzo.ru');
ga('send', 'pageview');


ga('require', 'ecommerce', 'ecommerce.js');
HTML;

	$sHTML .= getTransactionJs($trans);

	foreach ($items as &$item) {
		$sHTML .= getItemJs($trans['id'], $item);
	}
	
$sHTML .= <<<HTML
ga('ecommerce:send');
HTML;

$sHTML .= <<<HTML
</script>
</head>
<body>
HTML;

	return $sHTML;
}

// Function to return the JavaScript representation of a TransactionData object.
function getTransactionJs(&$trans) {
  return <<<HTML
ga('ecommerce:addTransaction', {
  'id': '{$trans['id']}',
  'affiliation': '{$trans['affiliation']}',
  'revenue': '{$trans['revenue']}',
});
HTML;
}

// Function to return the JavaScript representation of an ItemData object.
function getItemJs(&$transId, &$item) {
  return <<<HTML
ga('ecommerce:addItem', {
  'id': '$transId',
  'name': '{$item['name']}',
  'sku': '{$item['sku']}',
  'category': '{$item['category']}',
  'price': '{$item['price']}',
  'quantity': '{$item['quantity']}'
});
HTML;
}
/*\Google Analytics Ecommerce*/



/**
 * Mark an order as cancel
 *
 * @access public
 * @return void
 */
function woocommerce_mark_order_cancel() {

	if ( !is_admin() ) die;
	if ( !current_user_can('edit_shop_orders') ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce' ) );
	if ( !check_admin_referer('woocommerce-mark-order-cancel')) wp_die( __( 'You have taken too long. Please go back and retry.', 'woocommerce' ) );
	$order_id = isset($_GET['order_id']) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : '';
	if (!$order_id) die;

	$order = new WC_Order( $order_id );
	$order->update_status( 'cancelled' );
	
	$sHtml = cancelGoogleTransaction($order);
	
	if ($sHtml !== FALSE){
		$sHtml .= 'Заказ отменен';
		$sHtml .= "<script>setTimeout(\"window.location = '".wp_get_referer()."'\", \"2000\");</script>";
		$sHtml .= "</body></html>";
		
		die($sHtml);
	}
	
	wp_safe_redirect( wp_get_referer() );

}


