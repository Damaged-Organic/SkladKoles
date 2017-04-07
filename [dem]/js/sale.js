var app = app || {};

app.sale = (function(){

	function init(area){
		var form = area.find("form"),
			formButton = form.find("button[type=submit]"),
			message = $("#message"),
			progressBar = area.find(".progressBar"),
			progressLane = progressBar.find(".progress"),
			previewZone = form.find(".viewZone"),
			self, requestType,
			formData = {};

		area.on("click", ".delete", function(event){
			event.preventDefault();

			self = $(this);
			requestType = self.data("action");

			app.common.confirmation(area, function(response){

				if(response && requestType === "deleteSlide"){
					app.common.request(self.data("landmark")).done(function(answer){
						self.closest(".item").remove();
					});
				}
			});
		});
		
		form.on("change", "input[type=file]", function(event){

			progressBar.addClass("active");

			formData = app.uploaderExtensions.mergeData(new FormData(form[0]), event.target.files);
			formData.append("_request[type]", $(this).data("action"));

			app.uploaderExtensions.progress(function(percents){
				progressLane.css({"width": percents + "%"});
			});
			app.uploaderExtensions.sender(form.attr("action"), formData).done(function(answer){
				answer = $.parseJSON(answer);

				previewZone.prepend(answer.interlayer);
				editors = area.find(".editorWrapper");
				app.editor.init(editors);

				message.html(answer.message);
			})
			.fail(function(error){
				message.html(error.responseText);
			})
			.always(function(){
				progressBar.removeClass("active");
				form[0].reset();

				setTimeout(function(){
					message.empty();
				}, 4000);
			});
		});
		form.submit(function(event){
			event.preventDefault();

			var url = $(this).attr("action"),
				formData = new FormData(form[0]);

			formData.append("AR_method", formButton.data("action"));

			$.ajax({
				url: url,
				type: "POST",
				processData: false,
				contentType: false,
				data: formData
			})
			.done(function(answer){
				answer = JSON.parse(answer);
				message.html(answer.message);
			})
			.fail(function(error){
				message.html(error.responseText);
			})
			.always(function(){
				setTimeout(function(){
					message.empty();
				}, 4000);
			});
			
		});

		$("body").on("focus", ".saleDate", function(){
			$.datepicker.regional["ru"] = {
				closeText: "закрыть",
				prevText: "назад",
				nextText: "вперед",
				currentText: "сейчас",
				monthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
				monthNamesShort: ["Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"],
				dayNames: ["Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"],
				dayNamesShort: ["Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
				dayNamesMin: ["Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
				dateFormat: 'dd-mm-yy',
				firstDay: 0,
				isRTL: false
			}
			$.datepicker.setDefaults($.datepicker.regional["ru"]);
			$(this).datepicker({ buttonImageOnly: false });
		});
	}

	return{
		init: init
	}

}());

