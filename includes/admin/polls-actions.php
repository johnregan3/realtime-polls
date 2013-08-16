<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Functions used by Polls admin page
 *
 * @since  1.0
 */


/**
 * Fetches all Polls
 *
 * @since  1.0
 */
function polls_get_polls_actions() {
    if ( isset( $_GET['poll-action'] ) ) {
        do_action( 'polls_' . $_GET['poll-action'], $_GET );
        }
    }
add_action( 'init', 'polls_get_polls_actions' );



/**
 * Checks for POST/GET
 *
 * @since  1.0
 */
function polls_process_actions() {
    if ( isset( $_POST['poll-action'] ) ) {
        do_action( 'polls_' . $_POST['poll-action'], $_POST );
    }

    if ( isset( $_GET['poll-action'] ) ) {
        do_action( 'polls_' . $_GET['poll-action'], $_GET );
    }
}
add_action( 'admin_init', 'polls_process_actions' );



/**
 * Fetches array of new poll information, then sends it to be saved.
 *
 * @since  1.0
 * @param  array  $data  Data of poll to be added
 */
function polls_add_poll( $data ) {
	if ( isset( $data['realtime-polls-nonce'] ) && wp_verify_nonce( $data['realtime-polls-nonce'], 'realtime_polls_nonce' ) ) {
		// Setup the action code details
		$posted = array();

		foreach ( $data as $key => $value ) {
			if ( $key != 'realtime-polls-nonce' && $key != 'poll-action' && $key != 'poll-redirect' ) {
				$posted[$key] = $value;
			}
		}
				// Set the action code's default status to active
		if ( polls_store_poll( $posted ) ) {
			wp_redirect( add_query_arg( 'poll-message', 'poll_added', $data['poll-redirect'] ) ); die();
		} else {
			wp_redirect( add_query_arg( 'poll-message', 'poll_add_failed', $data['poll-redirect'] ) ); die();
		}

	}
}
add_action( 'polls_add_poll', 'polls_add_poll' );



/**
 * Fetches array of new poll information, then saves it and redirects.
 *
 * @since  1.0
 * @param  array  $details  Data of poll to be added
 * @param  int    $poll_id  poll for which to store the data.
 */
function polls_store_poll( $details, $poll_id = null ) {

	//Set up Metadata array
	$meta = array();
	$meta['votes_number'] = isset( $details['votes_number'] ) ? $details['votes_number'] : 'unlimited';
	$meta['votes_user']   = isset( $details['votes_user'] )   ? $details['votes_user']   : 'ip';
	$meta['votes_time']   = isset( $details['votes_time'] )   ? $details['votes_time']   : 'ever';


	//Add Label and Color fields
	$numbers = array( 1, 2, 3, 4, 5, 6 );
	$fields = array( 'label-title-', 'field-color-' );
	foreach ( $numbers as $number ) {
		foreach ( $fields as $field ) {
			$meta[$field . $number] = isset( $details[$field . $number] ) ? $details[$field . $number] : '';
		}
	}

	//var_dump($meta);

	if ( polls_poll_exists( $poll_id ) && ! empty( $poll_id ) ) {
		// Update an existing poll

		$existing_meta = get_post_meta( $poll_id, 'rt_polls_data', true);
		$new_meta = $meta + $existing_meta ;

		wp_update_post( array(
			'ID'          => $poll_id,
			'post_title'  => $details['name'],
		) );

		update_post_meta( $poll_id, 'rt_polls_data', $new_meta);


		// poll updated
		return true;

	} else {
		// Add the poll
		$poll_id = wp_insert_post( array(
			'post_type'   => 'rt_poll',
			'post_title'  => isset( $details['name'] ) ? $details['name'] : '',
			'post_status' => 'publish',
		) );

			update_post_meta( $poll_id, 'rt_polls_data', $meta);

		// poll created
		return true;
	}
}



/**
 * Fetches array of new poll information, then sends it to be saved.
 *
 * @since  1.0
 * @param  array  $data  Data of poll to be added
 */
function polls_edit_poll( $data ) {
	if ( isset( $data['realtime-polls-nonce'] ) && wp_verify_nonce( $data['realtime-polls-nonce'], 'realtime_polls_nonce' ) ) {

		$poll = array();
		foreach ( $data as $key => $value ) {
			if ( $key != 'realtime-polls-nonce' && $key != 'poll-action' && $key != 'poll_id' && $key != 'poll-redirect' ) {
					$poll[ $key ] = strip_tags( addslashes( $value ) );
			}
		}

		if ( polls_store_poll( $poll, $data['poll_id'] ) ) {
			wp_redirect( add_query_arg( 'poll-message', 'poll_updated', $data['poll-redirect'] ) ); die();
		} else {
			wp_redirect( add_query_arg( 'poll-message', 'poll_update_failed', $data['poll-redirect'] ) ); die();
		}
	}
}
add_action( 'polls_edit_poll', 'polls_edit_poll' );



/**
 * Checks to see if poll exists
 *
 * @since  1.0
 * @param  int  $poll_id  Poll for which to store the data.
 * @return bool
 */
function polls_poll_exists( $poll_id ) {
	if ( polls_get_poll( $poll_id ) )
		return true;

	return false;
}



/**
 * Checks to see if Poll exists
 *
 * @since  1.0
 * @param  int    $poll_id  Poll for which to store the data.
 * @return object $poll     Post object for requested poll ID.
 */
function polls_get_poll( $poll_id ) {
	$poll = get_post( $poll_id );
	if ( isset( $poll->ID ) && ( get_post_type( $poll->ID ) != 'rt_poll' ) )
		return false;

	return $poll;
}



/**
 * Listens for when a delete link is clicked and deletes the Poll
 *
 * @since  1.0
 * @param  array  $data
 */
function polls_delete_action( $data ) {
	if ( ! isset( $data['_wpnonce'] ) || ! wp_verify_nonce( $data['_wpnonce'], 'realtime_polls_nonce' ) )
		wp_die( __( 'Failed nonce verification', 'rt_polls' ), __( 'Error', 'rt_polls' ) );

	$poll_id = $data['poll_id'];
	wp_delete_post( $poll_id, true );
}
add_action( 'polls_delete_action', 'polls_delete_action' );



/**
 * Deletes a Poll
 *
 * @since 1.0
 * @param int $poll_id poll ID
 */
function polls_remove_poll( $poll_id = 0 ) {
	wp_delete_post( $poll_id, true );
	delete_post_meta($poll_id, '_rt_polls_activity_type');
	delete_post_meta($poll_id, '_rt_polls_activity_points');
}
