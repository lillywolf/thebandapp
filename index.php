<?php

// Remember to copy files from the SDK's src/ directory to a
// directory in your application on the server, such as php-sdk/
require_once('php-sdk/src/facebook.php');

$config = array(
  'appId' => '107796503671',
  'secret' => '10cc0163136a373aa6192f6ceafda96e',
);

$facebook = new Facebook($config);
$user_id = $facebook->getUser();

print_r($user_id);

?>