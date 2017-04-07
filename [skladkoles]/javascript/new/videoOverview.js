var app = app || {};

app.videoOverview = (function(){

    var el = $("#overview-holder"),
        videoHolder = $("#video-holder");

    return {
        init: init,
        _events: _events,
        openOverview: openOverview,
        closeOverview: closeOverview
    }

    function init(){
        this._events();
    }
    function _events(){
        el.on("click", "a", $.proxy(this.openOverview, this));
        videoHolder.on("click", $.proxy(this.closeOverview, this));
    }
    function openOverview(e){
        videoHolder.addClass("active");
        return false;
    }
    function closeOverview(e){
        var target = $(e.target);

        if(videoHolder.is(target) || target.hasClass("close")) videoHolder.removeClass("active");
        return false;
    }

})();