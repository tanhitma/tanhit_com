<?php

//Получаем максимальный статус имеющегося сертификата у пользователя
$iCertStatusMax = getUserStatus();

?>

<div class='content'>
	<div style='clear:both;'></div>
	<h2 style='float:left;'>Сертификаты учеников</h2>
	<button style='float:right;margin:20px 0px 0 0;' type='button' onClick='get_cert_archive()'>Сформировать и выслать архив на почту</button>
	<div style='clear:both;'></div>
	
	<?php echo do_shortcode('[cert_list manager=1'.(220 == $iCertStatusMax ? '' : ' full=1').' filter=1 sort=1 id_list=manager]');?>
</div>

<script>
	function get_cert_archive(){
		jQuery.ajax({
		  method: "POST",
		  url: "<?=admin_url('admin-ajax.php')?>",
		  data: { action: "get_cert_archive"}
		}).done(function( msg ) {
			//console.log(msg);
		});
		  
		alert('В ближайшее время ссылка на архив будет отправлена на вашу почту');
		  
		return false;
	}
</script>