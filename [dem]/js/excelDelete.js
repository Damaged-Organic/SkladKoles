var app = app || {};

app.excelDelete = (function(uploaderExtensions){

    function init(area){
		var form = area.find("form"),
			formData = {};

		form.on("submit", function(event){
			event.preventDefault();

            formData = new FormData(this);

            app.common.confirmation(area, function(response){
				if(response){
                    uploaderExtensions.sender($(this).attr("action"), formData).done(function(){
        				form[0].reset();
        			});
				}
			});

		});
	}
	return{
		init: init
	}

}(app.uploaderExtensions));
