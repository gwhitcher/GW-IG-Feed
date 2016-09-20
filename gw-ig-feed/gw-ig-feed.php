<?php
/**
 * Plugin Name: GW-IG-Feed
 * Plugin URI: http://georgewhitcher.com/instagram-gw/
 * Description: This is an Instagram plugin for Wordpress made by George Whitcher.
 * Version: 1.0.0
 * Author: George Whitcher
 * Author URI: http://georgewhitcher.com
 * License: GPL2
 */

//Remove error reports
error_reporting(E_ERROR | E_PARSE);

//Actions
add_action('admin_menu', array('InstagramGW','instagram_gw_admin_call'));
add_shortcode('GW-IG-FEED', array('InstagramGW','instagram_gw_shortcode'));

class InstagramGW {
    public function instagram_gw_admin_call(){
        add_menu_page( 'GW-IG-FEED Administration', 'GW-IG-FEED', 'manage_options', 'gw-ig-feed-plugin', array('InstagramGW','instagram_gw_admin'));
    }
    public function instagram_gw_admin() {
        include("config.php");
        include("administration.php");
    }
    public function instagram_gw_config($access_token=NULL, $username=NULL, $count=NULL) {
        $filename = dirname(__FILE__).'/config.php';
        $f = fopen($filename, "w");
        $msg = "<?php \ndefine('INSTA_ACCESS_TOKEN','".$access_token."');\ndefine('INSTA_USERNAME','".$username."');\ndefine('INSTA_COUNT','".$count."');";
        fwrite($f, $msg);
        fclose($f);
        chmod($filename, 0777);
    }
    public function instagram_gw_init(){
        include('config.php');
        $user_id_json_url = 'https://www.instagram.com/'.INSTA_USERNAME.'/?__a=1';
        $user_id_json = file_get_contents($user_id_json_url);
        $user_id = json_decode($user_id_json);

        $instagram_feed_json_url = "https://api.instagram.com/v1/users/".$user_id->user->id."/media/recent?access_token=".INSTA_ACCESS_TOKEN.'&count='.INSTA_COUNT;
        if(!empty($_GET['max_id'])) {
            $instagram_feed_json_url .= "&max_id=".$_GET['max_id'];
        }
        $instagram_feed_json = file_get_contents($instagram_feed_json_url);
        $instagram_feed = json_decode($instagram_feed_json);
        return $instagram_feed;
    }
    public function instagram_gw_shortcode() {
        $plugin_url = plugins_url().'/instagram-gw';
        $instagramgw = new InstagramGW();
        $instagramgw_data = $instagramgw->instagram_gw_init();
        $instagram_data_display = '<link href="'.$plugin_url.'/css/styles.css" rel="stylesheet">';
        $instagram_data_display .= '<div class="instagram-gw">';
        $i = 0;
        foreach ($instagramgw_data->data as $instagram_post) {
            $i++;
            $instagram_data_display .= '<div class="instagram-unit">';
            $instagram_data_display .= '<a target="blank" data-toggle="modal" data-target="#myModal'.$i.'">';
            $instagram_data_display .= '<img src="'.$instagram_post->images->low_resolution->url.'" alt="'.$instagram_post->caption->text.'" />';
            $instagram_data_display .= '</a>';
            $instagram_data_display .= '</div>';
            $instagram_data_display .= '<div id="myModal'.$i.'" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Instagram Image Preview</h4>
      </div>
      <div class="modal-body">
        <img class="img-responsive center-block" src="'.$instagram_post->images->standard_resolution->url.'" alt="'.$instagram_post->caption->text.'" />
        <p><div class="label label-default">Description</div></br>'.htmlentities($instagram_post->caption->text).' | '.htmlentities(date("F j, Y, g:i a", $instagram_post->caption->created_time)).'</p>
        <p><div class="label label-default">Link to Instagram post</div></br><a href="'.$instagram_post->link.'" target="_blank">'.$instagram_post->link.'</a></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>';
        }
            $instagram_data_display .= '<div class="instagram-gw-pagination">';
            if(isset($instagramgw_data->pagination->next_max_id)) {
                $instagram_data_display .= '<ul class="pagination">';
                $instagram_data_display .= '<li><a href="#" onclick="history.go(-1);">Back</a></li>';
                $instagram_data_display .= '<li><a href="?max_id='.$instagramgw_data->pagination->next_max_id.'">Next</a></li>';
                $instagram_data_display .= '</ul>';
            } else {
                $instagram_data_display .= '<ul class="pagination">';
                $instagram_data_display .= '<li><a href="#" onclick="history.go(-1);">Back</a></li>';
                $instagram_data_display .= '</ul>';
            }
            $instagram_data_display .= '</div>';
        $instagram_data_display .= '</div>';
        return $instagram_data_display;
    }
}