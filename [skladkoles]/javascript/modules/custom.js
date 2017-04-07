var app = app || {};

app.custom = (function(){

	function select(){
		var selects = $(".select");

		selects.each(function(){
			
			var select = $(this), link = $(this).find("a");

			select.on("click", function(event){
				event.preventDefault();
				(!$(this).hasClass("active")) ? $(this).addClass("active") : $(this).removeClass("active");
			});
			select.on("click", "li", function(event){
				chosenVal = $(this).data("count");
				link.html(chosenVal);
			});
		});
	}
	return{
		select: select
	}
}());