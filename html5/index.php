<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<!--script type="text/javascript" src="site/swfobject.js"></script-->
	<!--script type="text/javascript" src="site/FBJSBridge.js"></script-->
	<script type="text/javascript" src="../scripts/spin.js"></script>	
	<script type="text/javascript" src="../scripts/soundcloud.player.api.js"></script>
	<script type="text/javascript" src="../scripts/sc-player.js"></script>
	<!--script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script-->	
	<!--script type="text/javascript" src="scripts/prototype.js"></script-->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script src="http://connect.soundcloud.com/sdk.js" type="text/javascript"></script>	
	<link rel="stylesheet" type="text/css" href="../site/index.css" />
	<link rel="stylesheet" type="text/css" href="../style/sc-player-standard.css" />
	<!--script type="text/javascript" src="site/history/history.js"></script-->
</head>	
<body>
		
		<?php
		
		$then = microtime();
		
		require_once('../sc-api/Soundcloud.php');
		require_once('../php-sdk/src/facebook.php');
		
		session_start();

		$appId = '107796503671';
		$appSecret = '10cc0163136a373aa6192f6ceafda96e';
		$appUrl = 'http://apps.facebook.com/thebandapp';	
		$fbPageUrl = "facebook.com/lillywolfanddrnu?sk=app_107796503671";
		$scAccessCode = "302883";
		$scConsumerKey = "738091d6d02582ddd19de7109b79e47b";
	
		$config = array();
		$config['appId'] = $appId;
		$config['secret'] = $appSecret;
		$config['fileUpload'] = false; // optional	
		
		$MAX_SONGS_SHOWN = 4;	

		$facebook = new Facebook($config);	
		# $user_id = $facebook->getUser();
		$req = $facebook->getSignedRequest();
		# $accessToken = $facebook->getApplicationAccessToken();
		$pageId = $req['page']['id'];
		if ($req['page']['liked']) {
			$liked = "true";
		 	$downloads_enabled = "true";
		} else {
		 	$liked = "false";
		 	$downloads_enabled = "false";
		}
		
		$soundcloud = new Services_Soundcloud('738091d6d02582ddd19de7109b79e47b', 'b8f231ac6dc380b6efb2a8a88cd6d9fe', 'http://simple-ocean-7178.herokuapp.com/auth/');
		$soundcloud->setAccessToken('1-12872-7625335-e561f85b896d9158');
		
		try {
		    $trackdata = json_decode($soundcloud->get('me/tracks'), true);
			$playlistdata = json_decode($soundcloud->get('me/playlists'), true);
		} catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
		    exit($e->getMessage());
		}
		
		$track_uri = $trackdata[0]['stream_url'] . '?secret_token=1-12872-7625335-94e91695a1ea1e98&client_id=738091d6d02582ddd19de7109b79e47b';
		$playlist_id = $playlistdata[0]['id'];
						
		$after = microtime();	
				
		?>
		
		<div id="page_heading_div" class="hidden"></div>
		<div id="notice"></div>
		<!--iframe id="sc_iframe" width="100%" height="450" scrolling="no" frameborder="no" src="http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F<?php echo $playlist_id ?>&amp;auto_play=false&amp;show_artwork=true&amp;color=ff7700&amp;allowscriptaccess=always"></iframe-->
		
		<div id="flash">
			<!--div id="player" style="display: none"-->
			<div id="player">	
				<div id="player_bg">
					<div id="top_player_wrapper">
						<div id="top_player">
							<audio id="top_audio">
							</audio>	
							<div id="top_pic">
								<img id="top_pic_inner"></img>
							</div>
							<div id="play_btn_wrapper">
								<div id="play_btn" onClick="playButtonClick()"></div>
								<div id="pause_btn" onClick="playButtonClick()"></div>
							</div>	
							<div id="player_items">
								<div id="top_text">
									<div id="top_title"></div>
									<div id="top_timer"></div>
								</div>
								<div id="song_bar"></div>
								<div id="top_buttons">
									<div id="download_btn_wrapper">
										<div id="download_btn"></div>
									</div>
									<div id="buy_btn_wrapper">	
										<div id="buy_btn"></div>
									</div>	
									<div id="top_like"></div>
									<div id="top_tweet"></div>
								</div>
							</div>
						</div>	
					</div>	
					<div id="songlist">
						<?php $i = 1; foreach ($trackdata as $track) {
							echo '<div class="song" id="song_' . $i . '" onClick="populatePlayer(\'' . $track['title'] . '\', ' . $i . ', \'' . $track['permalink_url'] . '\', \'' . $track['artwork_url'] . '\', \'' . $track['stream_url'] . '?secret_token=1-12872-7625335-94e91695a1ea1e98&client_id=738091d6d02582ddd19de7109b79e47b\');">
							<audio class="hidden_audio" id="audio_' . $i . '"><source="' . $track['stream_url'] . '"></source></audio>
							<div class="song_title">' . $track['title'] . '</div>
							<div class="song_stats">
								<div class="stat_num_plays">' . $track['playback_count'] . '</div>
								<div class="stat_text_plays">plays</div>
							</div>
							<div class="song_btns">
								<div id="download_btn_wrapper"><div id="download_btn"></div></div>
								<div id="buy_btn_wrapper"><div id="buy_btn"></div></div>
							</div>	
							<!--button onClick="document.getElementById(\'audio_' . $i . '\').pause()">Pause</button--></div>';
							$i++;
						} ?>
					</div>
					<div id="scrollers">
						<div id="scroll_up_wrapper">
							<div id="scroll_up" onClick="scrollSongsUp()"></div>
						</div>
						<div id="scroll_down_wrapper">
							<div id="scroll_down" onClick="scrollSongsDown()"></div>
						</div>
					</div>	
				</div>	
			</div>
			
				<!--source src="<?php echo $track_uri ?>" type="audio/mpeg" /-->
				
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="musicPlayer" width="514" height="960">
			    <param name="movie" value="../site/Main.swf">
				<param name="allowFullScreen" value="true">
				<param name="allowScriptAccess" value="always">
				<param name="scale" value="noscale">
				<param name="wmode" value="transparent">
				<param name="flashvars" value="downloads_enabled=<?php echo $downloads_enabled ?>&liked=<?php echo $liked ?>">					
	            <!--[if !IE]>-->
	            <object type="application/x-shockwave-flash" data="../site/Main.swf" id="musicPlayer" width="514" height="960">
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
		
		var MAX_TRACKS = 4;
		var isPlaying = false;
		var currentTrackIndex = 1;
		var currentScrollIndex = 1;
		initializeJS();
		updateDisplayedSongs();
		
		// var timeleft = $('#top_player #top_timer');
		topPlayer = $('#top_player');
		// 
		// function addAudioListeners() {
		// 	var audio = document.getElementById('top_audio');
		// 	if ((audio.buffered != undefined) && (audio.buffered.length != 0)) {
		// 		$(audio).bind('progress', function) {
		// 			var loaded = parseInt(((audio.buffered.end(0) / audio.duration) * 100), 10);
		// 		}
		// 		$(audio).bind('timeupdate', function() {
		// 			var rem = parseInt(audio.duration - audio.currentTime, 10),
		// 		  	pos = (audio.currentTime / audio.duration) * 100,
		// 		  	mins = Math.floor(rem/60,10),
		// 		  	secs = rem - mins*60;
		// 		timeleft.text('-' + mins + ':' + (secs > 9 ? secs : '0' + secs));
		// 	}
		// }
		
		function swapAudio(url, trackIndex) {
			var idStr = 'audio_'+trackIndex.toString();
			alert(idStr);
			// audio = $(idStr);
			// alert(audio);
			//audio = document.getElementById('top_audio');
			// audio.remove();
			// audio.html('<source src="' + url + '" type="audio/mpeg"></source>');
			// topPlayer.appendChild(newAudio);
			// addAudioListeners();
			var topAudio = document.getElementById(idStr);
			alert(topAudio.innerHTML);
			topAudio.play();
		}
			
		SC.initialize({
			client_id: '738091d6d02582ddd19de7109b79e47b',
			redirect_uri: 'http://simple-ocean-7178.herokuapp.com/auth/',
		});
		
		SC.accessToken = '1-12872-7625335-e561f85b896d9158';
		
		function scrollSongsDown() {
			var songs = getElementsByClass('song', 'songlist');
			if (currentScrollIndex <= songs.length - MAX_TRACKS) {
				currentScrollIndex = currentScrollIndex + 1;
				updateDisplayedSongs();
			}
		}
		
		function scrollSongsUp() {
			if (currentScrollIndex > 1) {
				currentScrollIndex = currentScrollIndex - 1;
				updateDisplayedSongs();
			}
		}
		
		function updateDisplayedSongs() {
			var songs = getElementsByClass('song', 'songlist');
			var i;
			for (i = 1; i <= songs.length; i++) {
				var song = document.getElementById('song_'+i.toString());
				if (i >= currentScrollIndex && i < currentScrollIndex + MAX_TRACKS && song != null) {
					song.style.display = 'block';
				} else {
					song.style.display = 'none';
				}
			}
		}
		
		function getElementsByClass(matchClass, parentId)
		{
			var matches = [];
		    var elems = document.getElementById(parentId).getElementsByTagName('*');
			for (var i in elems)
			{
		        if(elems[i] != null && elems[i].className != null && elems[i].className.toString() == matchClass.toString())
				{
		            matches.push(elems[i]);
		        }
		    }
			return matches;
		}
		
		function populatePlayer(title, trackIndex, url, picUrl, streamUrl) {
			isPlaying = true;
			currentTrackIndex = trackIndex;
			document.getElementById('top_title').innerHTML = title;
			showPause();
			swapAudio(streamUrl, trackIndex);
			updateButtons(url);
			updatePic(picUrl);
		}
		
		function updatePic(pic_url) {
			document.getElementById('top_pic_inner').src = pic_url;
		}	
			
		function showPause() {
			document.getElementById('play_btn').style.display = "none";		
			document.getElementById('pause_btn').style.display = "block";		
		}
		
		function showPlay() {
			document.getElementById('pause_btn').style.display = "none";			
			document.getElementById('play_btn').style.display = "block";			
		}
		
		function playButtonClick() {
			if (isPlaying) {
				doPause();
			} else {
				doPlay();
			}
		}
		
		function doPlay() {
			document.getElementById('audio_'+currentTrackIndex.toString()).play();
			showPause();
		}
		
		function doPause() {
			document.getElementById('audio_'+currentTrackIndex.toString()).pause();
			showPlay();
		}
		
		function updateButtons(url) {
			updateTopFacebookLikeButton(url);
		}
		
		function updateTopFacebookLikeButton(url) {
			$('#top_like').html('<fb:like href="' + url + '" layout="button_count" show_faces="false" action="like" font="arial" colorscheme="light" send="true" />');
			if (typeof FB !== 'undefined') {
			    FB.XFBML.parse(document.getElementById('top_like'));
			}
		}	
		
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
			// initializeJS();
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