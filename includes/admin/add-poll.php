<?php

/**
 * Content of the Add Poll Page
 *
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap">
	<div class="icon32" id="rt-polls-icon">
		<br />
	</div>
	<h2><?php _e( 'Add New Poll', 'rt_polls' ); ?>&nbsp;&nbsp;&nbsp;<a href="<?php echo admin_url( 'admin.php?page=realtime-polls.php&post_type=rt_poll' ); ?>" class="button-secondary"><?php _e( 'Go Back', 'rt_polls' ); ?></a></h2>
	<form id="rew-add-item" action="" method="POST">
		<table class="form-table">
			<tbody>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="name"><?php _e( 'Poll Title', 'rt_polls' ); ?></label>
					</th>
					<td>
						<input name="name" id="name" type="text" value="" style="width: 300px;"/>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="orientation"><?php _e( 'Graph Orientation', 'rt_polls' ); ?></label>
					</th>
					<td>
							<input type="radio" id="orientation" name="orientation" value="Horizontal" style="width: 2%;" />
							<span class="description">&nbsp;&nbsp;<?php _e( 'Horizontal', 'rt_polls' ) ?></span><br />

							<input type="radio" id="orientation" name="orientation" value="Vertical" style="width: 2%;"/>
							<span class="description">&nbsp;&nbsp;<?php _e( 'Vertical', 'rt_polls' ) ?></span>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="label_title"><?php _e( 'Labels', 'rt_polls' ); ?></label>
					</th>
					<td>
						<input type="text" id="label-title-1" name="label-title-1" value="" class="rt-poll-label" placeholder="Label title" /><input type="text" name="field-color-1" value="#777777" class="color-field" />
						<br />
						<input type="text" id="label-title-2" name="label-title-2" value="" class="rt-poll-label" /><input type="text" name="field-color-2" value="#666666" class="color-field" />
						<br />
						<input type="text" id="label-title-3" name="label-title-3" value="" class="rt-poll-label" /><input type="text" name="field-color-3" value="#aaaaaa" class="color-field" />
						<br />
						<input type="text" id="label-title-4" name="label-title-4" value="" class="rt-poll-label" /><input type="text" name="field-color-4" value="#bbbbbb" class="color-field" />
						<br />
						<input type="text" id="label-title-5" name="label-title-5" value="" class="rt-poll-label" /><input type="text" name="field-color-5" value="#888888" class="color-field" />
						<br />
						<input type="text" id="label-title-6" name="label-title-6" value="" class="rt-poll-label" /><input type="text" name="field-color-6" value="#999999" class="color-field" />
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="limit-votes"><?php _e( 'Limit Votes', 'rt_polls' ); ?></label>
					</th>
					<td>
						<select name="votes_number">
							<option value="1">1</option>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="25">25</option>
							<option value="unlimited">Unlimited</option>
						</select>
						<span>&nbsp;Per&nbsp;</span>
						<select name="votes_user">
							<option value="ip">IP</option>
							<option value="user">Logged In User</option>
						</select>
						<span>&nbsp;Per&nbsp;</span>
						<select name="votes_time">
							<option value="day">Day</option>
							<option value="week">Week</option>
							<option value="month">Month</option>
							<option value="ever">Ever</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="hidden" name="poll-action" value="add_poll"/>
			<input type="hidden" name="poll-redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=realtime-polls.php' ) ); ?>"/>
			<input type="hidden" name="realtime-polls-nonce" value="<?php echo wp_create_nonce( 'realtime_polls_nonce' ); ?>"/>
			<input type="submit" value="<?php _e( 'Add New Poll', 'rt_polls' ); ?>" class="button-primary"/>
		</p>
	</form>
</div>
