var app = app || {};

app.goods = (function(){

	function init(area){
		var	form = area.find("form"),
			message = $("#message"),
			photoList = area.hasClass("newsZone") ? area.find(".picture") : $("#photoList"),
			hiddenPictureName = $("#hiddenPictureName"),
			formData = {},
			self,
			fileList = {},
			requestType,
			progressBar = area.find(".progressBar");

		area.on("click", ".add", function(event){
			event.preventDefault();

			requestType = $(this).data("action");
			addRow($(this), requestType);
		});
		area.on("click", ".delete", function(event){
			event.preventDefault();

			self = $(this);
			requestType = $(this).data("action");

			app.common.confirmation(area, function(response){
				if(response){
					deleteItem(self, requestType, fileList);
				}
			});
		});
		form.on("change", "input[type=file]", function(event){
			var fileList = event.target.files;

			if(area.hasClass("newsZone")){
				displayPreview(fileList, photoList);
			} else{
				displayPreviews(fileList, photoList);
			}
		});
		form.submit(function(event){
			event.preventDefault();

			formData = new FormData(this);

			if(!$.isEmptyObject(fileList)){
				progressBar.addClass("active");
				app.uploaderExtensions.progress(function(percents){
					progressBar.css({"width": percents + "%"});
				});
			}
			app.uploaderExtensions.sender($(this).attr("action"), formData).done(function(response){
				response = $.parseJSON(response);

				message.html(response.message);
				hiddenPictureName.html(response.hiddenPictureName);
				photoList.html(response.photos);
				if(response.reload) window.location = response.link;		
			})
			.fail(function(error){
				message.html(error.responseText);
			}).
			always(function(){
				progressBar.removeClass("active").width(0);

				setTimeout(function(){
					message.empty();
				}, 4000);
			});
		});
	}
	function addRow(el, type){
		app.common.request(el.data("landmark")).done(function(answer){
			answer = $.parseJSON(answer);
			el.parent().find("table").append(answer.interlayer);
		});
	}
	function deleteItem(el, type, fileList){

		app.common.request(el.data("landmark")).done(function(answer){
			if(type === "deleteRow"){
				el.closest("tr").remove();
			} else if(type === "deletePicture"){
				el.closest(".picture").find("img").attr("src", answer);
				fileList = {};
			} else if(type === "delete"){
				window.location = answer;
			}
		});
	}
	function displayPreviews(files, container){
		if(!files.length) return;

		container.empty();

		$.each(files, function(index, file){
			app.uploaderExtensions.reader(file, function(url, name){
				container.append('\
					<li>\
						<input type="checkbox" name="_request[deletePhotos]['+index+']" value="yes" id="delete_'+index+'">\
						<label for="delete_'+index+'"><img src="'+ url +'" alt="'+name+'"></label>\
					</li>\
				');
			});
		});
	}
	function displayPreview(file, container){
		app.uploaderExtensions.reader(file[0], function(url, name){
			container.find("figure").html('<img src="'+ url +'" alt="'+ name +'">');
		});
	}

	return{
		init: init
	}

}());

