<?php
session_start ();

$aInnerTable = array();
$aWhere = array();
$aFilterWhere = array();

if ( ! $atts['id_list'] ){
	$atts['id_list'] = get_the_ID();
}

//Сортировка
$sort = 'id';
if (isset($_POST['sort_'.$atts['id_list']]) && $_POST['sort_'.$atts['id_list']]){
	$sort = $_POST['sort_'.$atts['id_list']];
	$_SESSION['cert_sort_'.$atts['id_list']] = $sort;
}

if(isset($_SESSION['cert_sort_'.$atts['id_list']])){
	$sort = $_SESSION['cert_sort_'.$atts['id_list']];
}

$order = 'asc';
if (isset($_POST['order_'.$atts['id_list']]) && $_POST['order_'.$atts['id_list']]){
	$order = $_POST['order_'.$atts['id_list']];
	$_SESSION['cert_order_'.$atts['id_list']] = $order;
}

if(isset($_SESSION['cert_order_'.$atts['id_list']])){
	$order = $_SESSION['cert_order_'.$atts['id_list']];
}

$sFieldSort = '';
SWITCH($sort){
	case 'id':
		$sFieldSort = 'P.ID';
	break;
	
	case 'name':
		$sFieldSort = "TRIM(CONCAT(UM.meta_value,' ',UM2.meta_value))";
	break;
	
	case 'date':
		$sFieldSort = 'PM3.meta_value';
	break;
}

/*
$posts_per_page = 20;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$aData = $wpdb->get_results( "
	SELECT P.*, TRIM(CONCAT(UM.meta_value,' ',UM2.meta_value)) as cert_user_name, PM2.meta_value as cert_location, PM3.meta_value as cert_date 
	FROM {$wpdb->prefix}posts P 
	INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user')
	INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value)
	INNER JOIN {$wpdb->prefix}usermeta UM ON (UM.user_id = U.ID && UM.meta_key = 'first_name')
	INNER JOIN {$wpdb->prefix}usermeta UM2 ON (UM2.user_id = U.ID && UM2.meta_key = 'last_name')
	INNER JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'cert_location')
	INNER JOIN {$wpdb->prefix}postmeta PM3 ON (PM3.post_id = P.ID && PM3.meta_key = 'cert_date')
	WHERE P.post_type = 'certificates' && P.`post_status` = 'publish' 
	ORDER BY {$sFieldSort} {$order} 
	LIMIT ".($posts_per_page*($paged-1)).",{$posts_per_page}" 
);*/

$aStatuses = get_terms( 'certificate_status', array(
	'hide_empty' => false,
));

$aStatusesIDs = array();
if ($aStatuses){
	foreach($aStatuses as $aStatus){
		$aStatusesIDs[$aStatus->term_id] = $aStatus->name;
	}
}

$aPractika = get_terms( 'certificate_practika', array(
	'hide_empty' => false,
));

$aPractikaFilter = array();
$aStatusFilter = array();

if (isset($atts['my']) && $atts['my']){
	$aWhere[] = "U.ID = '".get_current_user_id()."'";
}

if (isset($atts['manager']) && $atts['manager']){
	$aInnerTable[] = "INNER JOIN {$wpdb->prefix}postmeta PM_MANAGER ON (PM_MANAGER.post_id = P.ID && PM_MANAGER.meta_key = 'cert_manager')";
	$aWhere[] = "PM_MANAGER.meta_value = '".get_current_user_id()."'";
}

