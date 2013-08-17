<?php
/**
 * Example Widget Class
 */
class rt_polls_widget extends WP_Widget {

		/** constructor -- name this the same as the class above */
		function rt_polls_widget() {
				parent::WP_Widget(false, $name = 'Simple Realtime Polls');
		}

		static $add_script;

		static function init() {
			add_action('init', array(__CLASS__, 'register_script'));
			add_action('wp_footer', array(__CLASS__, 'print_script'));
		}

		static function register_script() {
				wp_register_script( 'rt-polls-script', plugins_url( 'js/widget-scripts.js', __FILE__), array( 'jQuery' ), '1.0', true );
		}

		static function print_script() {
			if ( ! self::$add_script )
				return;

			wp_print_scripts('rt-polls-script');
		}

		/** @see WP_Widget::widget -- do not rename this */
		function widget($args, $instance) {
			self::init();
			self::$add_script = true;
			extract( $args );
			//Widget Variables
			$title          = apply_filters('widget_title', $instance['title']);
			$pre_message    = isset( $instance['pre_message'] )  ? $instance['pre_message'] : '';
			$poll_id        = isset( $instance['poll_id'] )      ? $instance['poll_id'] : '';
			$post_message   = isset( $instance['post_message'] ) ? $instance['post_message'] : '';

			//Poll Variables
			$poll_fields  = get_post_meta( $poll_id, 'rt_polls_data', true );
			$poll_obj     = get_post( $poll_id );
			$labels_array = RT_Polls::labels_array( $poll_id );

			//Form Variables
			$nonce       = wp_create_nonce( "rt_poll_vote_nonce" );
			$user        = wp_get_current_user();
			$ip          = $_SERVER['SERVER_ADDR'];
			$settings    = get_option('rt_polls_settings');
			$restriction = $settings['default_restriction'];
			$user        = ( 'user' == $restriction['votes_user'] ) ? $user->ID : $ip ;

			echo $before_widget;

			if ( $title )
				echo $before_title . esc_html( $title ) . $after_title;

			if ( ! empty( $pre_message ) ) : ?>
				<p><?php echo esc_html( $pre_message ); ?></p>
			<?php endif; ?>

			<h4 id="rt-polls-widget-poll-title"><?php echo esc_html( $poll_obj->post_title ) ?></h4>

			<?php
			/**
			 *	@todo Find a better way to load js with variables into the page (& only load on page with poll)
			 */
			?>
			<script type="text/javascript">
			<?php
				$js_data = RT_POLLS::combine_data( $poll_id );
				echo $js_data;

				$script_options = "{" . RT_POLLS::prep_options( $poll_id ) . "}";
				 ?>
				 jQuery(document).ready( function(){
					jQuery.plot( jQuery( "#widget-graph" ),  data_1, <?php echo $script_options ?> );
				});
			</script>

			<?php
			$i = 1;
			foreach ( $labels_array as $label => $val ) : ?>
				<div><input type="radio" value="<?php echo esc_html( $val ) ?>" name="rt_polls_data[<?php echo esc_html( 'label-title-' . $i ) ?>]" ?><span><?php echo esc_html( $val ) ?></span></div>
			<?php
			$i++;
			endforeach;

			$link = admin_url('admin-ajax.php?action=rt_poll_process&poll=' . $poll_id .'&user=' . esc_attr( $user ) . '&nonce=' . esc_attr( $nonce ) );
			echo '<input type="button" id="rt-poll-widget-button" data-poll="' . esc_attr( $poll_id ) . '" data-user="' . esc_attr( $user ) . '" data-nonce="' . esc_attr( $nonce )  . '" href="' . esc_url( $link ) . '" value="Vote" />';
			?>

			<div id="widget-graph" style="width: 100%; height: 150px;" ></div>

			<?php
			if ( ! empty( $post_message ) ) : ?>
				<p><?php echo esc_html( $post_message ); ?></p>
			<?php endif;

			echo $after_widget;
		}

		/** @see WP_Widget::update -- do not rename this */
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title']        = strip_tags($new_instance['title']);
			$instance['pre_message']  = strip_tags($new_instance['pre_message']);
			$instance['poll_id']      = strip_tags($new_instance['poll_id']);
			$instance['post_message'] = strip_tags($new_instance['post_message']);
					return $instance;
		}

		/** @see WP_Widget::form -- do not rename this */
		function form($instance) {
			$polls    = get_posts( array( 'post_type' => 'rt_poll' ) );
			$title    = isset( $instance['title'] ) ? $instance['title'] : '';
			$pre_message  = isset( $instance['pre_message'] ) ? $instance['pre_message'] : '';
			$post_message  = isset( $instance['post_message'] ) ? $instance['post_message'] : '';
			?>
			 <p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:'); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'pre_message' ); ?>"><?php _e('Before Text'); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pre_message' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pre_message' ) ); ?>" type="text" value="<?php echo esc_attr( $pre_message ); ?>" />
			</p>
			<p>
				<select name="<?php echo esc_attr( $this->get_field_name( 'poll_id' ) ); ?>">
					<?php foreach ( $polls as $poll ) : ?>
						<option value="<?php echo esc_attr( $poll->ID ) ?>"><?php echo esc_html( $poll->post_title ) ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'post_message' ); ?>"><?php _e('After Text'); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_message' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_message' ) ); ?>" type="text" value="<?php echo esc_attr( $post_message ); ?>" />
			</p>
			<?php
		}


} // end class example_widget
add_action('widgets_init', create_function('', 'return register_widget("rt_polls_widget");'));
?>