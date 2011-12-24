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
			error_log('parts: ' . print_r($parts, true));
			if ($parts[0] == $find)
			{
				return $parts[1];
			}
		}
		return null;
	}

}

?>