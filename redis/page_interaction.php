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

if ($method == 'update_missions')
{
	$missionHandler = new MissionHandler($fbid, $pageId);
	$addedApp = $utils->iterateThroughAndFind($pairs, 'added_app');
	$liked = $utils->iterateThroughAndFind($pairs, 'liked');
	$downloadedPlaylist = $utils->iterateThroughAndFind($pairs, 'downloaded_playlist');
	$completedMissionCount = $missionHandler->getCompletedMissionCount($addedApp, $liked, $downloadedPlaylist);	
	echo 'completed_mission_count='.$completedMissionCount;
	$nextMission = $missionHandler->getNextMission($addedApp, $liked, $downloadedPlaylist);	
	error_log('next mission: ' . print_r($nextMission, true));
	if ($nextMission != null) 
	{
		echo '&title='.$nextMission['title'].'&text='.$nextMission['text'].'&id='.$nextMission['id'];		
	}	
}

if ($method == 'get_page_missions')
{
	$missions = $redis->getPageMissions();
	foreach ($missions as $key=>$missionId)
	{
		echo 'id='.$missionId.'&rank='.$key.',';
	}
}

if ($method == 'create_mission')
{
	$missionId = $utils->iterateThroughAndFind($pairs, 'mission_id');
	$mission = $utils->getMissionData($missionId);
	$redis->createAppMission($mission['id'], $mission['title'], $mission['description'], $mission['explanation'], $mission['tag']);
}

if ($method == 'register_mission')
{
	$missionId = $utils->iterateThroughAndFind($pairs, 'mission_id');
	$missionRank = $utils->iterateThroughAndFind($pairs, 'mission_rank');
	$tag = $utils->iterateThroughAndFind($pairs, 'mission_tag');
	$mission = $utils->getMissionData($missionId);
	
	if (isset($tag)) 
	{
		$missionId = $missionId . '_' . $tag;
	}
	$redis->registerMission($missionId, $missionRank, $mission['id']);
}

if ($method == 'unregister_mission')
{
	$missionId = $utils->iterateThroughAndFind($pairs, 'mission_id');
	$tag = $utils->iterateThroughAndFind($pairs, 'mission_tag');
	
	if (isset($tag)) 
	{
		$missionId = $missionId . '_' . $tag;
	}
	$redis->unregisterMission($missionId, $missionRank);
}

if ($method == 'count_missions')
{
	$missionHandler = new MissionHandler($fbid, $pageId);
	$permissions = $utils->iterateThroughAndFind($pairs, 'perms');
	$liked = $utils->iterateThroughAndFind($pairs, 'liked');
	$downloadedPlaylist = $utils->iterateThroughAndFind($pairs, 'downloaded_playlist');
	$completedMissionCount = $missionHandler->getCompletedMissionCount($permissions, $liked, $downloadedPlaylist);	
	echo $completedMissionCount;
}

if ($method == 'next_mission')
{
	$missionHandler = new MissionHandler($fbid, $pageId);
	$permissions = $utils->iterateThroughAndFind($pairs, 'perms');
	$liked = $utils->iterateThroughAndFind($pairs, 'liked');
	$downloadedPlaylist = $utils->iterateThroughAndFind($pairs, 'downloaded_playlist');
	$nextMission = $missionHandler->getNextMission($permissions, $liked, $downloadedPlaylist);
	if ($nextMission != null) 
	{
		echo 'title='.$nextMission['title'].'&text='.$nextMission['text'].'&id='.$nextMission['id'];		
	}	
}

if ($method == 'log_pageview')
{
	$pageUrl = $utils->iterateThroughAndFind($pairs, 'pageUrl');
	error_log('logging pageview');
	$redis->logPageView($pageUrl, 1);
}

if ($method == 'log_click')
{
	$clickType = $utils->iterateThroughAndFind($pairs, 'clickType');
	error_log('logging click');
	$redis->logClick($clickType, 1);
}

?>