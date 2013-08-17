
(function ($) {


// Wait for the DOM to load everything, just to be safe
$(document).ready(function() {

	wp.heartbeat.interval( 'fast' );

	$("#rt-poll-widget-button").click( function() {

		user      = $(this).attr("data-user");
		nonce     = $(this).attr("data-nonce");
		poll_id   = $(this).attr("data-poll");
		selection = $('#rt-vote-select').val();

		$.ajax({
			type : "post",
			dataType : "json",
			url : rt_polls_ajax.ajaxurl,
			data : { action: "rt_poll_process", user : user, nonce : nonce, poll_id : poll_id, selection : selection },
			success: function(response) {
				$('#message-area').html(response.message);
				eval(response.data_1);
				eval(response.options);
			 	$.plot( $( "#widget-graph" ), data_1, options );
			},

			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
			}

		});
	});
});




//Update graph in realtime using WP's Heartbeat API

$(document).on('heartbeat-send', function(e, data) {
	data['rt_polls_heartbeat'] = 'graph_update';
	data['poll_id'] = $("#rt-poll-button").attr('data-poll');
});

// Listen for the custom event "heartbeat-tick" on $(document).
$(document).on( 'heartbeat-tick', function(e, data) {

	console.log("Test");

	eval(data['poll_data'].data_1);
	eval(data['poll_data'].options);

	$.plot( $( "#placeholder" ), data_1, options );


});


}(jQuery));