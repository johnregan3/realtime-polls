<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Set up Gamify WP Admin Menu Page
 *
 * @since  1.0
 */

add_action( 'admin_menu', 'register_polls_submenu_page' );

function register_polls_submenu_page() {
	add_submenu_page( 'rt-polls.php', __( 'Realtime Polls', 'rt_polls' ), __( 'Settings', 'rt_polls' ), 'manage_options', basename(__FILE__), 'polls_render_submenu_page' );
}

/**
 * Establish Settings, Sections, and Fields
 *
 * @since  1.0
 */
function rt_polls_render_fields() {
	register_setting( 'rt_polls_settings_group', 'rt_polls_settings' );

	add_settings_section(
		'primary_section',
		__( 'Defaults', 'rt_polls' ),
		'primary_section_cb',
		__FILE__
	);

	add_settings_field(
		'graph_orientation',
		__( 'Graph Orientation', 'rt_polls' ),
		'graph_orientation',
		__FILE__,
		'primary_section'
	);

	add_settings_field(
		'default_colors',
		__( 'Default Colors', 'rt_polls' ),
		'default_colors',
		__FILE__,
		'primary_section'
	);

	add_settings_field(
		'default_restriction',
		__( 'Default Vote Limit', 'rt_polls' ),
		'default_restriction',
		__FILE__,
		'primary_section'
	);

	add_settings_section(
		'secondary_section',
		__( 'Messages', 'rt_polls' ),
		'secondary_section_cb',
		__FILE__
	);

	add_settings_field(
		'success_message',
		__( 'Successful Vote Message', 'rt_polls' ),
		'success_message',
		__FILE__,
		'secondary_section'
	);

	add_settings_field(
		'failure_message',
		__( 'Vote Limit Message', 'rt_polls' ),
		'failure_message',
		__FILE__,
		'secondary_section'
	);

}

add_action( 'admin_init', 'rt_polls_render_fields' );


/**
 * Render the General Admin Page
 *
 * @since  1.0
 */
function polls_render_submenu_page() {
	?>
	<div id="rt_polls-settings-wrap" class="wrap">
		<div class="icon32" id="rt-polls-icon">
			<br />
		</div>
		<?php _e( '<h2>Simple Realtime Polls General Settings</h2>', 'rt_polls'); ?>
		<?php if( isset($_GET['settings-updated']) ) { ?>
			<div id="message" class="updated">
				<p><?php _e('Settings saved.') ?></p>
			</div>
		<?php } ?>
		<form method="post" id="rt-polls-settings-form" action="options.php" enctype="multipart/form-data">
			<p><a href="https://github.com/johnregan3/realtime-polls/wiki/General-Options"><?php _e( 'Get help for this page on our Wiki', 'rt_polls' ) ?></a></p>

			<?php settings_fields( 'rt_polls_settings_group' ); ?>
			<?php do_settings_sections( __FILE__ ); ?>

			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'rt_polls' ); ?>" />
			</p>
		</form>
	</div>
	<?php
}


/**
 * @since  1.0
 */
function primary_section_cb() {}


/**
 * @since  1.0
 */
function graph_orientation() {
	$options = get_option('rt_polls_settings');
	$settings_value = isset( $options['graph_orientation'] ) ? $options['graph_orientation'] : 'vertical';
	?>
		<select name="rt_polls_settings[graph_orientation]" >
			<option value="vertical"  <?php selected( $settings_value, 'vertical' ); ?>>Vertical</option>
			<option value="horizontal"  <?php selected( $settings_value, 'horizontal' ); ?>>Horizontal</option>
		</select>
	<?php
}


/**
 * @since  1.0
 */
