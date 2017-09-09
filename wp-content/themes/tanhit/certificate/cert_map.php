<?php

if ( ! function_exists('getCenterCoord')){
	function getCenterCoord($coord)
	{
		$centroid = array_reduce( $coord, function ($x,$y) use ($coord) {
			$len = count($coord);
			return [$x[0] + $y[0]/$len, $x[1] + $y[1]/$len];
		}, array(0,0));
		return $centroid;
	}
}

$aInnerTable = array();
$aWhere = array();
$aFilterWhere = array();

if ( ! $atts['id_list'] ){
	$atts['id_list'] = get_the_ID();
}

$aPractikaFilter = array();
$aStatusFilter = array();

if (isset($atts['my']) && $atts['my']){
	$aWhere[] = "U.ID = '".get_current_user_id()."'";
}
if (isset($atts['user_id']) && $atts['user_id']){
	$aWhere[] = "U.ID = '{$atts['user_id']}'";
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
			$aWhere[] = "UM_PRACTIKA.meta_value IN (".implode(',',$aAttrPractika).")";
			
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
	$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_PRACTIKA_F ON (UM_PRACTIKA_F.terms_id = TR.term_taxonomy_id && UM_PRACTIKA_F.meta_key = 'cert_practika')";
	$aWhere[] = "UM_PRACTIKA_F.meta_value = '{$cert_practika}'";
}
if ($cert_status){
	$aInnerTable[] = "LEFT JOIN {$wpdb->prefix}termsmeta UM_STATUS_F ON (UM_STATUS_F.terms_id = TR.term_taxonomy_id && UM_STATUS_F.meta_key = 'cert_status')";
	$aWhere[] = "UM_STATUS_F.meta_value = '{$cert_status}'";
}
//\Фильтр

$sQuery = "
	SELECT P.*, U.id as user_id, TR.term_taxonomy_id as cert_type, TRIM(CONCAT(UM.meta_value,' ',UM2.meta_value)) as cert_user_name, 
	PM2.meta_value as cert_location, PM4.meta_value as cert_location_2, PM3.meta_value as cert_date, UM3.meta_value as user_extra_adress1, UM4.meta_value as user_extra_adress2  
	FROM {$wpdb->prefix}posts P 
	INNER JOIN {$wpdb->prefix}postmeta PM ON (PM.post_id = P.ID && PM.meta_key = 'cert_user')
	INNER JOIN {$wpdb->prefix}users U ON (U.ID = PM.meta_value)
	INNER JOIN {$wpdb->prefix}usermeta UM ON (UM.user_id = U.ID && UM.meta_key = 'first_name')
	LEFT JOIN {$wpdb->prefix}usermeta UM2 ON (UM2.user_id = U.ID && UM2.meta_key = 'last_name')
	LEFT JOIN {$wpdb->prefix}postmeta PM2 ON (PM2.post_id = P.ID && PM2.meta_key = 'cert_location')
	LEFT JOIN {$wpdb->prefix}postmeta PM4 ON (PM4.post_id = P.ID && PM4.meta_key = 'cert_location_2')
	LEFT JOIN {$wpdb->prefix}postmeta PM3 ON (PM3.post_id = P.ID && PM3.meta_key = 'cert_date')
	LEFT JOIN {$wpdb->prefix}usermeta UM3 ON (UM3.user_id = U.ID && UM3.meta_key = 'user_extra_adress1')
	LEFT JOIN {$wpdb->prefix}usermeta UM4 ON (UM4.user_id = U.ID && UM4.meta_key = 'user_extra_adress2')
	INNER JOIN {$wpdb->prefix}term_relationships TR ON (TR.object_id = P.ID)
	".($aInnerTable ? implode(' ',$aInnerTable) : '')."
	WHERE (PM2.meta_value!='' || UM3.meta_value!='') && P.post_type = 'certificates' && P.`post_status` = 'publish'".($aWhere ? ' && ('.implode(' && ', $aWhere).')' : '').($aFilterWhere ? ' && '.implode(' && ', $aFilterWhere) : '')."  
	GROUP BY P.ID";

$aData = $wpdb->get_results( $sQuery );


