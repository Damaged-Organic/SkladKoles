var app = app || {};

app.map = (function(){

	function starter(){
		var mapArea = $("#map"), map, mapOptions, marker, shopPos = new google.maps.LatLng(50.379015, 30.469043), zoom;

		mapOptions = {
			center: shopPos,
			zoom: 12,
			disableDefaultUI: false,
			scrollwheel: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(mapArea[0], mapOptions);

		marker = new google.maps.Marker({
			position: map.getCenter(),
			map: map,
			animation: google.maps.Animation.DROP
		});

		google.maps.event.addListener(marker, "click", function(){
			zoom = map.getZoom();

			if(zoom <= 12){
				map.setZoom(15);
			} else{
				map.setZoom(12);
			}
		});
	}
	return{
		init: starter
	}
}());
