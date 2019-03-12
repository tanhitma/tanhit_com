/**
 * Interface JS functions
 *
 * @since 1.0.0
 *
 * @package Tanhit
 */
 
function wpguruLink() {
	var istS = 'Материал взят с сайта https://tanhit.com/ ТАНИТ – мастер интегрального знания об изначальной природе человека. Все права защищены © 2017.';
	var copyR = '';
	var body_element = document.getElementsByTagName('body')[0];
	var choose = window.getSelection();
	var myLink = document.location.href;
	/*var authorLink = "<br /><br />" + istS + ' ' + "<a href='"+myLink+"'>"+myLink+"</a>" + copyR + '';*/
	var authorLink = "<br /><br />" + istS;
	var copytext = choose + authorLink;
	var addDiv = document.createElement('div');
	addDiv.style.position='absolute';
	addDiv.style.left='-99999px';
	body_element.appendChild(addDiv);
	addDiv.innerHTML = copytext;
	choose.selectAllChildren(addDiv);
	window.setTimeout(function() {
		body_element.removeChild(addDiv);
	},0);
}
document.oncopy = wpguruLink;

/*jslint browser: true*/
/*global jQuery, console, TanhitFrontManager*/
jQuery(document).ready(function($) {
	"use strict";
	
	if ( typeof TanhitFrontManager !== 'undefined' ) {
		var api =  {
			iId: false,
			order: {},
			init: function() {
				if ( false !== TanhitFrontManager.duplKey && '' != TanhitFrontManager.duplKey ) {
					api.checkDupl();
				}
				api.setMailPoetPage();
			},
			getCart: function(){
				return TanhitFrontManager.cart;
			},
			checkDupl: function() {
				api.order['action'] = 'check_dupl';
				api.order['key'] 	= TanhitFrontManager.duplKey;
				api.iID 			= setInterval( api.timer, TanhitFrontManager.timerValue );
			},
			timer: function() {
				$.ajax({type:'POST', url:TanhitFrontManager.ajaxurl, data:{action:TanhitFrontManager.process_ajax, order:api.order}})
				.done(function (data) {
					if ( data.result == 'ok' ) {
						if ( data.isKey == 'forbidden' ) {
							window.location = window.location.origin + TanhitFrontManager.pathname_redir;
						}
					}	
				});
			},	
			setMailPoetPage: function() {
				if ( -1 == window.location.search.indexOf( 'wysija-page=') ) {
					return;	
				}	
				$( '#wysija-subscriptions .form-table tr' ).eq(0).css({'display':'none'});
				$( '#wysija-subscriptions .form-table tr' ).eq(1).css({'display':'none'});
				$( '#wysija-subscriptions .form-table tr' ).eq(2).css({'display':'none'});
			}
		};
		TanhitFrontManager = $.extend({}, TanhitFrontManager, api);
		TanhitFrontManager.init();
	}
	

	$(window).scroll(function() {
		if($(this).scrollTop() != 0) {
			$('#toTop').fadeIn();
		} else {
			$('#toTop').fadeOut();
		}
	});
	$('#toTop').click(function() {
		$('body,html').animate({scrollTop: 0}, 1000);
	});

	$('.smoothScroll').click(function(event) {
		event.preventDefault();
		var href=$(this).attr('href');
		var target=$(href);
		var top=target.offset().top;
		$('html,body').animate({
			scrollTop: top
		}, 1000);
	});

	$('.dropdown').hover(function() {
		$(this).addClass('open');
	},
	function() {
		$(this).removeClass('open');
	});

	$('.header.logo').click(function() {
		$(location).attr('href', '/')
	});

	/* Floating contact form*/
	$('#shildik').click(function() {
		$('#contact-form').toggle();
		$('#float-close').toggle();
		$('#float-contact').toggleClass('full-width');
	});

	/* Player */
	$('.show-video').click(function() {
		//alert($(this).attr("href"));

		/*if (jQuery($(this).attr("href")).find('.jwplayer').length){
			var plId = jQuery($(this).attr("href")).find('.jwplayer').attr('id');
			jwplayer(plId).play();
		}
		else
		if ($($(this).attr("href")).find('video').length){
			var vid = $($(this).attr("href")).find('video');
			vid.trigger('play');
		}
		else
		if (jQuery($(this).attr("href")).find('iframe').length){
			var oldsrc = jQuery($(this).attr("href")).find('iframe')[0].src;
			
			jQuery($(this).attr("href")).find('iframe').attr('data-src', oldsrc);
			jQuery($(this).attr("href")).find('iframe')[0].src += "?autoplay=1";
		}*/

		var $pContainer = $($($(this).attr("href"))[0]);
		//wpmVideo.initYT($('video', $pContainer).attr('id'), $pContainer.data('src'));
		$pContainer.show();
	});

	$('.show_vid').click(function() {
		if (jQuery(this).find('.video-js').length){
			var plId = jQuery(this).find('.video-js').attr('id');
			var player = videojs(plId);
			player.pause();
		}

		/*if (jQuery(this).find('.jwplayer').length){
			var plId = jQuery(this).find('.jwplayer').attr('id');
			jwplayer(plId).stop();
		}
		else
		if ($(this).find('video').length){
			var vid = $(this).find('video');
			vid.trigger('pause');
		}
		else
		if (jQuery(this).find('iframe').length){
			var iframe = jQuery(this).find('iframe');
			iframe[0].src = iframe.attr('data-src');
		}*/
		
		$(this).hide();
		$(".flowplayer").each(function () {
			$(this).data("flowplayer").stop();
		});
	}).children().click(function(e) {
		return false;
	});


	/* Floating cart */
	CheckCart($('#float_cart'));
	$('.add_to_cart_button,.single_add_to_cart_button').click(function() {
		CheckCart($('#float_cart'));
	});

	jQuery('body').on('updated_checkout', function(){
		console.log(jQuery('.shipping_method:checked').val());
		if (jQuery('.shipping_method').length && ! jQuery('.shipping_method:checked').val()){
			jQuery('.shipping_method:first').trigger('click');
		}
	});
});

/* Floating cart */
function CheckCart(cart_div) {
	var cart = TanhitFrontManager.getCart();
	//console.log(cart);
	if (cart.cart_count>0) {
		cart_div.find('span').text(cart.cart_count); // TODO: Обновлять количество в реальном времени, после нажатия на корзину
		cart_div.show();
	} else {
		cart_div.hide();
	}
}