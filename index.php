<?php

	require_once('php-sdk/src/facebook.php');

	$config = array(
  		'appId' => '107796503671',
  		'secret' => '10cc0163136a373aa6192f6ceafda96e',
	);

	$facebook = new Facebook($config);
	$user_id = $facebook->getUser();
	if($user_id)
	{
	
	}
	else
	{
		header('Location:' . $facebook->getLoginURL());
	}

	print_r($user_id);
	$signed_request = $facebook->getSignedRequest();
	print_r($signed_request);

?>