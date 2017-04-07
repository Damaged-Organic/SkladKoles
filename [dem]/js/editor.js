var app = app || {};

app.editor = (function(){

	function init(areas){

		areas.each(function(){

			var tools = $(this).find(".toolbar")[0],
				container = $(this).find(".container")[0],
				textarea = $(this).find("textarea"),
				quill;

			quill = new Quill(container, {
				modules: {
					"toolbar": {
						"container": tools
					},
					"link-tooltip": true,
					"image-tooltip": true,
				},
				theme: "snow"
			});
			quill.on("text-change", function(delta, source){
				textarea.html(this.getHTML());
			});
		});
	}
	return{
		init: init
	}

}());
