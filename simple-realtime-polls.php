<?php

/**
 * Plugin Name: Simple Realtime Polls
 * Plugin URI: http://johnregan3.github.io/simple-realtime-polls
 * Description: Create Polls that update in realtime!
 * Author: John Regan
 * Author URI: http://johnregan3.me
 * Version: 1.0
 * Copyright 2013  John Regan  (email : johnregan3@outlook.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Simple Realtime Polls
 * @author John Regan
 * @version 1.0
 */


//Polls Listing Page
include_once( plugin_dir_path(__FILE__) . 'includes/admin/rt-polls.php' );

//Polls Admin Page
include_once( plugin_dir_path(__FILE__) . 'includes/admin/polls-settings.php' );

//Polls Listing Page
include_once( plugin_dir_path(__FILE__) . 'includes/admin/rt-polls.php' );

//Shortcode
include_once( plugin_dir_path(__FILE__) . 'includes/poll.php' );

//Processor Classes
include_once( plugin_dir_path(__FILE__) . 'includes/process.php' );

//Processor Classes
include_once( plugin_dir_path(__FILE__) . 'includes/widget.php' );

/**
 * Enqueue Admin Styles and Scripts
 *
 * @since 1.0
 */
add_action( 'admin_enqueue_scripts', 'rt_polls_admin_scripts' );
function rt_polls_admin_scripts( ) {
	wp_register_style( 'rt-polls-style', plugins_url( 'css/style-admin.css', __FILE__) );
	wp_enqueue_style( 'rt-polls-style' );
	wp_enqueue_style( 'wp-color-picker' );

	wp_register_script( 'rt-polls-scripts', plugins_url( 'js/scripts-admin.js', __FILE__) );
	wp_enqueue_script( 'rt-polls-scripts' );
	wp_enqueue_script( 'wp-color-picker', plugins_url( __FILE__ ), array( 'wp-color-picker' ), false, true );
}

/**
 * Enqueue Front End Styles and Scripts
 *
 * @since 1.0
 */
add_action( 'wp_enqueue_scripts', 'rt_polls_scripts' );
function rt_polls_scripts( ) {

	global $is_IE;
	if ( $is_IE ) {
	    wp_enqueue_script( 'excanvas', plugins_url( 'js/flot/excanvas.js', __FILE__), 'jQuery' );
	}

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'heartbeat' );

	wp_register_style( 'rt-polls-style', plugins_url( 'css/style.css', __FILE__) );
	wp_enqueue_style( 'rt-polls-style' );

	wp_register_script( 'rt-polls-flot', plugins_url( 'js/flot/jQuery.flot.custom.js', __FILE__), 'jQuery' );
	wp_enqueue_script( 'rt-polls-flot' );

	wp_register_script( 'vote-process', plugins_url( 'js/scripts.js', __FILE__) );
	wp_localize_script( 'vote-process', 'rt_polls_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	wp_enqueue_script( 'vote-process' );

}

