<?php

require_once('redis.php');
require_once('util.php');
require_once('../missions/mission_handler.php');

$parts = explode('?', $_SERVER['REQUEST_URI']); 
$pairs = explode('&', $parts[1]);
$utils = new Util();
$fbId = $utils->iterateThroughAndFind($pairs, 'fbId');
$pageId = $utils->iterateThroughAndFind($pairs, 'pageId');
$method = $utils->iterateThroughAndFind($pairs, 'method');

$redis = new Redis($fbId, $pageId);	

if ($method == 'download')
{
	$downloadUrl = $utils->iterateThroughAndFind($pairs, 'download_url');
	if (!empty($downloadUrl))
	{
		$redis->recordDownload($downloadUrl);		
	}
}

if ($method == 'download_all')
{
	$redis->recordDownloadAll();		
}

if ($method == 'like')
{
	$redis->recordLike('true');		
}

if ($method == 'create_mission')
{
	$missionId = $utils->iterateThroughAndFind($pairs, 'mission_id');
	$mission = $utils->getMissionData($missionId);
	$redis->createAppMission($mission['id'], $mission['title'], $mission['description'], $mission['explanation']);
}

if ($method == 'register_mission')
{
	$missionId = $utils->iterateThroughAndFind($pairs, 'mission_id');
	$missionRank = $utils->iterateThroughAndFind($pairs, 'mission_rank');
	$mission = $utils->getMissionData($missionId);
	$redis->registerMission($mission['id'], $missionRank);
}

if ($method == 'count_missions')
{
	$missionHandler = new MissionHandler($fbid, $pageId);
	$permissions = $utils->iterateThroughAndFind($pairs, 'perms');
	$liked = $utils->iterateThroughAndFind($pairs, 'liked');
	error_log('permissions passed: ' . print_r($permissions, true));
	$completedMissionCount = $missionHandler->getCompletedMissionCount($permissions, $liked);	
	echo $completedMissionCount;
}

?>