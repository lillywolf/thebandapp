<?php

require_once('../predis/lib/Predis/Autoloader.php');
Predis\Autoloader::register();

class Redis
{
	public $redis;
	public $userId;
	public $pageId;
	public $userPageKey;
	
	public function __construct($userId = null, $pageId = null)
    {
		$this->redis = new Predis\Client(array(
		    'host'     => 'guppy.redistogo.com', 
		    'password' => 'ee54626c1544db50f85d8aaf85de4f5f', 
		    'port' => 9092, 
		));
		$this->pageId = $pageId;
		$this->userId = $userId;
		$this->userPageKey = $this->userId . '_' . $this->pageId . '_data';
    }

	public function recordDownload($downloadUrl)
	{
		$this->redis->hset($this->userPageKey, 'downloads', $downloadUrl);
		$dldata = $this->redis->hget($this->userPageKey, 'downloads');
		error_log('user data: ' . print_r($dldata, true));
	}
	
	public function recordDownloadAll($urls)
	{
		$this->redis->hset($this->userPageKey, 'download_all', true);
		foreach ($urls as $url)
		{
			recordDownload($url);
		}
	}
	
	public function recordLike($liked)
	{
		$this->redis->hset($this->userPageKey, 'liked', $liked);		
	}
	

	
}



?>