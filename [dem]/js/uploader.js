var app = app || {};

app.uploaderExtensions = (function(){
	return{
		reader: function(file, callback){
			var fileReader = new FileReader();

			fileReader.onload = (function(file, callback){
				return function(event){
					callback(event.target.result, file.name);
				}
			})(file, callback);

			fileReader.readAsDataURL(file);
		},
		sender: function(url, data){
			return $.ajax({
				url: url,
				type: "POST",
				data: data,
				contentType: false,
				processData: false
			});
		},
		progress: function(callback){
			var orgXHR = $.ajaxSettings.xhr;

			$.ajaxSettings.xhr = function(){
				var xhr = orgXHR(), percents = 0;

				if(xhr.upload){
					xhr.upload.addEventListener("progress", function(event){
						if(event.lengthComputable){
							percents = (event.loaded / event.total) * 100;
							callback(percents);
						}
					}, false);
				}
				return xhr;
			}
		},
		mergeData: function(formData, fileList){
			$.each(fileList, function(index, val){
				formData.append("files["+index+"]", val);
			});
			return formData;
		}
	}
}());






