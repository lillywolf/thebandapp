<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">

<body>

	<?php
	
	#####
	# Connect to the database
	#####
	// $dbuser="uc3rwdprf7ijm9z";
	// $pass="pab1kv3jcunuilewgh4op7kwht";
	// $host="ec2-107-22-196-151.compute-1.amazonaws.com";
	// $dbname="dcw8wyqwdih0rv";
	
	$host="fbmusicplayer.ccbcfvmpasrm.us-east-1.rds.amazonaws.com";
	$dbname="musicplayer";
	$port="3306";
	$dbuser="lillywolf";
	$pass="ysaura5";

	# This function reads your DATABASE_URL config var and returns a connection
	# string suitable for pg_connect. Put this in your app.
	function pg_connection_string_from_database_url() {
	  extract(parse_url($_ENV["DATABASE_URL"]));
	  return "user=$user password=$pass host=$host dbname=" . substr($path, 1);
	}
	# Here we establish the connection
	$pg_conn = pg_connect(pg_connection_string_from_database_url());
	# Get shows data
	pg_send_query($pg_conn, "SELECT name FROM interactions");
	$interactions_result = "";	
	$result = true;
	$last_result = true;
	while ($result && $result != false) {
		$result = pg_get_result($pg_conn);
		error_log(print_r($result, true));
		if (!$result || $result == $last_result || !pg_num_rows($result)) {
		} else {
			$last_result = $result;
			while ($row = pg_fetch_row($result)) { 
				error_log(print_r($row, true));
				print_r($row);
				# $shows_result .= $row;
		 		# print("<span class='show'>$row[0]</span>"); 
		 	}
		}		
	}
	error_log(print_r($interactions_result, true));
	# print "</div>";

	?>
	
	</body>
</html>	