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
	}
	
	public function recordLike($liked)
	{
		$this->redis->hset($this->userPageKey, 'liked', $liked);		
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
	
}



?>