if (isset($atts['practika']) && $atts['practika']){
	$atts['practika'] = trim($atts['practika']);
	$atts['practika'] = trim($atts['practika'],',');
	$atts['practika'] = trim($atts['practika']);
	
	if ($atts['practika']){
		$aAttrPractika = explode(',', $atts['practika']);
		if ($aAttrPractika){
			//$aWhere[] = "TR.term_taxonomy_id IN (".implode(',', $aAttrPractika).")";
			$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_PRACTIKA ON (UM_PRACTIKA.terms_id = TR.term_taxonomy_id && UM_PRACTIKA.meta_key = 'cert_practika')";
			$aWhere[] = "UM_PRACTIKA.meta_value IN (".implode(',',$aAttrStatuses).")";
			
			$aPractikaFilter = array_merge($aAttrPractika, $aPractikaFilter);
		}
	}
}
if (isset($atts['statuses']) && $atts['statuses']){
	$atts['statuses'] = trim($atts['statuses']);
	$atts['statuses'] = trim($atts['statuses'],',');
	$atts['statuses'] = trim($atts['statuses']);
	
	if ($atts['statuses']){
		$aAttrStatuses = explode(',', $atts['statuses']);
		if ($aAttrStatuses){
			$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_STATUS ON (UM_STATUS.terms_id = TR.term_taxonomy_id && UM_STATUS.meta_key = 'cert_status')";
			$aWhere[] = "UM_STATUS.meta_value IN (".implode(',',$aAttrStatuses).")";
			
			$aStatusFilter = array_merge($aAttrStatuses, $aStatusFilter);
		}
	}
}
if (isset($atts['practika_statuses']) && $atts['practika_statuses']){
	$atts['practika_statuses'] = trim($atts['practika_statuses']);
	$atts['practika_statuses'] = trim($atts['practika_statuses'],',');
	$atts['practika_statuses'] = trim($atts['practika_statuses']);
	
	if ($atts['practika_statuses']){
		$aSubWhere = array();
		
		$aAttrTStatuses = explode('|', $atts['practika_statuses']);
		if ($aAttrTStatuses){
			foreach ($aAttrTStatuses as $sItem){ 
				list($iPractikaId, $sStatus) = explode(':',$sItem);
				if ($iPractikaId && $sStatus){
					$aAttrStatuses = array();
					if ($sStatus){
						$aAttrStatuses = explode(',', $sStatus);
					}
					
					if($aAttrStatuses){
						/*$s_table_name 	= "UM_CERT_S_{$iPractikaId}";
						$s_meta_key 	= "certificate_type_{$iPractikaId}";
						
						$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}usermeta {$s_table_name} ON ({$s_table_name}.user_id = U.ID && {$s_table_name}.meta_key = '{$s_meta_key}')";
						$aSubWhere[] = "{$s_table_name}.meta_value IN (".implode(',',$aAttrStatuses).")";*/
						
						$p_table_name 	= "UM_PRACTIKA_G_{$iPractikaId}";
						$s_table_name 	= "UM_STATUS_G_{$iPractikaId}";
						
						$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta {$p_table_name} ON ({$p_table_name}.terms_id = TR.term_taxonomy_id && {$p_table_name}.meta_key = 'cert_practika')";
						$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta {$s_table_name} ON ({$s_table_name}.terms_id = TR.term_taxonomy_id && {$s_table_name}.meta_key = 'cert_status')";
						$aWhere[] = "({$p_table_name}.meta_value = '{$iPractikaId}' && {$s_table_name}.meta_value IN (".implode(',',$aAttrStatuses)."))";
					
						$aStatusFilter = array_merge($aAttrStatuses, $aStatusFilter);
					}
					
					$aPractikaFilter = array_merge(array($iPractikaId), $aPractikaFilter);
				}
			}
		}
		
		if($aSubWhere){
			$aWhere[] = '('.implode(' || ', $aSubWhere).')';
		}
	}
}


//Фильтр
$cert_practika = '';
if (isset($_POST['cert_practika_'.$atts['id_list']])){
	$cert_practika = $_POST['cert_practika_'.$atts['id_list']];
	$_SESSION['cert_practika_'.$atts['id_list']] = $cert_practika;
}

if(isset($_SESSION['cert_practika_'.$atts['id_list']])){
	$cert_practika = $_SESSION['cert_practika_'.$atts['id_list']];
}
//Сбрасываем практику
if ($aPractikaFilter && count($aPractikaFilter)==1){
	$cert_practika = '';
}

$cert_status = '';
if (isset($_POST['cert_status_'.$atts['id_list']])){
	$cert_status = $_POST['cert_status_'.$atts['id_list']];
	$_SESSION['cert_status_'.$atts['id_list']] = $cert_status;
}

if(isset($_SESSION['cert_status_'.$atts['id_list']])){
	$cert_status = $_SESSION['cert_status_'.$atts['id_list']];
}
//Сбрасываем статус
if ($aStatusFilter && count($aStatusFilter)==1){
	$cert_status = '';
}

