jQuery(document).ready(function($) {

	/** Cart Handling */
	$supports_html5_storage = ( 'sessionStorage' in window && window['sessionStorage'] !== null );

	$fragment_refresh = {
		url: carton_params.ajax_url,
		type: 'POST',
		data: { action: 'carton_get_refreshed_fragments' },
		success: function( data ) {

			if ( data && data.fragments ) {
				$.each( data.fragments, function( key, value ) {
					$(key).replaceWith(value);
					$(key).stop(true).removeClass('updating').css('opacity', '1').unblock();
					$(key).parent().stop(true).removeClass('updating').css('opacity', '1').unblock();
				});

				if ( $supports_html5_storage ) {
					sessionStorage.setItem( "ctn_fragments", JSON.stringify( data.fragments ) );
					sessionStorage.setItem( "ctn_cart_hash", data.cart_hash );
				}

			}
		}
	};
	if ( $supports_html5_storage ) {

		$('body').bind( 'added_to_cart', function( event, fragments, cart_hash ) {
			sessionStorage.setItem( "ctn_fragments", JSON.stringify( fragments ) );
			sessionStorage.setItem( "ctn_cart_hash", cart_hash );
		});

		try {
			var ctn_fragments = $.parseJSON( sessionStorage.getItem( "ctn_fragments" ) );
			var cart_hash    = sessionStorage.getItem( "ctn_cart_hash" );

			if ( ctn_fragments && ctn_fragments['div.widget_shopping_cart_content'] && cart_hash == $.cookie( "carton_cart_hash" ) ) {

				$.each( ctn_fragments, function( key, value ) {
					$(key).replaceWith(value);
					$(key).stop(true).removeClass('updating').css('opacity', '1').unblock();
					$(key).parent().stop(true).removeClass('updating').css('opacity', '1').unblock();
				});

			} else {
				throw "No fragment";
			}

		} catch(err) {
			$.ajax( $fragment_refresh );
		}

	} else {
		$.ajax( $fragment_refresh );
	}

});