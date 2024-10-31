<?php
/**
 * Version:       1.0.0
 * License:       GPLv2 or later
 *
 * Copyright (C) 2020  Papaya Design & Marketing (https://papaya.no/)
 *
 * @package    papaya-youtube-widget
 * @version    1.0.0
 */

/**
 * Set the attribute ID of the video iframe
 */
$videoTagId = 'pyw_video_sc';

if( $type == 'widget' ) {
	$videoTagId = 'pyw_video_wg';
}

if( !empty($arrChannelVideos) ) {
	if( absint($maxResults) == 0 ) {
		$maxResults = count($arrChannelVideos);
	}
	?>
	<div id="papaya_youtube_widget">
		<?php
		$videoData   = $arrChannelVideos[0];
		$videoId     = isset($videoData['video_id']) ? esc_html($videoData['video_id']) : null;
		$videoUrl    = 'https://www.youtube.com/embed/' . $videoId . '?rel=0';
		$videoTitle  = isset($videoData['video_title']) ? $videoData['video_title'] : null;
		$videoPoster = isset($videoData['video_thumbnail_url']) ? $videoData['video_thumbnail_url'] : null;
		?>
		<div class="width-full height-auto">
			<iframe width="100%" src="<?php echo esc_url($videoUrl); ?>?rel=0" id="<?php echo $videoTagId; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>
		<?php
		for( $i=1; $i<$maxResults; $i++ ) {
			if( empty($arrChannelVideos[$i]) ) {
				continue;
			}
				
			$videoData = $arrChannelVideos[$i];

			$videoId   = isset($videoData['video_id']) ? esc_html($videoData['video_id']) : null;

			if( $videoId == null ) {
				continue;
			}

			$videoUrl = 'https://www.youtube.com/embed/' . $videoId . '?rel=0';
			$videoTitle = isset($videoData['video_title']) ? $videoData['video_title'] : null;
			$videoPoster = isset($videoData['video_thumbnail_url']) ? $videoData['video_thumbnail_url'] : null;

			$orientationClass = 'pyw-right';

			if( $i % 2 == 0 ) {
				$orientationClass = 'pyw-left';
			}
			?>
			<div class="pyw_poster <?php echo $orientationClass; ?>">
				<img src="<?php echo esc_url($videoPoster); ?>" alt="<?php echo esc_attr($videoTitle); ?>" class="pyw_poster_image pointer" data-vurl="<?php echo esc_url($videoUrl); ?>" />
			</div>
			<?php
		}

		if( isset($arrChannelVideos[0]['channel_title']) && $arrChannelVideos[0]['channel_title'] != null ) {
			$channelTitle = $arrChannelVideos[0]['channel_title'];
			$channelId    = $arrChannelVideos[0]['yt_channel_id'];
			$youtubeChannelUrl = 'https://www.youtube.com/channel/' . $channelId;

			/**
			 * Get the anchor text from the plugin settings
			 * page and if it is not null override the 
			 * original YouTube Channel Name
			 */
			$getTektonicOptions = get_option('papaya_youtube_widget_options');
			$anchorTextOption = isset($getTektonicOptions['papaya_youtube_widget_anchor_text']) ? sanitize_text_field($getTektonicOptions['papaya_youtube_widget_anchor_text']) : null;

			if( $anchorTextOption != null ) {
				$channelTitle = $anchorTextOption;
			}

			if( $anchorText != null ) {
				$channelTitle = $anchorText;
			}
			?>
			<div id="pyw_channel_name"><a href="<?php echo esc_url($youtubeChannelUrl); ?>" title="<?php echo esc_attr($channelTitle); ?>" target="_blank"><?php echo esc_html($channelTitle); ?></a></div>
			<?php
		}
		?>
		<div id="pyw_yt_tos"><a href="<?php echo esc_url('https://www.youtube.com/t/terms'); ?>" title="<?php _e('YouTube Terms of Service'); ?>" target="_blank"><?php _e('YouTube Terms of Service'); ?></a> &bull; <a href="<?php echo esc_url('https://policies.google.com/privacy'); ?>" title="<?php _e('Google Privacy Policy'); ?>" target="_blank"><?php _e('Google Privacy Policy'); ?></a></div>
	</div>
	<?php
}
