<?php

/**
 * rt_polls WP Actions Table
 *
 * @since  1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Realtime_Polls_Table extends WP_List_Table {

	/**
	 * Set up Table
	 *
	 * @since  1.0
	 * @see    WP_List_Table::__construct()
	 * @return void
	 */
	public function __construct() {

		parent::__construct( array(
			'singular'  => 'Poll',    // Singular name of the listed records
			'plural'    => 'Polls',        // Plural name of the listed records
			'ajax'      => false                        // Does this table support ajax?
		) );
	}




	/**
	 * Retrieve the table columns
	 *
	 * @since  1.0
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'             => '<input type="checkbox" />',
			'name'           => __( 'Name', 'rt_polls' ),
			'date_created'   => __( 'Date Created', 'rt_polls' ),
			'shortcode'      => __( 'Shortcode', 'rt_polls' ),

		);

		return $columns;
	}




	/**
	 * Retrieve the table's sortable columns
	 *
	 * @since  1.0
	 * @since  1.4
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		return array(
			'name'   => array( 'name', true ),
			'date_created'   => array( 'date', true ),
		);
	}




	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @since 1.0
	 * @param array  $item Contains all the data of the reward
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	function column_default( $poll, $column_name ) {
		switch( $column_name ){
			default:
				return $poll[ $column_name ];
		}
	}



	/**
	 * Render the Name Column
	 *
	 * @since  1.0
	 * @param  array  $item Contains all the data of the action 111
	 * @return string Data shown in the Name column
	 */
	function column_name( $item ) {
		$row     = get_post( $item['ID'] );
		$base         = admin_url( 'admin.php?page=realtime-polls.php&poll_id=' . $item['ID'] );
		$row_actions  = array();

		$row_actions['edit'] = '<a href="' . add_query_arg( array( 'poll-action' => 'edit_poll', 'poll_id' => $row->ID ) ) . '">' . __( 'Edit', 'rt_polls' ) . '</a>';

		$row_actions['delete'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'poll-action' => 'delete_action', 'poll_id' => $row->ID ) ), 'realtime_polls_nonce' ) . '">' . __( 'Delete', 'rt_polls' ) . '</a>';

		return $item['name'] . $this->row_actions( $row_actions );
	}




	/**
	 * Render the checkbox column
	 *
	 * @since  1.0
	 * @param  array $item Contains all the data for the checkbox column
	 * @return string Displays a checkbox
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],
			/*$2%s*/ $item['ID']
		);
	}




	/**
	 * Retrieve the bulk actions
	 *
	 * @since  1.0
	 * @return array $actions Array of the bulk actions
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'rt_polls' )
		);

		return $actions;
	}




	/**
	 * Process the delete bulk action
	 *
	 * @since  1.0
	 * @return void
	 */
	public function process_bulk_action() {
		$ids = isset( $_GET['poll'] ) ? $_GET['poll'] : false;

		if ( ! is_array( $ids ) )
			$ids = array( $ids );

		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {
				polls_remove_poll( $id );
			}
		}

	}




	/**
	 * Retrieve all the data
	 *
	 * @since  1.0
	 * @return array Array of all the data for the action 111s
	 */
	public function polls_table_data() {
		$polls_table_data = array();

		$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'ID';
		$order   = isset( $_GET['order'] )   ? $_GET['order']   : 'DESC';

		$args = array(
			'post_type'      => 'rt_poll',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => $orderby,
			'order'          => $order,
			);

		$polls = get_posts( $args );

		if ( $polls ) {
			foreach ( $polls as $poll ) {
				$polls_table_data[] = array(
					'ID'           => $poll->ID,
					'name'         => get_the_title( $poll->ID ),
					'date_created' => get_the_time( 'd M Y H:i a', $poll->ID ),
					'shortcode'    => '[Poll ID=' . $poll->ID . ']'
				);
			}
		}

		return $polls_table_data;
	}


	/**
	 * Render Table
	 *
	 * @since 1.0
	 */
	public function prepare_items() {

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		$data                  = $this->polls_table_data();
		$this->items           = $data;

	}



}
