<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">

<body>
	found fb_auth

	<?php

		require_once('../php-sdk/src/facebook.php');

		$appId = '107796503671';
		$appSecret = '10cc0163136a373aa6192f6ceafda96e';
		$appUrl = 'http://apps.facebook.com/thebandapp';	
		$fbPageUrl = "facebook.com/lillywolfmusic";

		$config = array();
		$config['appId'] = $appId;
		$config['secret'] = $appSecret;
		$config['fileUpload'] = false; // optional	
		
		session_start();
		error_log(print_r($_SESSION, true));
		
		$facebook = new Facebook($config);	
		$user_id = $facebook->getUser();
	
		if($user_id) {
			error_log("found user id");
			try {
				$user_profile = $facebook->api('/me', 'GET');	
				$signed_request = $facebook->getSignedRequest();
				if ($signed_request['page']['liked']) { 
					error_log("liked");
				} else {
					error_log("not liked");
				}					
			}
			catch(FacebookApiException $e) {
				$loginUrl = $facebook->getLoginUrl($params);			
		        error_log($e->getType());
		        error_log($e->getMessage());			
			}	
		}
		else {	
			error_log("no user id");	
			$signed_request = $facebook->getSignedRequest();
			if ($signed_request['page']['liked']) { 
				error_log("liked");
			} else {
				error_log("not liked");
			}	
			error_log($signed_request);
		}		

	?>
	
	</body>
</html>	