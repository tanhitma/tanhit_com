<?php
/**
 * My Orders
 *
 * Shows recent orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
	
//Получаем все страницы которые с защищенным контентом
$sQuery = "SELECT DISTINCT P.ID, P.post_title   
FROM `{$wpdb->prefix}posts` P 
INNER JOIN `{$wpdb->prefix}postmeta` PM ON (PM.meta_key = 'access_protect' && PM.meta_value = '1' && PM.post_id = P.ID) 
WHERE P.post_type = 'page' && P.post_status = 'publish'";
$aQueryResults = $wpdb->get_results($sQuery, 'ARRAY_A');

$aPageIDs = array();
if ($aQueryResults){
	$aProductIDs = $aSertificateIDs = array();
	foreach($aQueryResults as $aQueryItem){

		//Если страница доступна когда куплены товары
		if($aValT = get_field( "product_ids", $aQueryItem['ID'] )){
			//Проверяем если заказы с товарами привязанными к странице
			$sQuery = "SELECT COUNT(P.ID) as cnt 
			FROM `{$wpdb->prefix}posts` P 
			INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.meta_key = '_customer_user' && PM.meta_value = '".get_current_user_id()."' && PM.post_id = P.ID) 
			INNER JOIN {$wpdb->prefix}woocommerce_order_items WOI ON (WOI.order_id = P.ID) 
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta WOM ON (WOM.order_item_id = WOI.order_item_id && WOM.meta_key = '_product_id') 
			WHERE P.post_type = 'shop_order' && P.post_status = 'wc-completed' && WOM.meta_value IN (".implode(',', $aValT).")";
			if($wpdb->get_var( $sQuery)){
				$aPageIDs[$aQueryItem['ID']] = $aQueryItem;
			}
		}
		

		//Если страница доступна когда имеются сертификаты
		if($aValT = get_field( "sertificate_ids", $aQueryItem['ID'] )){
			//Проверяем если сертификаты у пользователя которые привязанные к странице
			$sQuery = "SELECT COUNT(P.ID) as cnt 
			FROM `{$wpdb->prefix}posts` P 
			INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.meta_key = 'cert_user' && PM.meta_value = '".get_current_user_id()."' && PM.post_id = P.ID) 
			INNER JOIN {$wpdb->prefix}term_relationships TR ON (TR.object_id = P.ID) 
			WHERE P.post_type = 'certificates' && P.post_status = 'publish' && TR.term_taxonomy_id IN (".implode(',', $aValT).")";
			if($wpdb->get_var( $sQuery)){
				$aPageIDs[$aQueryItem['ID']] = $aQueryItem;
			}
		}
	}
}?>

<h2>Защищенные страницы к которым получен доступ</h2>

<?if ( $aPageIDs ){?>

	<table style='width:100%;border-spacing: 0;margin-top:20px;'>
		<?foreach($aPageIDs as $aPageItem){?>
		<tr>
			<td style='padding:5px;border:1px solid silver;'><a href="<?=get_permalink($aPageItem['ID'])?>" target='_blank'><?=$aPageItem['post_title']?></a></td>
		</tr>
		<?}?>
	</table>
	
<?}?>