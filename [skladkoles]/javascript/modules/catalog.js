var app = app || {};

app.catalog = (function(request){

	function init(){
		var catalog = $("#chosen-catalog"),
			viewButtons = catalog.find("a[class$=-view]"),
			catalogGrid = catalog.find(".grid"),
			navigation = catalog.find(".navigation"),
			currentPage = catalog.find("#catalogPage"),
			data = {},
			landMarkData = {};

		viewButtons.click(function(event){
			event.preventDefault();

			if($(this).hasClass("filter-view")){
				catalog.toggleClass("filter-open");
			} else{
				catalog.removeClass("list-view cell-view").addClass(this.className);
			}
		});
		catalog.on("click", ".count", function(event){

			data["count"] = $(this).data("count");
			landMarkData = $(this).parent("ul").data("landmark");
			landMarkData["_request"] = {};

			landMarkData["_request"] = data;

			request.sender(landMarkData).done(function(answer){
				answer = $.parseJSON(answer);

				catalogGrid.html(answer.catalogGrid);
				navigation.html(answer.navigation);
				currentPage.text(1);
			});
		});
		navigation.on("click", "a", function(event){
			event.preventDefault();

			current = $(this).data("current");
			currentPage.html(current);

			landMarkData = $(this).closest(".navigation").data("landmark");

			landMarkData["_request"] = current;

			request.sender(landMarkData).done(function(answer){
				answer = $.parseJSON(answer);

				catalogGrid.html(answer.catalogGrid);
				navigation.html(answer.navigation);
			});
		});
	}
	return{
		init: init
	}

}(app.request));