if ($cert_practika){
	//$aFilterWhere[] = "TR.term_taxonomy_id = '{$cert_practika}'";	
	
	$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_PRACTIKA_F ON (UM_PRACTIKA_F.terms_id = TR.term_taxonomy_id && UM_PRACTIKA_F.meta_key = 'cert_practika')";
	$aWhere[] = "UM_PRACTIKA_F.meta_value = '{$cert_practika}'";
}
if ($cert_status){
	/*$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}usermeta UM_CERT_{$cert_status} ON (UM_CERT_{$cert_status}.user_id = U.ID && UM_CERT_{$cert_status}.meta_key LIKE 'certificate_type_%')";
	$aFilterWhere[] = "UM_CERT_{$cert_status}.meta_value = '{$cert_status}'";	*/
	
	$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_STATUS_F ON (UM_STATUS_F.terms_id = TR.term_taxonomy_id && UM_STATUS_F.meta_key = 'cert_status')";
	$aWhere[] = "UM_STATUS_F.meta_value = '{$cert_status}'";
}
//\Фильтр


$sQuery = "
	SELECT P.*, U.id as user_id, TR.term_taxonomy_id as cert_type, TRIM(CONCAT(UM.meta_value,' ',UM2.meta_value)) as cert_user_name, PM2.meta_value as cert_location, PM3.meta_value as cert_date 
	FROM {$wpdb->prefix}posts P 
	INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user')
	INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value)
	INNER JOIN {$wpdb->prefix}usermeta UM ON (UM.user_id = U.ID && UM.meta_key = 'first_name')
	LEFT JOIN {$wpdb->prefix}usermeta UM2 ON (UM2.user_id = U.ID && UM2.meta_key = 'last_name')
	INNER JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'cert_location')
	LEFT JOIN {$wpdb->prefix}postmeta PM3 ON (PM3.post_id = P.ID && PM3.meta_key = 'cert_date')
	INNER JOIN {$wpdb->prefix}term_relationships TR ON (TR.object_id = P.ID)
	".($aInnerTable ? implode(' ',$aInnerTable) : '')."
	WHERE P.post_type = 'certificates' && P.`post_status` = 'publish'".($aWhere ? ' && ('.implode(' && ', $aWhere).')' : '').($aFilterWhere ? ' && '.implode(' && ', $aFilterWhere) : '')."  
	GROUP BY P.ID 
	ORDER BY {$sFieldSort} {$order}";

$aData = $wpdb->get_results( $sQuery );
?>

<style>
	.list-certificates{margin:10px 0;}
	.list-certificates table{width:100%;border-spacing:0;}
	.list-certificates table th,.list-certificates table td{padding:10px;border:1px solid;}
</style>

