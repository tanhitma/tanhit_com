<?php
session_start ();

function getCenterCoord($coord)
{
    $centroid = array_reduce( $coord, function ($x,$y) use ($coord) {
        $len = count($coord);
        return [$x[0] + $y[0]/$len, $x[1] + $y[1]/$len];
    }, array(0,0));
    return $centroid;
}

$aInnerTable = array();
$aWhere = array();
$aFilterWhere = array();

$aPractikaFilter = array();
$aStatusFilter = array();

if (isset($atts['my']) && $atts['my']){
	$aWhere[] = "U.ID = '".get_current_user_id()."'";
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
	GROUP BY P.ID";
	
$aData = $wpdb->get_results( $sQuery );

$aCertData = $aCoordData = array();
//Вычисляем центр координат
foreach ($aData as $oRow){
	$aLocation = unserialize($oRow->cert_location);
	
	$aCoordData[] = array($aLocation['lat'], $aLocation['lng']);
}

$aCenterCoord = getCenterCoord($aCoordData);
?>

<section style="min-height: 300px">
	<div id='map-certificates'>

		<?if($aData){?>
			<div id="map" style='width:100%;height:400px;'></div>
			<script>
				// The following example creates complex markers to indicate beaches near
				// Sydney, NSW, Australia. Note that the anchor is set to (0,32) to correspond
				// to the base of the flagpole.
				
				function initMap() {
				  var map = new google.maps.Map(document.getElementById('map'), {
					zoom: 3,
					center: {lat: <?=$aCenterCoord[0]?>, lng: <?=$aCenterCoord[1]?>}
				  });

				  setMarkers(map);
				}

				var beaches = [];
				<?foreach ($aData as $oRow){
					$aLocation = unserialize($oRow->cert_location);?>
					
					beaches.push(['<?="{$oRow->cert_user_name} - ".str_pad($oRow->ID, 10, 0, STR_PAD_LEFT)?>', <?=$aLocation['lat']?>, <?=$aLocation['lng']?>]);
				<?}?>

				function setMarkers(map) {
				  // Adds markers to the map.
				
				  for (var i = 0; i < beaches.length; i++) {
					var beach = beaches[i];
					new google.maps.Marker({
					  position: {lat: beach[1], lng: beach[2]},
					  map: map,
					  //icon: image,
					  //shape: shape,
					  title: beach[0],
					  zIndex: beach[3]
					});
				  }
				}
			</script>
			<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIf-8uF1c86zFX_ElUI8PKv9lQVS_n3wM&callback=initMap"></script>
		<?}?>
	</div>
</section>