if($aData){	
	$aCertData = $aCoordData = array();
	//Вычисляем центр координат
	foreach ($aData as $oRow){
		$aLocation = unserialize($oRow->cert_location);
		
		$aCoordData[] = array($aLocation['lat'], $aLocation['lng']);
		
		if( ! empty($oRow->cert_location_2)){
			$aLocation2 = unserialize($oRow->cert_location_2);
			
			$aCoordData[] = array($aLocation2['lat'], $aLocation2['lng']);
		}
	}

	$aCenterCoord = getCenterCoord($aCoordData);
?>

	<section style="min-height: 300px">
		<div id='map-certificates'>

			<?if($aData){?>
				<div id="map" style='width:100%;height:400px;'></div>
				<script>
				function initMap() {
					var map = new google.maps.Map(document.getElementById('map'), {
						zoom: 3,
						center: {lat: <?=$aCenterCoord[0]?>, lng: <?=$aCenterCoord[1]?>}
					});
					
					setMarkers(map);
					
					//Set markers by adress
					var geocoder = new google.maps.Geocoder();
					
					//Выполняется асинхронно, т.е. переменная beach меняется быстрее чем происходит возврат координат, таким образом - происходит что, данная переменная одинакова на несколько записей
					<?foreach ($aData as $oRow){
						$iCertStatusMax = getUserStatus($oRow->user_id);
					?>
						<?if($oRow->user_extra_adress1){?>
							geocoder.geocode({'address': '<?=$oRow->user_extra_adress1?>'}, function(results, status) {
								if (status === 'OK' && results[0].geometry.location) {
									var marker = new google.maps.Marker({
										position: results[0].geometry.location,
										map: map,
										//shape: shape,
										title: '<?="{$oRow->cert_user_name} - ".str_pad($oRow->ID, 10, 0, STR_PAD_LEFT)?>',
										//zIndex: beach[3],
										<?if (isset($atts['icon_img']) && $atts['icon_img']){?>
											icon: '/wp-content/themes/tanhit/images/gmap-label-icon/<?=$atts['icon_img']?>',
										<?}?>
										<?if($iCertStatusMax){?>
											url: '/users/'+'<?=$oRow->user_id?>',
										<?}?>
									});
									
									google.maps.event.addListener(marker, 'click', function() {
										if (this.url){
											window.open(this.url,'_blank'); 
											return false;
										}
									});
								}
							});
						<?}?>
						
						<?if($oRow->user_extra_adress2){?>
							geocoder.geocode({'address': '<?=$oRow->user_extra_adress2?>'}, function(results, status) {
								if (status === 'OK' && results[0].geometry.location) {
									var marker = new google.maps.Marker({
										position: results[0].geometry.location,
										map: map,
										//shape: shape,
										title: '<?="{$oRow->cert_user_name} - ".str_pad($oRow->ID, 10, 0, STR_PAD_LEFT)?>',
										//zIndex: beach[3],
										<?if (isset($atts['icon_img']) && $atts['icon_img']){?>
											icon: '/wp-content/themes/tanhit/images/gmap-label-icon/<?=$atts['icon_img']?>',
										<?}?>
										<?if($iCertStatusMax){?>
											url: '/users/'+'<?=$oRow->user_id?>',
										<?}?>
									});
									
									google.maps.event.addListener(marker, 'click', function() {
										if (this.url){
											window.open(this.url,'_blank'); 
											return false;
										}
									});
								}
							});
						<?}?>
					<?}?>
				}

				var beaches = [];
				<?foreach ($aData as $oRow){
					$aLocation = unserialize($oRow->cert_location);
					
					$iCertStatusMax = getUserStatus($oRow->user_id);
					?>
					
					<?if( ! $oRow->user_extra_adress1){?>
							beaches.push(['<?="{$oRow->cert_user_name} - ".str_pad($oRow->ID, 10, 0, STR_PAD_LEFT)?>', '<?=$aLocation['lat']?>', '<?=$aLocation['lng']?>', '<?=($iCertStatusMax ? $oRow->user_id : '')?>']);
					<?}?>
					
					<?if( ! empty($oRow->cert_location_2)){
						$aLocation2 = unserialize($oRow->cert_location_2);?>
							
						<?if( ! $oRow->user_extra_adress2){?>
							beaches.push(['<?="{$oRow->cert_user_name} - ".str_pad($oRow->ID, 10, 0, STR_PAD_LEFT)?>', '<?=$aLocation2['lat']?>', '<?=$aLocation2['lng']?>', '<?=($iCertStatusMax ? $oRow->user_id : '')?>']);
						<?}?>
					<?}?>
				<?}?>


				function setMarkers(map) {
					// Adds markers to the map.
					for (var i = 0; i < beaches.length; i++) {
						var beach = beaches[i];
						
						var latLng = new google.maps.LatLng(parseFloat(beach[1]), parseFloat(beach[2]));
						
						var marker = new google.maps.Marker({
							position: latLng,
							map: map,
							//shape: shape,
							title: beach[0],
							//zIndex: beach[3],
							<?if (isset($atts['icon_img']) && $atts['icon_img']){?>
								icon: '/wp-content/themes/tanhit/images/gmap-label-icon/<?=$atts['icon_img']?>',
							<?}?>
							url: (beach[3] ? '/users/'+beach[3] : ''),
						});
					
						google.maps.event.addListener(marker, 'click', function() {
							if (this.url){
								window.open(this.url,'_blank'); 
								return false;
							}
						});
					}
				}
				</script>
				<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIf-8uF1c86zFX_ElUI8PKv9lQVS_n3wM&callback=initMap"></script>
			<?}?>
		</div>
	</section>
<?}?>