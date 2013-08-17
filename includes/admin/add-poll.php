<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Content of the Add Poll Page
 *
 * @since 1.0
 */


/**
 * @todo add option for horizontal graph
 */

$defaults = get_option('rt_polls_settings');
?>

<div class="wrap">
	<div class="icon32" id="rt-polls-icon">
		<br />
	</div>
	<h2><?php _e( 'Add New Poll', 'rt_polls' ); ?>&nbsp;&nbsp;&nbsp;<a href="<?php echo admin_url( 'admin.php?page=tr-polls.php&post_type=rt_poll' ); ?>" class="button-secondary"><?php _e( 'Go Back', 'rt_polls' ); ?></a></h2>
	<form id="rew-add-item" action="" method="POST">
		<table class="form-table">
			<tbody>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="name"><?php _e( 'Poll Title', 'rt_polls' ); ?></label>
					</th>
					<td>
						<input name="name" id="name" type="text" value="" placeholder="Poll Title" style="width: 300px;"/>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="label_title"><?php _e( 'Labels', 'rt_polls' ); ?></label>
					</th>
					<td>
						<?php $numbers = array( 1, 2, 3, 4, 5, 6 );
						$default_colors = array( '', '#666666', '#bbbbbb', '#777777', '#aaaaaa', '#999999', '#cccccc');
						$fields = array( 'label-title-', 'field-color-' );
						foreach ( $numbers as $number ) :
							$color = isset( $defaults['default_colors']['field-color-' . $number] ) ? $defaults['default_colors']['field-color-' . $number] : $default_colors[$number];
							$value = isset( $meta['label-title-' . $number] ) ? $meta['label-title-' . $number] : ''; ?>
							<input type="text" id="<?php echo esc_html( 'label-title-' . $number ) ?>" name="<?php echo esc_html( 'label-title-' . $number ) ?>" value="<?php echo esc_html( $value ) ?>" class="rt-poll-label" placeholder="Label title" /><input type="text" name="<?php echo esc_html( 'field-color-' . $number ) ?>" value="<?php echo esc_html( $color ) ?>" class="color-field" />
							<br />
						<?php endforeach; ?></td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="limit-votes"><?php _e( 'Vote Limit', 'rt_polls' ); ?></label>
					</th>
					<td>
						<select name="votes_number">
							<option value="1"  <?php if ( $defaults['default_restriction']['votes_number'] == 1 )  echo 'selected="selected"'; ?>>1</option>
							<option value="5"  <?php if ( $defaults['default_restriction']['votes_number'] == 5 )  echo 'selected="selected"'; ?>>5</option>
							<option value="10" <?php if ( $defaults['default_restriction']['votes_number'] == 10 ) echo 'selected="selected"'; ?>>10</option>
							<option value="25" <?php if ( $defaults['default_restriction']['votes_number'] == 25 ) echo 'selected="selected"'; ?>>25</option>
							<option value="unlimited" <?php if ( $defaults['default_restriction']['votes_number'] == 'unlimited' ) echo 'selected="selected"'; ?>>Unlimited</option>
						</select>
						<span>&nbsp;Vote(s) Per&nbsp;</span>
						<select name="votes_user">
							<option value="ip"   <?php if ( $defaults['default_restriction']['votes_user'] == 'ip' )   echo 'selected="selected"'; ?>>IP</option>
							<option value="user" <?php if ( $defaults['default_restriction']['votes_user'] == 'user' ) echo 'selected="selected"'; ?>>Logged In User</option>
						</select>
						<span>&nbsp;Per&nbsp;</span>
						<select name="votes_time">
							<option value="hour" <?php if ( $defaults['default_restriction']['votes_time'] == 'hour' ) echo 'selected="selected"'; ?>>Hour</option>
							<option value="day" <?php if ( $defaults['default_restriction']['votes_time'] == 'day' ) echo 'selected="selected"'; ?>>Day</option>
							<option value="week" <?php if ( $defaults['default_restriction']['votes_time'] == 'week' ) echo 'selected="selected"'; ?>>Week</option>
							<option value="month" <?php if ( $defaults['default_restriction']['votes_time'] == 'month' ) echo 'selected="selected"'; ?>>Month</option>
							<option value="ever" <?php if ( $defaults['default_restriction']['votes_time'] == 'ever' ) echo 'selected="selected"'; ?>>Ever</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="hidden" name="poll-action" value="add_poll"/>
			<input type="hidden" name="poll-redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=rt-polls.php' ) ); ?>"/>
			<input type="hidden" name="realtime-polls-nonce" value="<?php echo wp_create_nonce( 'realtime_polls_nonce' ); ?>"/>
			<input type="submit" value="<?php _e( 'Add New Poll', 'rt_polls' ); ?>" class="button-primary"/>
		</p>
	</form>
</div>
