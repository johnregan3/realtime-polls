<?php

/**
 * Process Votes
 *
 * @since 1.0
 */

add_action( 'wp_ajax_rt_poll_process', 'rt_poll_process' );

add_action( 'wp_ajax_nopriv_rt_poll_process', 'rt_poll_must_login' );


/**
 * Receive and save Vote data.
 *
 * @since 1.0
 */
function rt_poll_process() {

	$help = New Helpers;

	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'rt_poll_vote_nonce') ) {

		exit( "Failed nonce verification." );

	}

	//get variables
	$poll_id = $_REQUEST['poll_id'];
	$user = $_REQUEST['user'];
	$time = current_time( 'timestamp', 1 );
	$selection = $_REQUEST['selection'];
	$poll_meta = get_post_meta( $poll_id, 'rt_poll_results' );

	if ( $poll_meta ) {

		//Cycle through fields, find a match and add one to the score.
		foreach( $poll_meta as $field => $val ) {
			if ( $selection == $field ) {
					$val++;
			} else {
				$val = 1;
		}

	} else {
		$val = 1;
	}

	//add new score to array
	$add_to_array[$selection] = $val;
	//record the act of voting
	$add_to_array[$user][$time] = $selection
	//We're doing an array_merge so existing keys will get overwritten with new values
	$new_poll_meta = array_merge( $poll_meta, $add_to_array);

	$result = update_post_meta( $poll_id, 'rt_poll_results' );
	$mesage = $result ? "Vote Successful.  Thank you for participating." : "Vote Failed.  Please try again.";
	$json_result = json_encode( $message );
	echo $json_result;

	die();

}

function rt_poll_must_login() {

	echo "If you'd like to vote, please Log in or Sign up!";

	die();

}
