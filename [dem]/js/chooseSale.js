var app = app || {};

app.chooseSale = (function($){

	return {
		checkbox: $("#sale"),
		select: $("#saleList"),
		init: function(){
			this._events();
			this.select.cSelect();
		},
		_events: function(){
			this.checkbox.on("change", $.proxy(this.handleChoose, this));
		},
		handleChoose: function(e){
			this.select.closest(".cSelectWrapper").toggleClass("open");
		}
	}

})(jQuery);