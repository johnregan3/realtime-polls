<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Vote Processor Class
 *
 * @since 1.0
 */

class RT_Polls {


	/**
	 * Fetch the time limits, as long as they aren't "Ever" (Unlimited).
	 *
	 * @since  1.0
	 * @param  array   $poll_meta  Array of data from the Poll's meta row
	 * @return string  $limit      User's selection of time limit
	 */
	static function get_limit( $poll_meta ) {
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
	 * Fetch the Plugin's general settings.
	 *
	 * @since  1.0
	 * @return array  $settings  Array of settings data
	 */
	static function setting( $field ) {
		$settings = get_option('rt_polls_settings');
		$setting = $settings[$field];
		return $setting;
	}


	/**
	 * Get the number of seconds within the time limit set by user.
	 *
	 * @since  1.0
	 * @param  array  $poll_meta   Array of data from the Poll's meta row
	 * @return int    $time_limit  Number of seconds within the time limit set by user.
	 */
	static function get_time_limit( $poll_meta ) {
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
	static function check_vote_limit( $user, $limit = null, $time, $time_limit = null, $poll_meta ) {
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

			$info['data_1'] = '';
			$info['options'] = '';
			$info['message'] = RT_Polls::setting( 'failure_message' );
			$json_result = json_encode( $info );
			echo $json_result;
			return false;

		} else {
			return true;
		}
	}



	/**
	 * Create an array of Field Labels
	 *
	 * @since  1.0
	 * @param  array  $poll_id       The ID of the poll being used
	 * @return array  $labels_array  Array of labels found in this Poll
	 */
	static function labels_array( $poll_id ) {
		$options = get_post_meta( $poll_id, 'rt_polls_data', true );
		foreach ( $options as $option => $val ) {
			if ( ( strpos($option, 'label-title-') !== false ) && ( ! empty( $val ) ) ) {
				$labels_array[] = $val;
			}
		}
		return $labels_array;
	}



	/**
	 * Generate Javascript information for running Ajax update on Poll graph
	 *
	 * @since  1.0
	 * @param  string  $poll_id  The ID of the poll being used
	 * @return string  $content  The content of the needed Javascript
	 */
	static function prep_coordinates( $poll_id ) {
		$options = get_post_meta( $poll_id, 'rt_polls_data', true );
		$labels_array = RT_POLLS::labels_array( $poll_id );
		$a = 0;
		$i = 1;
		end($labels_array);
		$last_key = key($labels_array);
			foreach ( $labels_array as $label => $val ) :
				$votes = isset( $options[$val] ) ? $options[$val] : 0 ;
				if( 'horizontal' == RT_Polls::setting( 'graph_orientation' ) ) :
					$sets[] = 'var dl_' . $i . ' = [[' . $votes . ', ' . $a . ']]; ';
				else :
					$sets[] = 'var dl_' . $i . ' = [[' . $a . ', ' . $votes . ']]; ';
				endif;
				$a++;
				$i++;
			endforeach;
		ob_start();
		foreach($sets as $set):
			echo $set;
		endforeach;
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * Generate Javascript Options information for running Ajax update on Poll graph
	 *
	 * @since  1.0
	 * @param  string  $poll_id  The ID of the poll being used
	 * @return string  $content  The content of the needed Javascript
	 */
	static function prep_data( $poll_id ) {
		$options = get_post_meta( $poll_id, 'rt_polls_data', true );
		$labels_array = RT_Polls::labels_array( $poll_id );
		end($labels_array);
		$last_key = key($labels_array);
			$i = 1;
			foreach ( $labels_array as $label => $val ) :
				$votes = isset( $options[$val] ) ? $options[$val] : 0 ;
				$horizontal = ( 'horizontal' == RT_Polls::setting( 'graph_orientation' ) ) ? 'horizontal : true,' : '';
				$ending = ( $label !== $last_key ) ? ', ' : '';
				$color = $options["field-color-" . $i];

				$datasets[] = '{label: "' . esc_html( $val ) . '", data: dl_' . $i .',
					bars: { show: true, ' . $horizontal . ' fill: true, align: "center", lineWidth: 1, order: ' . $i . ', fillColor: "' . $color . '" }, color: "' . $color . '" }'.  $ending . ' ';
				$i++;
			endforeach;
		ob_start();
			echo '[';
			foreach ($datasets as $set ) :
				echo $set . ' ';
			endforeach;
			echo '];';
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}


	/**
	 * Generate Javascript Options information for running Ajax update on Poll graph
	 *
	 * @since  1.0
	 * @param  string  $poll_id  The ID of the poll being used
	 * @return string  $content  The content of the needed Javascript
	 */
	static function prep_options( $poll_id, $widget ) {
		$horizontal = ('horizontal' == RT_Polls::setting( 'graph_orientation' ) );
		$widget = ( 'widget' == $widget );
		$labels_array = RT_POLLS::labels_array( $poll_id );
		end($labels_array);
		$last_key = key($labels_array);
		$i = 0;
		foreach ( $labels_array as $label => $val ) :
			$ending = ($label !== $last_key) ? ', ' : '';
			$ticks[] = "[" . $i . ", '']" . $ending;
			$i++;
		endforeach;
		if ( $widget ) :
			$content = "legend: { show: false, }, ";
		else :
			$content = "legend: { show: true, container: jQuery('#rt-legend') }, ";
		endif;

		if ( $widget && ! $horizontal ) :
			$axis = '';
		elseif ( $widget && $horizontal ) :
			$axis = "bars: { horizontal: true }, ";
		elseif ( ! $widget && $horizontal ) :
			$axis = "bars: { horizontal: true }, yaxis: ";
		else :
			$axis = "xaxis: ";
		endif;

		$content2 = "grid: { borderWidth: 0 }";

		ob_start();
				echo $content;
			echo $axis;
			if ( $widget ) :
				echo $axis;
				echo "xaxis: { ticks: 0 }, ";
			else :
				echo "{ tickLength: '0', ticks: [ ";
				foreach ( $ticks as $tick ) :
					echo $tick;
				endforeach;
				echo "] }, ";
			endif;
			echo $content2;
			$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}



	/**
	 * Combines JavasScript strings to be printed out later.
	 *
	 * @since  1.0
	 * @param  array   $poll_id  The ID of the poll being used
	 * @return string  New JavaScript string
	 */
	static function combine_data( $poll_id, $widget ){
		$js_coordinates = RT_POLLS::prep_coordinates( $poll_id );
		$js_data = RT_POLLS::prep_data( $poll_id );
		$var_name = ( 'widget' == $widget ) ? 'data_widget' : 'data_poll';
		$return = $js_coordinates . '
		  var ' . $var_name . ' = ' . $js_data;
		return $return;
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
	static function save_vote( $poll_id, $poll_meta, $selection, $user, $time, $widget ) {
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

		$info['widget'] = ( 'widget' == $widget ) ? true : false;
		$message = $result ? RT_Polls::setting( 'success_message' ) : "Vote Failed.";
			$info['message'] = esc_html( $message );
		$js_data = RT_POLLS::combine_data( $poll_id, $widget );
			$info['data'] = $js_data; //Don't esc this.  Breaks the JS.
		$js_options = RT_POLLS::prep_options( $poll_id, $widget );
			$info['options'] = "var options = {" . $js_options . "}"; //Don't esc this.  Breaks the JS.

		$json_result = json_encode( $info );
		echo $json_result;
		die();
	}

}




/**
 * Process Votes via AJAX
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
	$poll_id   = $_REQUEST['poll_id'];
	$user      = $_REQUEST['user'];
	$widget    = $_REQUEST['widget'];
	$time      = current_time( 'timestamp', 1 );
	$selection = $_REQUEST['selection'];
	$poll_meta = get_post_meta( $poll_id, 'rt_polls_data', true );

	//Get data to check if vote limit has been reached
	$limit = RT_POLLS::get_limit( $poll_meta );
	$time_limit = RT_POLLS::get_time_limit( $poll_meta );

	//Check to see if limit reached.  If so, exit.  If not, proceed.
	$limit_check = RT_POLLS::check_vote_limit( $user, $limit, $time, $time_limit, $poll_meta );

	if( $limit_check == true ) {
	//Save the Vote, and, on success, return a message
		RT_POLLS::save_vote( $poll_id, $poll_meta, $selection, $user, $time, $widget );
	}

}

function rt_poll_must_login() {

	echo "If you'd like to vote, please Log in or Sign up!";
	die();

}



/**
 * Use Heartbeat API to update graph bars
 *
 * @since  1.0
 * @param  array  $response
 * @param  array  $data      Information sent from heartbeat-send
 * @return array  $response  Infomation sent back to heartbeat-tick
 */

function rt_polls_heartbeat_received( $response, $data ) {

/*
	// Make sure we only run our query if the heartbeat key is present
	if ( $data['rt_polls_heartbeat'] == 'graph_update' ) {

		if ( ! empty( $data['poll_id'] ) ) {

			$js_data = RT_POLLS::combine_data( $data['poll_id'] );
				$poll['data_poll'] = $js_data;
			$js_options = RT_POLLS::prep_options( $data['poll_id'] );
				$poll['options_poll'] = "var optionsWidget = {" . $js_options . "}";

			$poll_array['poll_data'] = $poll;

		}

		if ( ! empty( $data['widget_poll_id'] ) ) {

			$js_data = RT_Polls::combine_data( $data['widget_poll_id'] );
				$widget['data_widget'] = $js_data;
			$js_options = RT_Polls::prep_options( $data['widget_poll_id'] );
				$widget['options_widget'] = "var optionsPoll = {" . $js_options . "}";

			$widget_array['widget_poll_data'] = $widget;

		}

	} */
		$response['polls_data'] = $data['widget_poll_id'];

		return $response;


}
add_filter( 'heartbeat_received', 'rt_polls_heartbeat_received', 10, 2 );