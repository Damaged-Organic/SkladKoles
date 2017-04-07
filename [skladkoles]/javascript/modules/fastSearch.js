var app = app || {};

app.fastSearch = (function(){

	var searchThreshold = 3,
		fastSearch = {},
		currentRequest = null;

	fastSearch = {
		el: $("#search-widget"),
		init: function(){
			this.bindEvents();

			this.fastSearch = this.el.find(".fastSearch");
			this.landMarkData = this.fastSearch.data("landmark");
		},
		bindEvents: function(){
			this.el.on("keyup", "input[type=search]", $.proxy(this.searchRequest, this));
			$(document).on("click", $.proxy(this.closeFastSearch, this))
		},
		searchRequest: function(e){
			if(currentRequest) currentRequest.abort();
			var searchValue = $(e.target).val();

			(searchValue.length >= searchThreshold) ? this.sendRequest(searchValue) : this.clearSearch();
		},
		closeFastSearch: function(e){
			if(!$(e.target).closest("#search-widget").length){
				this.clearSearch();
			}
		},
		sendRequest: function(searchValue){
			var self = this,
				data = $.extend({}, this.landMarkData, {'_request[search]': searchValue});

			this.fastSearch.addClass("loading").removeClass("active").empty();

			currentRequest = $.ajax({
				url: data.AR_origin,
				type: "POST",
				data: data
			})
			.done(function(response){
				response = JSON.parse(response);
				self.fastSearch.html(response.interlayer);
				setTimeout(function(){
					self.fastSearch.addClass("active");
				}, 10);
				self.fastSearch.removeClass("loading");
			});
		},
		clearSearch: function(){
			this.fastSearch.removeClass("active loading").empty();
		}
	}
	return fastSearch;

})();