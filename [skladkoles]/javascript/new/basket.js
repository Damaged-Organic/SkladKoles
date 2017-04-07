var app = app || {};

app.Basket = (function(){

    var btn = $(".addToCart"),
        widget = $("#basket-widget"),
        basket = $("#basket-holder"),
        totalPrice = $("#total-price");

    function Basket(){
        this.initialize.apply(this, arguments);
    }
    Basket.prototype = {
        initialize: initialize,
        _events: _events,
        addItem: addItem,
        handleAction: handleAction
    }

    function initialize(){
        this._events();
    }
    function _events(){
        btn.on("click", $.proxy(this.addItem, this));
        basket.on("click", ".action-btn", $.proxy(this.handleAction, this));
    }
    function addItem(e){
        var self = this,
            target = $(e.target).closest(".addToCart"),
            landMarkData = target.data("landmark");

        target.addClass("adding");

        $.ajax({
            url: landMarkData.AR_origin,
            type: "POST",
            data: landMarkData
        })
        .done(function(response){
            widget.html(response);
            target.removeClass("adding");
        });

        return false;
    }
    function handleAction(e){
        var self = this,
            target = $(e.currentTarget),
            actionType = target.data("action-type"),
            item = target.closest(".item"),
            landMarkData = item.data("landmark");

        landMarkData._request.action = actionType;

        $.ajax({
            url: landMarkData.AR_origin,
            type: "POST",
            data: landMarkData
        })
        .done(function(response){
            response = JSON.parse(response);

            if(response.hasOwnProperty("status") && response.status === "removed"){
                if(response.hasOwnProperty("is_last") && response.is_last){
                    item.closest(".content").append(response.message).find(".item-list-holder, .order-holder").remove();
                    totalPrice.remove();
                }
                item.remove();
            } else{
                item.find(".quantity").html(response.count);
                item.find(".price-holder").html("<span>"+ response.price +"</span>");
            }
            totalPrice.html(response.total_price);
        });

        return false;
    }

    return Basket;

})();
