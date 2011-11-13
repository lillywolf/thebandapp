<?php

  require 'php-sdk/src/facebook.php';

  $app_id = '107796503671';
  $app_secret = '10cc0163136a373aa6192f6ceafda96e';

  print_r("test");
  $config = array();
  $config['appId'] = $app_id;
  $config['secret'] = $app_secret;

  $facebook = new Facebook(array(
	'appId'  => '107796503671',
	'secret' => '10cc0163136a373aa6192f6ceafda96e',
  ));

	$session = $facebook->getSession();

	$fbme = null;
    // Session based graph API call.
    if ($session) {
      try {
        $uid = $facebook->getUser();
        $fbme = $facebook->api('/me');
      } catch (FacebookApiException $e) {
          d($e);
      }
    }

	print_r($uid);

?>