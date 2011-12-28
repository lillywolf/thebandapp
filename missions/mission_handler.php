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
	
	public function getCompletedMissionCount($permissions, $liked)
	{
		$completedMissions = $this->getCompletedMissions($permissions, $liked);
		$completedMissionCount = 0;
		while ($completedMissions[$completedMissionCount+1] == true) {
			$completedMissionCount++;
		}
		error_log('completed mission count: ' . print_r($completedMissionCount, true));
		return $completedMissionCount;		
	}
	
	public function getCompletedMissions($permissions, $liked)
	{
		$pageMissions = $this->redis->getPageMissions();
		$completedMissions = array();
		foreach ($pageMissions as $rank=>$pageMission) {
			if (($pageMission == 'like' && $liked) || 
			($pageMission == 'download_playlist' && ($this->util->downloadedPlaylist() || $this->redis->isMissionComplete('download_playlist'))) ||
			($pageMission == 'add_app' && isset($permissions) && in_array('publish_stream', explode(',', $permissions)))) {
				$completedMissions[$rank] = true;
			} else {
				$completedMissions[$rank] = false;
			}
		}	
		error_log('completed missions: ' . print_r($completedMissions, true));	
		return $completedMissions;
	}
}

?>