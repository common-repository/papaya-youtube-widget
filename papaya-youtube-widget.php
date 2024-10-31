<?php
/**
 * Plugin Name:   Papaya YouTube Widget
 * Description:   Papaya YouTube Widget is a simple, lightweight plugin that provides you with a widget and a shortcode to include videos from a YouTube channel in your sidebar or on a page, a post or any custom content.
 * Author:        Papaya Design & Marketing
 * Author URI:    https://papaya.no/
 * Text Domain:   papaya-youtube-widget
 * Network:       false
 * Slug:          papaya-youtube-widget
 * Version:       2.3
 * License:       GPLv2 or later
 *
 * Copyright (C) 2020  Papaya Design & Marketing (https://papaya.no/)
 *
 * @package    papaya-youtube-widget
 * @version    2.3
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) or die;

/**
 * Main class for PapayaYoutubePlugin
 */
if( !class_exists('PapayaYoutubePlugin') ) {
	class PapayaYoutubePlugin {
		/**
		 * Class variable for the site url
		 *
		 * @var  String
		 */
		public $site_url;

		/**
		 * Class variable for the plugin path
		 *
		 * @var  String
		 */
		public $plugin_path;

		/**
		 * Class variable for the plugin url
		 *
		 * @var  String
		 */
		public $plugin_url;

		/**
		 * Class variable for the upload URL
		 *
		 * @var  String
		 */
		public $upload_url;

		/**
		 * Class variable for the YouTube API Endpoint
		 *
		 * @var  string
		 */
		public $youtubeEndPoint = 'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId=';

		/**
		 * Class constructor
		 *
		 * @method  __construct
		 *
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->site_url    = get_site_url();
			$this->plugin_path = plugin_dir_path( __FILE__ );
			$this->plugin_url  = plugins_url( null, __FILE__ );

			add_action( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links') );
			add_action( 'wp_enqueue_scripts', array($this, 'enqueue_styles') );
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_styles') );
			add_action( 'wp_footer', array($this, 'add_modal_overlay') );
			add_action( 'wp_ajax_pyw_refresh_videos_list', array($this, 'refresh_videos_list') );
			add_action( 'admin_notices', array( $this, 'admin_notice__success' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice__error' ) );
			add_action( 'pyw_cron_hook', array( $this, 'cron_refresh_channels') );

			add_shortcode( 'papaya_youtube_widget', array($this, 'display_youtube_widget') );
			register_activation_hook( __FILE__, array($this, 'install') );
			register_activation_hook( __FILE__, array($this, 'pyw_cron_scheduler') );
			register_deactivation_hook( __FILE__, array($this, 'pyw_cron_unscheduler') );
		}

		/**
		 * Plugin CRON scheduler
		 *
		 * @method  pyw_cron_scheduler
		 *
		 * @since   2.1
		 */
		public function pyw_cron_scheduler() {
		    if ( !wp_next_scheduled('pyw_cron_hook') ) {
		        wp_schedule_event( time(), 'twicedaily', 'pyw_cron_hook' );
		    }
		}

		/**
		 * Plugin CRON unscheduler
		 *
		 * @method  pyw_cron_unscheduler
		 *
		 * @since   2.1
		 */
		public function pyw_cron_unscheduler() {
		    wp_clear_scheduled_hook( 'pyw_cron_hook' );
		}

		/**
		 * Create the requried database tables if the
		 * tables are not already created
		 *
		 * @method  install
		 *
		 * @since   2.1
		 */
		public function install() {
			global $wpdb;

			$parent_table_name = $wpdb->prefix . 'pyw_channels';
			$child_table_name  = $wpdb->prefix . 'pyw_video_data';

			$charset_collate   = $wpdb->get_charset_collate();

			$sql1 = "CREATE TABLE IF NOT EXISTS $parent_table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          yt_channel_id varchar(255) NOT NULL,
          channel_title varchar(255) NOT NULL,
          date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          PRIMARY KEY  (id)
          ) $charset_collate;";

          	$sql2 = "CREATE TABLE IF NOT EXISTS $child_table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          channel_id mediumint(9) NOT NULL,
          video_id varchar(200) NOT NULL,
          video_title varchar(200) NOT NULL,
          video_description tinytext NOT NULL,
          video_thumbnail_url varchar(255) NOT NULL,
          date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          PRIMARY KEY  (id)
          ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            /**
             * Create the table if it does not exist
             */
            if ( $wpdb->get_var("SHOW TABLES LIKE '$parent_table_name'") != $parent_table_name ) {
            	dbDelta( $sql1 );
            }

            /**
             * Create the table if it does not exist
             */
			if ( $wpdb->get_var("SHOW TABLES LIKE '$child_table_name'") != $child_table_name ) {
            	dbDelta( $sql2 );
            }
        }

		/**
		 * Callback to enqueue styles and/or scripts
		 *
		 * @method  enqueue_styles
		 *
		 * @since   1.0.0
		 */
		public function enqueue_styles() {
		    wp_enqueue_style( 'papaya-youtube-widget', $this->plugin_url . '/inc/css/style.css' );
		    wp_enqueue_script( 'jquery' );
		    wp_enqueue_script( 'papaya-youtube-widget', $this->plugin_url . '/inc/js/script.js', array('jquery'), '1.0', true );
		}

		/**
		 * Callback to enqueue admin styles and/or scripts
		 *
		 * @method  enqueue_admin_styles
		 *
		 * @since   2.1
		 */
		public function enqueue_admin_styles() {
			wp_enqueue_script( 'jquery' );
		    wp_enqueue_script( 'papaya-youtube-widget-admin', $this->plugin_url . '/inc/js/admin-script.js', array('jquery'), '1.0', true );

			wp_localize_script( 'papaya-youtube-widget-admin', 'papaya_youtube_widget', array(
		    	'ajax_url' => admin_url('admin-ajax.php'),
		    	'pywt'     => wp_create_nonce( 'papaya_youtube_widget' )
		    ) );
		}

		/**
		 * Callback function for the plugin widget
		 *
		 * @method  display_youtube_widget
		 *
		 * @param   Array                  $atts
		 *
		 * @return  String
		 *
		 * @since   1.0.0
		 */
		public function display_youtube_widget( $atts ) {
			$pluginPath = $this->plugin_path;

			/**
			 * Get the Papaya YouTube Settings from
			 * the plugin settings page
			 *
			 * @var  Array
			 */
			$papaya_youtube_widget_options = get_option('papaya_youtube_widget_options');

			$API_key   = isset($papaya_youtube_widget_options['papaya_youtube_widget_google_api_key']) ? $papaya_youtube_widget_options['papaya_youtube_widget_google_api_key'] : null;
			$channelId = isset($papaya_youtube_widget_options['papaya_youtube_widget_youtube_channel_id']) ? $papaya_youtube_widget_options['papaya_youtube_widget_youtube_channel_id'] : null;
			$maxResults = 5;
			$anchorText = $type = null;

			/**
			 * Get the shortcode attributes
			 */
			if( isset($atts['api_key']) && $atts['api_key'] != null ) {
				$API_key = $atts['api_key'];
			}

			if( isset($atts['channel_id']) && $atts['channel_id'] != null ) {
				$channelId = $atts['channel_id'];
			}

			if( isset($atts['videos']) && absint($atts['videos']) > 0 ) {
				$maxResults = $atts['videos'];
			}

			if( isset($atts['anchor']) && $atts['anchor'] != null ) {
				$anchorText = $atts['anchor'];
			}

			if( isset($atts['type']) && $atts['type'] != null ) {
				$type = $atts['type'];
			}

			$arrChannelVideos = $this->fetch_stored_youtube_videos( $channelId, $maxResults );

			if( $API_key != null && $channelId != null && $type != 'widget' ) {
				$arrChannelVideos = $this->fetch_stored_youtube_videos( $channelId, $maxResults );
				if( empty($arrChannelVideos) ) {
					$this->store_youtube_data( $API_key, $channelId, $maxResults );
				}
			}

			ob_start();
			include( $pluginPath . '/template_parts/show_video_grid.php' );

			return ob_get_clean();
		}

		/**
		 * Get the data from YouTube
		 *
		 * @method  get_youtube_data
		 *
		 * @param   String            $API_key
		 * @param   String            $channelId
		 * @param   integer           $maxResults
		 *
		 * @return  Array
		 *
		 * @since   1.0.0
		 */
		public function get_youtube_data( $API_key, $channelId, $maxResults = 25 ) {
			$youtube_data = array();
			$apiEndPoint  = $this->youtubeEndPoint . $channelId . '&maxResults=' . $maxResults . '&key=' . $API_key;

			/**
			 * Post request to the API end point above
			 * using cURL
			 */
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiEndPoint);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_REFERER, $this->site_url);

			if(curl_exec($ch) === false) {
				if( function_exists('add_settings_error') ) {
				    add_settings_error(
	                    'papaya_youtube_widget_options',
	                    esc_attr( 'invalid-google-api-key' ),
	                    'Request error: ' . curl_error($ch),
	                    'error'
	                );
	            } else {
	            	echo 'Request error: ' . curl_error($ch);
	            }
			} else {
			    $jsonChannelVideos = curl_exec($ch);
			}

			curl_close($ch);

			/**
			 * An array of YouTube channel videos
			 *
			 * @var  Array
			 */
			$youtube_data = json_decode($jsonChannelVideos, true);

			if( isset($youtube_data['error']['message']) ) {
				if( function_exists('add_settings_error') ) {
					add_settings_error(
	                    'papaya_youtube_widget_options',
	                    esc_attr( 'invalid-google-api-key' ),
	                    'Notice: ' . $youtube_data['error']['message'],
	                    'error'
	                );
	            } else {
	            	echo 'Notice: ' . $youtube_data['error']['message'];
	            }
			}

			return $youtube_data;
		}

		/**
		 * Store the data fetched from YouTube in the database
		 *
		 * @method  store_youtube_data
		 *
		 * @since   2.1
		 */
		public function store_youtube_data( $API_key, $channelId, $maxlength = 25 ) {
			/**
             * Get the video from the YouTube API endpoint
             *
             * @var  Array
             */
            $arrChannelVideos = $this->get_youtube_data( $API_key, $channelId, $maxlength );

            if( !empty($arrChannelVideos['items']) ) {
            	$channelTitle = null;
            	if( isset($arrChannelVideos['items'][0]['snippet']['channelTitle']) ) {
            		$channelTitle = sanitize_text_field($arrChannelVideos['items'][0]['snippet']['channelTitle']);
            	}

            	/**
            	 * Save the channel details if the channel
            	 * does not exist in the database table
            	 *
            	 * @var  Integer
            	 */
            	$channelTableId = $this->add_channel_data( $channelId, $channelTitle );

            	if( absint($channelTableId) > 0 ) {
	            	foreach( $arrChannelVideos['items'] as $videoData ) {
						$videoId = sanitize_text_field($videoData['id']['videoId']);
						$videoTitle = sanitize_text_field($videoData['snippet']['title']);
						$videoDescription = sanitize_textarea_field($videoData['snippet']['description']);
						$videoThubnailUrl = esc_url_raw($videoData['snippet']['thumbnails']['high']['url']);

						/**
						 * Add the videos which correspond to the
						 * YouTube channel id above
						 */
	            		$this->add_video_data( $channelTableId, $videoId, $videoTitle, $videoDescription, $videoThubnailUrl );
	            	}
	            }
            }

            return;
		}

		/**
		 * Save the YouTube channel data
		 *
		 * @method  store_channel_data
		 *
		 * @return  Integer
		 *
		 * @since   2.1
		 */
		public function add_channel_data( $channelId, $channelTitle ) {
			global $wpdb;

			$channelTableName = $wpdb->prefix . 'pyw_channels';

			/**
			 * Check whether the YouTube channel id exists
			 *
			 * @var  Array
			 */
			$arrChannelId = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT id FROM '.$channelTableName.' WHERE yt_channel_id=%s',
					$channelId
				),
				'ARRAY_A'
			);

			if( isset($arrChannelId[0]['id']) && absint($arrChannelId[0]['id']) > 0 ) {
				/**
				 * Set the existing YouTube channel id to return
				 *
				 * @var  Integer
				 */
				$intChannelId = absint($arrChannelId[0]['id']);
			} else {
				/**
				 * Get the last insert id when new record is inserted
				 *
				 * @var  Integer
				 */
				$intChannelId = $wpdb->query(
					$wpdb->prepare(
						'INSERT INTO ' . $channelTableName . ' (yt_channel_id, channel_title) values(%s, %s)',
						$channelId,
						$channelTitle
					)
				);
			}

			return $intChannelId;
		}

		/**
		 * Save the video data in the database table
		 *
		 * @method  add_video_data
		 *
		 * @param   String           $channelTableId
		 * @param   String           $videoId
		 * @param   String           $videoTitle
		 * @param   String           $videoDescription
		 * @param   String           $videoThubnailUrl
		 *
		 * @since   2.1
		 */
		public function add_video_data( $channelTableId, $videoId, $videoTitle, $videoDescription, $videoThubnailUrl ) {
			global $wpdb;

			$videoTableName = $wpdb->prefix . 'pyw_video_data';

			$lastInsertId = $wpdb->query(
				$wpdb->prepare( 'INSERT INTO ' . $videoTableName . ' (channel_id, video_id, video_title, video_description, video_thumbnail_url, date_added) SELECT * FROM (SELECT %d, %s, %s, %s, %s, %s) AS tmp WHERE NOT EXISTS ( SELECT video_id FROM ' . $videoTableName . ' WHERE video_id=%s ) LIMIT 1',
					$channelTableId,
					$videoId,
					$videoTitle,
					$videoDescription,
					$videoThubnailUrl,
					date('Y-m-d H:i:s'),
					$videoId
				)
			);

			return (int) $lastInsertId;
		}

		/**
		 * Fetch the YouTube videos data from the database table
		 *
		 * @method  fetch_stored_youtube_videos
		 *
		 * @param   String                       $channelId
		 * @param   String                       $maxResults
		 *
		 * @return  Array
		 *
		 * @since   2.1
		 */
		public function fetch_stored_youtube_videos( $channelId = null, $maxResults = 5 ) {
			global $wpdb;

			/**
			 * Query to get all the videos
			 *
			 * @var  string
			 */
			$query = 'SELECT pc.yt_channel_id, pc.channel_title, pv.channel_id, pv.video_id, pv.video_title, pv.video_description, pv.video_thumbnail_url FROM ' . $wpdb->prefix . 'pyw_channels as pc INNER JOIN ' . $wpdb->prefix . 'pyw_video_data as pv ON pv.channel_id = pc.id WHERE 1';

			/**
			 * Associate the above query with channel
			 * if $channelId is not null
			 */
			if( $channelId != null ) {
				$query .= ' AND pc.yt_channel_id="' . $channelId . '"';
			}

			/**
			 * Limit the query results by $maxResults if the
			 * value of $maxResults is more than zero
			 */
			if( $maxResults > 0 ) {
				$query .= ' LIMIT ' . $maxResults;
			}

			/**
			 * Fetch the YouTube videos from the database table
			 * as an array
			 *
			 * @var  [type]
			 */
			$storedYoutubeVideos = $wpdb->get_results( $query, 'ARRAY_A' );

			return $storedYoutubeVideos;
		}

		/**
		 * Called by AJAX to refresh the videos list
		 * in the database
		 *
		 * @method  refresh_videos_list
		 *
		 * @since   2.1
		 */
		public function refresh_videos_list() {
			if ( ! wp_verify_nonce( $_POST['pywt'], 'papaya_youtube_widget' ) ) {
			    die( __( 'Insecure request!', 'papaya-youtube-widget' ) ); 
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				die( __('You are trying to attempt an unauthorized action!', 'papaya-youtube-widget' ) );
			}

			if( $this->refresh_all_channel_videos() ) {
				update_option('error_notice', 1, 'no');
				die('Channels found and updated');
			}

			update_option('error_notice', 2, 'no');
			die('No channels found');
		}

		/**
		 * Refresh videos for all the channels
		 *
		 * @method  refresh_all_channel_videos
		 *
		 * @return  Boolean
		 *
		 * @since   2.1
		 */
		public function refresh_all_channel_videos() {
			/**
			 * Fetch all the channels from the database
			 *
			 * @var  Array
			 */
			$arrAllChannels = $this->get_all_channels();

			if( !empty($arrAllChannels) ) {
				/**
				 * Get the YouTube API key from the saved settings
				 *
				 * @var  Array
				 */
				$getTektonicOptions = get_option('papaya_youtube_widget_options');
            	$googleApiKey = isset($getTektonicOptions['papaya_youtube_widget_google_api_key']) ? sanitize_text_field($getTektonicOptions['papaya_youtube_widget_google_api_key']) : null;

            	/**
            	 * Loop through each channel to insert new data
            	 * in the datbase table - wp-prefix_pyw_video_data
            	 */
				foreach( $arrAllChannels as $channel ) {
					if( isset($channel['yt_channel_id']) && $channel['yt_channel_id'] != null && $googleApiKey != null ) {
						$this->store_youtube_data( $googleApiKey, $channel['yt_channel_id'] );
					}
				}

				return true;
			}

			return false;
		}

		/**
		 * Called by CRON to refresh the videos list
		 * in the database
		 * 
		 * @method  cron_refresh_channels
		 *
		 * @return  [type]
		 *
		 * @since   2.1
		 */
		public function cron_refresh_channels() {
			$this->refresh_all_channel_videos();
		}

		/**
		 * Function to create action links
		 *
		 * @method  add_action_links
		 *
		 * @param   String                   $links
		 *
		 * @return  Array
		 *
		 * @since   1.0.0
		 */
		public function add_action_links( $links ) {
			$settingsPageUrl = admin_url( 'options-general.php?page=papaya-youtube-widget-settings' );

			$mylinks = array(
				'papaya-youtube-widget-settings' => '<a href="' . esc_html($settingsPageUrl) . '" id="papaya-youtube-widget-settings" title="' . __('Click here to go to the settings page') . '">'.__('Settings').'</a>',
				'papaya-youtube-widget-refresh-all-channels' => '<a href="#" id="papaya-youtube-widget-refresh-all-channels" title="' . __('Click here to let the plugin manually look for new videos') . '">'.__('Refresh').'</a>',
			);

			return $links + $mylinks;
		}

		/**
		 * Shows the overlay for the modal when displayed
		 *
		 * @method  add_modal_overlay
		 *
		 * @since   1.0.0
		 */
		public function add_modal_overlay() {
			$pluginPath = $this->plugin_path;

			include( $pluginPath . 'template_parts/show_video_modal.php' );
		}

		/**
		 * Fetch all the channels from the database
		 *
		 * @method  get_all_channels
		 *
		 * @param   String            $ytChannelId
		 *
		 * @return  Array
		 *
		 * @since   2.1
		 */
		public function get_all_channels( $ytChannelId = null ) {
			global $wpdb;

			$channelTableName = $wpdb->prefix . 'pyw_channels';
			$query = 'SELECT yt_channel_id, channel_title FROM ' . $channelTableName . ' WHERE 1';

			if( $ytChannelId != null ) {
				$query .= ' AND yt_channel_id=' . $ytChannelId;
			}

			$arrAllChannels = $wpdb->get_results(
				$query, 
				'ARRAY_A'
			);

			return $arrAllChannels;
		}

		/**
		 * Callback function to show the admin success notice
		 *
		 * @method  admin_notice__success
		 *
		 * @since   2.1
		 */
		public function admin_notice__success() {
			if(get_option('error_notice') == 1) {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'The Operation was successful', 'papaya-youtube-widget' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );

				delete_option('error_notice');
			}
		}

		/**
		 * Callback function to show the admin error notice
		 *
		 * @method  admin_notice__error
		 *
		 * @since   2.1
		 */
		public function admin_notice__error() {
			if(get_option('error_notice') == 2) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'The operation failed! Please check the plugin settings and try again.', 'papaya-youtube-widget' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );

				delete_option('error_notice');
			}
		}
	}

	/**
	 * Instantiate Class object
	 *
	 * @var  PapayaYoutubePlugin
	 */
	$objPapayaYoutubePlugin = new PapayaYoutubePlugin;
}

/**
 * Shows the custom widget at the front end
 */
include 'inc/class-papaya-youtube-widget.php';

/**
 * Include the settings page for admin user
 */
if( is_admin() ) {
	include 'inc/class-papaya-youtube-plugin-settings.php';

	$objPapayaYoutubePluginSettings = new PapayaYoutubePluginSettings();
}
