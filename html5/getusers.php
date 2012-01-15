<?php

	require_once('../sc-api/Soundcloud.php');


	$soundcloud = new Services_Soundcloud('738091d6d02582ddd19de7109b79e47b', 'b8f231ac6dc380b6efb2a8a88cd6d9fe', 'http://simple-ocean-7178.herokuapp.com/auth/');
	$soundcloud->setAccessToken('1-12872-7625335-e561f85b896d9158');
	
	try 
	{
		$usersdata = json_decode($soundcloud->get('users?q=coney+island'), true);
	} 
	catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) 
	{
	    exit($e->getMessage());
	}
	
	$users = array();
	$i = 0;
	
	foreach ($usersdata as $userdata) 
	{
		$users[]['id'] = $userdata['id'];
		if ($i < 50)
		{
			$following = json_decode($soundcloud->get('users/'.$userdata['id'].'/following'), true);
			print_r(count($following));
		}
		$i++;
	}
	
	print_r(count($users));
	
	// Love Too Serious - 25756679
	// Disaster - 25822353
	// $result = $soundcloud->post('tracks/25756679/shared-to/users', array(
	// 		"users[][id]" => "10822550"
	// 	));
	// print_r($result);

?>