<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<script type="text/javascript" src="site/swfobject.js"></script>
	<script type="text/javascript" src="site/FBJSBridge.js"></script>
	<script type="text/javascript" src="scripts/spin.js"></script>	
	<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<!--script type="text/javascript" src="site/history/history.js"></script-->
	
	<style>
		body {
			margin: 0px;
			overflow-y: hidden;
		}
		#flashContent {
			
		}
		#fb-root {
			overflow-y: hidden;
		}
		#tweet {
			position: absolute;
		}
		#like {
			position: absolute;
		}
		#big_like {
			position: absolute;
		}
		#spinner {
			margin: 150px 0;
		}
		.twitter-follow-button {
			margin: 15px 0;
		}
		a.soundcloud-badge:hover {
			background-position: bottom left !important;
		}
		*html a.soundcloud-badge {
			background-image: none !important; 
			filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='(http://a1.sndcdn.com/images/badges/fmonsc/horizontal/dark-orange.png?6485e1c)', sizingMethod='crop') !important;
		}	
		.soundcloud-badge {
			text-align: left; 
			display: block; 
			margin: 0 auto 4px auto; 
			width: 246px; 
			height: 27px; 
			font-size: 11px; 
			padding: 36px 0 0 104px; 
			background: transparent url(http://a1.sndcdn.com/images/badges/fmonsc/horizontal/dark-orange.png?6485e1c) top left no-repeat; 
			color: #ffffff; 
			text-decoration: none; 
			font-family: "Lucida Grande", Helvetica, Arial, sans-serif; 
			line-height: 1.3em; 
			outline: 0;			
		}
		#soundcloud-badge-inner {
			display: block; 
			width: 230px; 
			white-space: nowrap; 
			height: 20px; 
			margin: 0 0 0 0; 
			overflow: hidden; 
			-o-text-overflow: ellipsis; 
			text-overflow: ellipsis;			
		}
		#downloader-frame {
			visibility: hidden;
			height: 0px;
		}
		#shows {
			font-family: Arial;
			color: #8F8668;
			font-size: 14px;
		}
		#flash {
			position: absolute;
			top: 0px;
		}
	</style>
</head>	
<body>
		
	<div id="page_heading_div" class="hidden"></div>
	<div id="spinner"></div>
	<span id="tweet"></span>
	<span id="like"></span>
	<span id="big_like"></span>
	<iframe id="downloader-frame" frameborder="0"></iframe>
	<span id="downloaders"></span>
	<!--a href="http://soundcloud.com/lillywolf/follow" class="soundcloud-badge"><span id="soundcloud-badge-inner">http://soundcloud.com/lillywolf</span></a-->
	<!--div class="fb-add-to-timeline" data-show-faces="true"></div-->
	
