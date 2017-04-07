var app = app || {};

app.lightbox = (function(){

	return {
		init: function(){
			this.popUp = $("#lightBoxPopUp");
			this.lightLink = $(".lightbox");

			this._bindEvents();
		},
		_bindEvents: function(){
			this.lightLink.on("click", $.proxy(this.handlePhoto, this));
			this.popUp.on("click", ".close", $.proxy(this.handleClose, this));
		},
		handlePhoto: function(e){
			e.preventDefault();
			this.loadPhoto($(e.target).attr("href"));
		},
		handleClose: function(e){
			this.popUp.removeClass("active");
		},
		loadPhoto: function(source){
			var photo = new Image();
			photo.onload = this.show(photo);
			photo.src = source;
		},
		show: function(photo){
			$(".view-picture", this.popUp).html(photo).andSelf().addClass("active");
		}
	}

}());