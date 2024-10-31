=== Papaya YouTube Widget ===
Contributors: papayadm, sagargurnani
Tags: youtube, widget, channel
Requires at least: 4.8
Tested up to: 6.1.1
Requires PHP: 5.6
Stable tag: 2.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Simple, lightweight plugin that provides a widget and shortcode to include videos from a YouTube channel in your sidebar or on a page.

== Description ==
Papaya YouTube Widget is a simple, lightweight plugin that provides you with a widget and a shortcode to include videos from a YouTube channel in your sidebar or on a page, a post or any custom content.

For simplicity's sake, the design and layout is limited to one main video, followed by a grid.

To use this plugin, you will need a Google API Key and your YouTube Channel's ID. Short descriptions of how to get these are included in the instructions. If you need more help, please refer to Google's YouTube's own documentation. 

If you would like to customize the way this plugin works, or add features to it, consider hiring the plugin author. 

== Installation ==
1. In your WordPress dashboard, go to **Plugins > Add New**
2. Search for **Papaya YouTube Widget**
3. Click **Install Now** next to the plugin's name.
4. Click **Activate**

Alternatively:
1. Download the plugin
2a. In your WordPress dashboard, go to **Plugins > Add New > Upload Plugin**, and select "Papaya-YouTube-Widget.zip"
OR
2b. unzip the folder, and it upload it via (S)FTP to the **"/wp-content/plugins/"** directory of your WordPress install.
3. Activate the plugin through the "Plugins" menu in WordPress.

== Frequently Asked Questions ==
= How do I find my Google API Key? =
* Log onto https://developers.google.com with a Google account
* Go to https://console.developers.google.com/project
* Click the blue plus sign to Create Project
* Name it whatever you want, e.g. your website's name, and click create
* Once Google is finished creating the project, use the navigation menu to go to the API Library
* Choose YouTube Data API v3
* Double-check that you have the right project selected in the dropdown menu at the top, then click Enable
* Click Create Credentials on the new screen you are taken to.
* In the settings, choose YouTube Data API v3, Web Browser (Javascript), Public data, then click "What credentials do I need)
* Your key should appear. Copy it to the plugin settings. (If you lose it, it will still be available from your Google developers console.)
* Click Done.

… Or, take the optional security steps before you click done:
* You may want to restrict the use of your API key to only calls from your website. If so, click restrict key.
* On the page that appears, choose HTTP referrers, and enter your website's domain, like this .your-domain.com/, under website restrictions.
* Click Save.

