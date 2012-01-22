<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#" class="no-js">
<head>
	<link rel="stylesheet" type="text/css" href="../site/index.css" />
	<!--script type="text/javascript" src="../scripts/soundcloud.player.api.js"></script-->
	<!--script type="text/javascript" src="../scripts/sc-player.js"></script-->
	<!--script src="http://connect.soundcloud.com/sdk.js" type="text/javascript"></script-->	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<script type="text/javascript" src="../scripts/sm2/soundmanager2.js"></script>
</head>	
<body>
		
		<?php
		
		$then = microtime();
		
		require_once('../sc-api/Soundcloud.php');
		require_once('../php-sdk/src/facebook.php');
		// require_once('../redis/redis.php');
		
		session_start();
		
		/**
		 * @return the home URL for this site
		 */
		function getHome () {
		  return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?: "http") . "://" . $_SERVER['HTTP_HOST'] . "/";
		}

		$appId = '107796503671';
		$appSecret = '10cc0163136a373aa6192f6ceafda96e';
		$appUrl = 'http://apps.facebook.com/thebandapp';	
		$fbPageUrl = "facebook.com/lillywolfmusic?sk=app_107796503671";
		$scAccessCode = "302883";
		$scConsumerKey = "738091d6d02582ddd19de7109b79e47b";
		$scope = 'email,publish_stream,publish_actions';
		$home = 'http://www.facebook.com/' . 'lillywolfmusic' . '?sk=app_' . '107796503671';
		$perms = null;
	
		$config = array();
		$config['appId'] = $appId;
		$config['secret'] = $appSecret;
		$config['fileUpload'] = false; // optional	
		
		$MAX_SONGS_SHOWN = 4;	
		$DOWNLOAD_ALL_PLAYLIST_NAME = 'lilly-and-dr-nu-mp3s';
		$DOWNLOAD_PLAYLIST = 'lilly-and-dr-nu-mp3s';
		$PLAYLIST_NAME = 'lilly-and-dr-nu-mp3s';

		$facebook = new Facebook($config);	
		// $appAccessToken = $facebook->getApplicationAccessToken();
		$user_id = $facebook->getUser();
		$loginUrl = $facebook->getLoginUrl(array(
			'redirect_uri' => $home, 
			'scope' => $scope			
			));
		$req = $facebook->getSignedRequest();
		$pageId = $req['page']['id'];
		if ($req['page']['liked']) {
			$liked = "true";
		} else {
		 	$liked = "false";
		}
		
		$soundcloud = new Services_Soundcloud('738091d6d02582ddd19de7109b79e47b', 'b8f231ac6dc380b6efb2a8a88cd6d9fe', 'http://simple-ocean-7178.herokuapp.com/auth/');
		$soundcloud->setAccessToken('1-12872-7625335-e561f85b896d9158');
		
		try {
		    # $trackdata = json_decode($soundcloud->get('me/tracks'), true);
			$playlistdata = json_decode($soundcloud->get('me/playlists'), true);
		} catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
		    exit($e->getMessage());
		}
		
		// echo '<script> var playlists = new Array(); </script>';
		$download_tracks = null;
		$playlist_id = null;
		$playlists = null;
		foreach ($playlistdata as $playlist) {
			$download_tracks_urls = '';
			$download_tracks = $playlist['tracks']; 
			foreach($download_tracks as $track) {
				$download_tracks_urls = $download_tracks_urls . $track['download_url'] . ',';
			}
			$playlists[$playlist['permalink']] = $download_tracks_urls;
			echo '<script> playlists["'.$playlist['permalink'].'"] = '.$download_tracks_urls.'; </script>';
			if ($playlist['permalink'] == $PLAYLIST_NAME) {
				$trackdata = $playlist['tracks']; 
			}
		}
		
		// $redisWrapper = new Redis($user_id, $pageId);	
		// $redisWrapper->getLogs('pageviews');
		// $redisWrapper->getLogs('clicks');
									
		# Record data for users who've added the app
		// if ($user_id) {
		// 	$perms = $facebook->api('/me/permissions', 'GET');			
			// $redisWrapper = new Redis($user_id, $pageId);	
		// 	$redisWrapper->recordPermissions($perms['data'][0]);
		// 	$redisWrapper->recordAppAdded();			
		// 	$redisWrapper->recordVisits();		
		// 	$redisWrapper->recordLike($liked);
		// 	$redisWrapper->recordDownloadPlaylist($_COOKIE['download_playlist']);
		// }
							
		# Record time for efficiency analytics				
		$after = microtime();			
				
		?>
		
		<div id="page_heading_div" class="hidden"></div>
		<!--div id="missions">
			<div id="progress_label">
				GOALS COMPLETE:
			</div>	
			<div id="progress_bg">
				<img id="progress_bar" onMouseOver="progressBarHover()"/>
			</div>	
			<div id="progress_tip">
			</div>	
		</div-->
		<!--div id="notice">
			<div id="notice_bg">
				<div id="notice_title"></div>
				<div id="notice_text"></div>
				<div id="notice_btn_wrapper">
					<div id="download_all_btn" onClick="downloadPlaylist('lilly-and-dr-nu-mp3s', true)"></div>
					<div id="download_song_btn" onClick="downloadMissionSong()"></div>
					<div id="download_instrumentals_btn" onClick="downloadPlaylist('play-loud-instrumentals')"></div>
					<div id="add_app_btn" onClick="addApp()"></div>
				</div>		
			</div>	
		</div-->
		<!--div id="like_song_banner">
			<img id="like_song_prompt" src="../images/html5/like_song_prompt.png">
		</div-->
		<div id="like_page_banner">
			<!--img id="like_page_prompt" src="../images/html5/like_page_prompt.png"-->
		</div>	
		<div id="listen_banner">
			<img id="listen_img" src="../images/html5/listen_banner.jpg" onClick="playFirstTrack()">
			<div id="banner_song_like"></div>
		</div>
		<!--div id="song_play_btn_over"><img src="../images/html5/play_btn_tiny.png" /></div-->
		<!--img class="banner_pic" id="like_banner" src="../images/banners/like_lillywolf_512px.jpg" /-->				
		<span id="big_like">
			<span id="like_song_text"></span>
			<span id="big_like_btn"></span>
		</span>
		
		<div id="flash">
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
									<div id="top_timer">0:00</div>
								</div>
								<div id="song_bar"></div>
								<div id="top_buttons">
									<div id="download_btn_wrapper">
										<div id="download_btn" onClick="downloadCurrentSong()"></div>
									</div>
									<div id="buy_btn_wrapper">	
										<div id="buy_btn" onClick="buyCurrentSong()"></div>
									</div>	
									<div id="top_like"></div>
									<div id="top_tweet"></div>
								</div>
							</div>
						</div>	
					</div>	
					<div id="songlist">
						<?php $i = 1; foreach ($trackdata as $track) {
							echo '<div class="song" id="song_' . $i . '" onClick="populatePlayer(\'' . $track['title'] . '\', ' . $i . ', \'' . $track['permalink_url'] . '\', \'' . $track['artwork_url'] . '\', \'' . $track['download_url'] . '\', \'' . $track['stream_url'] . '?secret_token=1-12872-7625335-94e91695a1ea1e98&client_id=738091d6d02582ddd19de7109b79e47b\', \'' . $track['purchase_url'] . '\');">
							<audio class="hidden_audio" id="audio_' . $i . '"><source src="' . $track['stream_url'] . '?secret_token=1-12872-7625335-94e91695a1ea1e98&client_id=738091d6d02582ddd19de7109b79e47b"></source></audio>
							<div class="song_listing">
								<div class="song_title" id="song_title_' . $i . '">' . $track['title'] . '</div>
								<div class="play_prompt" id="play_prompt_' . $i . '">PLAY</div>
							</div>	
							<div class="song_stats">
								<div class="stat_num_plays">' . $track['playback_count'] . '</div>
								<div class="stat_text_plays">plays</div>
							</div>
							<div class="song_btns">
								<div id="download_btn_wrapper"><div class="download_song" id="download_btn" onClick="downloadSong(\'' . $track['download_url'] . '\')"></div></div>
								<div id="buy_btn_wrapper"><div class="buy_song" id="buy_btn" onClick="buySong(\'' . $track['purchase_url'] . '\')"></div></div>
							</div>
							<div class="song_sc_id" id="sc_id_' . $i . '">' . $track['id'] . '</div>	
							<div class="song_download_url" id="download_url_' . $i . '">' . $track['download_url'] . '</div>	
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
			<!--img class="banner_pic" src="../images/banners/lillynu_poster_520x520.png" /-->				
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
		<!--img src="../images/html5/song_bg_hover2.png" style="display: none" /-->
		
	<script type="text/javascript">
		
		var MAX_TRACKS = 4;
		var MAX_POSTS = 5;
		var DOWNLOAD_PLAYLIST = 'lilly-and-dr-nu-mp3s';
		var REWARD_PLAYLIST = 'play-loud-instrumentals';
		
		var totalMissions = 4;
		var missionSongIndex;
		var missionSongId;
		var isPlaying = false;
		var currentTrackIndex = 1;
		var currentScrollIndex = 1;
		var currentAudioElement;
		var currentSongData;
		var soundManager;
		var mp3Support = true;
		var smSongId;
		var goals = new Array();
		var indexedGoals = new Array();
		
		var liked = '<?php echo $liked ?>';
		var fbUserId = '<?php echo $user_id ?>';
		var fbPageUrl = '<?php echo $fbPageUrl ?>';
		var downloadedPlaylist = getCookie('download_playlist');
		
		timeleft = $('#top_timer');
		topPlayer = $('#top_player');
		
		var testAudio = document.createElement('audio');
		if (testAudio.canPlayType && testAudio.canPlayType('audio/mpeg') != 'no' && testAudio.canPlayType('audio/mpeg') != '') {
			mp3Support = true;
		} else {
			mp3Support = false;
		}
		
		// SC.initialize({
		// 	client_id: '738091d6d02582ddd19de7109b79e47b',
		// 	redirect_uri: 'http://simple-ocean-7178.herokuapp.com/auth/',
		// });
		// 
		// SC.accessToken = '1-12872-7625335-e561f85b896d9158';	
		
		init();	
		initSoundManager();
		// playButtonClick();
		
		function initSoundManager() {
			soundManager.url = '../scripts/sm2/swf/';
			soundManager.flashVersion = 9; // optional: shiny features (default = 8)
			soundManager.useFlashBlock = false; // optionally, enable when you're ready to dive in
			soundManager.debugMode = false;
			soundManager.onready(function() {
			});			
		}
		
		function init() {			
			setToFirstTrack();
			initializeJS();
			updateDisplayedSongs();
			listenForHovers();
			updatePlayerData(currentSongData['title'], 1, currentSongData['url'], currentSongData['picUrl'], currentSongData['downloadUrl'], currentSongData['streamUrl'], currentSongData['purchaseUrl']);
			stopButtonPropagations();
			setPageGoals();		
		}
		
		function setToFirstTrack() {
			currentSongData = {
				streamUrl: '<?php echo $trackdata[0]["stream_url"] . "?secret_token=1-12872-7625335-94e91695a1ea1e98&client_id=738091d6d02582ddd19de7109b79e47b" ?>',
				downloadUrl: '<?php echo $trackdata[0]["download_url"] ?>',
				url: '<?php echo $trackdata[0]["permalink_url"] ?>',
				title: '<?php echo $trackdata[0]["title"] ?>',
				picUrl: '<?php echo $trackdata[0]["artwork_url"] ?>',
				purchaseUrl: '<?php echo $trackdata[0]["purchase_url"] ?>'
			};
			currentAudioElement = document.getElementById('audio_1');
		}
		
		function playFirstTrack() {
			swapBanner();
			setToFirstTrack();
			playButtonClick();
		}
		
		function swapBanner() {
			$('#listen_img').fadeOut();
			var e = window.document.createElement('img');
			e.src = '../images/banners/like_song.jpg';
			e.id = 'like_img';
			e.css('opacity', '0');
			document.getElementById('listen_banner').appendChild(e);
			$('#like_img').fadeIn();
			$('#banner_song_like').html('<fb:like href="' + url + '" layout="button_count" show_faces="false" action="like" font="arial" colorscheme="light" send="true" />');
			if (typeof FB !== 'undefined') {
			    FB.XFBML.parse(document.getElementById('banner_song_like'));
			}					
		}
		
		// $('#progress_bar').mouseover(function(e) {
		// 	var goalNumber;
		// 	$('#progress_bar').mousemove(function(e) {
		// 		var distanceY = e.pageX - this.offsetLeft;
		// 		var segment = parseInt($('#progress_bg').width())/totalMissions;
		// 		var newGoal = Math.ceil(parseInt(distanceY)/segment);
		// 		if (newGoal != goalNumber) {
		// 			goalNumber = newGoal;
		// 			var ttText = toolTipGoal(goalNumber);
		// 			document.getElementById('progress_tip').style.display = 'block';
		// 			document.getElementById('progress_tip').innerHTML = ttText;
		// 			document.getElementById('progress_tip').style.marginLeft = this.offsetLeft + segment * (goalNumber-1);
		// 		}	
		// 	});
		// });
		// 
		// $('#progress_bar').mouseleave(function(e) {
		// 	$('#progress_bar').unbind('mousemove');
		// 	document.getElementById('progress_tip').style.display = 'none';
		// });
		
		// function toolTipGoal(goalNumber) {
		// 	if (parseInt(goalNumber) in goals) {
		// 		var ttText = getGoalToolTipText(goals[goalNumber]);
		// 	} 
		// 	return ttText;
		// }
		// 
		// function getGoalToolTipText(goal) {
		// 	if (goal && goal.id.indexOf('download_song') != -1) {
		// 		return '#' + goal.rank.toString() + ': DOWNLOADED SONG';
		// 	} else if (goal && goal.id == 'like') {
		// 		return '#' + goal.rank.toString() + ': LIKED PAGE';
		// 	} else if (goal && goal.id == 'add_app') {
		// 		return '#' + goal.rank.toString() + ': ADDED APP';
		// 	}
		// }
		
		function setPageGoals() {
			// $.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=get_page_missions', function(data, status) {
			// 	var listings = data.split(',');
			// 	for (var i = 0; i < listings.length; i++) {
			// 		var missionId = getPairValue(listings[i].split('&'), 'id');
			// 		var missionRank = parseInt(getPairValue(listings[i].split('&'), 'rank'));
			// 		if (missionId != "" && missionId != null) {
			// 			goals[missionRank] = {
			// 				id: missionId,
			// 				rank: missionRank
			// 			};
			// 			indexedGoals.push(goals[missionRank]);	
			// 		}
			// 	}
			// 	updateProgressBar();
			// });
			
			updateProgressBar();
		}

		function updateProgressBar() {
			// var highestMissionRank = 0;
			// currentMission = goals[1];
			// var currentMission;
			// for (var i = 0; i < indexedGoals.length; i++) {
			// 	var goalId = indexedGoals[i]['id'];
			// 	var rank = indexedGoals[i]['rank'];
			// 	indexedGoals[i]['complete'] = 0;
			// 	if (goalId == 'like') {
			// 		if (liked == 'true') {
			// 			indexedGoals[i]['complete'] = 1;
			// 			goals[rank]['complete'] = 1;	
			// 		} 
			// 	} else if (goalId.indexOf('download_song_') != -1) {
			// 		var checkSongId = goalId.split('download_song_')[1];
			// 		if (getCookie('download_song_'+checkSongId)) {
			// 			indexedGoals[i]['complete'] = 1;
			// 			goals[rank]['complete'] = 1;
			// 		}
			// 	} else if (goalId == 'add_app') {
			// 		if (fbUserId != null) {
			// 			indexedGoals[i]['complete'] = 1;
			// 			goals[rank]['complete'] = 1;
			// 		}
			// 	}
			// }
			// for (var i = 0; i <= indexedGoals.length; i++) {
			// 	if (goals[i] && goals[i]['complete'] == 0) {
			// 		var goalIndex = i;
			// 		currentMission = goals[i];
			// 		break;
			// 	}
			// }
			
			// alert(currentMission.toSource());
			
			// var title;
			// var buttonId;
			// var missionId = currentMission['id'];
			// document.getElementById('notice').style.display = 'block';
			
			// document.getElementById('flash').style.top = 105;
			// document.getElementById('big_like').style.top = 73;
			// document.getElementById('like_banner').style.display = 'none';
			
			// if (missionId == 'like') {
			if (liked == "false") {
				$.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=log_pageview&pageUrl=unliked', function(data, status) { });					
				
				// title = 'Click "Like" above to follow us on Facebook & get free downloads!';
				// // document.getElementById('notice').style.display = 'none';
				
				// document.getElementById('flash').style.top = 405;
				// document.getElementById('big_like').style.top = 370;
				// document.getElementById('like_banner').style.display = 'block';
				
				var e = window.document.createElement('img');
				e.src = '../images/html5/like_page_prompt.png';
				e.id = 'like_page_prompt';
				document.getElementById('like_page_banner').appendChild(e);
				document.getElementById('like_page_banner').style.display = 'block';
				// document.getElementById('listen_banner').style.display = 'none';				
				// document.getElementById('flash').style.top = 129;
				// document.getElementById('big_like').style.top = 90;
				document.getElementById('listen_banner').style.top = 129;
				document.getElementById('flash').style.top = 385;
				document.getElementById('big_like').style.top = 345;
			// } else if (missionId.indexOf('download_song_') != -1) {
			} else {
				if (fbUserId != '202357') {
					$.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=log_pageview&pageUrl=liked', function(data, status) { });					
				} else {
					$.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=pageview_logs', function(data, status) { });
				}
				
				// document.getElementById('notice').style.display = 'none';
				document.getElementById('like_page_banner').style.display = 'none';
				document.getElementById('listen_banner').style.display = 'block';				
				document.getElementById('flash').style.top = 262;
				document.getElementById('big_like').style.top = 215;
				
				// var parts = missionId.split('download_song_');
				// missionSongId = parts[1];
				// missionSongIndex = getTrackById(missionSongId);	
				// title = 'Download ' + document.getElementById('song_title_'+missionSongIndex.toString()).innerHTML + ', free!';
				// buttonId = 'download_song_btn';
			} 
			// else if (missionId == 'add_app') {
			// 	title = 'Add the music player app: ';
			// 	buttonId = 'add_app_btn';
			// }
			// document.getElementById('notice_title').innerHTML = title;
			// document.getElementById('download_all_btn').style.display = 'none';						
			// document.getElementById('download_song_btn').style.display = 'none';						
			// document.getElementById('add_app_btn').style.display = 'none';						
			// document.getElementById(buttonId).style.display = 'block';
			
			// if (goalIndex > 1) {
			// 	document.getElementById('progress_bar').src = '../images/html5/progress_bar_'+indexedGoals.length.toString()+'_'+(goalIndex-1).toString()+'_green.png';				
			// }
		}				
		
		function getTrackById(sc_id) {
			var ids = getElementsByClass('song_sc_id', 'songlist');
			for (var i = 1; i <= ids.length; i++) {
				var elem = document.getElementById('sc_id_'+i.toString());
				if (elem.innerHTML.toString() == sc_id.toString()) {
					return i;
				} 
			}
		}
				
		function getPairValue(arr, match)
		{
			for (var i = 0; i < arr.length; i++) {
				if (arr[i].split('=')[0] == match) {
					return arr[i].split('=')[1];
				}
			}
			return '';
		}
		
		function addApp() {
			window.open('<?php echo $loginUrl ?>');
		}
		
		function stopButtonPropagations() {
			$('.download_song').bind('click', function(event) {
				event.stopPropagation();	
			});
			$('.download_song').bind('mouseover', function(event) {
				$('.play_prompt').css('display', 'none');
				event.stopPropagation();	
			});
			$('.download_song').bind('hover', function(event) {
				event.stopPropagation();	
			});
			$('.buy_song').bind('click', function(event) {
				event.stopPropagation();	
			});
			$('.buy_song').bind('mouseover', function(event) {
				$('.play_prompt').css('display', 'none');
				event.stopPropagation();	
			});
			$('.buy_song').bind('hover', function(event) {
				event.stopPropagation();	
			});	
			$('#song_play_btn_over').off();
		}
		
		function listenForHovers() {
			$('.song').mouseover('', function(event) {
				var elem = event.delegateTarget;
				var str = '#'+elem.id.toString() + ' .play_prompt';
				var playPrompt = $(str)[0];
				var playPromptStr = '#' + playPrompt.id.toString();
				$(playPromptStr).css('display', 'block');
			});
		}
		
		$('.song').mouseleave(function(e) {
			$('.play_prompt').css('display', 'none');
		});
		 
		function addAudioListeners(idStr) {
			audio = $('#'+idStr).get(0);
			audio.currentTime = 0;
		 	if ((audio.buffered != undefined) && (audio.buffered.length != 0)) {
		 		$(audio).bind('timeupdate', function() {
		 			var rem = parseInt(audio.currentTime, 10),
		 				pos = (audio.currentTime / audio.duration) * 100,
		 		  		mins = Math.floor(rem/60, 10),
		 		  		secs = rem - mins*60;
		 			timeleft.text(mins + ':' + (secs > 9 ? secs : '0' + secs));
				});
		 	}
		}
		
		function swapAudio(url, trackIndex) {
			// Log plays
			$.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=log_click&clickType='+currentSongData.title, function(data, status) {
			});
			
			var idStr = 'audio_'+trackIndex.toString();
			var topAudio = document.getElementById(idStr);
 			currentAudioElement = topAudio;
			if (mp3Support) {
				addAudioListeners(idStr);
				topAudio.play();
			} else {
				if (smSongId != null && soundManager.getSoundById(smSongId) != null) {
					soundManager.destroySound(smSongId);					
				}
				smSongId = 'sm_'+trackIndex.toString();
				soundManager.play(smSongId, url);
			}
		}
		
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
		
		function downloadMissionSong() {
			var downloadUrl = document.getElementById('download_url_'+missionSongIndex.toString()).innerHTML;
			setCookie('download_song_'+missionSongId, 1, 365);
			downloadSong(downloadUrl);
		}
		
		function downloadCurrentSong() {
			downloadSong(currentSongData.downloadUrl);
		}
		
		function buyCurrentSong() {
			buySong(currentSongData.purchaseUrl);
		}
		
		function populatePlayer(title, trackIndex, url, picUrl, downloadUrl, streamUrl, purchaseUrl) {
			pauseCurrent();
			updatePlayerData(title, trackIndex, url, picUrl, downloadUrl, streamUrl, purchaseUrl);
			startPlayer(title, trackIndex, url, picUrl, downloadUrl, streamUrl);
		}
		
		function updatePlayerData(title, trackIndex, url, picUrl, downloadUrl, streamUrl, purchaseUrl) {
			currentSongData = {
				title: title,
				url: url,
				downloadUrl: downloadUrl,
				picUrl: picUrl, 
				streamUrl: streamUrl,
				purchaseUrl: purchaseUrl,
			};
			currentTrackIndex = trackIndex;
			smSongId = 'sm_'+trackIndex.toString();
			document.getElementById('top_title').innerHTML = title;
			document.getElementById('like_song_text').innerHTML = 'LIKE ' + title.toUpperCase() + ':';
			updateButtons(url);
			updatePic(picUrl);			
		}
		
		function pauseCurrent() {
			if (currentAudioElement != null) {
				if (mp3Support) {
					currentAudioElement.pause();	
				} else if (smSongId != null) {
					soundManager.pause(smSongId);
				}
			}
		}
		
		function startPlayer(title, trackIndex, url, picUrl, downloadUrl, streamUrl) {
			isPlaying = true;
			showPause();
			swapAudio(streamUrl, trackIndex);			
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
			// Log plays
			$.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=log_click&clickType='+currentSongData.title, function(data, status) {
			});
			// Play song
			var elem = document.getElementById('audio_'+currentTrackIndex.toString());
			if (mp3Support) {
				elem.play();				
			} else {
				if (parseInt(soundManager.position) > 0) {
					soundManager.resume(smSongId, currentSongData.streamUrl);
				} else {
					soundManager.play(smSongId, currentSongData.streamUrl);
				}	
			}
			isPlaying = true;
			showPause();
		}
		
		function doPause() {
			var elem = document.getElementById('audio_'+currentTrackIndex.toString());
			if (mp3Support) {
				elem.pause();				
			} else {
				soundManager.pause(smSongId);
			}
			isPlaying = false;
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
			$('#big_like_btn').html('<fb:like href="' + url + '" show_faces="false" width="350" action="like" font="arial" colorscheme="light" send="true" />');
			if (typeof FB !== 'undefined') {
			    FB.XFBML.parse(document.getElementById('big_like_btn'));
			}
		}	
		
		// var spinner;

		// function preload() {
		// 	var opts = {
		// 	  lines: 10, // The number of lines to draw
		// 	  length: 12, // The length of each line
		// 	  width: 7, // The line thickness
		// 	  radius: 16, // The radius of the inner circle
		// 	  color: '#00E1FA', // #rgb or #rrggbb
		// 	  speed: 1, // Rounds per second
		// 	  trail: 60, // Afterglow percentage
		// 	  shadow: false // Whether to render a shadow
		// 	};
		// 	var target = window.document.getElementById('spinner');
		// 	spinner = new Spinner(opts).spin(target);
		// 	target.appendChild(spinner.el);						
		// }
		// 
		// function stopPreload() {
		// 	spinner.stop();
		// 	window.document.getElementById('spinner').style.margin = "0px";			
		// 	window.document.getElementById('spinner').style.visibility = "hidden";
		// 	shiftElements();
		// }
		
		function shiftElements() {
			var offY = window.document.getElementById("flash").offsetHeight;
			window.document.getElementById("extra-content").style.top = offY.toString();
		}		
		
		function downloadSong(downloadUrl) {
			window.document.getElementById("downloader-frame").src=downloadUrl+"?consumer_key=738091d6d02582ddd19de7109b79e47b";
			
			setCookie('download_song', downloadUrl, 365);
			
			// Record download if user id exists
			// if ('<?php echo $user_id ?>' != null) {
			// 	$.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=download&download_url='+downloadUrl, function(data, status) {
			// 	      // parse
			// 	},'html');	
			// }
		}
		
		function downloadPlaylist(playlistName) {
			var downloadUrlString;
			if (playlistName == DOWNLOAD_PLAYLIST) {
				downloadUrlString = '<?php echo $playlists["lilly-and-dr-nu-mp3s"] ?>';
				setCookie('download_playlist', 1, 365);
				downloadedPlaylist = getCookie('download_playlist');	
			} else if (playlistName == REWARD_PLAYLIST) {
				downloadUrlString = '<?php echo $playlists["play-loud-instrumentals"] ?>';
			}
			var urls = downloadUrlString.split(",");
			createDownloadElement(urls, 0, urls.length);
			updateProgressBar();
			
			// Record download all if user id exists
			// if ('<?php echo $user_id ?>' != null) {
			// 	$.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=download_all', function(data, status) {
			// 	      // parse
			// 	},'html');	
			// }	
		}
		
		function setCookie(c_name, value, exdays)
		{
			var exdate = new Date();
			exdate.setDate(exdate.getDate() + exdays);
			var c_value = escape(value) + ((exdays==null) ? "" : "; expires=" + exdate.toUTCString());
			document.cookie = c_name + "=" + c_value;
		}
		
		function getCookie(c_name)
		{
			var i, x, y, ARRcookies = document.cookie.split(";");
			for (i=0; i<ARRcookies.length; i++)
			{
		  		x = ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		  		y = ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		  		x = x.replace(/^\s+|\s+$/g,"");
		  		if (x == c_name)
		    	{
		    		return unescape(y);
		    	}
		  	}
		}
		
		function createDownloadElement(urls, i, limit) {
			for (i = 0; i < limit; i++) {
				if (urls[i] != null && urls[i] != "," && urls[i] != "") {
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

				// $.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=download&download_url='+urls[i], function(data, status) {
				//       // parse
				// },'html');
			}	
		}
		
		function buySong(buyUrl) {
			window.open(buyUrl);
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
				// alert("show value: " + val1);				
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
				// FB.Event.subscribe('edge.create', function(response) {
				// 	if (response.indexOf(fbPageUrl) != -1) {
				//  		// window.location.reload();
				// 		liked = true;
				// 		updateProgressBar();
				// 	}
				// });	
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
		
		// REGISTER MISSION		
		// $.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=register_mission&mission_id=download_song&mission_rank=2&mission_tag=25756679', function(data, status) {
		//       // parse
		// },'html');
		// 
		// $.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=register_mission&mission_id=like&mission_rank=1', function(data, status) {
		//       // parse
		// },'html');
		// // 
		// $.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=register_mission&mission_id=download_song&mission_rank=3&mission_tag=24351743', function(data, status) {
		//       // parse
		// },'html');
		// 
		// $.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=register_mission&mission_id=add_app&mission_rank=4', function(data, status) {
		//       // parse
		// },'html');
		// 
		// $.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=unregister_mission&mission_id=download_playlist', function(data, status) {
		//       // parse
		// },'html');
		
		// CREATE MISSION
		// $.get('../redis/page_interaction.php?fbId=<?php echo $user_id ?>&pageId=<?php echo $pageId ?>&method=create_mission&mission_id=download_song', function(data, status) {
		//       // parse
		// },'html');
	
	</script>
	
<?php

	
		
	# FOR ADMIN PANEL

	/* $state = md5(uniqid(rand(), TRUE));
	$scope = 'email,publish_stream,read_stream,manage_pages';
	$home = getHome();
	$authorize_url = "https://www.facebook.com/dialog/oauth?client_id=$appId" .
	      	"&redirect_uri=$home&state=" . $state . "&scope=$scope";		
	      	echo("<script> top.location.href='" . $authorize_url . "'</script>"); */	

	# Use this for non-facebook canvas page (i.e. Facebook Connect)		
	# header('Location:' . $facebook->getLoginURL());	
		
	// $fp = fsockopen("simple-ocean-7178.herokuapp.com", 80, $errno, $errstr);
	// if (!$fp) {
	// 	echo "$errstr ($errno)<br />\n";
	// } else {
	// 	$out = "GET /fb_auth/ HTTP/1.1\r\n";
	// 	$out .= "Host: simple-ocean-7178.herokuapp.com\r\n";
	// 	$out .= "Connection: Close\r\n\r\n";
	// 	$result = fwrite($fp, $out);
	// 	$ret = "";
	// 	while ($line = fgets($fp)) {
	// 		$ret .= $line;
	// 	}
	// 	print_r($ret);
	// 	fclose($fp);
	// }				

?>
</body>
</html>