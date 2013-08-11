<?php

 /**
  * Content of the Edit Action Page
  *
  * @since 1.0
  */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! isset( $_GET['poll_id'] ) || ! is_numeric( $_GET['poll_id'] ) )
	wp_die( __( 'Error.', 'rt_polls' ), __( 'Error', 'rt_polls' ) );


$poll_id         = absint( $_GET['poll_id'] );
$poll            = polls_get_poll( $poll_id );
$meta = get_post_meta( $poll_id, 'rt_polls_data', true );

?>

<div class="wrap">
	<div class="icon32" id="rt-polls-icon">
		<br />
	</div>
	<h2><?php _e( 'Edit Poll', 'rt_polls' ); ?> - <a href="<?php echo admin_url( 'admin.php?page=realtime-polls.php&post_type=rt_poll' ); ?>" class="button-secondary"><?php _e( 'Go Back', 'rt_polls' ); ?></a></h2>
	<form id="rt-polls-edit-tiem" action="" method="post">
		<table class="form-table">
			<tbody>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="name"><?php _e( 'Name', 'rt_polls' ); ?></label>
					</th>
					<td>
						<input name="name" id="name" type="text" value="<?php echo esc_attr( $poll->post_title ); ?>" style="width: 300px;"/>
						<p class="description"><?php _e( 'Poll Table.', 'rt_polls' ); ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="orientation"><?php _e( 'Graph Orientation', 'rt_polls' ); ?></label>
					</th>
					<td>
							<input type="radio" id="orientation" name="orientation" value="Horizontal" style="width: 2%;" <?php esc_html( checked( 'Horizontal', $meta['orientation'] ) ) ?>/>
							<span class="description">&nbsp;&nbsp;<?php _e( 'Horizontal', 'rt_polls' ) ?></span><br />

							<input type="radio" id="orientation" name="orientation" value="Vertical" style="width: 2%;" <?php esc_html( checked( 'Vertical', $meta['orientation'] ) ) ?>/>
							<span class="description">&nbsp;&nbsp;<?php _e( 'Vertical', 'rt_polls' ) ?></span>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="label_title"><?php _e( 'Labels', 'rt_polls' ); ?></label>
					</th>
					<td>
						<?php
						$numbers = array( 1, 2, 3, 4, 5, 6 );
						$fields = array( 'label-title-', 'field-color-' );
						foreach ( $numbers as $number ) : ?>
								<input type="text" id="<?php echo esc_html( 'label-title-' . $number ) ?>" name="<?php echo esc_html( 'label-title-' . $number ) ?>" value="<?php echo esc_html( $meta['label-title-' . $number] ) ?>" class="rt-poll-label" placeholder="Label title" /><input type="text" name="<?php echo esc_html( 'field-color-' . $number ) ?>" value="<?php echo esc_html( $meta['field-color-' . $number] ) ?>" class="color-field" />
								<br />
						<?php endforeach; ?>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="limit-votes"><?php _e( 'Limit Votes', 'rt_polls' ); ?></label>
					</th>
					<td>
						<select name="votes_number">
							<option value="1" <?php if ( $meta['votes_number'] == 1 ) echo 'selected="selected"'; ?>>1</option>
							<option value="5" <?php if ( $meta['votes_number'] == 5 ) echo 'selected="selected"'; ?>>5</option>
							<option value="10" <?php if ( $meta['votes_number'] == 10 ) echo 'selected="selected"'; ?>>10</option>
							<option value="25" <?php if ( $meta['votes_number'] == 25 ) echo 'selected="selected"'; ?>>25</option>
							<option value="unlimited" <?php if ( $meta['votes_number'] == 'unlimited' ) echo 'selected="selected"'; ?>>Unlimited</option>
						</select>
						<span>&nbsp;Per&nbsp;</span>
						<select name="votes_user">
							<option value="ip" <?php if ( $meta['votes_user'] == 'ip' ) echo 'selected="selected"'; ?>>IP</option>
							<option value="user" <?php if ( $meta['votes_user'] == 'user' ) echo 'selected="selected"'; ?>>Logged In User</option>
						</select>
						<span>&nbsp;Per&nbsp;</span>
						<select name="votes_time">
							<option value="day" <?php if ( $meta['votes_time'] == 'day' ) echo 'selected="selected"'; ?>>Day</option>
							<option value="week" <?php if ( $meta['votes_time'] == 'week' ) echo 'selected="selected"'; ?>>Week</option>
							<option value="month" <?php if ( $meta['votes_time'] == 'month' ) echo 'selected="selected"'; ?>>Month</option>
							<option value="ever" <?php if ( $meta['votes_time'] == 'ever' ) echo 'selected="selected"'; ?>>Ever</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="hidden" name="poll-action" value="edit_poll"/>
			<input type="hidden" name="poll_id" value="<?php echo absint( $_GET['poll_id'] ); ?>"/>
			<input type="hidden" name="poll-redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=realtime-polls.php&post_type=rt_poll' ) ); ?>"/>
			<input type="hidden" name="realtime-polls-nonce" value="<?php echo wp_create_nonce( 'realtime_polls_nonce' ); ?>"/>
			<input type="submit" value="<?php _e( 'Update Poll', 'rt_polls' ); ?>" class="button-primary"/>
		</p>
	</form>
</div>