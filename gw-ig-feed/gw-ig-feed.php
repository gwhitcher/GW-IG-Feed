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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Actions
add_action('admin_menu', array('InstagramGW','instagram_gw_admin_call'));
add_shortcode('GW-IG-Feed', array('InstagramGW','instagram_gw_shortcode'));
function myplugin_register_widgets() {
    register_widget( 'InstagramGW' );
}
add_action( 'widgets_init', 'myplugin_register_widgets' );

class InstagramGW extends WP_Widget {
    function __construct() {
        // Instantiate the parent object
        parent::__construct( false, 'GW-IG-Feed' );
    }
    public function instagram_gw_admin_call(){
        add_menu_page( 'GW-IG-Feed Administration', 'GW-IG-Feed', 'manage_options', 'gw-ig-feed-plugin', array('InstagramGW','instagram_gw_admin'));
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
    public function instagram_gw_init($count = INSTA_COUNT){
        include('config.php');
        $user_id_json_url = 'https://www.instagram.com/'.INSTA_USERNAME.'/?__a=1';
        $user_id_json = file_get_contents($user_id_json_url);
        $user_id = json_decode($user_id_json);

        $instagram_feed_json_url = "https://api.instagram.com/v1/users/".$user_id->user->id."/media/recent?access_token=".INSTA_ACCESS_TOKEN.'&count='.$count;
        if(!empty($_GET['max_id'])) {
            $instagram_feed_json_url .= "&max_id=".$_GET['max_id'];
        }
        $instagram_feed_json = file_get_contents($instagram_feed_json_url);
        $instagram_feed = json_decode($instagram_feed_json);
        return $instagram_feed;
    }function widget($args, $instance) {
        //wp_register_style( 'bootstrap-css', plugins_url( '/gw-ig-feed/bootstrap/css/bootstrap.css' ) );
        //wp_enqueue_style( 'bootstrap-css' );
        //wp_register_script( 'bootstrap-js', plugins_url( '/gw-ig-feed/bootstrap/js/bootstrap.js' ) );
        //wp_enqueue_script( 'bootstrap-js' );
        wp_register_style( 'styles', plugins_url( '/gw-ig-feed/css/styles.css' ) );
        wp_enqueue_style( 'styles' );
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if ( $title ) echo $before_title . $title . $after_title;
        $instagramgw = new InstagramGW();
        $instagramgw_data = $instagramgw->instagram_gw_init(1);
        $i = 0;
        foreach ($instagramgw_data->data as $instagram_post) {
            $i++;
            echo '<div class="instagram-widget">';
            echo '<a data-toggle="modal" data-target="#myModal'.$i.'">';
            echo '<img class="center-block" src="'.$instagram_post->images->low_resolution->url.'" alt="'.$instagram_post->caption->text.'" />';
            echo '</a>';
            echo '</div>';
            echo '<div id="myModal'.$i.'" class="modal fade" role="dialog">
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
        echo '<div class="instagram-gw-pagination">';
        if(isset($instagramgw_data->pagination->next_max_id)) {
            echo '<ul class="pagination">';
            echo '<li><a href="#" onclick="history.go(-1);">Back</a></li>';
            echo '<li><a href="?max_id='.$instagramgw_data->pagination->next_max_id.'">Next</a></li>';
            echo '</ul>';
        } else {
            echo '<ul class="pagination">';
            echo '<li><a href="#" onclick="history.go(-1);">Back</a></li>';
            echo '</ul>';
        }
        echo '</div>';
    echo $after_widget;
    }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }
    function form($instance) {
        $title 		= esc_attr($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php
    }
    public function instagram_gw_shortcode() {
        wp_register_style( 'bootstrap-css', plugins_url( '/gw-ig-feed/bootstrap/css/bootstrap.css' ) );
        wp_enqueue_style( 'bootstrap-css' );
        wp_register_script( 'bootstrap-js', plugins_url( '/gw-ig-feed/bootstrap/js/bootstrap.js' ) );
        wp_enqueue_script( 'bootstrap-js' );
        wp_register_style( 'styles', plugins_url( '/gw-ig-feed/css/styles.css' ) );
        wp_enqueue_style( 'styles' );
        $instagramgw = new InstagramGW();
        $instagramgw_data = $instagramgw->instagram_gw_init();
        $instagram_data_display = '<div class="instagram-gw">';
        $i = 0;
        foreach ($instagramgw_data->data as $instagram_post) {
            $i++;
            $instagram_data_display .= '<div class="instagram-unit">';
            $instagram_data_display .= '<a data-toggle="modal" data-target="#myModal'.$i.'">';
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