function default_colors() {
	$options  = get_option('rt_polls_settings');
	$colors   = isset( $options['default_colors'] ) ? $options['default_colors'] : 0;
	$defaults = array( '', '#B8D0DE', '#9FC2D6', '#86B4CF', '#73A2BD', '#6792AB', '#5A8799');
	$numbers  = array( 1, 2, 3, 4, 5, 6 );
		foreach ( $numbers as $number ) :
			$color = isset( $colors['field-color-' . $number] ) ? $colors['field-color-' . $number] : $defaults[$number]; ?>
			<input type="text" name="<?php echo esc_html( 'rt_polls_settings[default_colors][field-color-' . $number . ']' ) ?>" value="<?php echo esc_html( $color ) ?>" class="color-field" />&nbsp;&nbsp;<span class="description">Field <?php echo $number; ?></span>
			<br />
		<?php endforeach; ?>
	<?php
}


/**
 * @since  1.0
 */
function default_restriction() {
	$options = get_option('rt_polls_settings');
	$restrictions = isset( $options['default_restriction'] ) ? $options['default_restriction'] : 0;
	//base settings
	$number = isset( $restrictions['votes_number'] ) ? $restrictions['votes_number'] : 'unlimited';
	$user   = isset( $restrictions['votes_user'] ) ? $restrictions['votes_user'] : 'ip';
	$time   = isset( $restrictions['votes_time'] ) ? $restrictions['votes_time'] : 'ever';
	?>
		<select name="rt_polls_settings[default_restriction][votes_number]">
			<option value="1"  <?php if ( $number == 1 )  echo 'selected="selected"'; ?>>1</option>
			<option value="5"  <?php if ( $number == 5 )  echo 'selected="selected"'; ?>>5</option>
			<option value="10" <?php if ( $number == 10 ) echo 'selected="selected"'; ?>>10</option>
			<option value="25" <?php if ( $number == 25 ) echo 'selected="selected"'; ?>>25</option>
			<option value="unlimited" <?php if ( $number == 'unlimited' ) echo 'selected="selected"'; ?>>Unlimited</option>
		</select>
		<span>&nbsp;Vote(s) Per&nbsp;</span>
		<select name="rt_polls_settings[default_restriction][votes_user]">
			<option value="ip"   <?php if ( $user == 'ip' )   echo 'selected="selected"'; ?>>IP</option>
			<option value="user" <?php if ( $user == 'user' ) echo 'selected="selected"'; ?>>Logged In User</option>
		</select>
		<span>&nbsp;Per&nbsp;</span>
		<select name="rt_polls_settings[default_restriction][votes_time]">
			<option value="hour"  <?php if ( $time == 'hour' )  echo 'selected="selected"'; ?>>Hour</option>
			<option value="day"   <?php if ( $time == 'day' )   echo 'selected="selected"'; ?>>Day</option>
			<option value="week"  <?php if ( $time == 'week' )  echo 'selected="selected"'; ?>>Week</option>
			<option value="month" <?php if ( $time == 'month' ) echo 'selected="selected"'; ?>>Month</option>
			<option value="ever"  <?php if ( $time == 'ever' )  echo 'selected="selected"'; ?>>Ever</option>
		</select>
	<?php
}


/**
 * @since  1.0
 */
function secondary_section_cb() {}


/**
 * @since  1.0
 */
function success_message() {
	$options  = get_option('rt_polls_settings');
	$settings_value   = isset( $options['success_message'] ) ? $options['success_message'] : 'Vote recieved.  Thank you for participating!'; ?>
	<textarea id="failure-message" name="rt_polls_settings[success_message]" class="rt-poll-textarea" placeholder="Success message" ><?php echo esc_html( $settings_value ) ?></textarea>	<?php
}


/**
 * @since  1.0
 */
function failure_message() {
	$options  = get_option('rt_polls_settings');
	$settings_value   = isset( $options['failure_message'] ) ? $options['failure_message'] : 'Vote limit has been reached.  Please try again later.'; ?>
	<textarea id="failure-message" name="rt_polls_settings[failure_message]" class="rt-poll-textarea" placeholder="Failure message" ><?php echo esc_html( $settings_value ) ?></textarea>
	<?php
}

