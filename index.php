<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<script type="text/javascript" src="site/swfobject.js"></script>
	<script type="text/javascript" src="site/FBJSBridge.js"></script>
	<script type="text/javascript" src="scripts/spin.js"></script>	
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>	
	<script type="text/javascript" src="scripts/prototype.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="site/index.css" />
	<!--script type="text/javascript" src="site/history/history.js"></script-->
</head>	
<body>
	
		<?php

		# require_once('php-sdk/src/facebook.php');

		$appId = '107796503671';
		$appSecret = '10cc0163136a373aa6192f6ceafda96e';
		$appUrl = 'http://apps.facebook.com/thebandapp';	
		$fbPageUrl = "facebook.com/lillywolfmusic";
	
		$config = array();
		$config['appId'] = $appId;
		$config['secret'] = $appSecret;
		$config['fileUpload'] = false; // optional	

		# $facebook = new Facebook($config);	
		# $user_id = $facebook->getUser();
		# $req = $facebook->getSignedRequest();
		session_start();
		$req = getSignedRequest();
		print_r($req);
		if ($req['page']['liked']) {
			$liked = "true";
			$downloads_enabled = "true";
		} else {
			$liked = "false";
			$downloads_enabled = "false";
		}
		
		public function getSignedRequest() {
	  		if (isset($_REQUEST['signed_request'])) {
	        	$signedRequest = parseSignedRequest($_REQUEST['signed_request']);
	      	} else if (isset($_COOKIE[getSignedRequestCookieName()])) {
	        	$signedRequest = parseSignedRequest($_COOKIE[$this->getSignedRequestCookieName()]);
	      	}
	    	return $signedRequest;
	  	}	
	
	  	protected static function base64UrlDecode($input) {
	    	return base64_decode(strtr($input, '-_', '+/'));
	  	}	
	
	  	protected function getSignedRequestCookieName() {
	    	return 'fbsr_'.$appId();
	  	}
		
	  	/**
	   	* Parses a signed_request and validates the signature.
	   	*
	   	* @param string $signed_request A signed token
	   	* @return array The payload inside it or null if the sig is wrong
	   	*/
	  	protected function parseSignedRequest($signed_request) {
	    	list($encoded_sig, $payload) = explode('.', $signed_request, 2);

	    	// decode the data
	    	$sig = self::base64UrlDecode($encoded_sig);
	    	$data = json_decode(self::base64UrlDecode($payload), true);

	    	if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
	      		# self::errorLog('Unknown algorithm. Expected HMAC-SHA256');
	      		return null;
	    	}
	
	    	// check sig
	    	$expected_sig = hash_hmac('sha256', $payload, $appSecret, $raw = true);
	    	if ($sig !== $expected_sig) {
	      		# self::errorLog('Bad Signed JSON signature!');
	      		return null;
	    	}

	    	return $data;
	  	}		
		
		?>
		
		<div id="page_heading_div" class="hidden"></div>
		<div id="notice"></div>
		<div id="flash">
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="musicPlayer" width="514" height="880">
			    <param name="movie" value="site/Main.swf">
				<param name="allowFullScreen" value="true">
				<param name="allowScriptAccess" value="always">
				<param name="scale" value="noscale">
				<param name="wmode" value="transparent">
				<param name="flashvars" value="downloads_enabled=<?php echo $downloads_enabled ?>&liked=<?php echo $liked ?>">					
	            <!--[if !IE]>-->
	            <object type="application/x-shockwave-flash" data="site/Main.swf" id="musicPlayer" width="514" height="880">
	                <param name="quality" value="high" />
	                <param name="bgcolor" value="#ffffff" />
	                <param name="allowScriptAccess" value="always" />
	                <param name="allowFullScreen" value="true" />	
					<param name="wmode" value="transparent" />
					<param name="flashvars" value="downloads_enabled=<?php echo $downloads_enabled ?>&liked=<?php echo $liked ?>">					
	            <!--[if !IE]>-->
	            </object>				
			</object>
		</div>	
		<div id="spinner"></div>
		<span id="tweet"></span>
		<span id="like"></span>
		<span id="big_like"></span>
		<iframe id="downloader-frame" frameborder="0"></iframe>
		<span id="downloaders"></span>
		<div id="extra-content">
			<a href="https://twitter.com/lillywolf" class="twitter-follow-button" data-show-count="false">Follow @lillywolf</a>
		<!--a href="http://soundcloud.com/lillywolf/follow" class="soundcloud-badge"><span id="soundcloud-badge-inner">http://soundcloud.com/lillywolf</span></a-->		
		
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
		# $pg_conn = pg_connect(pg_connection_string_from_database_url());
		# Get shows data
		# $result = pg_query($pg_conn, "SELECT venue FROM shows WHERE artist_id=1");
		# Print shows data
		# print "<div id='shows'><img id='shows-header' src='/images/headers/shows_header.png'/>";
		/* if (!pg_num_rows($result)) {
		} else {
			while ($row = pg_fetch_row($result)) { 
		 		print("<span class='show'>$row[0]</span>"); 
		 	}
		}
		print "</div>"; */
		
		/* $fp = fsockopen("simple-ocean-7178.herokuapp.com", 80, $errno, $errstr);
		if (!$fp) {
			echo "$errstr ($errno)<br />\n";
		} else {
			$out = "GET /fb_auth/ HTTP/1.1\r\n";
			$out .= "Host: simple-ocean-7178.herokuapp.com\r\n";
			$out .= "Cookie: PHPSESSID=" . $_COOKIE['PHPSESSID'] . "\r\n";
			$out .= "Connection: Close\r\n\r\n";
			$result = fwrite($fp, $out);
			fclose($fp);
		} */				
	
		?>
	
    	<div id="fb-root"></div>
	</div>

	<script type="text/javascript">
	
		fbPageUrl = '<?php echo $fbPageUrl; ?>';

		var spinner;
		preload();
		
		window.onload = function() {
		}

		function preload() {
			var opts = {
			  lines: 10, // The number of lines to draw
			  length: 12, // The length of each line
			  width: 7, // The line thickness
			  radius: 16, // The radius of the inner circle
			  color: '#000', // #rgb or #rrggbb
			  speed: 1, // Rounds per second
			  trail: 60, // Afterglow percentage
			  shadow: false // Whether to render a shadow
			};
			var target = window.document.getElementById('spinner');
			spinner = new Spinner(opts).spin(target);
			target.appendChild(spinner.el);						
		}
		
		function stopPreload() {
			spinner.stop();
			initializeJS();
			window.document.getElementById('spinner').style.margin = "0px";			
			window.document.getElementById('spinner').style.visibility = "hidden";
			shiftElements();
		}
		
		function shiftElements() {
			var offY = window.document.getElementById("flash").offsetHeight;
			window.document.getElementById("extra-content").style.top = offY.toString();
		}		
		
		function downloadSong(downloadUrl) {
			window.document.getElementById("downloader-frame").src=downloadUrl+"?consumer_key=738091d6d02582ddd19de7109b79e47b";
		}
		
		function downloadAllSongs(downloadUrlString) {
			var urls = downloadUrlString.split(",");
			createDownloadElement(urls, 0, urls.length);			
		}
		
		function createDownloadElement(urls, i, limit) {
			for (i = 0; i < limit; i++) {
				var e = window.document.createElement("iframe");
				e.id = "download-frame-" + i.toString();
				e.style.visibility = "hidden";
				e.style.height = "0";
				e.style.border = "0";
				e.onreadystatechange = function() {
					if (e.readyState == "interactive") {
						window.setTimeout("createDownloadElement()", 100);
					} else {
						window.document.getElementById("downloaders").removeChild(e);
					}
				}
				e.src = urls[i]+"?consumer_key=738091d6d02582ddd19de7109b79e47b";				
				window.document.getElementById("downloaders").appendChild(e);
			}	
		}
		
		function buySong(buyUrl) {
			alert(buyUrl);
		}
		
		function songChanged(songUrl, likeBtnY) {
			updateLittleFacebookLikeButton(songUrl, likeBtnY);			
			updateBigFacebookLikeButton(songUrl, likeBtnY);	
			updateTweetButton(songUrl, likeBtnY);					
		}
		
		function updateLittleFacebookLikeButton(url, likeBtnY) {
			// var yOffset = getOffset(window.document.getElementById("music-player"));
			$('#like').html('<fb:like href="' + url + '" layout="button_count" show_faces="false" action="like" font="arial" colorscheme="light" send="true" />');
			if (typeof FB !== 'undefined') {
			    FB.XFBML.parse(document.getElementById('like'));
			}
			window.document.getElementById("like").style.top = parseInt(likeBtnY) + 2;
			window.document.getElementById("like").style.left = 271;
		}
		
		function updateTweetButton(url, likeBtnY) {
			$('#tweet').html('<a href="https://twitter.com/share" class="twitter-share-button" data-url="' + url + '" data-text="Listening To: " data-count="none" data-via="lillywolf">Tweet</a>');
			$.ajax({ url: 'http://platform.twitter.com/widgets.js', dataType: 'script', cache:true});						
			window.document.getElementById("tweet").style.top = parseInt(likeBtnY) + 2;
			window.document.getElementById("tweet").style.left = 407;			
		}
		
		function updateBigFacebookLikeButton(url, likeBtnY) {
			$('#big_like').html('<fb:like href="' + url + '" layout="standard" width="507" show_faces="false" action="like" colorscheme="light" send="true" />');
			if (typeof FB !== 'undefined') {
			    FB.XFBML.parse(document.getElementById('big_like'));
			}
			window.document.getElementById("big_like").style.top = parseInt(likeBtnY) - window.document.getElementById("big_like").offsetHeight - 80;
		}
		
		function showValues(val1, val2) {
			if (val1) {
				alert("show value: " + val1);				
			}
		}
		
		function getOffset( el ) {
		    var _x = 0;
		    var _y = 0;
		    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
		        _x += el.offsetLeft - el.scrollLeft;
		        _y += el.offsetTop - el.scrollTop;
		        el = el.parentNode;
		    }
			return _y;
		    // return { top: _y, left: _x };
		}	
		
		function thisMovie(movieName) {
		    var movie;
		    try {
		        movie = document[movieName];
		        movie = (movie == null) ? window[movieName] : movie;        
		    } catch (e) {
		        return null;
		    }
		    return movie;
		}
		
		function updateSongDownloads(enable) {
			thisMovie("musicPlayer").updateDownloads(enable.toString());
		}		
		
		function initializeJS() {
			window.fbAsyncInit = function() {
				FB.init({
			    	appId      : '<?php echo $appId ?>', 
					channelURL : 'simple-ocean-7178.herokuapp.com/fb_auth/channel.html',
			    	cookie     : true,
			    	oauth      : true,
			    	xfbml      : true 
			  	});

			  	// Additional initialization code here
				FB.Canvas.setSize({ width: 520, height: 1200 });
				FB.Event.subscribe('edge.create', function(response) {
					if (response.indexOf(fbPageUrl) != -1) {
				 		window.location.reload();					
					}
				});			
			};

			// Load the SDK Asynchronously
		 	(function() {
				var e = document.createElement('script'); e.async = true;
			    e.src = document.location.protocol +
			    '//connect.facebook.net/en_US/all.js';
				document.getElementById('fb-root').appendChild(e);
			}());			
		}
	
	</script>
	
<?php
	
	/**
	 * @return the home URL for this site
	 */
	function getHome () {
	  return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: "http") . "://" . $_SERVER['HTTP_HOST'] . "/";
	}

?>
</body>
</html>