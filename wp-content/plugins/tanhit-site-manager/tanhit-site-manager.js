/*jslint browser: true*/
/*global jQuery, console, TanhitSiteManager*/
(function($) {
	"use strict";
	if ( typeof TanhitSiteManager === 'undefined' ) {
		return;	
	}
	var api = {	
		option : {},
		init: function(args) {
			api.option = $.extend( api.option, args );
			api.addListeners();
		},
		beforeAjaxSend: function(order) {
		},	
		addListeners: function(){

			$( '.tanhit-ajaxify' ).on( 'click', function(ev){

				var $t = $(this), 
					order = {};
					
				order.action = $t.data( 'action' );
				order.data	= {};
				order.data.source = $t.attr('type');
				order.data.event  = 'click';
				if ( 'checkbox' == $t.attr('type') ) {
					order.data.checked = $t.prop('checked');
				}	
				
				order.options = {};
				if ( 'update_options' == order.action ) {
					$('.tanhit-option').each(function(i,e) {
						order.options[ $(this).attr('id') ] = $(this).val();
					});
				}	
				api.ajax(order, api.beforeAjaxSend)
					.done(function (data) {
						if ( data.status == 'success' ) {
						} else if ( data.status == 'error' ) {
						}	
						window.location = window.location;
					})
					.fail(function (error) {})
					.always(function (jqXHR, status){});	
			});
			
		},
		ajax : function(order, beforeSend) {
			return $.ajax({
				beforeSend:function(){
					if ( typeof beforeSend !== 'undefined' ) beforeSend(order);
			},type:'POST', url:ajaxurl, data:{action:TanhitSiteManager.process_ajax, order:order}, dataType:'json'});
		}	
	};

	TanhitSiteManager = $.extend({}, TanhitSiteManager, api);
	TanhitSiteManager.init();
	
})(jQuery);	