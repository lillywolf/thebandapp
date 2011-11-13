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
			if ($signed_request['page']['liked']) {
				// Show downloads and other content for users who liked the page
			}
			else {
				// Show default page for users who haven't liked it
			}					
		}
		catch(FacebookApiException $e) {
			$loginUrl = $facebook->getLoginUrl($params);			
	        error_log($e->getType());
	        error_log($e->getMessage());			
		}	
	}
	else {
		$state = md5(uniqid(rand(), TRUE));
		$scope = 'email,publish_stream,manage_pages';
		$authorize_url = "https://www.facebook.com/dialog/oauth?client_id=$appId" .
      	"&redirect_uri=$appUrl&state=" . $state . "&scope=$scope";		
      	echo("<script> top.location.href='" . $authorize_url . "'</script>");
			
		// Use this for non-facebook canvas page (i.e. Facebook Connect)		
		// header('Location:' . $facebook->getLoginURL());
	}

?>