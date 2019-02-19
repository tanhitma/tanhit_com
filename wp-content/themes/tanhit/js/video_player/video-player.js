/**
 * Interface JS functions
 *
 * @since 1.0.0
 *
 * @package Tanhit
 * @subpackage my-account
 */
/*jslint browser: true*/
/*global jQuery, console*/
var fancyboxModule = (function($, undefined) {
	indexVid = function() {
		var vidOpener = $(".vidOpener"),
			videoTag = vidOpener.siblings(".videoTag").html(),
			video;

		if (vidOpener.length) {
			vidOpener.fancybox({
				content: videoTag,
				padding:0,
			    helpers: {
			        overlay : {
			            locked : false,
			            css : {
			                'background' : 'rgba(0, 0, 0, 0.8)'
			            }
			        }
			    },
			    beforeShow: function() {
			    	video = $('.fancybox-inner').find("video").get(0);
			    	video.load();
			    	video.play();
			    }
			});
		}
	};
	
	return {
		init: function() {
            indexVid();
		}
	}
})(jQuery);

jQuery(function() {
	fancyboxModule.init();
});