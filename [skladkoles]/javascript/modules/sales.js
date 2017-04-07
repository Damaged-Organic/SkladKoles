var app = app || {};

app.sales = (function(){

	return {
		init: function(){
			this.sale = $("#sale");
			this.link = this.sale.find(".saleLink");

			this._bindEvents();
		},
		_bindEvents: function(){
			this.sale.on("click", ".point", $.proxy(this.handleClick, this));
		},
		handleClick: function(e){
			this.loadSlide($(e.target).find("img"));
		},
		loadSlide: function(target){
			var slide = new Image(),
				link = target.data("link");

			slide.onload = this.show(slide, link);
			slide.src = target.attr("src");
		},
		show: function(slide, link){
			var wrapper = $("<figure>").append(slide).css("opacity", 0);

			this.link.html(wrapper).attr("href", link);
			wrapper.stop(true, true).animate({"opacity": 1}, 500, "easeInOutQuad");
		}
	}

})();