/**
 * Papaya YouTube Widget Plugin JS file admin
 *
 * Version:       1.0.0
 * License:       GPLv2 or later
 *
 * Copyright (C) 2020  Papaya Design & Marketing (https://papaya.no/)
 */

jQuery(document).ready(function(){
	jQuery(document).on( 'click', '#pyw_refresh_all_channels, #papaya-youtube-widget-refresh-all-channels', function(e) {
		e.preventDefault();

		jQuery.ajax({
			url: papaya_youtube_widget.ajax_url,
			method: 'POST',
			beforeSend: function(){},
			data: {
				action: 'pyw_refresh_videos_list',
				pywt: papaya_youtube_widget.pywt
			},
			success: function(res){
				console.log(res);
			},
			error: function(err){
				console.log(err);
			},
			complete:  function(){
				location.reload();
			}
		});
	});
});
