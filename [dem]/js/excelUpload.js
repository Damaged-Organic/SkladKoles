var app = app || {};

app.excelUpload = (function(uploaderExtensions){

	function init(area){
		var form = area.find("form"),
			progressBar = form.find(".progressBar"),
			progressLane = progressBar.find(".progress"),
			previewZone = form.find(".previewZone"),
			fileList = {};
			formData = {};

		var submitButton = null;

		form.on("click", "button[type=submit]", function(event) {
			submitButton = this;
		});
		form.on("change", "input[type=file]", function(event){
			fileList = event.target.files;
			displayPreview(fileList[0], previewZone);
		});
		form.on("submit", function(event){
			event.preventDefault();

			formData = new FormData(this);

			formData.append(submitButton.name, submitButton.value);

			if(!$.isEmptyObject(fileList)){
				if(submitButton.value == 'excel_upload_clean') {
					app.common.confirmation(area, function(response){
						if(response){
		                    sendData();
						}
					});
				} else {
					sendData();
				}
			} else{
				previewZone.html("<p>"+ $(this).data("error-msg") +"</p>");
			}
		});

		function sendData() {
			progressBar.addClass("active");
			uploaderExtensions.progress(function(percents){
				progressLane.css({"width": percents + "%"});
			});

			uploaderExtensions.sender($(this).attr("action"), formData).done(function(answer){
				previewZone.html(answer);
				progressBar.removeClass("active");
				progressLane.width(0);
				form[0].reset();
				fileList = {};

				setTimeout(function(){
					previewZone.empty();
				}, 4000);
			});
		}
	}
	function displayPreview(file, container){
		container.html("\
			<span class='icon fa fa-file-excel-o'></span>\
			<span>"+ file.name +"</span>\
		");
	}
	return{
		init: init
	}

}(app.uploaderExtensions));
