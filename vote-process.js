(function ($) {

	$(document).ready( function() {

		$("#rt-poll-button").click( function() {

			user      = $(this).attr("data-user");
			nonce     = $(this).attr("data-nonce");
			poll_id   = $(this).attr("data-poll");
			selection = $('#rt-vote-select').val();

			$.ajax({
				type : "post",
				dataType : "json",
				url : cpjr3_AJAX.ajaxurl,
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
					if( response ) {
						console.log(response);
					} else {
						message = "Error processing the event.  Vote not saved."
						console.log(message);
					}
				}
			});
		});
	});

}(jQuery));