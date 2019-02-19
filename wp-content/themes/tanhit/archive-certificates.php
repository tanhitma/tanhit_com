<?php
session_start ();

get_header(); 

$sort = 'id';
if (isset($_POST['sort']) && $_POST['sort']){
	$sort = $_POST['sort'];
	$_SESSION['cert_sort'] = $sort;
}

if(isset($_SESSION['cert_sort'])){
	$sort = $_SESSION['cert_sort'];
}

$order = 'asc';
if (isset($_POST['order']) && $_POST['order']){
	$order = $_POST['order'];
	$_SESSION['cert_order'] = $order;
}

if(isset($_SESSION['cert_order'])){
	$order = $_SESSION['cert_order'];
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
);
?>

<?if($aData){?>
	<style>
	#archive-certificates .list-certificates{margin:10px 0;}
	#archive-certificates .list-certificates table{width:100%;border-spacing:0;}
	#archive-certificates .list-certificates table th,#archive-certificates table td{padding:10px;border:1px solid;}
	</style>

	<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
	<section style="min-height: 300px">
		<div id='archive-certificates' class="container">
			<h1>Выданные сертификаты</h1>
			
			<div id="map" style='width:100%;height:400px;'></div>
			
			<div class="content">
				<div class='list-certificates'>
					<div style='padding:10px 0;'>
						<form method='post'>
							сертификат: 
							<select name='sort' onChange='jQuery(this).parent().submit()'>
								<option value='0'<?=('asc' == $order ? ' selected="selected"': '')?>>все</option>
								<option value='id'<?=('id' == $sort ? ' selected="selected"': '')?>>по номеру</option>
								<option value='name'<?=('name' == $sort ? ' selected="selected"': '')?>>по имени</option>
								<option value='date'<?=('date' == $sort ? ' selected="selected"': '')?>>по дате выдачи</option>
							</select>
							статус: 
							<select name='order' onChange='jQuery(this).parent().submit()'>
								<option value='0'<?=('asc' == $order ? ' selected="selected"': '')?>>все</option>
								<option value='asc'<?=('asc' == $order ? ' selected="selected"': '')?>>по возрастанию</option>
								<option value='desc'<?=('desc' == $order ? ' selected="selected"': '')?>>по убыванию</option>
							</select>
							
							<div style='float:right;'>
								сортировка: 
								<select name='sort' onChange='jQuery(this).parent().submit()'>
									<option value='id'<?=('id' == $sort ? ' selected="selected"': '')?>>по номеру</option>
									<option value='name'<?=('name' == $sort ? ' selected="selected"': '')?>>по имени</option>
									<option value='date'<?=('date' == $sort ? ' selected="selected"': '')?>>по дате выдачи</option>
								</select>
								<select name='order' onChange='jQuery(this).parent().submit()'>
									<option value='asc'<?=('asc' == $order ? ' selected="selected"': '')?>>по возрастанию</option>
									<option value='desc'<?=('desc' == $order ? ' selected="selected"': '')?>>по убыванию</option>
								</select>
							</div>
						</form>
					</div>
					<table>
						<tr>
							<th>Номер сертификата</th>
							<th>Имя участника</th>
							<th>Местоположение</th>
							<th>Дата получения</th>
						</tr>
					<?foreach($aData as $oRow){
						$aLocation = unserialize($oRow->cert_location);
						?>
						<tr>
							<td><a href="<?=get_the_permalink($oRow->ID)?>"><?=str_pad($oRow->ID, 10, 0, STR_PAD_LEFT)?></a></td>
							<td><?=($oRow->cert_user_name ? $oRow->cert_user_name : 'имя отсуствует')?></td>
							<td><?=($aLocation['address'])?></td>
							<td><?=date('d.m.Y', strtotime($oRow->cert_date))?></td>
						</tr>
					<?}?>
					</table>
				</div>


				<div class="pagination">
					<?php echo paginate_links(); ?>
				</div>

			</div>
		</div>
	</section>

	<?$aLocation = unserialize($aData[0]->cert_location);?>
	<script>
		// The following example creates complex markers to indicate beaches near
		// Sydney, NSW, Australia. Note that the anchor is set to (0,32) to correspond
		// to the base of the flagpole.
		
		function initMap() {
		  var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 4,
			center: {lat: <?=$aLocation['lat']?>, lng: <?=$aLocation['lng']?>}
		  });

		  setMarkers(map);
		}

		var beaches = [];
		<?foreach ($aData as $oRow){
			$aLocation = unserialize($oRow->cert_location);?>
			
			beaches.push(['<?="{$oRow->IDcert_user_name} - ".str_pad($oRow->ID, 10, 0, STR_PAD_LEFT)?>', <?=$aLocation['lat']?>, <?=$aLocation['lng']?>]);
		<?}?>

		function setMarkers(map) {
		  // Adds markers to the map.

		  // Marker sizes are expressed as a Size of X,Y where the origin of the image
		  // (0,0) is located in the top left of the image.

		  // Origins, anchor positions and coordinates of the marker increase in the X
		  // direction to the right and in the Y direction down.
		  /*var image = {
			url: 'images/beachflag.png',
			// This marker is 20 pixels wide by 32 pixels high.
			size: new google.maps.Size(20, 32),
			// The origin for this image is (0, 0).
			origin: new google.maps.Point(0, 0),
			// The anchor for this image is the base of the flagpole at (0, 32).
			anchor: new google.maps.Point(0, 32)
		  };*/
		  
		  // Shapes define the clickable region of the icon. The type defines an HTML
		  // <area> element 'poly' which traces out a polygon as a series of X,Y points.
		  // The final coordinate closes the poly by connecting to the first coordinate.
		  /*var shape = {
			coords: [1, 1, 1, 20, 18, 20, 18, 1],
			type: 'poly'
		  };*/
		  
		  for (var i = 0; i < beaches.length; i++) {
			var beach = beaches[i];
			var marker = new google.maps.Marker({
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

<?php get_footer(); ?>
