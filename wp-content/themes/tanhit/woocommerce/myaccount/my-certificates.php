<?php
/**
 * My Orders
 *
 * Shows recent orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-downloads.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$aData = $wpdb->get_results("
	SELECT P.*, TRIM(CONCAT(UM.meta_value,' ',UM2.meta_value)) as cert_user_name, PM2.meta_value as cert_location, PM3.meta_value as cert_date 
	FROM {$wpdb->prefix}posts P 
	INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user')
	INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value && U.ID = '".get_current_user_id()."')
	INNER JOIN {$wpdb->prefix}usermeta UM ON (UM.user_id = U.ID && UM.meta_key = 'first_name')
	INNER JOIN {$wpdb->prefix}usermeta UM2 ON (UM2.user_id = U.ID && UM2.meta_key = 'last_name')
	INNER JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'cert_location')
	INNER JOIN {$wpdb->prefix}postmeta PM3 ON (PM3.post_id = P.ID && PM3.meta_key = 'cert_date')
	WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' 
	ORDER BY PM3.meta_value DESC" 
);

echo '<h2>Мои сертификаты</h2>';

if($aData){?>
	<style>
	.list-certificates{margin:10px 0;}
	.list-certificates table{width:100%;border-spacing:0;}
	.list-certificates table th,.list-certificates table td{padding:10px;border:1px solid;}
	</style>

	<div class='list-certificates'>
		<table>
			<tr>
				<th>Номер сертификата</th>
				<th>Местоположение</th>
				<th>Дата получения</th>
			</tr>
		<?foreach($aData as $oRow){
			$aLocation = unserialize($oRow->cert_location);
			?>
			<tr>
				<td><a href="<?=get_the_permalink($oRow->ID)?>"><?=str_pad($oRow->ID, 10, 0, STR_PAD_LEFT)?></a></td>
				<td><?=($aLocation['address'])?></td>
				<td><?=date('d.m.Y', strtotime($oRow->cert_date))?></td>
			</tr>
		<?}?>
		</table>
	</div>
<?}else{?>
	У вас ещё нет сертификатов.
<?}?>