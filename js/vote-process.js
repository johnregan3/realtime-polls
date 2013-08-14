
(function ($) {
// Wait for the DOM to load everything, just to be safe
$(document).ready(function() {


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
				$('#message-area').html(response.message);
				var fieldTitle = response.updatedlabel;
					console.log(fieldTitle);
					$('#data-table th').each(function() {
						if( fieldTitle == $(this).text() ) {
							var td = $(this).siblings();
							var value = parseInt(td.text());
							value++;
							$(td).text(value);
						}
					});
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

// Hook into the heartbeat-send
	$(document).on('heartbeat-send', function(e, data) {
		data['rt_polls_heartbeat'] = 'graph_update';
	});

	// Listen for the custom event "heartbeat-tick" on $(document).
	$(document).on( 'heartbeat-tick', function(e, data) {

		// Only proceed if our EDD data is present
		if ( ! data['graph-percentage'] )
			return;

		// Log the response for easy proof it works
		console.log( data['graph-percentage'] );

		// Update sale count and bold it to provide a highlight
		$('.edd_dashboard_widget .b.b-sales').text( data['graph-percentage'] ).css( 'font-weight', 'bold' );

		// Return font-weight to normal after 2 seconds
		setTimeout(function(){
			$('.edd_dashboard_widget .b.b-sales').css( 'font-weight', 'normal' );;
		}, 2000);
	});


}(jQuery));