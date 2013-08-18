
( function ($) {


// Wait for the DOM to load everything, just to be safe
$( document ).ready( function() {

	wp.heartbeat.interval( 'fast' );

	$( '#rt-polls-widget-legend div' ).click( function() {
		$( '#rt-polls-widget-legend span' ).css( 'font-weight', 'normal' );
		$( '#rt-polls-widget-legend input' ).prop( 'checked', false );
		$( 'span', this ).css( 'font-weight', 'bold' );
		$( 'input', this ).prop( 'checked', true );
	});

	$( "#rt-poll-button" ).click( function() {

		user      = $( this ).attr( "data-user" );
		nonce     = $( this ).attr( "data-nonce" );
		poll_id   = $( this ).attr( "data-poll" );
		selection = $( '#rt-vote-select' ).val();
		widget    = 'not-widget';

		rt_polls_ajax_call(user, nonce, poll_id, selection, widget);
	});

	$( "#rt-poll-widget-button" ).click( function() {

		user      = $( this ).attr( "data-user" );
		nonce     = $( this ).attr( "data-nonce" );
		poll_id   = $( this ).attr( "data-poll" );
		selection = $( 'input[name=rt-polls-widget-checkbox]:checked' ).val();
		widget    = 'widget';

		rt_polls_ajax_call(user, nonce, poll_id, selection, widget);
	});

	function rt_polls_ajax_call( user, nonce, poll_id, selection, widget ) {
		$.ajax({
			type : "post",
			dataType : "json",
			url : rt_polls_ajax.ajaxurl,
			data : { action: "rt_poll_process", user : user, nonce : nonce, poll_id : poll_id, selection : selection, widget : widget },
			success: function( response ) {
				if ( response.widget != false ) {
					eval( response.data );
					eval( response.options );
				 	$.plot( $( "#widget-graph" ), data_widget, options );
				} else {
					$( '#message-area' ).html( response.message );
					eval( response.data );
					eval( response.options );
				 	$.plot( $( "#placeholder" ), data_poll, options );
				 };

			},
			error: function( jqXHR, textStatus, errorThrown ) {
				console.log( jqXHR);
				console.log( textStatus );
				console.log( errorThrown );
			}

		});
	};

});



//
// Heartbeat doesn't work.
//

//Update graph in realtime using WP's Heartbeat API
$( document ).on( 'heartbeat-send', function( e, data ) {


	data['rt_polls_heartbeat'] = 'graph_update';

	data['poll_id'] = $( "#rt-poll-button" ).attr( 'data-poll' );
	data['widget_poll_id'] = $( "#rt-poll-widget-button" ).attr( 'data-poll' );
	console.log( data['widget_poll_id'] );
});

// Listen for the custom event "heartbeat-tick" on $(document).
$( document ).on( 'heartbeat-tick', function( e, data ) {
	console.log( 'tick' );

	console.log( data['polls_data'] );
/*
	eval( data['polls_data'].data_widget );
	eval( data['polls_data'].options_widget );

	$.plot( $( "#widget-graph" ), data_widget, optionsWidget );

	eval( data['polls_data'].data_poll );
	eval( data['polls_data'].options_poll );

	$.plot( $( "#placeholder" ), data_poll, optionsPoll );
	*/

});

}( jQuery ) );
