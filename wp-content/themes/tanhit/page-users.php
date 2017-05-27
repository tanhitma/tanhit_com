<?php 
$user_id = get_query_var('user_id');
$user_info = get_userdata($user_id);
//Получаем максимальный статус имеющегося сертификата у пользователя
$iCertStatusMax = getUserStatus($user_id);

if ($user_info && $iCertStatusMax){
	$aAvatar = wp_get_attachment_image_src( get_user_meta($user_id, 'wp_user_avatar', true) );

	get_header();
	?>
	
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