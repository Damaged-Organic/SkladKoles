var app = app || {};

app.liftUp = (function(request){

	return{
		init: function(el){
			this.el = el;
			this.liftContainer = el.find(".lift-data"),
			this.landMarkData = this.liftContainer.data("landmark");

			this._bindEvents();
		},
		_bindEvents: function(){
			this.el.on("click", ".lift-button", $.proxy(this.handleLift, this));
		},
		handleLift: function(e){
			e.preventDefault();
			var button = $(e.target).closest(".lift-button");
			this.setData();
			this.request(button);
		},
		setData: function(){
			this.landMarkData._request = {};
			this.landMarkData._request["count"] = this.liftContainer.find(".lift").size();
		},
		request: function(button){
			var self = this;

			button.addClass("loading");

			request.sender(this.landMarkData).done(function(response){
				response = JSON.parse(response);

				self.liftContainer.append(response.interlayer);
				button.removeClass("loading");

				if(!response.isLastItem) return;
				button.css({"display": "none"});
			});
		},
	}

}(app.request));
