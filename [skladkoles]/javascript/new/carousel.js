"use strict";

(function($, window, document){

    function getVendor(property){
        var style = document.createElement("div").style,
            vendors = ["ms", "O", "Moz", "Webkit"],
            i;

        if(style[property] === "") return property;

        property = property.charAt(0).toUpperCase() + property.slice(1);

        for(i = 0; i < vendors.length; i++){
            if(style[vendors[i]] + property === "") return vendors[i] + property;
        }
    }

    var pluginName = "carousel",
        defaults = {
            time: 500,
            easing: "ease"
        },

        transform = getVendor("transform"),
        transition = getVendor("transition");

    function Plugin(el, options){
        this.el = $(el);
        this.options = $.extend({}, defaults, options);
        
        this.initialize();   
    }
    Plugin.prototype = {
        initialize: initialize,
        _events: _events,
        getPerSlide: getPerSlide,
        handleArrow: handleArrow,
        handleResize: handleResize,
        defineCurrent: defineCurrent,
        checkBoundaries: checkBoundaries,
        disableDirectionArrows: disableDirectionArrows,
        getSlideDistance: getSlideDistance,
        slide: slide
    }

    function initialize(){
        this.elWidth = this.el.outerWidth();
        this.carouselUl = this.el.find(".carousel-ul");
        this.items = this.el.find(".carousel-item");
        this.itemWidth = this.items.outerWidth();
        this.itemCount = this.items.length;

        this.arrowLeft = this.el.find(".arrow-left");
        this.arrowRight = this.el.find(".arrow-right");

        this.totalWidth = this.itemCount * this.itemWidth;

        this.perSlide = this.getPerSlide();

        this.current = 0;
        this.isAnimate = false;
        this.isFirst = true;
        this.isLast = false;

        this.carouselUl.css({ width: this.totalWidth });

        if(this.itemCount <= this.perSlide) this.el.addClass("disable-ui");
        this.disableDirectionArrows();

        this._events();
    }
    function _events(){
        this.el.on("click", ".arrow", $.proxy(this.handleArrow, this));
        $(window).on("resize", $.proxy(this.handleResize, this));
    }
    function getPerSlide(){
        return parseFloat((this.elWidth / this.itemWidth).toFixed(2));
    }
    function handleArrow(e){
        if(this.isAnimate) return;
        this.isAnimate = true;
        this.isLast = false;
        this.isFirst = false;

        var self = this,
            target = $(e.currentTarget),
            slideDistance = 0;

        this.defineCurrent(target);
        this.checkBoundaries();

        this.disableDirectionArrows();

        slideDistance = this.getSlideDistance();
        
        this.slide(slideDistance);

        window.setTimeout(function(){
            self.isAnimate = false;
        }, this.options.time);

        return false;
    }
    function handleResize(e){
        this.elWidth = this.el.outerWidth();
        this.perSlide = this.getPerSlide();

        return false;
    }
    function defineCurrent(target){
        target.hasClass("arrow-left") ? this.current-- : this.current++;
    }
    function checkBoundaries(){
        if(this.current >= this.itemCount - this.perSlide){
            this.current = this.itemCount - this.perSlide;
            this.isLast = true;
        } else if(this.current <= 0){
            this.current = 0;
            this.isFirst = true;
        }
    }
    function disableDirectionArrows(){
        this.isLast ? this.arrowRight.addClass("disabled") : this.arrowRight.removeClass("disabled");
        this.isFirst ? this.arrowLeft.addClass("disabled") : this.arrowLeft.removeClass("disabled");
    }
    function getSlideDistance(){
        return (this.itemWidth * this.current) * -1;
    }
    function slide(slideDistance){
        this.carouselUl.css({
            transition: "all "+ this.options.time +"ms "+ this.options.easing,
            transform: "translateX("+ slideDistance +"px)"
        });  
    }

    $.fn[pluginName] = function(options){

        return this.each(function(){
            if(!$.data(this, "plugin_" + pluginName)){
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    }

})(jQuery, window, document);