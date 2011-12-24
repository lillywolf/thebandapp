<?php

require_once('redis.php');
require_once('util.php');

$parts = explode('?', $_SERVER['REQUEST_URI']); 
$pairs = explode('&', $parts[1]);
$utils = new Util();
$fbId = $utils->iterateThroughAndFind($pairs, 'fbId');
$pageId = $utils->iterateThroughAndFind($pairs, 'pageId');
$method = $utils->iterateThroughAndFind($pairs, 'method');

if (!empty($fbId) && !empty($pageId))
{
	$redis = new Redis($fbId, $pageId);	
}

if ($method == 'download')
{
	$downloadUrl = $utils->iterateThroughAndFind($pairs, 'download_url');
	if (!empty($downloadUrl))
	{
		$redis->recordDownload($downloadUrl);		
	}
}

if (method == 'download_all')
{
	$redis->recordDownloadAll();		
}

if ($method == 'like')
{
	$redis->recordLike('true');		
}

?>