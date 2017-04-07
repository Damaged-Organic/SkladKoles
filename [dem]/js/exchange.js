var app = app || {};

app.exchange = (function(common){

	function init(area){
		
		var form = area.find("form"),
			rateZone = area.find(".rate"),
			rateInput = area.find("#currencyRate"),
			message = $("#message"),
			landMarkData = {},
			rate = 0;

		console.log(form);

		$.getJSON("http://rate-exchange.appspot.com/currency?from=USD&to=UAH&callback=?", function(data){
			rate = parseFloat((data.rate).toFixed(2));

			rateZone.html(rate + " " + data.to);
			rateInput.attr("placeholder", rate);
		});

		form.submit(function(event){
			event.preventDefault();

			$.each($(this).serializeArray(), function(key, obj){
				landMarkData[obj.name] = obj.value;
			});
			
			common.request(landMarkData).done(function(answer){
				message.html(answer);
			})
			.fail(function(error){
				message.html(error.responseText);
			})
			.always(function(){
				setTimeout(function(){
					message.empty();
				}, 4000);
			});
		});
	}	
	return{
		init: init
	}

}(app.common));

