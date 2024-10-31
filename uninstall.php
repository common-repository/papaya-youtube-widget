<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
$option_name = 'papaya_youtube_widget_options';
 
delete_option($option_name);
 
// For site options in Multisite
delete_site_option($option_name);

// Drop a custom database table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pyw_video_data");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pyw_channels");
