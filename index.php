<?php

	require_once('php-sdk/src/facebook.php');

	$appId = '107796503671';
	$appSecret = '10cc0163136a373aa6192f6ceafda96e';
	$appUrl = 'http://apps.facebook.com/thebandapp';
	
	$config = array();
	$config[‘appId’] = $appId;
	$config[‘secret’] = $appSecret;
	$config[‘fileUpload’] = false; // optional	

	$facebook = new Facebook($config);
		
	$user_id = $facebook->getUser();
	
	if($user_id) {
		try {
			$user_profile = $facebook->api('/me', 'GET');	
			$signed_request = $facebook->getSignedRequest();
			if ($signed_request['page']['liked']) { ?>
		        <script type="text/javascript">
					alert("liked");
		            // For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection. 
		            // var swfVersionStr = "10.2.0";
		            // // To use express install, set to playerProductInstall.swf, otherwise the empty string. 
		            // var xiSwfUrlStr = "site/playerProductInstall.swf";
		            // var flashvars = {};
		            // var params = {};
		            // params.quality = "high";
		            // params.bgcolor = "#ffffff";
		            // params.allowscriptaccess = "sameDomain";
		            // params.allowfullscreen = "true";
		            // var attributes = {};
		            // attributes.id = "Main";
		            // attributes.name = "Main";
		            // attributes.align = "middle";
		            // swfobject.embedSWF(
		            //     "site/Main.swf", "flashContent", 
		            //     "510", "100%", 
		            //     swfVersionStr, xiSwfUrlStr, 
		            //     flashvars, params, attributes);
		            // // JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
		            // swfobject.createCSS("#flashContent", "display:block;text-align:left;");
		        </script>		
			<?php } else { ?>
			<?php }					
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