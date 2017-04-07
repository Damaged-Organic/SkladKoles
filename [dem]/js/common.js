var app = app || {};

app.common = (function(){

	function request(data){
		return $.ajax({
			url: data.AR_origin,
			type: "POST",
			data: data
		});
	}
	function confirmation(area, callback){
		area.addClass("confirm");

		area.find(".confirmDialog").on("click.confirm", ".yes, .no", function(event){
			event.preventDefault();
			event.stopPropagation();

			callback($(this).data("choice"));
			area.removeClass("confirm");
			$(this).parent(".confirmDialog").off("click.confirm");
		});
	}
	return{
		request: request,
		confirmation: confirmation
	}

}());
