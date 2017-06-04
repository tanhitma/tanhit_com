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

if ( $downloads = WC()->customer->get_downloadable_products() ) : ?>

	<?php do_action( 'woocommerce_before_available_downloads' ); ?>

	<h2><?php echo apply_filters( 'woocommerce_my_account_my_downloads_title', __( 'Available Downloads', 'woocommerce' ) ); ?></h2>

	<ul class="digital-downloads offer-block">
		<?php foreach ( $downloads as $download ) : ?>
			<li data-product="<?php echo $download[ 'product_id' ]; ?>">
				<?php
					do_action( 'woocommerce_available_download_start', $download );

					if ( is_numeric( $download['downloads_remaining'] ) )
						echo apply_filters( 'woocommerce_available_download_count', '<span class="count">' . sprintf( _n( '%s download remaining', '%s downloads remaining', $download['downloads_remaining'], 'woocommerce' ), $download['downloads_remaining'] ) . '</span> ', $download );

					echo apply_filters( 'woocommerce_available_download_link', '<a href="' . esc_url( $download['download_url'] ) . '">' . $download['download_name'] . '</a>', $download );

					do_action( 'woocommerce_available_download_end', $download );
				?>
			</li>
		<?php endforeach; ?>
		<?php do_action( 'tanhit_free_download_products' ); ?>
	</ul>

	<?php do_action( 'woocommerce_after_available_downloads' ); ?>

<?php else: ?>

	<h2><?php echo apply_filters( 'woocommerce_my_account_my_downloads_title', __( 'Available Downloads', 'woocommerce' ) ); ?></h2>
	
	<ul class="digital-downloads offer-block">
		<?php do_action( 'tanhit_free_download_products' ); ?>
	</ul>

<?php endif; ?>

<link href="/wp-content/themes/tanhit/js/videojs/video-js.min.css" rel="stylesheet">
<script type="text/javascript" src="/wp-content/themes/tanhit/js/videojs/video.min.js"></script>
<script type="text/javascript" src="/wp-content/themes/tanhit/js/videojs/youtube.min.js"></script>
<script type="text/javascript" src="/wp-content/themes/tanhit/js/videojs/video_init.js"></script>

<style>
	.vid_player2{height:400px;margin-top:-200px;width:600px;margin-left:-300px;}
</style>
<script>
	//window.onload = function(){
		/*videos = document.querySelectorAll("video");
		for (var i = 0, l = videos.length; i < l; i++) {
			var video = videos[i];
			var src = video.src || (function () {
				var sources = video.querySelectorAll("source");
				for (var j = 0, sl = sources.length; j < sl; j++) {
					var source = sources[j];
					var type = source.type;
					var isMp4 = type.indexOf("mp4") != -1;
					if (isMp4) return source.src;
				}
				return null;
			})();
			if (src) {
				var isYoutube = src && src.match(/(?:youtu|youtube)(?:\.com|\.be)\/([\w\W]+)/i);
				if (isYoutube) {
					var id = isYoutube[1].match(/watch\?v=|[\w\W]+/gi);
					id = (id.length > 1) ? id.splice(1) : id;
					id = id.toString();
					var mp4url = "http://www.youtubeinmp4.com/redirect.php?video=";
					video.src = mp4url + id;
				}
			}
		}*/
	//}
	
	/* Player */
	jQuery('.show-video').click(function() {
		var pContainer = jQuery(jQuery(jQuery(this).attr("href"))[0]);
		pContainer.show();
	});

	jQuery('.show_vid').click(function() {
		if (jQuery(this).find('.video-js').length){
			var plId = jQuery(this).find('.video-js').attr('id');
			var player = videojs(plId);
			player.pause();
		}

		jQuery(this).hide();
		jQuery(".flowplayer").each(function () {
			$(this).data("flowplayer").stop();
		});
	}).children().click(function(e) {
		return false;
	});
</script>
