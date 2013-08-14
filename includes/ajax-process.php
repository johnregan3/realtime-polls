<?php

/**
 * Vote Processor Class
 *
 * @since 1.0
 */

class RT_POLLS {


	/**
	 * Fetch the time limits, as long as they aren't "Ever" (Unlimited).
	 *
	 * @since  1.0
	 * @param  array   $poll_meta  Array of data from the Poll's meta row
	 * @return string  $limit      User's selection of time limit
	 */
	public static function get_limit( $poll_meta ) {
		$limits = array( 1, 5, 10, 25 );
		foreach ( $limits as $limit ) {
			if ( $poll_meta['votes_number'] == $limit ) {
				$limit = $poll_meta['votes_number'];
			} else {
				$limit = '99999999';
			}
		}
		return $limit;
	}



	/**
	 * Get the number of seconds within the time limit set by user.
	 *
	 * @since  1.0
	 * @param  array  $poll_meta   Array of data from the Poll's meta row
	 * @return int    $time_limit  Number of seconds within the time limit set by user.
	 */
	public static function get_time_limit( $poll_meta ) {
		$times = array( 'hour', 'day', 'week', 'month' );
		if (  in_array( $poll_meta['votes_time'], $times ) == true ) {
			if ( $poll_meta['votes_time'] == 'hour' )
				$time_limit = 3600 ;
			elseif ( $poll_meta['votes_time'] == 'day' )
				$time_limit = 86400 ;
			elseif ( $poll_meta['votes_time'] == 'week' )
				$time_limit = 604800 ;
			elseif ( $poll_meta['votes_time'] == 'month' )
				$time_limit = 592000 ;

		} else {
			$time_limit = '';
		}
		return $time_limit;
	}



	/**
	 * Check to see if voting limit has been reached for the set time period
	 *
	 * Begin by determining the exact time of the time limit (e.g., One hour ago).  Then,
	 * cycle through the array of user's input on this poll, each marked with a timestamp.
	 * Compare timestamp to time limit (One hour ago).  If the timestamp is less than the time limit,
	 * count it.
	 *
	 * Compare the number of votes submitted within the time limit against the $limit set by the user.
	 * If there are too many votes, return a message and exit.  If not, continue on.
	 *
	 * @since  1.0
	 * @param  string  $limit       Type of time limit on voting
	 * @param  int     $time        Current timestamp
	 * @param  int     $time_limit  Number of seconds returned by get_time_limit
	 * @param  array   $poll_meta   Array of data from the Poll's meta row
	 * @return mixed   Either exit function and die, or simply return
	 */
	public static function check_vote_limit( $user, $limit = null, $time, $time_limit = null, $poll_meta ) {
		if ( isset( $limit ) && isset( $time_limit ) && isset( $poll_meta[$user] ) ) {

		$time_ago = $time - $time_limit;
		$votes_submitted = '';
		$user_votes_array = $poll_meta[$user];

			if ( $user_votes_array ) {
				foreach ( $user_votes_array as $timestamp => $value ) {
					if ( $timestamp >= $time_ago ) {
						$votes_count_array[] = $timestamp;
					}
				}
			}
			if ( isset( $votes_count_array ) ) {
				$votes_submitted = count( $votes_count_array );
			} else {
				$votes_submitted = 0;
			}
		}
		if ( isset( $votes_submitted ) && $votes_submitted >= $limit ) {

			$info['message'] = "Your vote limit has been reached.  Please try again later." ;
			$json_result = json_encode( $info );
			echo $json_result;
			die();

		} else {
			return;
		}
	}




	/**
	 * Save the Vote, and then, on success, return a message
	 *
	 * Begin by cycling through keys in poll_meta.  When the key matches $selection,
	 * add 1 to the score for that selection. Then create new keys to be added to $poll_meta.
	 * The first, is the updated $selection key, the second is an array that tracks the user's
	 * selection based on the time, so that they can be counted by the graph later.
	 * Finally, save $poll_meta to the Poll Meta row.
	 *
	 * @since  1.0
	 * @param  array   $poll_meta  Data from poll meta
	 * @param  string  $selection  Selected item to vote on
	 * @param  string  $user       User IP/Username
	 * @param  string  $time       Current time
	 * @return void
	 */
	public static function save_vote( $poll_id, $poll_meta, $selection, $user, $time ) {
		//Cycle through fields, find a match and add one to the score.
		foreach( $poll_meta as $field => $val ) {
			if ( $selection == $field ) {
				if ( isset( $poll_meta[$val] ) ) {
					$poll_meta[$val]++;
				} else {
					$poll_meta[$val] = 1;
				}
				//record the act of voting
				$selection_label = $poll_meta[$selection];
				$poll_meta[$user][$time] = $selection_label;
			}
		}

		$result = update_post_meta( $poll_id, 'rt_polls_data', $poll_meta );
		$message = $result ? "Vote Successful.  Thank you for participating." : "Vote Failed.  Please try again.";
		$info['updatedlabel'] = $selection_label;
		$info['updatedid'] = $selection;
		$info['message'] = $message;
		$json_result = json_encode( $info );
		echo $json_result;
		die();
	}

}

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

	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'rt_poll_vote_nonce') )
		exit( "Failed nonce verification." );

	//get variables
	$poll_id = $_REQUEST['poll_id'];
	$user = $_REQUEST['user'];
	$time = current_time( 'timestamp', 1 );
	$selection = $_REQUEST['selection'];
	$poll_meta = get_post_meta( $poll_id, 'rt_polls_data', true );

	//Get data to check if vote limit has been reached
	$limit = RT_POLLS::get_limit( $poll_meta );
	$time_limit = RT_POLLS::get_time_limit( $poll_meta );

	//Check to see if limit reached.  If so, exit.  If not, proceed.
	RT_POLLS::check_vote_limit( $user, $limit, $time, $time_limit, $poll_meta );

	//Save the Vote, and, on success, return a message
	RT_POLLS::save_vote( $poll_id, $poll_meta, $selection, $user, $time );

}

function rt_poll_must_login() {

	echo "If you'd like to vote, please Log in or Sign up!";
	die();

}



/**
 * Use Heartbeat API to update graph bars
 *
 * @since 1.0
 */
// Modify the data that goes back with the heartbeat-tick
function rt_polls_heartbeat_received( $response, $data ) {

	// Make sure we only run our query if the rt_polls_heartbeat key is present
	if( $data['rt_polls_heartbeat'] == 'graph_update' ) {

		// Send back the number of complete payments
		$response['graph-percentage'] = number_format_i18n( $payments->publish );

	}
	return $response;
}
add_filter( 'heartbeat_received', 'rt_polls_heartbeat_received', 10, 2 );