<section>
	<div>		
		<div class='list-certificates'>
			<?if($atts['filter'] || $atts['sort']){?>
			<div style='padding:10px 0;'>
				<form method='post'>
					<?if($atts['filter']){?>
						<?if( ! $aPractikaFilter || count($aPractikaFilter)>1){?>
						практика: 
						<select style='width:150px;' name='cert_practika_<?=$atts['id_list']?>' onChange='jQuery(this).closest("form").submit()'>
							<option value='0'<?=( ! $cert_practika ? ' selected="selected"': '')?>>все</option>
							<?if($aPractika){?>
								<?foreach($aPractika as $aPractikaItem){?>
									<?if( ! $aPractikaFilter || in_array($aPractikaItem->term_id, $aPractikaFilter)){?>
										<option value='<?=$aPractikaItem->term_id?>'<?=($cert_practika == $aPractikaItem->term_id ? ' selected="selected"' : '')?>><?=$aPractikaItem->name?></option>
									<?}?>
								<?}?>
							<?}?>
						</select>
						<?}?>
					
						<?if( ! $aStatusFilter || count($aStatusFilter)>1){?>
						статус: 
						<select style='width:150px;' name='cert_status_<?=$atts['id_list']?>' onChange='jQuery(this).closest("form").submit()'>
							<option value='0'<?=( ! $cert_status ? ' selected="selected"': '')?>>все</option>
							<?if($aStatuses){?>
								<?foreach($aStatuses as $aStatus){?>
									<?if( ! $aStatusFilter || in_array($aStatus->term_id, $aStatusFilter)){?>
										<option value='<?=$aStatus->term_id?>'<?=($cert_status == $aStatus->term_id ? ' selected="selected"' : '')?>><?=$aStatus->name?></option>
									<?}?>
								<?}?>
							<?}?>
						</select>
						<?}?>
					<?}?>
					
					<?if($atts['sort']){?>
					<div style='float:right;'>
						сортировка: 
						<select style='width:150px;' name='sort_<?=$atts['id_list']?>' onChange='jQuery(this).closest("form").submit()'>
							<option value='id'<?=('id' == $sort ? ' selected="selected"': '')?>>по номеру</option>
							<option value='name'<?=('name' == $sort ? ' selected="selected"': '')?>>по имени</option>
							<option value='date'<?=('date' == $sort ? ' selected="selected"': '')?>>по дате выдачи</option>
						</select>
						<select style='width:150px;' name='order_<?=$atts['id_list']?>' onChange='jQuery(this).closest("form").submit()'>
							<option value='asc'<?=('asc' == $order ? ' selected="selected"': '')?>>по возрастанию</option>
							<option value='desc'<?=('desc' == $order ? ' selected="selected"': '')?>>по убыванию</option>
						</select>
					</div>
					<?}?>
				</form>
			</div>
			<hr style='margin:0 0 10px;' />
			<?}?>
			
			<?if($aData){?>
				<div>
					<table>
						<tr>
							<th>Номер сертификата</th>
							<?if($atts['full']){?>
							<th>Фото</th>
							<th>Информация</th>
							<?}else{?>
								<?if($atts['my']){?>
								<th>Информация</th>
								<?}else{?>
								<th>Имя участника</th>
								<?}?>
							<?}?>
							<th><?=($atts['column_location_title'] ? $atts['column_location_title'] : 'Местоположение')?></th>
							<th>Дата получения</th>
						</tr>
					<?foreach($aData as $oRow){
						$aLocation = unserialize($oRow->cert_location);
						
						if($atts['full'] || $atts['my']){
							$i_practika_id = wp_get_terms_meta($oRow->cert_type, 'cert_practika', true);
							$oDataTaxonomyT = get_term_by( 'term_taxonomy_id', $i_practika_id, 'certificate_praktica' );
							$sPractika = (isset($oDataTaxonomyT->name) ? $oDataTaxonomyT->name : '');
							
							$i_status_id = wp_get_terms_meta($oRow->cert_type, 'cert_status', true);
							$oDataTaxonomyT = get_term_by( 'term_taxonomy_id', $i_status_id, 'certificate_status' );
							$sStatus = (isset($oDataTaxonomyT->name) ? $oDataTaxonomyT->name : '');
							
							$user_info = get_userdata($oRow->user_id);
							$aAvatar = wp_get_attachment_image_src( get_user_meta($oRow->user_id, 'wp_user_avatar', true) );
						}
					?>
						<tr>
							<td><a href="<?=get_the_permalink($oRow->ID)?>"><?=str_pad($oRow->ID, 10, 0, STR_PAD_LEFT)?></a></td>
							
							<?if($atts['full']){?>
							<td>
								<div style='width:100px;height:100px;line-height:100px;text-align;center;vertical-align:middle;'>
									<img style='max-width:100%;max-height:100%;' src='<?=$aAvatar[0]?>' />
								</div>
							</td>
							<td>
								<div>Имя: <?=($oRow->cert_user_name ? $oRow->cert_user_name : 'отсуствует')?></div>
								<div>E-mail: <a href='mailto:<?=$user_info->data->user_email?>'><?=($user_info->data->user_email)?></a></div>
								<div>Практика: <?=$sPractika?></div>
								<div>Статус: <?=$sStatus?></div>
								<?if(get_user_meta($oRow->user_id, 'description', true)){?>
								<br />Описание:<br /><div style='font-style:italic;'><?=get_user_meta($oRow->user_id, 'description', true)?></div>
								<?}?>
							</td>
							<?}else{?>
								<?if($atts['my']){?>
									<td>
										<div>Практика: <?=$sPractika?></div>
										<div>Статус: <?=$sStatus?></div>
									</td>
								<?}else{?>
									<td>
										<div><?=($oRow->cert_user_name ? $oRow->cert_user_name : 'имя отсуствует')?></div>
									</td>
								<?}?>
							<?}?>
							
							<td><?=($aLocation['address'])?></td>
							<td><?=date('d.m.Y', strtotime($oRow->cert_date))?></td>
						</tr>
					<?}?>
					</table>
				</div>

				<?/*
				<div class="pagination">
					<?php echo paginate_links(); ?>
				</div>*/?>
			<?}?>

		</div>
	</div>
</section>