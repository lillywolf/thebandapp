<?php

	require_once('php-sdk/src/facebook.php');

	$appId = '107796503671';
	$appSecret = '10cc0163136a373aa6192f6ceafda96e';
	$appUrl = 'http://apps.facebook.com/thebandapp';	
	$fbPageUrl = "facebook.com/lillywolfmusic";

	$config = array();
	$config['appId'] = $appId;
	$config['secret'] = $appSecret;
	$config['fileUpload'] = false; // optional	

	$facebook = new Facebook($config);	
	$user_id = $facebook->getUser();
	$this->getSignedRequest();
	
	function getSignedRequest() {
		if($user_id) {
			try {
				$user_profile = $facebook->api('/me', 'GET');	
				$signed_request = $facebook->getSignedRequest();
				if ($signed_request['page']['liked']) { 
					echo '<script>alert("got req");</script>';
				} else {
					echo '<script>alert("got req");</script>';
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
			if ($signed_request['page']['liked']) { 
				echo '<script>alert("got req");</script>';
			} else {
				echo '<script>alert("got req");</script>';
			}	
		}		
	}	

?>