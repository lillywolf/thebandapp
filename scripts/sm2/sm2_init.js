alert("init sm!");
$.getScript("/soundmanager2.js", function(){
	
	alert("Script loaded and executed.");
	soundManager.url = '/swf/';
	soundManager.flashVersion = 9; // optional: shiny features (default = 8)
	soundManager.useFlashBlock = false; // optionally, enable when you're ready to dive in
	/*
 	* read up on HTML5 audio support, if you're feeling adventurous.
 	* iPad/iPhone and devices without flash installed will always attempt to use it.
	*/
	soundManager.onready(function() {
		alert("sm ready");
  		// Ready to use; soundManager.createSound() etc. can now be called.
	});
});
