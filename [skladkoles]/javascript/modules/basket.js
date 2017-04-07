var app = app || {};

app.basket = (function(request){

	var basket = {
		init: function(){
			var self = this,
				basket = $("#basket"),
				closeButton = basket.find(".close"),
				widget = $("#basket-widget"),
				addButtons = $(".addToCart"),
				goodsArea = basket.find(".basket-goods-grid"),
				tabs = basket.find(".tabs"),
				tabLabels = tabs.find(".tabs-label"),
				tabContent = tabs.find(".tabs-content"),
				//order
				orderForm = $("#orderForm"),
				regionRadios = null,
				step = 0;

			addButtons.on("click", function(event){
				event.preventDefault();
				self.addItem($(this), goodsArea, widget);
			});
			goodsArea.on("click", ".plus, .minus, .remove", function(){
				self.actions($(this), goodsArea, widget);
			});
			basket.on("click", ".nextStep, .tabs-label:not(.disabled)", function(event){
				event.preventDefault();
				
				step = $(this).data("step");

				if($(this).hasClass("validateStep")){

					if(self.validate(orderForm)){
						self.autoSwitchTab(tabLabels, tabContent, step);
					}
				} else{
					self.autoSwitchTab(tabLabels, tabContent, step);
				}
			});
			basket.on("click", "#pickup, #shipping", function(event){
				basket.find(".delivery-content").removeClass("active").filter("." + this.id).addClass("active");
			});
			basket.on("click", ".deliveryType", function(){			
				self.delivery($(this));
			});
			orderForm.submit(function(event){
				event.preventDefault();

				regionRadios = $(this).find(".region > input[type=radio]");
				isValid = self.validate($(this));
				step = $(this).data("step");

				if(isValid && regionRadios.length > 0 && regionRadios.is(":checked")){
					self.order($(this), tabContent.eq(step), closeButton);
					self.autoSwitchTab(tabLabels, tabContent, step);
				} else if(isValid && regionRadios.length === 0){
					self.order($(this), tabContent.eq(step), closeButton);
					self.autoSwitchTab(tabLabels, tabContent, step);
				} else{
					$(this).find(".region-button").addClass("error");
				}
			});
		},
		validate: function(form){
			var state = false;

			form.validate({
				errorPlacement: function(error, element){ return true; }
			});
			if(form.valid()){
				state = true;
			}
			return state;
		},
		autoSwitchTab: function(labels, contents, step){
			labels.removeClass("active").eq(step).removeClass("disabled").addClass("active");
			contents.removeClass("active").eq(step).addClass("active");
		},
		addItem: function(button, area, widget){
			var landMarkData = button.data("landmark");

			button.addClass("adding");

			request.sender(landMarkData).done(function(answer){
				answer = $.parseJSON(answer);

				button.addClass("success");

				area.html(answer.basketGoods);
				widget.find(".info").html(answer.widgetInfo);

			}).always(function(){
				setTimeout(function(){
					button.removeClass("adding success");
				}, 1500);
			});
		},
		actions: function(button, area, widget){
			var landMarkData = area.data("landmark");

			landMarkData._request = {};

			landMarkData._request.action = button.data("action");
			landMarkData._request.type = button.data("type");
			landMarkData._request.id = button.data("id");

			request.sender(landMarkData).done(function(answer){
				answer = $.parseJSON(answer);

				area.html(answer.basketGoods);
				widget.find(".info").html(answer.widgetInfo);
			});
		},
		delivery: function(radio){
			var self = this,
				landMarkData = {};

			landMarkData = radio.closest(".tabs-content.delivery").data("landmark");
			landMarkData._request = {
				type: radio.data("type")
			}
			request.sender(landMarkData).done(function(answer){
				answer = $.parseJSON(answer);
				radio.closest(".tabs-content.delivery").find(".delivery-content").html(answer.interlayer);
				self.switchRegion(radio.closest(".tabs-content.delivery"));
			});
		},
		switchRegion: function(deliveryContent){
			var button = deliveryContent.find(".region-button"),
			regionContainer = deliveryContent.find(".region"),
			regionRadios = regionContainer.find("input[type=radio]");

			button.on("click", function(event){
				event.preventDefault();

				$(this).toggleClass("active");
				regionContainer.toggleClass("active");
			});
			regionRadios.on("click", function(){
				button.removeClass("error").text(this.getAttribute("data-region"));
				regionContainer.removeClass("active");
			});
		},
		order: function(form, content, close){
			var formData = {}, landMarkData = {};

			formData = form.serializeArray();
			landMarkData = form.data("landmark");

			landMarkData = request.reconstruct(landMarkData, formData);

			request.sender(landMarkData).done(function(answer){
				answer = $.parseJSON(answer);

				content.html(answer["interlayer"]);
				close.replaceWith('<a href='+ answer.linkUrl +' class="close">'+ answer.linkText +'</a>');
			});
		}
	}
	return basket;

}(app.request));