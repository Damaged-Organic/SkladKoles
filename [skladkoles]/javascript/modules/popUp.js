var app = app || {};

app.popUp = (function(){

	var popUp = {
		init: function(){
			var self = this,
				button = $(".popUpButton"),
				popUp = $(".popUp"),
				selectorName = "";

			button.on("click", function(event){
				event.preventDefault();
				selectorName = $(this).attr("data-popup");
				self.open($(selectorName));
			});
			popUp.on("click", ".close", function(){
				self.close($(this).parent(".popUp"));
			});
		},
		open: function(popUp){
			$("body").css({"overflow-y": "hidden"});
			popUp.addClass("active");
		},
		close: function(popUp){
			popUp.removeClass("active");
			$("body").css({"overflow-y": "auto"});
		}
	}
	return popUp;

}());