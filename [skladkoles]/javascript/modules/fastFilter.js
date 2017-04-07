var app = app || {};

app.fastFilter = (function(){

	return {
		init: function(){
			var selects = $(".cSelect"),
				filter = $("#intro-filter");

			this.landMarkData = filter.data("landmark");
			this.nextSelect = null;

			this.customizeSelects(selects, false);
		},
		customizeSelects: function(selects, update){
			var self = this;

			$(selects).сSelect({
				update: update,
				afterChoose: function(wrap, select, option){
					self.handleSelected(wrap, select, option);
				}
			});
		},
		handleSelected: function(wrap, select, option){
			if(!select.hasClass("follows")) return;
			this.nextSelect = wrap.next(".cSelectWrapper").find("select");
			this.resetSelects(wrap)
			this.ensureData(option.val(), select.data("next"));
		},
		resetSelects: function(wrap){
			var select;

			wrap.nextAll(".cSelectWrapper").each(function(){
				select = $(this).find("select");

				$(this).find(".cSelectPlaceholder").html(select.data("placeholder"));
				$(this).find(".cSelectList").html("");
				select.html("");
			});
		},
		ensureData: function(value, next){
			if(!next) return;
			var data = $.extend({}, this.landMarkData, {'_request[value]': value, '_request[next]': next});
			this.sendRequest(data);
		},
		sendRequest: function(data){
			var self = this;

			$.ajax({
				url: data.AR_origin,
				type: "POST",
				data: data
			}).done(function(response){
				response = JSON.parse(response);
				self.nextSelect.html(response.interlayer);
				self.reCustomizeSelect();
			});
		},
		reCustomizeSelect: function(){
			this.customizeSelects(this.nextSelect, true);
		}
	}

})();
