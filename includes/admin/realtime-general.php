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
	register_setting( 'rt_polls_settings_group', 'rt_polls_settings', 'validate_rt_polls_settings' );

	add_settings_section(
		'daily_limit_section',
		__( 'Daily Points Limit', 'rt_polls' ),
		'daily_limit_section_cb',
		__FILE__
	);

	add_settings_field(
		'daily_limit',
		__( 'Daily Points Limit', 'rt_polls' ),
		'daily_limit',
		__FILE__,
		'daily_limit_section'
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
function checkbox() {
	$options = get_option('rt_polls_settings');
	$settings_value = isset( $options['widgets_check'] ) ? $options['widgets_check'] : 0;
	?>
		<input type="checkbox" id="rt_polls_settings[widgets_check]" name="rt_polls_settings[widgets_check]" value="1" <?php checked( 1, $settings_value ) ?> />
	<?php
}


/**
 * @since  1.0
 */
function radio() {
	$options = get_option('rt_polls_settings');
	//if reward_types isset and is an array, return it, otherwise create a blank array.
	$settings_value = isset( $options['reward_class'] ) ? $options['reward_class'] : 'reward_score';
	?>

	<input type="radio" id="reward_currency" name="rt_polls_settings[reward_class]" value="reward_currency" <?php checked( 'reward_currency', $settings_value ) ?> />
	<span class="description">&nbsp;&nbsp;<?php _e( 'Use Points as Currency and Activate Rewards Catalog', 'rt_polls' ) ?></span><br />

	<input type="radio" id="reward_score" name="rt_polls_settings[reward_class]" value="reward_score" <?php checked( 'reward_score', $settings_value ) ?> />
	<span class="description">&nbsp;&nbsp;<?php _e( 'Use Points to keep Scores', 'rt_polls' ) ?></span>
	<?php
}

/**
 * Validate General Settings
 *
 * @todo Get this up and running
 * @since  1.0
 */
function validate_rt_polls_settings( $input ) {
	$options = get_option( 'rt_polls_settings' );
	$output = array();
	foreach( $input as $key => $value ) {
		if( isset( $input[$key] ) ) {
			$output[$key] = strip_tags( stripslashes( $input[$key] ) );
		}
	}
	return apply_filters( 'validate_rt_polls_settings', $output, $input );
}
