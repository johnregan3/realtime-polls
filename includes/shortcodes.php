<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Generate Poll Shortcode
 *
 * @since 1.0
 * @todo Calculate percent of total votes for each Label
 */

class RT_Polls_Shortcode {

	static $add_script;

	static function init() {
		add_shortcode('Poll', array(__CLASS__, 'rt_shortcode'));

		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
	}

	static function register_script() {
			wp_register_script( 'rt-polls-script', plugins_url( 'js/scripts.js', __FILE__), array( 'jQuery' ), '1.0', true );
	}

	static function print_script() {
		if ( ! self::$add_script )
			return;

		wp_print_scripts('rt-polls-script');
	}



 static function rt_shortcode( $atts, $content = null ) {
 	self::$add_script = true;
	extract( shortcode_atts( array('id' => ''), $atts ) );

	//Gather vars
	$nonce   = wp_create_nonce( "rt_poll_vote_nonce" );
	$poll_id = $id;
	$options = get_post_meta( $poll_id, 'rt_polls_data', true );
	$user    = wp_get_current_user();
	$user_id = $user->ID;
	$ip      = $_SERVER['SERVER_ADDR'];
	$user    = ( 'user' == $options['votes_user'] ) ? $user_id : $ip ;
	$rt_options = get_option('rt_polls_settings');

	$labels_array = RT_POLLS::labels_array( $poll_id ); ?>

	<script type="text/javascript">
	<?php
		$js_data = RT_POLLS::combine_data( $poll_id );
		echo $js_data;

		$script_options = "{" . RT_POLLS::prep_options( $poll_id ) . "}";
		 ?>
		 jQuery(document).ready( function(){
			jQuery.plot( jQuery( "#placeholder" ),  data_1, <?php echo $script_options ?> );
		});
	</script>

	<!-- Render the graph -->
	<div id="placeholder" style="width: 100%; height: 400px;"></div>
	<div id="newlegend"></div>

	<!-- Render the Message Area -->
	<div id="rt-poll-vote-area">
		<select id="rt-vote-select">
			<?php $numbers = array( 1, 2, 3, 4, 5, 6 );
			foreach ( $numbers as $num ) :
				${'label_' . $num } = isset( $options['label-title-' . $num] ) ? $options['label-title-' . $num] : '';
				if ( ${'label_' . $num } ) {
					echo "<option value=" . esc_attr( 'label-title-' . $num ) . ">" . esc_html( ${'label_' . $num } ) . "</option>";
				}
			endforeach; ?>
		</select>

		<?php $link = admin_url('admin-ajax.php?action=rt_poll_process&poll=' . $poll_id .'&user=' . esc_attr( $user ) . '&nonce=' . esc_attr( $nonce ) );
			echo '<input type="button" id="rt-poll-button" data-poll="' . esc_attr( $poll_id ) . '" data-user="' . esc_attr( $user ) . '" data-nonce="' . esc_attr( $nonce )  . '" href="' . esc_url( $link ) . '" value="Vote" />';
		?>
		<div id="message-area"></div>
	</div>
	<?php } // End Shortcode

} // End Class



RT_Polls_Shortcode::init();
