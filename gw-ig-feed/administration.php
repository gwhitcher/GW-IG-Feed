<?php
$msg = 'Sign in with Instagram first...';
if(!empty($_GET['access_token'])) {
    $get_access_token = $_GET['access_token'];
} elseif(defined('INSTA_ACCESS_TOKEN')) {
    $get_access_token = INSTA_ACCESS_TOKEN;
} else {
    $get_access_token = $msg;
}
if(defined('INSTA_USERNAME')) {
    $get_username = INSTA_USERNAME;
} else {
    $get_username = '';
}
if(defined('INSTA_COUNT')) {
    $get_count = INSTA_COUNT;
} else {
    $get_count = 20;
}
if(!empty($_POST['submit'])) {

    if($_POST['access_token'] == $msg) {
        echo '<script>';
        echo 'alert("'.$msg.'");';
        echo '</script>';
    } else {
        $post_access_token = $_POST['access_token'];
        $post_username = $_POST['username'];
        $post_count = $_POST['count'];
        $instagramgw = new InstagramGW();
        $instagramgw->instagram_gw_config($post_access_token, $post_username, $post_count);
        echo '<script>';
        echo 'alert("Data saved!");';
        echo 'window.location.reload();';
        echo '</script>';
    }
}
$plugin_url = plugins_url().'/gw-ig-feed';
$current_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>
<link href="<?php echo $plugin_url;?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $plugin_url;?>/css/styles.css" rel="stylesheet">
<h1 class="page-header">Instagram GW Administration</h1>

<div class="container">

    <div class="alert alert-success">
        <p>Before you can use Instagram GW you need to get an access_token and add your username.  Sign in with the button below to authorize Instagram GW and then save the access_token and username you just authorized by submitting the form below.</p>
    </div>

    <p><a href="https://api.instagram.com/oauth/authorize/?client_id=d1b6241d1a9f4e279061c7d4aa456cb8&scope=basic+public_content&redirect_uri=http://georgewhitcher.com/instagram?return_url=<?php echo $current_url; ?>&response_type=token" class="btn btn-primary">Sign in with Instagram</a></p>

    <form class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8" action="<?php esc_url( $_SERVER['REQUEST_URI'] ); ?>">

        <div class="form-group">
            <label for="access_token" class="col-sm-2 control-label">Access Token</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="access_token" name="access_token" placeholder="<?php echo $msg; ?>" value="<?php echo $get_access_token; ?>" readonly>
            </div>
        </div>

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo $get_username; ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="count" class="col-sm-2 control-label">Count</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="count" name="count" placeholder="20" value="<?php echo $get_count; ?>" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" id="submit" name="submit" class="btn btn-default" value="Submit">
            </div>
        </div>
    </form>

<h2 class="page-header">Integration</h2>
    <div class="alert alert-success">
        <p>To integrate Instagram GW use one of the below methods.</p>
    </div>
<form class="form-horizontal">
    <div class="form-group">
        <label for="access_token" class="col-sm-2 control-label">SHORTCODE</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="access_token" name="access_token" placeholder="<?php echo $msg; ?>" value="[INSTAGRAM-GW]" readonly>
        </div>
    </div>

    <div class="form-group">
        <label for="access_token" class="col-sm-2 control-label">PHP (raw)</label>
        <div class="col-sm-10">
            <textarea class="form-control" rows="8" readonly>
$instagramgw = new InstagramGW();
$instaData = $instagramgw->instagram_gw_init();
foreach ($instaData->data as $instaPost) {
    echo '<a href="'.$instaPost->images->standard_resolution->url.'" target="blank">';
    echo '<img src="'.$instaPost->images->low_resolution->url.'" alt="'.$instaPost->caption->text.'" />';
    echo htmlentities($instaPost->caption->text).' | '.htmlentities(date("F j, Y, g:i a", $instaPost->caption->created_time));
    echo '</a>';
}</textarea>
        </div>
    </div>

    <div class="form-group">
        <label for="access_token" class="col-sm-2 control-label">PHP (bootstrap)</label>
        <div class="col-sm-10">
            <textarea class="form-control" rows="32" readonly>
$instagramgw = new InstagramGW();
$instaData = $instagramgw->instagram_gw_init();
foreach ($instaData->data as $instaPost) {
    echo '<div class="instagram-unit">';
    echo '<a target="blank" data-toggle="modal" data-target="#myModal'.$instaPost->id.'">';
    echo '<img src="'.$instaPost->images->low_resolution->url.'" alt="'.$instaPost->caption->text.'" />';
    echo '<div class="instagram-desc">'.htmlentities($instaPost->caption->text).' | '.htmlentities(date("F j, Y, g:i a", $instaPost->caption->created_time)).'</div>';
    echo '</a>';
    echo '</div>';
    echo '<div id="myModal'.$instaPost->id.'" class="modal fade" role="dialog">
              <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Instagram Image Preview</h4>
                  </div>
                  <div class="modal-body">
                    <img class="img-responsive center-block" src="'.$instaPost->images->standard_resolution->url.'" alt="'.$instaPost->caption->text.'" />
                    <p><div class="label label-default">Description</div></br>'.htmlentities($instaPost->caption->text).' | '.htmlentities(date("F j, Y, g:i a", $instaPost->caption->created_time)).'</p>
                      <p><div class="label label-default">Link to Instagram post</div></br><a href="'.$instaPost->link.'" target="_blank">'.$instaPost->link.'</a></p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>';
                }</textarea>
        </div>
    </div>
 </form>

</div>
<script src="<?php echo $plugin_url;?>/jquery/jquery.min.js"></script>
<script src="<?php echo $plugin_url;?>/bootstrap/js/bootstrap.min.js"></script>