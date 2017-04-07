var app = app || {};

app.formProtocol = (function(request){

	function starter(form){
		var landMarkData, formData, button,
			notify = $("#notifyPopUp"),
			msgCont = notify.find(".info");

		form.submit(function(event){
			event.preventDefault();

			$(this).validate({
				errorPlacement: function(error, element){
					return true;
				}
			});
			if($(this).valid()){

				landMarkData = $(this).data("landmark");
				formData = $(this).serializeArray();
				button = $(this).find("button[type=submit]");

				landMarkData = request.reconstruct(landMarkData, formData);

				button.addClass("loading");
				request.sender(landMarkData).done(function(data){
					msgCont.html(data);
					notify.addClass("active success");
				})
				.fail(function(jqXHR){
					msgCont.html(jqXHR.responseText);
					notify.addClass("active error");
				})
				.always(function(){
					button.removeClass("loading");
					form[0].reset();
				});
			}
		});
		notify.on("click", ".close", function(event){
			notify.removeClass("active success error");
		});
	}
	return{
		init: starter
	}
}(app.request));

app.filters = (function(request, popUps, historyFilter){

	function init(){
		//common variables
		var catalog = {
				grid: $(".grid"),
				navigation: $(".navigation"),
				loading: $(".loading"),
				tabs: $(".tabs"),
				autoResult: $(".auto-filter-result"),
				currentPage: $("#catalogPage")
			},
			//base filter form
			baseForm = $("#baseFilter"),
			//specification and auto
			autoForm = $("#autoFilter"),
			specificationArea = $(".specifications-filter"),
			linkToFilter = $(".filter-button"),
			autoPopUp = $("#autoPopUp"),
			//brands variables
			brandsPopUp = $("#brandsPopUp"),
			//price range variables
			priceRange = $("#price-range"),
			priceInput = $("#price-input"),
			minLabel = $("#min"),
			maxLabel = $("#max"),
			priceMin = parseInt(priceRange.data("min")),
			priceMax = parseInt(priceRange.data("max")),
			currentPriceMin = parseInt(priceRange.data("min-current")),
			currentPriceMax = parseInt(priceRange.data("max-current"));

		priceRange.slider({
			range: true,
			step: 1,
			min: priceMin,
			max: priceMax,
			values : [currentPriceMin, currentPriceMax],
			create: function(){
				minLabel.html(currentPriceMin);
				maxLabel.html(currentPriceMax);
			},
			slide: function(event, ui){
				minLabel.html(ui.values[0]);
				maxLabel.html(ui.values[1]);
			},
			change: function(event, ui){
				priceInput.val(ui.values);
				form = $(this).closest("form")
				base(form, catalog);
			}
		});
		baseForm.on("click", ":checkbox, :radio", function(event){

			catalog.loading.css({
				"display": "block",
				"top": $(this).next("label").position().top + "px"
			});
			base(baseForm, catalog);
		});
		autoForm.on("click", ":radio", function(){

			auto(autoForm, catalog, autoPopUp, $(this).data("step"));
		});
		linkToFilter.click(function(event){
			event.preventDefault();

			if($(this).hasClass("mods")){
				var current = $(this).index() - 1; //finding specific block

				$(this).toggleClass("active");
				$(this).nextAll(".specifications-filter").eq(current).toggleClass("active");
			}
		});
		$(":radio[form=baseFilter]").on("click", function(){

			base($("#" + this.getAttribute("form")), catalog, $("#brandsPopUp"));
		});
	}
	function base(form, catalog, popUp){
		var formData = {}, landMarkData = {};

		formData = form.serializeArray();
		landMarkData = form.data("landmark");

		landMarkData = request.reconstruct(landMarkData, formData);

		if(typeof(popUp) !== "undefined"){
			popUp.addClass("loader");
		}

		request.sender(landMarkData).done(function(answer){

			// HISTORY FILTER
			var filterParameters = historyFilter.getFilterParameters(
				formData, historyFilter.getModificationFilter
			);

			console.log(filterParameters);

			var queryString = historyFilter.composeQueryString(filterParameters);

			historyFilter.replaceState(queryString);
			// END\HISTORY FILTER

			answer = $.parseJSON(answer);

			catalog.grid.html(answer.catalogGrid);
			catalog.navigation.html(answer.navigation);
		})
		.always(function(){
			catalog.loading.css({"display": "none"});
			catalog.currentPage.text(1);

			if(typeof(popUp) !== "undefined"){
				popUp.removeClass("loader");
				popUps.close(popUp);
			}
		});
	}
	function auto(form, catalog, popUp, step){
		var formData = {},
			landMarkData = {},

			tabLabels = form.find(".tabs-label"),
			tabsContent = form.find(".tabs-content");

		tabLabels.eq(step).nextAll().removeClass("active").addClass("disabled");
		tabsContent.eq(step).nextAll().removeClass("active").empty();

		formData = form.serializeArray();
		landMarkData = form.data("landmark");

		landMarkData = request.reconstruct(landMarkData, formData);

		popUp.addClass("loader");

		request.sender(landMarkData).done(function(answer){
			// HISTORY FILTER
			var filterParameters = historyFilter.getFilterParameters(
				formData, historyFilter.getAutoFilter
			);

			var queryString = historyFilter.composeQueryString(filterParameters);

			historyFilter.replaceState(queryString);
			// END\HISTORY FILTER

			answer = $.parseJSON(answer);

			tabLabels.removeClass("active").eq(answer.step).removeClass("disabled").addClass("active");
			tabsContent.removeClass("active").eq(answer.step).html(answer.interlayer).addClass("active");

			if(answer.step === "last"){
				//insert filtered items and navigation
				catalog.autoResult.html(answer.autoResult);
				catalog.grid.html(answer.catalogGrid);
				catalog.navigation.html(answer.navigation);
				//restore initial tabs position and classes
				tabLabels.not(".tabs-label:first").removeClass("active").addClass("disabled").end().eq(0).addClass("active");
				tabsContent.not(".tabs-content:first").empty().end().eq(0).addClass("active");
				//reset form to default state and ajax variables
				form[0].reset();

				//hide popUp
				popUps.close(popUp);
				//scroll to begin of content
				$("html, body").animate({"scrollTop": "250px"}, 500);
			}
		}).always(function(){
			popUp.removeClass("loader");
			catalog.currentPage.text(1);
		});
	}
	return{
		init: init
	}
}(app.request, app.popUp, app.historyFilter));
