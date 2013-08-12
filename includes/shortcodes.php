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
	foreach ( $options as $option => $val ) {
		if ( strpos($option, 'label-title-') !== false ) {
			$labels_array[] = $val;
		}
	}

	$count =  count( $labels_array );
	$bar_width = 100 / $count;
	?>
	<style type="text/css">
		.bar { width: <?php echo $bar_width; ?>%; ?> }

		<?php $i = 0;
		foreach( $labels_array as $label ) {
			$num = $i + 1;
			echo ".fig" . $i . " { background: " . $options['field-color-' . $num] . "; left: " . $bar_width * $i . "% }\n";
			$i++;
		}
		?>

	</style>
	<div id="wrapper">
		<div class="chart">
			<table id="data-table" border="1" cellpadding="10" cellspacing="0">
				<tbody>
					<?php foreach ( $labels_array as $label => $val ) :
							$votes = isset( $options[$val] ) ? $options[$val] : 0 ; ?>
							<tr>
								<th scope="row"><?php echo esc_html( $val ); ?></th>
								<td><?php echo esc_html( $votes ) ?></td>
							</tr>
						<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>

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
