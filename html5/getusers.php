<?php

	require_once('../sc-api/Soundcloud.php');


	$soundcloud = new Services_Soundcloud('738091d6d02582ddd19de7109b79e47b', 'b8f231ac6dc380b6efb2a8a88cd6d9fe', 'http://simple-ocean-7178.herokuapp.com/auth/');
	$soundcloud->setAccessToken('1-12872-7625335-e561f85b896d9158');
	
	try 
	{
		// $usersdata = json_decode($soundcloud->get('users?q=new+york&offset=1000'), true);
		$offset = $_REQUEST['offset'];
		$term = $_REQUEST['term'];
		
		$usersdata = json_decode($soundcloud->get('users?q='.$term.'&offset='.$offset.'&limit=25'), true);
	} 
	catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) 
	{
	    exit($e->getMessage());
	}
	
	$users = array();
	$strusers = array();
	
	foreach ($usersdata as $userdata) 
	{
		$users[]['id'] = $userdata['id'];
		
		$result = $soundcloud->post('playlists/1569731/shared-to/users', array(
			"users[][id]" => $userdata['id']
		));
	}
	
	print_r($users);
	// $sendto = array('10822550', '10822550');
	
	// 10822550
	// Love Too Serious - 25756679
	// Disaster - 25822353
	// Play Loud Private - 1526982

		// $result = $soundcloud->post('tracks/25756679/shared-to/users', array(
		// 		"users[][id]" => "10822550"
		// 	));
		// print_r($result);	

?>