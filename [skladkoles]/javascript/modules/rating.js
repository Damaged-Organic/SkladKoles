var app = app || {};

app.rating = (function(){

	return {
		init: function(){
			this.$el = $(".rating");
			this.landMardData = this.$el.data("landmark");
			this.voteCount = $("#voteCount");
			this.totalRating = $("#totalRating");

			this._bindEvents();
		},
		_bindEvents: function(){
			this.$el.on("click", ".star", $.proxy(this.handleStar, this));
		},
		handleStar: function(e){
			this.applyRating($(e.target));
		},
		applyRating: function(star){
			var self = this,
				data = $.extend({}, this.landMardData, {'_request[score]': star.data("vote")});
			
			this.$el.addClass("ajaxLoading");

			$.ajax({
				url: data.AR_origin,
				type: "POST",
				data: data
			}).done(function(response){
				response = JSON.parse(response);
				
				self.voteCount.html(response.voteCount);
				self.totalRating.html(response.totalRating);
				self.$el.html(response.wheels);
				self.$el.removeClass("ajaxLoading");
			})
			.fail(function(){
				self.$el.removeClass("ajaxLoading");
			});
		}
	}

})();