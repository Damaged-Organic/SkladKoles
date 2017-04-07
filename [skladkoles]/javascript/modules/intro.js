var app = app || {};

app.intro = (function(){

	var deviceDetection = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i,
		intro = {};

	intro = {
		init: function(){
			var video = $("#intro-video"),
				play = $("#video-play"),
				picture = $("#intro-picture");

			play.on("click", function(event){
				if(!video[0].paused){
					video[0].pause();
					$(this).removeClass("fa-pause").addClass("fa-play");
				} else{
					video[0].play();
					$(this).removeClass("fa-play").addClass("fa-pause");
				}
			});
			this.switcher(video, play, picture);
		},
		switcher: function(video, play, picture){

			if(deviceDetection.test(navigator.userAgent)){
				video.css('display', 'none');
				play.css('display', 'none');
				picture.css('display', 'block');
			} else{
				video.css('display', 'block');
				play.css('display', 'block');
				picture.css('display', 'none');
			}
		}
	}
	return intro;

}());