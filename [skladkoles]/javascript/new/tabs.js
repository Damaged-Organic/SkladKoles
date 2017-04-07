var app = app || {};

app.tabs = (function(){

    var tabs = {
        init: function(){
            $(".tabs").each(function(){
                var tab = $(this),
                    labels = $(this).find(".tabs-label"),
                    contents = $(this).find(".tabs-content"),
                    current = 0;

                labels.eq(current).addClass("active");
                contents.eq(current).addClass("active");

                tab.on("click", ".tabs-label:not(.disabled)", function(event){
                    current = $(this).index();

                    labels.removeClass("active").eq(current).addClass("active");
                    contents.removeClass("active").eq(current).addClass("active");
                });
            });
        }
    }
    return tabs;

}());