
(function ($) {
// Wait for the DOM to load everything, just to be safe
$(document).ready(function() {

	//speed up heartbeat
	wp.heartbeat.interval( 'fast' );

	$("#rt-poll-button").click( function() {

		user      = $(this).attr("data-user");
		nonce     = $(this).attr("data-nonce");
		poll_id   = $(this).attr("data-poll");
		selection = $('#rt-vote-select').val();

		$.ajax({
			type : "post",
			dataType : "json",
			url : rt_polls_ajax.ajaxurl,
			data : { action: "rt_poll_process", user : user, nonce : nonce, poll_id : poll_id, selection : selection },
			beforeSend: function() {
					$(this).attr( "disabled", "disabled" );

					//spinner
			},
			complete: function() {
					//if limit reached, do not re-enable.  send a message.
					$(this).removeAttr( "disabled" );
					//spinner
			},
			success: function(response) {
				$('#message-area').html(response.message)
				eval(response.data_1);
				eval(response.options);

			    jQuery.plot( jQuery( "#placeholder" ), data_1, options );

			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
			}

		});
	});
});




/**
 * Animated Graph Updater
 * August 2013
 *
 * Author:  John Regan
 *          johnregan3.me
 *          @johnregan3
 */
		$(document).on('heartbeat-send', function(e, data) {
            data['rt_polls_heartbeat'] = 'graph_update';
            data['poll_id'] = $("#rt-poll-button").attr('data-poll');
        });

        // Listen for the custom event "heartbeat-tick" on $(document).
        $(document).on( 'heartbeat-tick', function(e, data) {

				eval(data['poll_data'].data_1);
				eval(data['poll_data'].options);

			    jQuery.plot( jQuery( "#placeholder" ), data_1, options );


        });


}(jQuery));