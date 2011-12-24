<?php

require_once('redis.php');

$parts = explode('?', $_SERVER['REQUEST_URI']); 
error_log('check url parts: ' . print_r($parts, true));
$params = explode('&', $parts[1]);

// $redis = new Redis($parts['fbId'], $parts['pageId']);

// if ($parts['method'] == 'download')
// {
// 	$redis->recordDownload($parts['download_url']);
// }

?>