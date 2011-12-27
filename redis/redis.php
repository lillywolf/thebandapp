<?php

require_once('../predis/lib/Predis/Autoloader.php');
Predis\Autoloader::register();

class Redis
{
	public $redis;
	public $userId;
	public $pageId;
	public $userPageKey;
	public $userKey;
	public $pageKey;
	
	public function __construct($userId = null, $pageId = null)
    {
		$this->redis = new Predis\Client(array(
		    'host'     => 'guppy.redistogo.com', 
		    'password' => 'ee54626c1544db50f85d8aaf85de4f5f', 
		    'port' => 9092, 
		));
		$this->pageId = $pageId;
		$this->userId = $userId;
		$this->userKey = $this->userId . '_userdata';
		$this->pageKey = $this->pageId . '_pagedata';
		$this->userPageKey = $this->userId . '_' . $this->pageId . '_data';
    }

	public function recordFbid()
	{
		$storedFbid = $this->redis->hget($this->userKey, 'fbid');
		# Set user data if it doesn't exist
		if (empty($storedFbid)) 
		{
			$this->redis->hset($this->userKey, 'fbid', $userId);	
		}
	}

	public function recordDownload($downloadUrl)
	{
		$downloadsKey = $this->userPageKey . '_downloads';
		$this->redis->sadd($downloadsKey, $downloadUrl);
		$downloads = $this->redis->smembers($downloadsKey);
		error_log('download data: ' . print_r($downloads, true));
	}
	
	public function recordDownloadAll()
	{
		$this->redis->hset($this->userPageKey, 'download_all', true);
		$this->checkForMission('download_playlist');
	}
	
	public function recordPermissions($perms)
	{
		# Takes in array of key / value pairs
		$str = '';
		foreach ($perms as $key => $value)
		{
			if ($value == 1)
			{
				$str = $str . $key . ',';
			}
		}
		error_log('permissions: ' . print_r($str, true));
		$this->redis->hset($this->userKey, 'perms', $str);
		if (isset($perms['publish_stream']))
		{
			$this->checkForMission('add_app');
		}
	}
	
	public function recordLike($liked)
	{
		$this->redis->hset($this->userPageKey, 'liked', $liked);
		$this->checkForMission('like');	
	}
	
	public function recordVisits()
	{
		# Set visits
		$visits = $this->redis->hget($this->userkey, 'visits');
		if (!$visits) {
			$visits = 0;
		}
		$visits = intval($visits)+1;
		$this->redis->hset($this->userkey, 'visits', $visits);	
	}
	
	public function getCompletedMissionsCount()
	{
		$completed = array();
		$missions = $this->redis->smembers('missions');
		foreach ($missions as $missionId)
		{
			$mission = $this->redis->hget('missions_' . $missionId);
			$rank = $this->pageHasMission($missionId);
			if ($rank != null && $this->isMissionComplete($missionId))
			{
				$completed[$rank] = $mission; 
			}
		}
		for ($i = 0; $i < count($completed); $i++)
		{
			if (!array_key_exists($i, $completed))
			{
				return $i;
			}
		}
		return 0;
	}
	
	public function checkForMission($missionId)
	{
		if ($this->pageHasMission($missionId) != null)
		{
			$mission = $this->redis->hget('missions', 'id', $missionId);
			$this->recordMissionComplete($mission['id']);
		}		
	}
	
	public function pageHasMission($missionId)
	{
		$missionsKey = $this->pageKey . '_missions';
		$missions = $this->redis->zrangebyscore($missionsKey);
		if (in_array($missionId, $missions))
		{
			return $this->redis->zscore($missionsKey, $missionId);
		}
		return null;		
	}
	
	public function recordMissionComplete($missionId)
	{
		$missionsKey = $this->userPageKey . '_missions';
		$this->redis->sadd($missionsKey, $missionId);				
	}
	
	public function isMissionComplete($missionId)
	{
		$missionsKey = $this->userPageKey . '_missions';
		$completed = $this->redis->smembers($missionsKey);
		if (in_array($missionId, $completed))
		{
			return true;
		}
		return false;
	}
	
	public function registerMission($missionId, $missionRank)
	{
		$missionsKey = $this->pageKey . '_missions';
		$missions = $this->redis->zrangebyscore($missionsKey, '-inf', '+inf');
		$this->redis->zadd($missionId, $missionRank);
		error_log('missions: ' . print_r($missions, true));
	}
	
	public function createAppMission($id, $title, $description = null, $explanation = null)
	{
		$key = 'missions_' . $id;
		$this->redis->hset($key, 'id', $id);
		$this->redis->hset($key, 'title', $title);
		$this->redis->hset($key, 'text', $description);
		$this->redis->hset($key, 'explanation', $explanation);
		$this->redis->sadd('missions', $id);
		$missions = $this->redis->hgetall($key);
		error_log('missions: ' . print_r($missions, true));
	}
	
}



?>