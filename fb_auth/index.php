<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">

<body>

	<?php
	
	#####
	# Connect to the database
	#####
	$dbuser="uc3rwdprf7ijm9z";
	$pass="pab1kv3jcunuilewgh4op7kwht";
	$host="ec2-107-22-196-151.compute-1.amazonaws.com";
	$dbname="dcw8wyqwdih0rv";

	# This function reads your DATABASE_URL config var and returns a connection
	# string suitable for pg_connect. Put this in your app.
	function pg_connection_string_from_database_url() {
	  extract(parse_url($_ENV["DATABASE_URL"]));
	  return "user=$user password=$pass host=$host dbname=" . substr($path, 1);
	}
	# Here we establish the connection
	$pg_conn = pg_connect(pg_connection_string_from_database_url());
	# Get shows data
	pg_send_query($pg_conn, "SELECT venue FROM shows WHERE artist_id=1");
	$shows_result = "";	
	$result = true;
	while ($result != false) {
		$result = pg_get_result($pg_conn);
		if (!pg_num_rows($result)) {
		} else {
			while ($row = pg_fetch_row($result)) { 
				$shows_result .= $row;
		 		# print("<span class='show'>$row[0]</span>"); 
		 	}
		}		
	}
	print_r($shows_result);
	# print "</div>";

	?>
	
	</body>
</html>	