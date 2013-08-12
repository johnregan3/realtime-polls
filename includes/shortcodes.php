<?php

/**
 * Generate Poll Shortcode
 *
 * @since 1.0
 */

function rt_polls_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array('id' => ''), $atts ) );
	$nonce  = wp_create_nonce( "rt_poll_vote_nonce" );
	$poll_id = $id;
	$options = get_post_meta( $poll_id, false);
	$options = unserialize($options['rt_polls_data'][0]);

	$user = wp_get_current_user();
	$user_id = $user->ID;
	$ip = $_SERVER['SERVER_ADDR'];
	$user = ( 'user' == $options['votes_user'] ) ? $user : $ip ;


	?>
	<div id="wrapper">
		<div class="chart">
			<table id="data-table" border="1" cellpadding="10" cellspacing="0">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<th scope="col">2012</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row">Carbon Tiger</th>
						<td>4080</td>
					</tr>
					<tr>
						<th scope="row">Blue Monkey</th>
						<td>5680</td>
					</tr>
					<tr>
						<th scope="row">Tanned Zombie</th>
						<td>1040</td>
					</tr>
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
			echo '<button id="rt-poll-button" data-poll="' . esc_attr( $poll_id ) . '" data-user="' . esc_attr( $user ) . '" data-nonce="' . esc_attr( $nonce )  . '" href="' . esc_url( $link ) . '">Vote</button>';
		?>
	</div>
	<?php

}

add_shortcode( 'Poll', 'rt_polls_shortcode' );
