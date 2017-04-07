var app = app || {};

app.accessories = (function(common){

	function init(area){

		var form = area.find("form"),
			message = $("#message"),
			requestType,
			actionButton;

		area.on("click", ".add, .delete", function(event){
			event.preventDefault();
			
			actionButton = $(this);
			requestType = actionButton.data("action");

			if(requestType === "deleteRow"){
				common.confirmation(area, function(status){
					if(status){
						actions(actionButton, requestType);
					}
				});
			} else{
				actions(actionButton, requestType);
			}
			
		});
		form.submit(function(event){
			event.preventDefault();

			sender($(this), message);
		});
	}
	function actions(el, type){

		common.request(el.data("landmark")).done(function(answer){
			if(type === "addRow"){
				answer = $.parseJSON(answer);
				el.parent().find("table").append(answer.interlayer);
			} else{
				el.closest("tr").remove();
			}
		});
	}
	function sender(form, message){

		var landMarkData = {};

		$.each(form.serializeArray(), function(key, obj){
			landMarkData[obj.name] = obj.value;
		});
		common.request(landMarkData).done(function(answer){
			message.html(answer);
		})
		.fail(function(error){
			message.html(error.responseText);
		})
		.always(function(){
			setTimeout(function(){
				message.empty();
			}, 4000);
		});
	}
	return{
		init: init
	}

}(app.common));

