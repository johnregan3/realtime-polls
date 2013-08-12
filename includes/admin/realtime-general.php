<?php


/**
 * Set up rt_polls WP Admin Menu Page
 *
 * @since  1.0
 */
add_action( 'admin_menu', 'register_rt_polls_menu_page' );

function register_rt_polls_menu_page() {
	add_menu_page( __( 'Realtime Polls', 'rt_polls' ), __( 'Realtime Polls', 'rt_polls' ), 'manage_options', basename(__FILE__), 'rt_polls_general_settings' );
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
		__( 'Primary Section', 'rt_polls' ),
		'primary_section_cb',
		__FILE__
	);

	add_settings_field(
		'fancy_styles',
		__( 'Fancy Styling', 'rt_polls' ),
		'fancy_styles',
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
		__( 'Default Restriction', 'rt_polls' ),
		'default_restriction',
		__FILE__,
		'primary_section'
	);

}

add_action( 'admin_init', 'rt_polls_render_fields' );


/**
 * Render the General Admin Page
 *
 * @since  1.0
 */
function rt_polls_general_settings() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.slidebox').parent().css( 'padding', '0' );
		if(jQuery('#reward_currency').is(':checked')) {
				jQuery('.slidebox').hide();
		};
		jQuery('form input:radio').change(function() {
			if(jQuery('.slidebox').is(':visible') && jQuery('#reward_currency').is(':checked') ) {
				jQuery('.slidebox').fadeOut();
			} else if ( jQuery('#reward_score').is(':checked') ) {
				jQuery('.slidebox').fadeIn();
			};
		});
	});
	</script>
	<div id="rt_polls-settings-wrap" class="wrap">
		<div class="icon32" id="rt-polls-icon">
			<br />
		</div>
		<?php _e( '<h2>Realtime Polls Settings</h2>', 'rt_polls'); ?>
		<?php if( isset($_GET['settings-updated']) ) { ?>
			<div id="message" class="updated">
				<p><?php _e('Settings saved.') ?></p>
			</div>
		<?php } ?>
		<p><a href="https://github.com/johnregan3/rt_polls-wp-plugin/wiki/General-Settings"><?php _e( 'Get help for this page on our Wiki', 'rt_polls' ) ?></a>.</p>
		<form method="post" action="options.php" enctype="multipart/form-data">
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
function primary_section_cb() {
	echo "test";
}


/**
 * @since  1.0
 */
function fancy_styles() {
	$options = get_option('rt_polls_settings');
	$settings_value = isset( $options['fancy_styles'] ) ? $options['fancy_styles'] : 0;
	?>
		<input type="checkbox" id="fancy-styles" name="rt_polls_settings[fancy_styles]" value="1" <?php checked( 1, $settings_value ) ?> />
	<?php
}


/**
 * @since  1.0
 */
function default_colors() {
	$options = get_option('rt_polls_settings');
	$colors = isset( $options['default_colors'] ) ? $options['default_colors'] : 0;
	$defaults = array( '', '#B8D0DE', '#9FC2D6', '#86B4CF', '#73A2BD', '#6792AB', '#577B8F');
	$numbers = array( 1, 2, 3, 4, 5, 6 );
		foreach ( $numbers as $number ) :
			$color = isset( $colors['field-color-' . $number] ) ? $colors['field-color-' . $number] : $defaults[$number]; ?>
			<span class="description">Field <?php echo $number; ?></span>&nbsp;&nbsp;<input type="text" name="<?php echo esc_html( 'rt_polls_settings[default_colors][field-color-' . $number . ']' ) ?>" value="<?php echo esc_html( $color ) ?>" class="color-field" />
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
	$user = isset( $restrictions['votes_user'] ) ? $restrictions['votes_user'] : 'ip';
	$time = isset( $restrictions['votes_time'] ) ? $restrictions['votes_time'] : 'ever';
	?>
		<select name="rt_polls_settings[default_restriction][votes_number]">
			<option value="1"  <?php if ( $number == 1 )  echo 'selected="selected"'; ?>>1</option>
			<option value="5"  <?php if ( $number == 5 )  echo 'selected="selected"'; ?>>5</option>
			<option value="10" <?php if ( $number == 10 ) echo 'selected="selected"'; ?>>10</option>
			<option value="25" <?php if ( $number == 25 ) echo 'selected="selected"'; ?>>25</option>
			<option value="unlimited" <?php if ( $number == 'unlimited' ) echo 'selected="selected"'; ?>>Unlimited</option>
		</select>
		<span>&nbsp;Per&nbsp;</span>
		<select name="rt_polls_settings[default_restriction][votes_user]">
			<option value="ip"   <?php if ( $user == 'ip' )   echo 'selected="selected"'; ?>>IP</option>
			<option value="user" <?php if ( $user == 'user' ) echo 'selected="selected"'; ?>>Logged In User</option>
		</select>
		<span>&nbsp;Per&nbsp;</span>
		<select name="rt_polls_settings[default_restriction][votes_time]">
			<option value="hour" <?php if ( $time == 'hour' ) echo 'selected="selected"'; ?>>Hour</option>
			<option value="day" <?php if ( $time == 'day' ) echo 'selected="selected"'; ?>>Day</option>
			<option value="week" <?php if ( $time == 'week' ) echo 'selected="selected"'; ?>>Week</option>
			<option value="month" <?php if ( $time == 'month' ) echo 'selected="selected"'; ?>>Month</option>
			<option value="ever" <?php if ( $time == 'ever' ) echo 'selected="selected"'; ?>>Ever</option>
		</select>
	<?php
}

/**
 * @since  1.0
 */
function checkbox() {
	$options = get_option('rt_polls_settings');
	$settings_value = isset( $options['widgets_check'] ) ? $options['widgets_check'] : 0;
	?>
		<input type="checkbox" id="rt_polls_settings[widgets_check]" name="rt_polls_settings[widgets_check]" value="1" <?php checked( 1, $settings_value ) ?> />
	<?php
}
