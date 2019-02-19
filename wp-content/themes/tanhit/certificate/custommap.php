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

$aData = get_posts(array(
	'numberposts' 	=> -1, 
	'post_type' 	=> $atts['post_type']
));


if($aData){	
	$aCoordData = array();
	//Вычисляем центр координат
	foreach ($aData as $oRow){
		$cert_location 	= get_field('cert_location', $oRow->ID);
		
		$aCoordData[] = array($cert_location['lat'], $cert_location['lng']);
	}

	$aCenterCoord = getCenterCoord($aCoordData);
?>

	<style>
		.gm-style .gmnoprint{border-radius:50px;}
	</style>
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
				}

				var beaches = [];
				<?foreach ($aData as $oRow){
					$cert_location 	= get_field('cert_location', $oRow->ID);
					$cert_user 		= get_field('cert_user', $oRow->ID);
					
					$aAvatar = wp_get_attachment_image_src( get_user_meta($cert_user['ID'], 'wp_user_avatar', true) );
				?>
					
					beaches.push(['<?=trim("{$cert_user['user_lastname']} {$cert_user['user_firstname']}")?>', '<?=$cert_location['lat']?>', '<?=$cert_location['lng']?>', '<?=($cert_user['ID'])?>', '<?=$aAvatar[0]?>']);
				<?}?>

				function setMarkers(map) {
					var markers = [];
					
					// Adds markers to the map.
					for (var i = 0; i < beaches.length; i++) {
						var beach = beaches[i];
						
						var latLng = new google.maps.LatLng(parseFloat(beach[1]), parseFloat(beach[2]));
						
						if (beach[4]){
							var icon = {
								url: beach[4], // url
								scaledSize: new google.maps.Size(30, 30), // scaled size
								origin: new google.maps.Point(0,0), // origin
								anchor: new google.maps.Point(0, 0) // anchor
							};
						}else{
							var icon = {};
						}
						

						var marker = new google.maps.Marker({
							position: latLng,
							map: map,
							//shape: shape,
							title: beach[0],
							//zIndex: beach[3],
							icon: icon,
							url: (beach[3] ? '/users/'+beach[3] : ''),
						});
					
						google.maps.event.addListener(marker, 'click', function() {
							if (this.url){
								window.open(this.url,'_blank'); 
								return false;
							}
						});
						

						markers.push(marker);
					}
					
					// Add a marker clusterer to manage the markers.
					var markerCluster = new MarkerClusterer(map, markers,{
						imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
						gridSize: 50, 
						maxZoom: 15
					});
				}
				</script>
				<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
				<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIf-8uF1c86zFX_ElUI8PKv9lQVS_n3wM&callback=initMap"></script>
			<?}?>
		</div>
	</section>
<?}?>