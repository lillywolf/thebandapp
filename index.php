<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<script type="text/javascript" src="site/swfobject.js"></script>
	<script type="text/javascript" src="site/FBJSBridge.js"></script>
	<script type="text/javascript" src="scripts/spin.js"></script>	
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
		#like {
			position: absolute;
		}
	</style>
</head>	
<body>
		
	<div id="page_heading_div" class="hidden"></div>
	<div id="spinner"></div>
	<span id="like"></span>
	<span id="big_like"></span>
    <div id="flashContent"></div>	
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
	?>
	
    <div id="fb-root"></div>

	<script type="text/javascript">
	
		FB_PAGE_URL = <?php echo $fbPageUrl; ?>
	
		// function addLikeButtonOverlay() {
		// 	
		// }
		
		window.onload = function() {
			alert("do shit");
			var spinner;
			preload();			
		}
		
		function preLoad() {
			alert("preload");
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
			alert("found target: " + target);
			spinner = new Spinner(opts).spin(target);
			target.appendChild(spinner.el);						
		}
		
		function stopPreLoad() {
			spinner.stop();
			window.document.getElementById('spinner').style.visibility = "hidden";
		}
		
		function songChanged(songUrl, likeBtnY) {
			updateBigFacebookLikeButton(songUrl);			
			updateLittleFacebookLikeButton(songUrl, likeBtnY);			
		}
		
		function updateLittleFacebookLikeButton(url, likeBtnY) {
			var yOffset = getOffset(window.document.getElementById("music-player"));
			$('#like').html('<fb:like href="' + url + '" layout="button_count" show_faces="false" action="like" font="arial" colorscheme="light" send="true" />');
			if (typeof FB !== 'undefined') {
			    FB.XFBML.parse(document.getElementById('like'));
			}
			window.document.getElementById("like").style.top = window.document.getElementById("big_like").offsetHeight + parseInt(likeBtnY) + 14;
			window.document.getElementById("like").style.left = 321;
		}
		
		function updateBigFacebookLikeButton(url) {
			$('#big_like').html('<fb:like href="' + url + '" layout="standard" width="507" show_faces="false" action="like" colorscheme="light" send="true" />');
			if (typeof FB !== 'undefined') {
			    FB.XFBML.parse(document.getElementById('big_like'));
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
		echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="514" height="440">
		    <param name="movie" value="site/Main.swf">
			<param name="allowFullScreen" value="true">
			<param name="allowScriptAccess" value="always">
			<param name="scale" value="noscale">
			<param name="wmode" value="transparent">
			<param name="flashvars" value="downloads_enabled=' . $downloads_enabled . '&liked=' . $liked . '">					
            <!--[if !IE]>-->
            <object type="application/x-shockwave-flash" data="site/Main.swf" id="music-player" width="514" height="440">
                <param name="quality" value="high" />
                <param name="bgcolor" value="#ffffff" />
                <param name="allowScriptAccess" value="always" />
                <param name="allowFullScreen" value="true" />	
				<param name="wmode" value="transparent" />
				<param name="flashvars" value="downloads_enabled=' . $downloads_enabled . '&liked=' . $liked . '">					
		    <!--embed src="site/Main.swf" width="514" height="440">
		    </embed-->
            <!--[if !IE]>-->
            </object>				
		</object>';
	}
	
	if($user_id) {
		try {
			$user_profile = $facebook->api('/me', 'GET');	
			$signed_request = $facebook->getSignedRequest();
			if ($signed_request['page']['liked']) { 
				printSwf("true", "true");
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