/**
 * Interface JS functions
 *
 * @since 1.0.0
 *
 * @package Tanhit
 * @subpackage My Account
 */
/*jslint browser: true*/
/*global jQuery, console, TanhitMyAccount*/
/*jQuery(document).ready(function($) {
	"use strict";

	if ( typeof TanhitMyAccount === 'undefined' ) {
		return;
	}
	
	var api =  {
		init: function() {
			api.showOrderItems();
		},
		showOrderItems: function() {
			$('.show-order-items.view').on( 'click', function(ev) {
				var id = $(this).data( 'order-id' );
				var stop = false,
					clone;
				if ( $( '.clone-wrapper' ).length > 0 ) {
					$( '.clone-wrapper' ).each( function(i,e){
						var td = $(e).find( 'td' );
						if ( td.attr('id') == 'clone-'+id ) {
							stop = true;
						}	
						$(e).remove();
					});	
				}	
				if ( stop ) return;
				var table = $( '#table-order-' + id );
				var p = table.parents( 'tr.order' );
				clone = $( table.clone() );
				clone.attr( 'id', 'clone-' + id );
				$( clone ).insertAfter( p ).css({'display':'table-cell'});
				$( '#clone-' + id ).wrap( '<tr class="order clone-wrapper">' );
			});	
		}
	};	
	TanhitMyAccount = $.extend({}, TanhitMyAccount, api);
	TanhitMyAccount.init();	
});*/