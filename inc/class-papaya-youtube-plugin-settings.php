<?php
/**
 * Instantiate Class object
 *
 * @var  PapayaYoutubePlugin
 */
if( !class_exists('PapayaYoutubePluginSettings')) {
    /**
     * Class to create the Papaya YouTube Widget Plugin
     * settings in the WordPress Backend
     *
     * Copyright (C) 2020  Papaya Design & Marketing (https://papaya.no/)
     *
     * @package    papaya-youtube-widget
     * @version    1.0
     */
    class PapayaYoutubePluginSettings extends PapayaYoutubePlugin
    {
        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;

        private $validate = true;

        /**
         * Start up
         */
        public function __construct() {
            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
            add_action( 'admin_init', array( $this, 'page_init' ) );

            parent::__construct();
        }

        /**
         * Add options page
         */
        public function add_plugin_page() {
            // This page will be under "Settings"
            add_options_page(
                __('YouTube Widget'), 
                __('YouTube Widget'), 
                'manage_options', 
                'papaya-youtube-widget-settings', // Page
                array( $this, 'papaya_youtube_widget_settings' )
            );
        }

        /**
         * Options page callback
         */
        public function papaya_youtube_widget_settings() {
            ?>
            <div class="wrap">
                <?php settings_errors( 'papaya_youtube_widget_group', false, false ); ?>
                <form method="post" action="options.php">
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'papaya_youtube_widget_group' );
                    do_settings_sections( 'papaya-youtube-widget-settings' );
                    submit_button('Save Settings');

                    /**
                     * This function shows accordion sections
                     * defined using the add_meta_box() function
                     */
                    do_accordion_sections( 'papaya-youtube-widget-settings', 'normal', null );
                ?>
                </form>
            </div>
            <?php
        }

        /**
         * Register and add settings
         */
        public function page_init() {
            register_setting(
                'papaya_youtube_widget_group', // Option group
                'papaya_youtube_widget_options', // Option name
                array( $this, 'sanitizeSettingsFields' ) // Sanitize
            );

            add_settings_section(
                'papaya_youtube_widget_setting_section_id', // ID
                '<h1>'.__('Papaya Youtube Widget').'<a href="' . admin_url('plugins.php') . '#papaya-youtube-widget-settings" class="upload-view-toggle page-title-action" role="button" aria-expanded="false"><span class="upload">'.__('Plugins').'</span></a><a href="#" title="' . __('Click here to let the plugin manually look for new videos') . '" id="pyw_refresh_all_channels" class="upload-view-toggle page-title-action" role="button" aria-expanded="false"><span class="upload">'.__('Refresh').'</span></a></h1>', // Title
                array( $this, 'print_section_info' ), // Callback
                'papaya-youtube-widget-settings' // Page
            );

            add_settings_field(
                'papaya_youtube_widget_google_api_key', 
                __('Google API Key'), 
                array( $this, 'papayaYoutubeWidgetGoogleAPIKey' ), 
                'papaya-youtube-widget-settings', 
                'papaya_youtube_widget_setting_section_id'
            );

            add_settings_field(
                'papaya_youtube_widget_youtube_channel_id', 
                __('YouTube Channel ID'), 
                array( $this, 'papayaYoutubeWidgetYouTubeChannelId' ), 
                'papaya-youtube-widget-settings', 
                'papaya_youtube_widget_setting_section_id'
            );

            add_settings_field(
                'papaya_youtube_widget_anchor_text', 
                __('Anchor Text (Optional)'), 
                array( $this, 'papayaYoutubeWidgetAnchorText' ), 
                'papaya-youtube-widget-settings', 
                'papaya_youtube_widget_setting_section_id'
            );

            add_meta_box( 
                'howto_google_apikey',
                __( 'How do I find my Google API Key?' ),
                array($this, 'howto_google_apikey'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'howto_youtube_channelid',
                __( 'How do I find my YouTube Channel ID?' ),
                array($this, 'howto_youtube_channelid'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'howto_change_layout',
                __( 'Can I change the layout?' ),
                array($this, 'howto_change_layout'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'howto_use_widget',
                __( 'How do I use the widget?' ),
                array($this, 'howto_use_widget'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'howto_use_shortcode',
                __( 'How do I use the shortcode?' ),
                array($this, 'howto_use_shortcode'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'it_does_not_work_why',
                __( 'It doesn\'t work. Why?' ),
                array($this, 'it_does_not_work_why'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'why_do_my_videos_stop_showing',
                __( 'Why do my videos stop showing?' ),
                array($this, 'why_do_my_videos_stop_showing'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box(
                'http_referer_error',
                __( 'I get the error message "The request did not specify any referer. Please ensure that the client is sending referer or use the API Console to remove the referer restrictions.". How do I fix it?' ),
                array($this, 'http_referer_error'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'newly_published_videos',
                __( 'Why does it take so long for newly published videos to show up in the widget?' ),
                array($this, 'newly_published_videos'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'refresh_videos',
                __( 'Can I manually make Papaya YouTube Widget look for new videos?' ),
                array($this, 'refresh_videos'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'howto_hire_developer',
                __( 'How to hire the developer to make custom plugin versions for you?' ),
                array($this, 'howto_hire_developer'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );

            add_meta_box( 
                'howto_hire_papaya',
                __( 'How to hire Papaya for help with content?' ),
                array($this, 'howto_hire_papaya'),
                'papaya-youtube-widget-settings',
                'normal',
                'low'
            );
        }

        /**
         * Sanitize each setting field as needed
         */
        public function sanitizeSettingsFields() {
            $new_input = array();
            $API_key   = null;
            $channelID = null;

            $is_valid_data = $this->validateSettingsFields( $_POST );

            if( false === $is_valid_data ) {
                return $new_input;
            }

            if( isset( $_POST['papaya_youtube_widget_google_api_key'] ) )
                $new_input['papaya_youtube_widget_google_api_key'] = sanitize_text_field( $_POST['papaya_youtube_widget_google_api_key'] );

            if( isset( $_POST['papaya_youtube_widget_youtube_channel_id'] ) )
                $new_input['papaya_youtube_widget_youtube_channel_id'] = sanitize_text_field( $_POST['papaya_youtube_widget_youtube_channel_id'] );

            if( isset( $_POST['papaya_youtube_widget_anchor_text'] ) )
                $new_input['papaya_youtube_widget_anchor_text'] = sanitize_text_field( $_POST['papaya_youtube_widget_anchor_text'] );

            if( !empty($_POST) ) {
                $API_key   = $new_input['papaya_youtube_widget_google_api_key'];
                $channelID = $new_input['papaya_youtube_widget_youtube_channel_id'];

                $this->store_youtube_data( $API_key, $channelID, 25 );
            }

            return $new_input;
        }

        /**
         * Validate each setting field as needed
         */
        public function validateSettingsFields( $input ) {
            if( $this->validate == false ) {
                return false;
            }

            if( $input['papaya_youtube_widget_google_api_key'] == null ) {
                add_settings_error(
                    'papaya_youtube_widget_options',
                    esc_attr( 'invalid-google-api-key' ),
                    __('Invalid Google API Key!'),
                    'error'
                );

                $this->validate = false;
            } else if( $input['papaya_youtube_widget_youtube_channel_id'] == null ) {
                add_settings_error(
                    'papaya_youtube_widget_options',
                    esc_attr( 'invalid-youtube-channel-id' ),
                    __('Invalid YouTube Channel Id!'),
                    'error'
                );

                $this->validate = false;
            }

            return $this->validate;
        }

        /** 
         * Print the Section text
         */
        public function print_section_info() {
            $sectionInfo = __('Configure the plugin settings below.');

            print $sectionInfo;
        }

        /**
         * Adds the Google API key field to the
         * plugin settings page form
         */
        public function papayaYoutubeWidgetGoogleAPIKey() {
            $getTektonicOptions = get_option('papaya_youtube_widget_options');
            $googleApiKey = isset($getTektonicOptions['papaya_youtube_widget_google_api_key']) ? sanitize_text_field($getTektonicOptions['papaya_youtube_widget_google_api_key']) : null;

            printf(
                '<input type="text" name="papaya_youtube_widget_google_api_key" id="papaya_youtube_widget_google_api_key" value="%s" class="regular-text" /><p class="description">'.__('Please enter your Google API Key here').'</p>',
                esc_html( $googleApiKey )
            );
        }

        public function papayaYoutubeWidgetYoutubeChannelId() {
            $getTektonicOptions = get_option('papaya_youtube_widget_options');
            $googleApiKey = isset($getTektonicOptions['papaya_youtube_widget_youtube_channel_id']) ? sanitize_text_field($getTektonicOptions['papaya_youtube_widget_youtube_channel_id']) : null;

            printf(
                '<input type="text" name="papaya_youtube_widget_youtube_channel_id" id="papaya_youtube_widget_youtube_channel_id" value="%s" class="regular-text" /><p class="description">'.__('Please enter your YouTube Channel ID here').'</p>',
                esc_html( $googleApiKey )
            );
        }

        public function papayaYoutubeWidgetAnchorText() {
            $getTektonicOptions = get_option('papaya_youtube_widget_options');
            $anchorText = isset($getTektonicOptions['papaya_youtube_widget_anchor_text']) ? sanitize_text_field($getTektonicOptions['papaya_youtube_widget_anchor_text']) : null;

            printf(
                '<input type="text" name="papaya_youtube_widget_anchor_text" id="papaya_youtube_widget_anchor_text" value="%s" class="regular-text" /><p class="description">'.__('This field overrides your original YouTube Channel Name.').'</p>',
                esc_html( $anchorText )
            );
        }

        public function howto_google_apikey() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/howto_google_apikey.php' );
        }

        public function howto_youtube_channelid() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/howto_youtube_channelid.php' );
        }

        public function howto_change_layout() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/howto_change_layout.php' );
        }

        public function howto_use_widget() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/howto_use_widget.php' );
        }

        public function howto_use_shortcode() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/howto_use_shortcode.php' );
        }

        public function howto_hire_developer() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/howto_hire_developer.php' );
        }

        public function howto_hire_papaya() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/howto_hire_papaya.php' );
        }

        public function it_does_not_work_why() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/it_does_not_work_why.php' );
        }

        public function why_do_my_videos_stop_showing() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/why_do_my_videos_stop_showing.php' );
        }

        public function newly_published_videos() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/newly_published_videos.php' );
        }

        public function refresh_videos() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/refresh_videos.php' );
        }

        public function http_referer_error() {
            $plugin_path = $this->plugin_path;

            include( $plugin_path . '/template_parts/accordion-templates/http_referer_error.php' );
        }
    }
}