<?php

	require_once('php-sdk/src/facebook.php');

	$appId = '107796503671';
	$appSecret = '10cc0163136a373aa6192f6ceafda96e';
	$appUrl = 'http://apps.facebook.com/thebandapp';	
	$fbPageUrl = "facebook.com/lillywolfmusic";
	
	$config = array();
	$config['appId'] = $appId;
	$config['secret'] = $appSecret;
	$config['fileUpload'] = false; // optional	

	$facebook = new Facebook($config);	
	$user_id = $facebook->getUser();
	
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

	# Here we establish the connection. Yes, that's all.
	$pg_conn = pg_connect(pg_connection_string_from_database_url());

	# Now let's use the connection for something silly just to prove it works:
	$result = pg_query($pg_conn, "SELECT venue FROM shows WHERE artist_id=1");

	print "<div id='shows'>";
	if (!pg_num_rows($result)) {
	  # print("Your connection is working, but your database is empty.\nFret not. This is expected for new apps.\n");
	} else {
	  	while ($row = pg_fetch_row($result)) { 
			# print("- $row[0]\n"); 
			print("<span class='show'>$row[0]</span>"); 
		}
	}
	print "</div>";	
	
	?>
	
    <div id="fb-root"></div>

	<script type="text/javascript">
	
		// FB_PAGE_URL = <?php echo $fbPageUrl; ?>

		var spinner;
		preload();
		
		window.onload = function() {
			// preload();			
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
			window.document.getElementById('spinner').style.margin = "0px";			
			window.document.getElementById('spinner').style.visibility = "hidden";
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
	
	</script>

	<script type="text/javascript">
	    window.fbAsyncInit = function() {
			FB.init({
	        	appId: '<?php echo $facebook->getAppID() ?>', 
	        	cookie: true, 
	        	xfbml: true,
	        	oauth: true
	      	});
	      	// FB.Event.subscribe('auth.login', function(response) {
	      	// 	        	window.location.reload();
	      	// });
	      	// FB.Event.subscribe('auth.logout', function(response) {
	      	// 	        	window.location.reload();
	      	// });
			FB.Event.subscribe('edge.create', function(response) {
				if (response.indexOf(FB_PAGE_URL) != -1) {
		        	window.location.reload();					
				}
				else
				{
					alert("not a page like!");
				}
			});	
			FB.Canvas.setSize({ width: 520, height: 1200 });
	    };
	    (function() {
	      var e = document.createElement('script'); e.async = true;
	      e.src = document.location.protocol +
	        '//connect.facebook.net/en_US/all.js';
	      document.getElementById('fb-root').appendChild(e);
	    }());	
	</script>
	
	<?php
	
	function printSwf($liked, $downloads_enabled) {
		echo '<div id="flash">
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="514" height="880">
			    <param name="movie" value="site/Main.swf">
				<param name="allowFullScreen" value="true">
				<param name="allowScriptAccess" value="always">
				<param name="scale" value="noscale">
				<param name="wmode" value="transparent">
				<param name="flashvars" value="downloads_enabled=' . $downloads_enabled . '&liked=' . $liked . '">					
	            <!--[if !IE]>-->
	            <object type="application/x-shockwave-flash" data="site/Main.swf" id="music-player" width="514" height="880">
	                <param name="quality" value="high" />
	                <param name="bgcolor" value="#ffffff" />
	                <param name="allowScriptAccess" value="always" />
	                <param name="allowFullScreen" value="true" />	
					<param name="wmode" value="transparent" />
					<param name="flashvars" value="downloads_enabled=' . $downloads_enabled . '&liked=' . $liked . '">					
			    <!--embed src="site/Main.swf" width="514" height="880">
			    </embed-->
	            <!--[if !IE]>-->
	            </object>				
			</object>
		</div>
		<a href="https://twitter.com/lillywolf" class="twitter-follow-button" data-show-count="false">Follow @lillywolf</a>';
	}
	
	function shiftElements() {
		echo '<script>
			var offsetY = window.document.getElementById("flash").offsetHeight;
			window.document.getElementById("shows").style.top = offsetY.toString()+"px";
		</script>';
	}
	
	if($user_id) {
		try {
			$user_profile = $facebook->api('/me', 'GET');	
			$signed_request = $facebook->getSignedRequest();
			if ($signed_request['page']['liked']) { 
				printSwf("true", "true");
				shiftElements();
				?>
				
				<script type="text/javascript">				
					// document.getElementById("page_heading_div").style.display = "inline";
					// document.getElementById("flashContent").style.display = "inline";
					// var params = { wmode: "opaque" };
					// params.allowfullscreen = "true";
					// params.allowscriptaccess = "always";
					// params.scale = "noscale";
					// var attributes = {};
					// attributes.name = "flashContent";	
					// var flashvars = {};
					// flashvars.downloads_enabled = "true";
					// flashvars.liked = "true";
					// swfobject.embedSWF("site/Main.swf", "flashContent", "514", "100%", "10.0", null, flashvars, params, attributes);				
				</script>			
				
				<?php
			} else {
				printSwf("false", "false");				
				?>
				
				<script type="text/javascript">
					
					FB.Event.subscribe('edge.create',
					    function(response) {
							if (response.indexOf(FB_PAGE_URL) != -1) {
					        	window.location.reload();					
							} else {
								alert("not a page like!");
							}					    
						}
					);								
				</script>				
				
				<?php
			}					
		}
		catch(FacebookApiException $e) {
			$loginUrl = $facebook->getLoginUrl($params);			
	        error_log($e->getType());
	        error_log($e->getMessage());			
		}	
	}
	else {		
		$signed_request = $facebook->getSignedRequest();
		if ($signed_request['page']['liked']) { 
			printSwf("true", "true");
		} else {
			printSwf("false", "false");
		}	
		
		// FOR ADMIN PANEL
		
		// $state = md5(uniqid(rand(), TRUE));
		// $scope = 'email,publish_stream,manage_pages';
		// $home = getHome();
		// $authorize_url = "https://www.facebook.com/dialog/oauth?client_id=$appId" .
		//       	"&redirect_uri=$home&state=" . $state . "&scope=$scope";		
		//       	echo("<script> top.location.href='" . $authorize_url . "'</script>");
			
		// Use this for non-facebook canvas page (i.e. Facebook Connect)		
		// header('Location:' . $facebook->getLoginURL());
	}
	
	/**
	 * @return the home URL for this site
	 */
	function getHome () {
	  return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: "http") . "://" . $_SERVER['HTTP_HOST'] . "/";
	}

?>
</body>
</html>