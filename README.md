##GW-IG-FEED

GW-IG-FEED is an Instagram feed plugin made by [George Whitcher](http://georgewhitcher.com).  Due to restrictions on the Instagram API you can only get 20 items at this time.

1. Download and extract contents to the /wp-content/plugins/ folder
2. Activate plugin.
3. Visit plugin administration page and click "Sign in with Instagram"
4. Click "Authorize".
5. You will be forwarded to a URL and back with the access token now filled in.
6. Enter your Instagram username and submit the form.
7. Use the shortcode or PHP code provided below to display your Instagram feed.

####Shortcode
Instagram GW comes with a shortcode.  Once configured you can display the feed anywhere you like with simply inserting [INSTAGRAM-GW] into any page.

####PHP
To insert Instagram GW into a PHP page, activate the plugin and then enter the following into the page in which you would like it displayed.

1. $instagramgw = new InstagramGW();
2. $instagramgw->instagram_gw_shortcode();

OR (to design your own (see administration.php for example))

1. $instagramgw = new InstagramGW();
2. $instagramgw_array = $instagramgw->instagram_gw_init();
3. foreach($instagramgw_array as $instagw) { //your code here
}