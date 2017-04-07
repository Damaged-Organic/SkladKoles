var app = app || {};

app.gallery = (function(){

    return {
        el: $("#gallery"),
        popUp: $("#galleryPopUpWrapper"),
        init: function(){
            this.slides = this.el.find("li");
            this.totalSlides = this.slides.length;
            
            this._current = 0;
            this._bindEvents();
        },
        _bindEvents: function(){
            this.el.on("click", ".preview", $.proxy(this.handlePreview, this));
            this.el.on("click", ".mainPreivew", $.proxy(this.handleMainPreview, this));

            this.el.on("click", ".arrow", $.proxy(this.handleArrow, this));
            this.el.on("click", ".close", $.proxy(this.closePopUp, this));
            
            $(window).on("click", $.proxy(this.handleBeyondClose, this));
        },
        handlePreview: function(e){
            var self = this,
                target = $(e.target).closest(".preview");

            this.loadPhoto(target.data("path"), function(photo){
                self.switchPreview(photo);
                self.switchActive(target);
                self._current = target.closest("li").index();
            });
        },
        handleMainPreview: function(e){
            var self = this,
                target = $(e.target);

            this.loadPhoto(target.attr("src"), function(photo){
                self.switchMainPreview(photo);
                self.openPopUp();
            });
        },
        handleArrow: function(e){
            var self = this,
                slide = null;

            $(e.target).hasClass("left") ? this._current-- : this._current++;
            if(this._current < 0){
                this._current = this.totalSlides - 1;
            } else if(this._current > this.totalSlides - 1){
                this._current = 0;
            }
            slide = this.slides.eq(this._current).find(".preview");

            this.loadPhoto(slide.data("path"), function(photo){
                self.switchMainPreview(photo);
            });
            this.loadPhoto(slide.data("path"), function(photo){
                self.switchPreview(photo);
            });
            this.switchActive(slide);
        },
        handleBeyondClose: function(e){
            if(!$(e.target).closest(".galleryPopUp").length) this.closePopUp();
        },
        loadPhoto: function(source, callback){
            var self = this,
                img = new Image();
                
            img.onload = function(){ callback(this); }
            img.src = source;
        },
        switchPreview: function(photo){
            this.el.find(".mainPreivew").html(photo);
        },
        switchMainPreview: function(photo){
            this.el.find(".fullView").html(photo);
        },
        switchActive: function(el){
            el.closest("li").addClass("active").siblings().removeClass("active");
        },
        openPopUp: function(){
            this.popUp.addClass("active");
        },
        closePopUp: function(){
            this.popUp.removeClass("active");
        }
    }

})();