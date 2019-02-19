<?php 
$user_id = get_query_var('user_id');
$user_info = get_userdata($user_id);
//Получаем максимальный статус имеющегося сертификата у пользователя
$iCertStatusMax = getUserStatus($user_id);

if ($user_info && $iCertStatusMax){
	$aAvatar = wp_get_attachment_image_src( get_user_meta($user_id, 'wp_user_avatar', true) );

	get_header();
	
	$aUserExtra = get_user_meta($user_id, 'user_extra', true);
	$aUserSocial = get_user_meta($user_id, 'user_social',true);
	$sUserExtraAdress1 = get_user_meta($user_id, 'user_extra_adress1',true);
	$sUserExtraAdress2 = get_user_meta($user_id, 'user_extra_adress2',true);
	?>
	
	<style>
		.user-social{margin:0;padding:0;}
		.user-social li{margin:5px 0;font-size:20px;display:inline-block;margin-right:10px;}
		.user-social li span{background:url('/wp-content/themes/tanhit/images/social_sprite.png') no-repeat;width:45px;height:45px;display:block;}
		.user-social li.user-social-in span{background-position:-55px -57px;}
		.user-social li.user-social-fb span{background-position:-169px -5px;}
		.user-social li.user-social-vk span{background-position:-225px -5px;}
		.user-social li.user-social-ok span{background-position:-169px -57px;}
		.user-social li.user-social-youtube span{background-position:0 -108px;}
		.user-social li.user-social-google span{background-position:-55px -5px;}
	</style>
	
	<section style="min-height: 300px">
		<div class="container">
			<div class="content">
				<div class="row">
					<div class="col-sm-12">
						<br />
						
						<table style='width:100%;'>
							<tr>
								<?if($aAvatar[0]){?>
								<td style='width:100px;padding:5px 5px 5px 0;'>
									<div style='width:100px;height:100px;line-height:100px;text-align;center;vertical-align:middle;'>
										<img style='max-width:100%;max-height:100%;' src='<?=$aAvatar[0]?>' />
									</div>
								</td>
								<td style='padding:5px;'>
								<?}else{?>
								<td style='padding:5px 5px 5px 0;'>
								<?}?>
									<div style='text-decoration:underline;'><?=(trim($user_info->first_name.' '.$user_info->last_name))?></div>
									<?if($val = get_user_meta($user_id, 'description', true)){?>
										<br /><div style='font-style:italic;'><?=$val?></div>
									<?}?>
								</td>
								<td style='width:100px;padding:5px 0 5px 5px;text-align:right;'>
									<strong><?=(220==$iCertStatusMax ? 'ВЕДУЩИЙ' : 'МАСТЕР')?></strong>
								</td>
							</tr>
						</table>
						
						<?if($aUserExtra['phone'] || $aUserExtra['email'] || $aUserExtra['site']){?>
							<table style='width:100%;border-spacing:0;' border='1' cellspacing='0' cellpadding='0'>
								<tr>
									<td style='text-align:center;padding:5px;'>Телефон</td>
									<td style='text-align:center;padding:5px;'>E-mail</td>
									<td style='text-align:center;padding:5px;'>Сайт</td>
								</tr>
								<tr>
									<td style='text-align:center;padding:5px;'><?=$aUserExtra['phone']?></td>
									<td style='text-align:center;padding:5px;'><?=$aUserExtra['email']?></td>
									<td style='text-align:center;padding:5px;'><?=$aUserExtra['site']?></td>
								</tr>
							</table>
						<?}?>
						
						<?if($aUserExtra['anons']){?>
							<br /><div><div><span style='text-decoration:underline;'>Анонс мероприятий:</div><?=str_replace("\r\n", '<br />', $aUserExtra['anons'])?></div>
						<?}?>
						
						<?if($sUserExtraAdress1){?>
							<br /><div><div><span style='text-decoration:underline;'>Адрес 1:</div><?=$sUserExtraAdress1?></div>
						<?}?>
						
						<?if($sUserExtraAdress2){?>
							<br /><div><div><span style='text-decoration:underline;'>Адрес 2:</div><?=$sUserExtraAdress2?></div>
						<?}?>
						
						<?if($aUserSocial){?>
							<br /><div style='text-decoration:underline;'>Я в социальных сетях:</div>
							<ul class='user-social'>
								<?foreach($aUserSocial as $sKeySocial => $sItemSocial){?>
									<li class='user-social-<?=$sKeySocial?>'><a href='<?=$sItemSocial?>' target='_blank'><span></span></a></li>
								<?}?>
							</ul>
						<?}?>

						<?php echo do_shortcode("[cert_list user_id={$user_id} id_list=list]");?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php get_footer(); ?>
<?}else{
	force404();
}?>