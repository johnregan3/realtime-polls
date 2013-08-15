<?php

/**
 * Generate Poll Shortcode
 *
 * @since 1.0
 * @todo Calculate percent of total votes for each Label
 */

function rt_polls_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array('id' => ''), $atts ) );
	$nonce   = wp_create_nonce( "rt_poll_vote_nonce" );
	$poll_id = $id;
	$options = get_post_meta( $poll_id, 'rt_polls_data', true );
	$user    = wp_get_current_user();
	$user_id = $user->ID;
	$ip      = $_SERVER['SERVER_ADDR'];
	$user    = ( 'user' == $options['votes_user'] ) ? $user_id : $ip ;

	//calculate css
	$labels_array = RT_POLLS::labels_array( $poll_id );

$rt_options = get_option('rt_polls_settings');
	if ( isset( $rt_options['fancy_styles'] ) && ( 1 == $rt_options['fancy_styles'] ) ) {
		?>
		<style type="text/css">
		.legendColorBox div {
			border-radius: 2px;
			-webkit-border-radius: 2px;
			-moz-border-radius: 2px;
		}
		</style>
	<?php } ?>
	<script type="text/javascript">
	jQuery(document).ready(function () {
<?php
$a = 0;
$i = 1;
end($labels_array);
$last_key = key($labels_array);
	foreach ( $labels_array as $label => $val ) :
	$votes = isset( $options[$val] ) ? $options[$val] : 0 ;
		echo 'var dl_' . $i . ' = [[' . $a . ', ' . esc_html( $votes ) . ']];';
		$a++;
		$i++;
		?>


		<?php
		endforeach; ?>

	<?php
	$i = 1;
	echo 'var data_1 = [';
		foreach ( $labels_array as $label => $val ) :
	$votes = isset( $options[$val] ) ? $options[$val] : 0 ;
	$org_color = $options["field-color-" . $i];
		$gradient = RT_Colors::adjustBrightness( $org_color, -80);
		$rt_options = get_option('rt_polls_settings');
			if ( isset( $rt_options['fancy_styles'] ) && 1 == $rt_options['fancy_styles'] ) {
				$color = "{ colors: [ '" . $org_color . "', '" . $gradient . "'] }";
			} else {
				$color = '"' . $org_color . '"';
			}

			echo '{label: "' . esc_html( $val ) . '",
			data: dl_' . $i .',
			bars: {
				show: true,
                barWidth: .9,
                fill: true,
                align: "center",
                lineWidth: 1,
                order: ' . $i . ',
				fillColor: ' . $color . ',
				},
			color: "' . $org_color . '"
		}';
		if($label !== $last_key)
			echo ',';
		$i++;
endforeach;
	?>
];


    jQuery.plot(jQuery("#placeholder"), data_1, {
    	legend: {
			show: true,
			container : jQuery('#newlegend')
		},
        xaxis: {
        	tickLength: '0',
        	ticks: [
        	<?php
        	$i = 0;
        	foreach ( $labels_array as $label => $val ) : ?>
        		[<?php echo $i; ?>, ""]
        		<?php
        		if($label !== $last_key)
					echo ',';
				$i++;
        	endforeach;
        	?>]
        },
        grid: {
        	borderWidth: 0,
        }
       });
});
</script>
	<div id="placeholder" style="width: 100%; height: 400px;">
	</div>
	<div id="newlegend"></div>

	<div id="rt-poll-vote-area">
		<select id="rt-vote-select">
			<?php
			$numbers = array( 1, 2, 3, 4, 5, 6 );
			foreach ( $numbers as $num ) {
				${'label_' . $num } = isset( $options['label-title-' . $num] ) ? $options['label-title-' . $num] : '';
				if ( ${'label_' . $num } ) {
					echo "<option value=" . esc_attr( 'label-title-' . $num ) . ">" . esc_html( ${'label_' . $num } ) . "</option>";
				}
			}
			?>
		</select>

		<?php
			$link = admin_url('admin-ajax.php?action=rt_poll_process&poll=' . $poll_id .'&user=' . esc_attr( $user ) . '&nonce=' . esc_attr( $nonce ) );
			echo '<input type="button" id="rt-poll-button" data-poll="' . esc_attr( $poll_id ) . '" data-user="' . esc_attr( $user ) . '" data-nonce="' . esc_attr( $nonce )  . '" href="' . esc_url( $link ) . '" value="Vote" />';
		?>
		<div id="message-area"></div>
	</div>
	<?php

}

add_shortcode( 'Poll', 'rt_polls_shortcode' );
