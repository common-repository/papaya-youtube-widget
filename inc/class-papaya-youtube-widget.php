<?php
if( !class_exists('PapayaYoutubeWidget')) {
	/**
	 * Class to create the Papaya YouTube Widget
	 * in the WordPress Backend Widgets Section
	 *
	 * Copyright (C) 2020  Papaya Design & Marketing (https://papaya.no/)
	 *
	 * @package    papaya-youtube-widget
	 * @version    1.0
	 */
	class PapayaYoutubeWidget extends WP_Widget {
		/**
		 * The class constructor
		 *
		 * @method  __construct
		 *
		 * @since   1.0
		 */
		public function __construct() {
			$options = array( 
				'classname'   => 'papaya_youtube_widget',
				'description' => esc_html__( 'This lets you add a widget to your sidebar', 'papaya_youtube_widget' )
			);

			parent::__construct( 'PapayaYoutubeWidget', esc_html__('Papaya YouTube Widget', 'papaya_youtube_widget'), $options );
		}
	 
	 	/**
	 	 * This is how it will look at the front end
	 	 *
	 	 * @method  widget
	 	 *
	 	 * @param   Array           $args
	 	 * @param   Array           $instance
	 	 *
	 	 * @return  Array
	 	 *
	 	 * @since   1.0
	 	 */
		public function widget( $args, $instance ) {
			$papaya_youtube_widget_options = get_option('papaya_youtube_widget_options');

			$papaya_youtube_API_key = isset($papaya_youtube_widget_options['papaya_youtube_widget_google_api_key']) ? $papaya_youtube_widget_options['papaya_youtube_widget_google_api_key'] : null;
			$papaya_youtube_channel_id = isset($papaya_youtube_widget_options['papaya_youtube_widget_youtube_channel_id']) ? $papaya_youtube_widget_options['papaya_youtube_widget_youtube_channel_id'] : null;

			echo $args['before_widget'];

			if ( ! empty( $instance['papaya_youtube_widget_title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['papaya_youtube_widget_title'] ) . $args['after_title'];
			}

			$papaya_maximum_limit = 5;
			if( isset( $instance[ 'papaya_maximum_limit' ] ) ) {
				$papaya_maximum_limit = $instance[ 'papaya_maximum_limit' ];
			}

			if( isset( $instance[ 'papaya_youtube_channel_id' ] ) ) {
				$papaya_youtube_channel_id = $instance[ 'papaya_youtube_channel_id' ];
			}

			if( isset( $instance[ 'papaya_anchor_text' ] ) ) {
				$papaya_anchor_text = $instance[ 'papaya_anchor_text' ];
			}

			echo do_shortcode( '[papaya_youtube_widget api_key="'.$papaya_youtube_API_key.'" channel_id="'.$papaya_youtube_channel_id.'" videos="'.$papaya_maximum_limit.'" anchor="'.$papaya_anchor_text.'" type="widget"]' );

			echo $args['after_widget'];
		}

		/**
		 * Create the form which will appear in
		 * the widgets area in the admin backend
		 *
		 * @method  form
		 *
		 * @param   Array           $instance
		 *
		 * @return  String
		 *
		 * @since   1.0
		 */
		public function form( $instance ) {
			$papaya_youtube_widget_title = $papaya_youtube_channel_id = $papaya_maximum_limit = $papaya_anchor_text = null;

			if( isset( $instance[ 'papaya_youtube_widget_title' ] ) ) {
				$papaya_youtube_widget_title = $instance[ 'papaya_youtube_widget_title' ];
			}

			if( isset( $instance[ 'papaya_youtube_channel_id' ] ) ) {
				$papaya_youtube_channel_id = $instance[ 'papaya_youtube_channel_id' ];
			}

			if( isset( $instance[ 'papaya_maximum_limit' ] ) ) {
				$papaya_maximum_limit = $instance[ 'papaya_maximum_limit' ];
			}

			if( isset( $instance[ 'papaya_anchor_text' ] ) ) {
				$papaya_anchor_text = $instance[ 'papaya_anchor_text' ];
			}
			?>
				<p>
					<?php settings_errors(); ?>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'papaya_youtube_widget_title' ); ?>"><?php _e( 'Widget Title:' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'papaya_youtube_widget_title' ); ?>" name="<?php echo $this->get_field_name( 'papaya_youtube_widget_title' ); ?>" type="text" value="<?php echo esc_attr( $papaya_youtube_widget_title ); ?>" placeholder="<?php _e('Enter the widget title here'); ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'papaya_youtube_channel_id' ); ?>"><?php _e( 'YouTube Channel ID:' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'papaya_youtube_channel_id' ); ?>" name="<?php echo $this->get_field_name( 'papaya_youtube_channel_id' ); ?>" type="text" value="<?php echo esc_attr( $papaya_youtube_channel_id ); ?>" placeholder="<?php _e('Enter your YouTube Channel Id'); ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'papaya_maximum_limit' ); ?>"><?php _e( 'Maximum number of videos:' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'papaya_maximum_limit' ); ?>" name="<?php echo $this->get_field_name( 'papaya_maximum_limit' ); ?>" type="number" min="1" max="25" value="<?php echo esc_attr( $papaya_maximum_limit ); ?>" placeholder="<?php _e('Enter the value in number. Default: 5'); ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'papaya_anchor_text' ); ?>"><?php _e( 'Anchor Text:' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'papaya_anchor_text' ); ?>" name="<?php echo $this->get_field_name( 'papaya_anchor_text' ); ?>" type="text" value="<?php echo esc_attr( $papaya_anchor_text ); ?>" placeholder="<?php _e('Enter anchor text here'); ?>" />
				</p>
				<?php wp_nonce_field( 'papaya_youtube_widget_settings', 'papaya_youtube_widget_settings_save' ); ?>
			<?php 
		}

		/**
		 * Updating widget by replacing old instances with new
		 *
		 * @method  update
		 *
		 * @param   Array           $new_instance
		 * @param   Array           $old_instance
		 *
		 * @return  Array
		 *
		 * @since   1.0
		 */
		public function update( $new_instance, $old_instance ) {
			if ( ! isset( $_POST['papaya_youtube_widget_settings_save'] ) || ! wp_verify_nonce( $_POST['papaya_youtube_widget_settings_save'], 'papaya_youtube_widget_settings' ) ) {
			    die( __( 'Insecure request!', 'papaya-youtube-widget' ) ); 
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				die( __('You are trying to attempt an unauthorized action!', 'papaya-youtube-widget' ) );
			}

			$instance = array();
			$instance['papaya_youtube_widget_title'] = ( !empty( $new_instance['papaya_youtube_widget_title'] ) ) ? sanitize_text_field( $new_instance['papaya_youtube_widget_title'] ) : '';
			$instance['papaya_youtube_channel_id'] = ( !empty( $new_instance['papaya_youtube_channel_id'] ) ) ? sanitize_text_field( $new_instance['papaya_youtube_channel_id'] ) : '';
			$instance['papaya_maximum_limit'] = ( !empty( $new_instance['papaya_maximum_limit'] ) ) ? sanitize_text_field( $new_instance['papaya_maximum_limit'] ) : '';
			$instance['papaya_anchor_text'] = ( !empty( $new_instance['papaya_anchor_text'] ) ) ? sanitize_text_field( $new_instance['papaya_anchor_text'] ) : '';

			$getTektonicOptions = get_option('papaya_youtube_widget_options');
    		$googleApiKey = isset($getTektonicOptions['papaya_youtube_widget_google_api_key']) ? sanitize_text_field($getTektonicOptions['papaya_youtube_widget_google_api_key']) : null;

			if( empty($new_instance['papaya_youtube_channel_id']) ) {
				$papaya_youtube_channel_id = isset($getTektonicOptions['papaya_youtube_widget_youtube_channel_id']) ? sanitize_text_field($getTektonicOptions['papaya_youtube_widget_youtube_channel_id']) : null ;
			} else {
				$papaya_youtube_channel_id = $new_instance['papaya_youtube_channel_id'];
			}

			$maxlimit = $instance['papaya_maximum_limit'];
			if( $instance['papaya_maximum_limit'] == null || absint($instance['papaya_maximum_limit']) == 0 ) {
				$maxlimit = 5;
			}

			if( class_exists('PapayaYoutubePlugin') && $papaya_youtube_channel_id != null ) {
				global $objPapayaYoutubePlugin;

				$objPapayaYoutubePlugin->store_youtube_data( $googleApiKey, $papaya_youtube_channel_id, $maxlimit );
			}

			return $instance;
		}
	}
}

/**
 * Register and load the widget
 *
 * @method  papaya_youtube_load_widget
 *
 * @since   1.0
 */
function papaya_youtube_load_widget() {
    register_widget( 'PapayaYoutubeWidget' );
}

add_action( 'widgets_init', 'papaya_youtube_load_widget' );
