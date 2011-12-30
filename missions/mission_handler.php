<?php

require_once('../redis/redis.php');
require_once('../redis/util.php');

class MissionHandler
{
	public $redis;
	public $util;
	
	public function __construct($userId, $pageId)
	{
		$this->redis = new Redis($userId, $pageId);
		$this->util = new Util();
	}
	
	public function getCompletedMissionCount($added_app, $liked, $downloadedPlaylist)
	{
		$completedMissions = $this->getCompletedMissions($added_app, $liked, $downloadedPlaylist);
		$completedMissionCount = 0;
		while ($completedMissions[$completedMissionCount+1] == true) {
			$completedMissionCount++;
		}
		return $completedMissionCount;		
	}
	
	public function getCompletedMissions($added_app, $liked, $downloadedPlaylist)
	{
		$pageMissions = $this->redis->getPageMissions();
		$completedMissions = array();
		foreach ($pageMissions as $rank=>$pageMission) {
			if (($pageMission == 'like' && $liked) || 
			($pageMission == 'download_playlist' && (intval($downloadedPlaylist) == 1 || $this->redis->isMissionComplete('download_playlist'))) ||
			($pageMission == 'add_app' && !empty($added_app))) {
				$completedMissions[$rank] = true;
			} else {
				$completedMissions[$rank] = false;
			}
		}	
		// error_log('completed missions: ' . print_r($completedMissions, true));	
		return $completedMissions;
	}
	
	public function getNextMission($permissions, $liked, $downloadedPlaylist)
	{
		$completedMissionCount = $this->getCompletedMissionCount($permissions, $liked, $downloadedPlaylist);
		$pageMissions = $this->redis->getPageMissions();
		if (isset($pageMissions[$completedMissionCount+1]))
		{
			error_log('next mission: ' . print_r($pageMissions[$completedMissionCount+1], true));	
			$appMission = $this->redis->getAppMission($pageMissions[$completedMissionCount+1]);
			error_log('app mission: ' . print_r($appMission, true));	
			return $appMission;
		}
		return null;
	}
}

?>