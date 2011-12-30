<?php

class Util
{

	public function __construct()
	{
	}

	public function iterateThroughAndFind($pairs, $find)
	{
		foreach ($pairs as $pair)
		{ 
			$parts = explode('=', $pair);
			if ($parts[0] == $find)
			{
				return $parts[1];
			}
		}
		return null;
	}
	
	public function getMissionData($m_id)
	{
		$data = array();
		switch ($m_id)
		{
			case 'like':
				$data['id'] = 'like';
				$data['title'] = 'click the \'Like\' button (above) to follow us on Facebook';
				$data['description'] = 'Click the like button above';
				$data['explanation'] = 'Have users click your page\'s \'like\' button';
				break;
			case 'download_playlist':
				$data['id'] = 'download_playlist';
				$data['title'] = 'download our free music!';
				$data['description'] = 'Download tracks for free';
				$data['explanation'] = 'Have users download a playlist you\'ve defined';
				break;
			case 'add_app':
				$data['id'] = 'add_app';
				$data['title'] = 'add the music player app';
				$data['description'] = 'Add the music player to your facebook';
				break;
		}
		return $data;
	}

}

?>