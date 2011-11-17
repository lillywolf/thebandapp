<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<script type="text/javascript" src="site/swfobject.js"></script>
	<script type="text/javascript" src="site/FBJSBridge.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="site/history/history.js"></script>
</head>	
<body>
	
	<div id="page_heading_div" class="hidden">
	</div>
	
	<span id="like"></span>

    <div id="flashContent">
    </div>	

	<iframe src="www.facebook.com/plugins/add_to_timeline.php?show-faces=true&amp;mode=box&amp;appId=APP_ID" scrolling="no" frameborder="0" style="border:none; overflow:hidden;" allowTransparency="true"></iframe>
	
<?php

	require_once('php-sdk/src/facebook.php');

	$appId = '107796503671';
	$appSecret = '10cc0163136a373aa6192f6ceafda96e';
	$appUrl = 'http://apps.facebook.com/thebandapp';
	
	$config = array();
	$config['appId'] = $appId;
	$config['secret'] = $appSecret;
	$config['fileUpload'] = false; // optional	

	$facebook = new Facebook($config);	
	$user_id = $facebook->getUser();
	?>
	
    <div id="fb-root"></div>	

	<script type="text/javascript">
	
		// function addLikeButtonOverlay() {
		// 	
		// }
		
		function songChanged(songUrl) {
			alert("song changed " + songUrl);
			updateFacebookLikeButton(songUrl);
		}
		
		function updateFacebookLikeButton(url) {
			$('#like').html('<fb:like href="' + url + '" layout="button_count" show_faces="false" action="like" font="segoe ui" colorscheme="light" send="true" />');
			if (typeof FB !== 'undefined') {
			    FB.XFBML.parse(document.getElementById('like'));
			}
		}
	
	</script>

	<script type="text/javascript">
	    window.fbAsyncInit = function() {
			FB.init({
	        	appId: '<?php echo $facebook->getAppID() ?>', 
	        	cookie: true, 
	        	xfbml: true,
	        	oauth: true
	      	});
	      	// FB.Event.subscribe('auth.login', function(response) {
	      	// 	        	window.location.reload();
	      	// });
	      	// FB.Event.subscribe('auth.logout', function(response) {
	      	// 	        	window.location.reload();
	      	// });
			FB.Event.subscribe('edge.create', function(response) {
	        	window.location.reload();
			});	
	    };
	    (function() {
	      var e = document.createElement('script'); e.async = true;
	      e.src = document.location.protocol +
	        '//connect.facebook.net/en_US/all.js';
	      document.getElementById('fb-root').appendChild(e);
	    }());	
	</script>
	
	<?php
	if($user_id) {
		try {
			$user_profile = $facebook->api('/me', 'GET');	
			$signed_request = $facebook->getSignedRequest();
			if ($signed_request['page']['liked']) { 
				?>
				
				<script type="text/javascript">
					document.getElementById("page_heading_div").style.display = "inline";
					document.getElementById("flashContent").style.display = "inline";
					var params = { wmode: "opaque" };
					params.allowfullscreen = "true";
					params.allowscriptaccess = "always";
					var flashvars = {};
					flashvars.downloads_enabled = "true";
					flashvars.liked = "true";
					swfobject.embedSWF("site/Main.swf", "flashContent", "510", "100%", "10.0", null, flashvars, params, {name:"flashContent"});				
				</script>
				
				<?php
			} 
			else {
				?>
				
				<script type="text/javascript">
					document.getElementById("page_heading_div").style.display = "inline";
					document.getElementById("flashContent").style.display = "inline";
					var params = { wmode: "opaque" };
					params.allowfullscreen = "true";
					params.allowscriptaccess = "always";					
					var flashvars = {};
					flashvars.downloads_enabled = "false";
					flashvars.liked = "false";
					swfobject.embedSWF("site/Main.swf", "flashContent", "510", "100%", "10.0", null, flashvars, params, {name:"flashContent"});	
					
					FB.Event.subscribe('edge.create',
					    function(response) {
					        alert('You liked the URL: ' + response);
					    }
					);								
				</script>				
				
				<?php
			}					
		}
		catch(FacebookApiException $e) {
			$loginUrl = $facebook->getLoginUrl($params);			
	        error_log($e->getType());
	        error_log($e->getMessage());			
		}	
	}
	else {		
		$signed_request = $facebook->getSignedRequest();
		print_r($signed_request);
		if ($signed_request['page']['liked']) { 
			?>
			
			<script type="text/javascript">
				document.getElementById("page_heading_div").style.display = "inline";
				document.getElementById("flashContent").style.display = "inline";
				var params = { wmode: "opaque" };
				params.allowfullscreen = "true";
				params.allowscriptaccess = "always";				
				var flashvars = {};
				flashvars.downloads_enabled = "true";
				flashvars.liked = "true";
				swfobject.embedSWF("site/Main.swf", "flashContent", "510", "100%", "10.0", null, flashvars, params, {name:"flashContent"});				
			</script>
			
			<?php
		} 
		else {
			?>
			
			<script type="text/javascript">
				document.getElementById("page_heading_div").style.display = "inline";
				document.getElementById("flashContent").style.display = "inline";
				var params = { wmode: "opaque" };
				params.allowfullscreen = "true";
				params.allowscriptaccess = "always";				
				var flashvars = {};
				flashvars.downloads_enabled = "false";
				flashvars.liked = "false";
				swfobject.embedSWF("site/Main.swf", "flashContent", "510", "100%", "10.0", null, flashvars, params, {name:"flashContent"});				
			</script>				
			
			<?php
		}	
		
		// FOR ADMIN PANEL
		
		// $state = md5(uniqid(rand(), TRUE));
		// $scope = 'email,publish_stream,manage_pages';
		// $home = getHome();
		// $authorize_url = "https://www.facebook.com/dialog/oauth?client_id=$appId" .
		//       	"&redirect_uri=$home&state=" . $state . "&scope=$scope";		
		//       	echo("<script> top.location.href='" . $authorize_url . "'</script>");
			
		// Use this for non-facebook canvas page (i.e. Facebook Connect)		
		// header('Location:' . $facebook->getLoginURL());
	}
	
	/**
	 * @return the home URL for this site
	 */
	function getHome () {
	  return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: "http") . "://" . $_SERVER['HTTP_HOST'] . "/";
	}

?>
</body>
</html>