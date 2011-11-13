<?php

// Enforce https on production
if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == "http" && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
  exit();
}

/**
 * This sample app is provided to kickstart your experience using Facebook's
 * resources for developers.  This sample app provides examples of several
 * key concepts, including authentication, the Graph API, and FQL (Facebook
 * Query Language). Please visit the docs at 'developers.facebook.com/docs'
 * to learn more about the resources available to you
 */

// Provides access to Facebook specific utilities defined in 'FBUtils.php'
require_once('FBUtils.php');
// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');
// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');

/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

// Log the user in, and get their access token
$token = FBUtils::login(AppInfo::getHome());

// Get signed request
$fb_request = FBUtils::signedRequest();

if ($token) {

  // Fetch the viewer's basic information, using the token just provided
  $basic = FBUtils::fetchFromFBGraph("me?access_token=$token");
  $my_id = assertNumeric(idx($basic, 'id'));

  // Fetch the viewer's accounts, using the token just provided
  $accounts = FBUtils::fetchFromFBGraph("me/accounts?access_token=$token");

  // Fetch the basic info of the app that they are using
  $app_id = AppInfo::appID();
  $app_info = FBUtils::fetchFromFBGraph("$app_id?access_token=$token");

  // This fetches some things that you like . 'limit=*" only returns * values.
  // To see the format of the data you are retrieving, use the "Graph API
  // Explorer" which is at https://developers.facebook.com/tools/explorer/
  $likes = array_values(
    idx(FBUtils::fetchFromFBGraph("me/likes?access_token=$token&limit=4"), 'data')
  );

  // This fetches 4 of your friends.
  $friends = array_values(
    idx(FBUtils::fetchFromFBGraph("me/friends?access_token=$token&limit=4"), 'data')
  );

  // And this returns 16 of your photos.
  $photos = array_values(
    idx($raw = FBUtils::fetchFromFBGraph("me/photos?access_token=$token&limit=16"), 'data')
  );

  // Here is an example of a FQL call that fetches all of your friends that are
  // using this app
  $app_using_friends = FBUtils::fql(
    "SELECT uid, name, is_app_user, pic_square FROM user WHERE uid in (SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1",
    $token
  );

  // This formats our home URL so that we can pass it as a web request
  $encoded_home = urlencode(AppInfo::getHome());
  $redirect_url = $encoded_home . 'close.php';

  // These two URL's are links to dialogs that you will be able to use to share
  // your app with others.  Look under the documentation for dialogs at
  // developers.facebook.com for more information
  $send_url = "https://www.facebook.com/dialog/send?redirect_uri=$redirect_url&display=popup&app_id=$app_id&link=$encoded_home";
  $post_to_wall_url = "https://www.facebook.com/dialog/feed?redirect_uri=$redirect_url&display=popup&app_id=$app_id";
} else {
  // Stop running if we did not get a valid response from logging in
  exit("Invalid credentials");
}
?>

<!-- This following code is responsible for rendering the HTML   -->
<!-- content on the page.  Here we use the information generated -->
<!-- in the above requests to display content that is personal   -->
<!-- to whomever views the page.  You would rewrite this content -->
<!-- with your own HTML content.  Be sure that you sanitize any  -->
<!-- content that you will be displaying to the user.  idx() by  -->
<!-- default will remove any html tags from the value being      -->
<!-- and echoEntity() will echo the sanitized content.  Both of  -->
<!-- these functions are located and documented in 'utils.php'.  -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <!-- We get the name of the app out of the information fetched -->
    <title><?php echo(idx($app_info, 'name')) ?></title>
    <link rel="stylesheet" href="stylesheets/screen.css" media="screen">

    <!-- These are Open Graph tags.  They add meta data to your  -->
    <!-- site that facebook uses when your content is shared     -->
    <!-- over facebook.  You should fill these tags in with      -->
    <!-- your data.  To learn more about Open Graph, visit       -->
    <!-- 'https://developers.facebook.com/docs/opengraph/'       -->
    <meta property="og:title" content=""/>
    <meta property="og:type" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:image" content=""/>
    <meta property="og:site_name" content=""/>
    <?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>
    <script>
      function popup(pageURL, title,w,h) {
        var left = (screen.width/2)-(w/2);
        var top = (screen.height/2)-(h/2);
        var targetWin = window.open(
          pageURL,
          title,
          'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left
          );
      }
    </script>
    <!--[if IE]>
      <script>
        var tags = ['header', 'section'];
        while(tags.length)
          document.createElement(tags.pop());
      </script>
    <![endif]-->
  </head>
  <body>
	<div class="test">
		<?php
		?>
	</div>
    <div class="list">
    	<h3>Accounts</h3>
      		<ul class="friends">
        		<?php
          			foreach ($accounts as $account) {
            			// Extract the pieces of info we need from the requests above
            			echo('
              				<li>
                			account: ' . print_r($account) . '
              				</li>');
          			}
        		?>
      		</ul>
  		</div>	
  </body>
</html>
