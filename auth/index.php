<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<!--script type="text/javascript" src="site/swfobject.js"></script-->
	<!--script type="text/javascript" src="site/FBJSBridge.js"></script-->
	<script type="text/javascript" src="../scripts/spin.js"></script>	
	<!--script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script-->	
	<!--script type="text/javascript" src="scripts/prototype.js"></script-->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script src="http://connect.soundcloud.com/sdk.js" type="text/javascript"></script>	
	<link rel="stylesheet" type="text/css" href="../site/index.css" />
	<!--script type="text/javascript" src="site/history/history.js"></script-->
</head>	
<body>
		
		<?php
		
		$then = microtime();
		
		require_once('../sc-api/Soundcloud.php');
		require_once('../php-sdk/src/facebook.php');

		$scAccessCode = "302883";
		$scConsumerKey = "738091d6d02582ddd19de7109b79e47b";
				
		$soundcloud = new Services_Soundcloud('738091d6d02582ddd19de7109b79e47b', 'b8f231ac6dc380b6efb2a8a88cd6d9fe', 'http://simple-ocean-7178.herokuapp.com/auth/');				
		try {
			$accessToken = $soundcloud->accessToken($_GET['code']);
			print_r($accessToken);
		} catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
		    exit($e->getMessage());
		}
				
		$after = microtime();	
				
		?>
		
	<script type="text/javascript">
		
		var tracks;
			
		// SC.initialize({
		// 	// client_id: '<?php echo $scConsumerKey; ?>',
		// 	// redirect_uri: '<?php echo $fbPageUrl; ?>',
		// 	client_id: '738091d6d02582ddd19de7109b79e47b',
		// 	redirect_uri: 'http://facebook.com/lillywolfanddrnu?sk=app_107796503671',
		// });
		
		// SC.connect(function(){
		// 	alert("connected");
		// });
			
		// SC.get("/groups/<?php echo $scAccessCode; ?>/tracks", 
		// 	{limit: 1}, 
		// 	function (received_tracks) {
		// 		tracks = received_tracks;
		// 		alert(tracks);
		//     	// alert("Latest track: " + tracks[0].title);
		// 	}
		// );	
		
		// SC.whenStreamingReady(function() {
		//   var soundObj = SC.stream(tracks[0].id);
		//   soundObj.play();
		// });
		
		fbPageUrl = '<?php echo $fbPageUrl; ?>';

		var MAX_POSTS = 5;
		var spinner;
		// preload();
		
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
			window.document.getElementById('spinner').style.margin = "0px";			
			window.document.getElementById('spinner').style.visibility = "hidden";
			shiftElements();
			// addTwitterFollowButton();
			initializeJS();
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
				// FB.Canvas.setSize({ width: 520, height: 1200 });
				FB.Canvas.setAutoGrow();
				FB.Event.subscribe('edge.create', function(response) {
					if (response.indexOf(fbPageUrl) != -1) {
				 		window.location.reload();					
					}
				});	
			};
			
			// loadWall();	

			// Load the SDK Asynchronously
		 	(function() {
				var e = document.createElement('script'); e.async = true;
			    e.src = document.location.protocol +
			    '//connect.facebook.net/en_US/all.js';
				document.getElementById('fb-root').appendChild(e);
			}());			
		}
		
		function loadWall() {
			FB.api('/<?php echo $pageId ?>/posts?access_token=AAAAAGRksHHcBAEHhDiAkSg6IKmhBHB9ZBirFtLh2AKCsSH5sU8oHtIUfVwDfqNEjk7MSEWeKsjpMQDJsY5NQRJN0tXxT3PM6CwvYlnwZDZD', function(response) {
				var header = document.createElement('img');
				header.setAttribute('src', '/images/headers/posts_header.png');
				document.getElementById('shows').appendChild(header);
				for (var i = 0; i < Math.min(response.data.length, MAX_POSTS); i++) {
					var e = document.createElement('div');
					e.setAttribute('class', 'fb-post');
					if (response.data[i].from) {
						var pf = document.createElement('a');
						pf.appendChild(document.createTextNode(response.data[i].from.name));
						pf.setAttribute('href', 'http://facebook.com/' + response.data[i].from.id);
						pf.setAttribute('class', 'post-from');
						e.appendChild(pf);							
					}
					if (response.data[i].message) {
						var pm = document.createElement('div');
						pm.appendChild(document.createTextNode(response.data[i].message));
						pm.setAttribute('class', 'post-message');
						e.appendChild(pm);							
					}
					if (response.data[i].name) {
						var pn = document.createElement('a');
						pn.appendChild(document.createTextNode(response.data[i].name));
						pn.setAttribute('href', response.data[i].link)
						pn.setAttribute('class', 'post-name');
						e.appendChild(pn);							
					}
					if (response.data[i].caption) {
						var pc = document.createElement('div');
						pc.appendChild(document.createTextNode(response.data[i].caption));
						pc.setAttribute('class', 'post-caption');
						e.appendChild(pc);							
					}					
					if (response.data[i].description) {
						var pd = document.createElement('div');
						pd.appendChild(document.createTextNode(response.data[i].description));
						pd.setAttribute('class', 'post-description');
						e.appendChild(pd);						
					}
					
					document.getElementById('shows').appendChild(e);
					
					if (response.data[i].icon) {
						var pf = document.createElement('div');
						pf.setAttribute('class', 'post-footer');
						e.appendChild(pf);
						
						var pi = document.createElement('img');
						pi.setAttribute('src', response.data[i].icon);
						pi.setAttribute('class', 'post-icon');
						pf.appendChild(pi);		
						
						for (j = 0; j < response.data[i].actions.length; j++) {
							if (response.data[i].actions[j].name == "Like") {
								var post_like = document.createElement('div');
								post_like.setAttribute('id', 'post-like-' + i.toString());
								post_like.setAttribute('class', 'post-like');
								pf.appendChild(post_like);
								$('#post-like-' + i.toString()).html('<fb:like href="' + response.data[i].actions[j].link + '" layout="button_count" show_faces="false" action="like" font="arial" colorscheme="light" send="false" />');
								if (typeof FB !== 'undefined') {
								    FB.XFBML.parse(document.getElementById('post-like-' + i.toString()));
								}
							}
						}				
					}
										
					if (i != MAX_POSTS-1 && i != response.data.length-1) {
						e.style.borderBottom = "1px solid #E9E9E9";
					}				
				}						
			});							
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