= How do I find my YouTube Channel ID? =
To get your channel ID:
* Sign in to YouTube.
* In the top right, click your profile picture.
* Select Settings from the dropdown menu.
* Select Advanced settings from the menu on the left.
* You'll see your channel's user and channel IDs.
* Copy the channel ID to the plugin settings.
If you have trouble finding it, refer to [Google's own documentation](https://support.google.com/youtube/answer/3250431?hl=en).

= How do I use the widget? =
Under **Appearance > Widgets**, add the Papaya YouTube Widget to the sidebar/widgetized area where you want your YouTube Channel featured. If you set a Channel ID for the Widget, it will override the Channel ID set on the settings page.

= How do I use the shortcode? =
Using the classic editor or the Gutenberg shortcode block, insert the shortcode **[papaya_youtube_widget]** into your site where you would like your YouTube channel videos to appear.

You can optionally add the following attributes to the shortcode:
* **channel_id:** A string that is a valid YouTube channel ID. Overrides the channel ID set in the plugin settings.
* **videos:** An integer between 1 and 25. The number of videos to display. Defaults to 5.
* **anchor:** Any alfa-numeric string. The anchor text for the link to the YouTube channel. Defaults to no link.

E.g. **[papaya_youtube_widget videos=9]** will insert a grid with nine videos into the post or page where you use the shortcode.

= Can I change the design or layout? =
Well, there aren't any settings for doing so, but you can make some changes with a bit of custom CSS in your theme customizer: 

You can, for example, change the number of columns of videos under the main video by changing the width of the videos.

**For one column, add this:**
.pyw_poster {
width: 100%;
}

**For three columns:**
@media only screen and (min-width: 481px) {
.pyw_poster {
width: 31%;
margin-right: 3%;
}

div.pyw_poster:nth-child(3n+1) {
margin-right: 0;
}
}

**For four, five or six columns, change the values in the code above according to the values below:**
| columns | width | margin-right | :nthchild value |
| 4 | 23% | 2.75% | 4n+1
| 5 | 19% | 1% | 5n+1
| 6 | 15% | 2% | 6n+1

= It does not work, why? =
Double check that you are using a valid YouTube Channel ID. If you go to **https://www.youtube.com/channel/YOUR-CHANNEL-ID**, you should see the channel you want to embed. Then, log on to **https://console.developers.google.com/apis/** double check that you are using a valid API key.

Also check the API key settings, to make sure there are no restrictions API key, preventing it from being used to access YouTube through your site. Refer to Google and YouTube's own documentation of you are unsure about to find out. Make sure there are no unintentional spaces or characters before or after your Channel ID or API key in the settings.

Note that the widget settings are optional, and override the general plugin settings. So even if the plugin settings are correct, you may have a typo in the widget settings. When you are troubleshooting, remember to clear your caches (server- and browser-side) frequently, so you are not seeing old versions of your website.

= Why do my videos stop showing suddenly? =
If your videos show at first, but then stop showing during the day, you may be exceeding the YouTube API's daily quota. That quota is quite generous, so this won't be a problem for most users. However, if you have many thousands of video views daily (or if you are sharing an API key), you may have to apply for a quota extension.

Refer to Google's own documentation for more about [how the daily quota is calculated](https://developers.google.com/youtube/v3/getting-started#quota), or [to apply for an extension](https://support.google.com/youtube/contact/yt_api_form).

= I get the error message "The request did not specify any referer. Please ensure that the client is sending referer or use the API Console to remove the referer restrictions.". How do I fix it? =
In the http restrictions section in the [Google API Console](https://console.developers.google.com/project/), make sure you have added the domain of the site as it appears under site address in settings. If you can't get it to work at first, add all working versions of your domain, e.g. with both http and https, with and without sub domains, with and without a trailing slash.

= How can I get help with this plugin? =
If you would like to hire someone to help you customize this plugin to fit your site better, please contact [plugin developer Sagar Gurnani](https://www.linkedin.com/in/sagargurnani36/)

If you would like to hire someone to help you with content production, digital marketing and promotion of your site, please [contact Papaya design & marketing](https://papaya.no)

== Screenshots ==
1. The settings
2. The widget settings, with example values
3. The widget in the footer of the Twenty Twenty theme

== Upgrade Notice ==
= 2.3 =
* Compatibility with latest WordPress version 6.1.1 and related fixes
* Fixed a UI bug where the Close Popup button was unaligned

= 2.2 =
Compatibility with latest WordPress version 5.9 and related fixes

= 2.1 =
Compatibility with latest WordPress version 5.7 and related fixes

= 2.0 =
Performance and stability improvements

== Changelog ==
= 2.3 =
* Compatibility with latest WordPress version 6.1.1 and related fixes
* Fixed a UI bug where the Close Popup button was unaligned

= 2.2 =
* Compatibility with latest WordPress version 5.9
* Bug fixes

= 2.1 =
* Compatibility with latest WordPress version 5.7 and related fixes

= 2.0 =
* In order to save on calls to the YouTube API, which might quickly exhaust your quota, the plugin stores information about the videos in your WordPress database
* Automatic checks to look for new videos twice per day to update the stored data in your WordPress database
* For you to update the video information in the database manually so that you don't have to wait for the automatic update we have added "Refresh" button on the plugin settings page
* Performance and stability improvements
