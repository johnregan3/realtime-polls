<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates Gamify WP Admin Menu Page and Actions view
 *
 * @since  1.0
 */

//Table View
include_once( plugin_dir_path(__FILE__) . 'polls-table.php' );

//Process Add/Edit pages
include_once( plugin_dir_path(__FILE__) . 'polls-actions.php' );



/**
 * Set up Gamify WP Admin Menu Page
 *
 * @since  1.0
 */

add_action( 'admin_menu', 'register_polls_submenu_page' );

function register_polls_submenu_page() {
	add_submenu_page( 'realtime-general.php', __( 'Realtime Polls', 'gamify' ), __( 'Polls', 'gamify' ), 'manage_options', basename(__FILE__), 'polls_render_submenu_page' );
}


/**
 * Render Actions Menu Page
 *
 * Detects which page (Edit/Add) is requested, then returns the view.
 *
 * @since  1.0
 */

function polls_render_submenu_page(){

	if ( isset( $_GET['poll-action'] ) && $_GET['poll-action'] == 'edit_poll' ) {
		require_once plugin_dir_path(__FILE__) . 'edit-poll.php';
	} elseif ( isset( $_GET['poll-action'] ) && $_GET['poll-action'] == 'add_poll' ) {
		require_once plugin_dir_path(__FILE__) . 'add-poll.php';
	} else {
		require_once plugin_dir_path(__FILE__) . 'realtime-polls.php';

		$polls_items_table = new Realtime_Polls_Table();
		$polls_items_table->prepare_items();
		?>

		<div class="wrap">
			<div class="icon32" id="rt-polls-icon">
				<br />
			</div>
			<h2><?php _e( 'Realtime Polls', 'rt_polls' ); ?><a href="<?php echo add_query_arg( array( 'poll-action' => 'add_poll' ) ); ?>" class="add-new-h2">Add New</a></h2>
			<form id="polls-items-filter" method="get" action="<?php echo admin_url( 'admin.php?page=realtime-polls.php&post-type=rt_poll' ); ?>">
				<input type="hidden" name="post_type" value="rt_poll" />
				<input type="hidden" name="page" value="realtime-polls.php" />
				<?php $polls_items_table->display() ?>
			</form>
		</div>

		<?php
	}

}
