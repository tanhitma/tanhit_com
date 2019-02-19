<?php
/**
 * @package Tanhit
 * @see for example https://www.youtube.com/embed/CA6pdQkFGG0?autoplay=1
 * @see videoID = 'CA6pdQkFGG0'
 */
?>
<?php
//$vid_data = explode("CA6pdQkFGG0",$video_id[0]);
//$vid_id = array_pop($vid_data); // Yoube video ID

//$vid_data = explode("/",$video_id[0]);
//$vid_id = array_pop($vid_data); // Yoube video ID
//$vid_id = substr($vid_id, 8);
//$vid_id = $video_id[1];
?>

<div id="player"><iframe width="480" height="300" src="<?=$free_featured_video?>?autoplay=0" frameborder="0" allowfullscreen vloume="0"></iframe></div>
<script>
  /*
	// 2. This code loads the IFrame Player API code asynchronously.
	var tag = document.createElement('script');
	tag.src = "https://www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

	var player;
	function onYouTubeIframeAPIReady() {
		player = new YT.Player('player', {
			height: '300px',
			width: '480px',
			playerVars: {
				autoplay: 1,
				loop: 1,
				controls: 1,
				showinfo: 0,
				autohide: 1,
				modestbranding: 1,
				vq: 'hd1080'},
			videoId: '<?php echo $vid_id; ?>',
			events: {
				'onReady': onPlayerReady,
				'onStateChange': onPlayerStateChange
			}


		});
	}

	// 4. The API will call this function when the video player is ready.
	function onPlayerReady(event) {
		event.target.playVideo();
		player.mute();
	}

	var done = false;
	function onPlayerStateChange(event) {

	}
	function stopVideo() {
		player.stopVideo();
	}
  */
</script>