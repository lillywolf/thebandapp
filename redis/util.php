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
			if (isset($pair[$find]))
			{
				return $pair[$find];
			}
		}
		return null;
	}

}

?>