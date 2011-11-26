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
		
		$then = microtime();
		
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
		# $user_id = $facebook->getUser();
		$req = $facebook->getSignedRequest();
		$accessToken = $facebook->getApplicationAccessToken();
		$pageId = $req['page']['id'];
		if ($req['page']['liked']) {
			$liked = "true";
			$downloads_enabled = "true";
		} else {
			$liked = "false";
			$downloads_enabled = "false";
		}

		$after = microtime();	
				
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
			<div id="twitter"></div>
			<div id="shows"></div>
			<!--a href="http://soundcloud.com/lillywolf/follow" class="soundcloud-badge"><span id="soundcloud-badge-inner">http://soundcloud.com/lillywolf</span></a-->			
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
			  color: '#00E1FA', // #rgb or #rrggbb
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
			addTwitterFollowButton();
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
		
		function addTwitterFollowButton() {
			var link = document.createElement('a');
			link.setAttribute('href', 'https://twitter.com/lillywolf');
			link.setAttribute('class', 'twitter-follow-button');
			link.setAttribute('data-show-count', 'false');
			link.setAttribute('name', 'Follow @lillywolf');
			window.document.getElementById("twitter").appendChild(link);			
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
				
				alert("app access token: <?php echo $accessToken ?>");	
				
				FB.api('/202357?access_token=<?php echo $accessToken ?>', function(response) {
					alert("access token: " + response.toSource());
				});
				
				// alert("get url: <?php echo $pageId ?>?access_token=<?php echo $accessToken ?>");
				// FB.api('/<?php echo $pageId ?>?access_token=<?php echo $accessToken ?>', function(response) {
					// for (var i = 0; i < response.length; i++) {
					// 	alert("show post " + response[i]);
					// 	var e = document.createElement('div');
					// 	e.setAttribute('class', 'fb-post');
					// 	var pn = document.createElement('div');
					// 	alert("name: " + response[i].name);
					// 	pn.setAttribute('name', response[i].name);
					// 	pn.setAttribute('class', 'post-name');
					// 	e.appendChild(pn);
					// 	var pd = document.createElement('div');
					// 	pd.setAttribute('name', response[i].description);
					// 	pd.setAttribute('class', 'post-description');
					// 	e.appendChild(pd);						
					// 	document.getElementById('extra-content').appendChild(e);
					// }
				  // alert('Your p: ' + response.toSource());
				// });		
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
	
		
	# FOR ADMIN PANEL

	/* $state = md5(uniqid(rand(), TRUE));
	$scope = 'email,publish_stream,read_stream,manage_pages';
	$home = getHome();
	$authorize_url = "https://www.facebook.com/dialog/oauth?client_id=$appId" .
	      	"&redirect_uri=$home&state=" . $state . "&scope=$scope";		
	      	echo("<script> top.location.href='" . $authorize_url . "'</script>"); */	

	# Use this for non-facebook canvas page (i.e. Facebook Connect)		
	# header('Location:' . $facebook->getLoginURL());	
	
	/*$fp = fsockopen("simple-ocean-7178.herokuapp.com", 80, $errno, $errstr);
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
	} else {
		$out = "GET /fb_auth/ HTTP/1.1\r\n";
		$out .= "Host: simple-ocean-7178.herokuapp.com\r\n";
		$out .= "Connection: Close\r\n\r\n";
		$result = fwrite($fp, $out);
		$ret = "";
		while ($line = fgets($fp)) {
			$ret .= $line;
		}
		print_r($ret);
		fclose($fp);
	}*/				

?>
</body>
</html>