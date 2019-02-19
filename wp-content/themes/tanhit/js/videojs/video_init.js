var wpmVideo = {
    initYT : function (videoId, link) {
		/*videojs(videoId).ready(function() {
			var player = this;
			player.src({type: 'video/youtube', src: link});
			console.log(link);
		});*/

    }
};
jQuery(function () {
	jQuery('.video-js').each(function() {
		videojs(this);
	});
});