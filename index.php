<html>
<head>
	<script type="text/javascript" src="site/swfobject.js"></script>
	<script type="text/javascript" src="site/FBJSBridge.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="site/history/history.js"></script>
</head>	
<body>
	
	<div id="page_heading_div" class="hidden">
	</div>
    <div id="fb-root"></div>	
	
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
	
	if($user_id) {
		try {
			$user_profile = $facebook->api('/me', 'GET');	
			$signed_request = $facebook->getSignedRequest();
			if ($signed_request['page']['liked']) { 
				echo "liked";
				?>
				
				<script type="text/javascript">
					document.getElementById('page_heading_div').style.display="inline";
					document.getElementById("flashContent").style.display = "inline";
					var params = { wmode: "opaque" };
					params.allowfullscreen = "true";
					var flashvars = {};
					swfobject.embedSWF("site/Main.swf", "flashContent", "510", "100%", "10.0", null, flashvars, params, {name:"flashContent"});				
				</script>
				
				<?php
			} 
			else {
			}					
		}
		catch(FacebookApiException $e) {
			$loginUrl = $facebook->getLoginUrl($params);			
	        error_log($e->getType());
	        error_log($e->getMessage());			
		}	
	}
	else {
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