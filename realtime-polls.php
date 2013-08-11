<?php

/**
 * Plugin Name: Realtime Polls
 * Plugin URI:
 * Description: Create Polls that update in realtime!
 * Author: John Regan
 * Author URI: http://johnregan3.me
 * Version: 1.0
 * Copyright 2013  John Regan  (email : johnregan3@outlook.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Realtime Polls
 * @author John Regan
 * @version 1.0
 */

//Polls Admin Page
include_once( plugin_dir_path(__FILE__) . 'includes/admin/realtime-general.php' );

//Polls Listing Page
include_once( plugin_dir_path(__FILE__) . 'includes/admin/realtime-polls.php' );