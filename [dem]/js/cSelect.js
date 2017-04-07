;(function($, root, document, undefined){

	var pluginName = "сSelect",
		defaults = {
			update: false,
			afterChoose: function(wrapper, select, option){}
		};

	function Plugin(el, options){
		this.el = el;
		this.$el = $(el);

		this._options = $.extend({}, defaults, options);
		
		this._defaults = defaults;
		this._pluginName = pluginName;

		this.initialize();
	}

	Plugin.prototype = {
		initialize: function(){
			this.$cOptions = null;
			this.cSelectWrapper = null;

			this.ensureHTML();
			this.bindEvents();
		},
		getOptionList: function(){
			this.$cOptions = this.$el.find("option");
			
			var options = {
				placeholder: "",
				optionList: ""
			}

			if(this.$cOptions.length > 0){
				this.$cOptions.each(function(){
					if($(this).attr("selected")) options.placeholder = '<span class="cSelectPlaceholder">'+ $(this).text() +'</span>';
					options.optionList += '<li class="cSelectOption" data-value="'+ $(this).val() +'">'+ $(this).text() +'</li>';
				});
			} else{
				options.placeholder = '<span class="cSelectPlaceholder">'+ this.$el.data("placeholder") +'</span>';
				options.optionList = "";
			}
			return options;
		},
		ensureHTML: function(){
			var list = this.getOptionList();

			this.cSelectWrapper = this.$el.wrap("<div class='cSelectWrapper'></div>").parent();
			this.cSelectWrapper.prepend('\
				'+ list.placeholder +'\
				<div class="cSelectOptions">\
					<ul class="cSelectList">\
					'+ list.optionList +'\
					</ul>\
				</div>\
			');
		},
		updateHTML: function(){
			if(!this.cSelectWrapper) return;
			var list = this.getOptionList();
			this.cSelectWrapper.find(".cSelectPlaceholder").replaceWith(list.placeholder).andSelf().find(".cSelectList").html(list.optionList);
		},
		bindEvents: function(){
			this.cSelectWrapper.on("click", ".cSelectPlaceholder", $.proxy(this.toggleList, this));
			this.cSelectWrapper.on("click", ".cSelectOption", $.proxy(this.handleSelected, this));
			$(document).on("click", $.proxy(this.beyondClose, this));
		},
		toggleList: function(e){
			if(this.cSelectWrapper.find(".cSelectOption").length < 1) return;
			this.cSelectWrapper.toggleClass("active");
		},
		handleSelected: function(e){
			var selected = $(e.target),
				text = selected.text(),
				index = selected.index();

			this.$cOptions.removeAttr("selected").eq(index).attr("selected", "selected");
			this.cSelectWrapper.find(".cSelectPlaceholder").text(text).andSelf().removeClass("active");

			this._options.afterChoose(this.cSelectWrapper, this.$el, this.$cOptions.eq(index));
		},
		beyondClose: function(e){
			if(!$(e.target).closest(this.cSelectWrapper).length) this.cSelectWrapper.removeClass("active");
		}
	}

	$.fn.cSelect = function(options){
		return this.each(function(){
			if(!$.data(this, "plugin_" + pluginName)){
				$.data(this, "plugin_" + pluginName, new Plugin(this, options));
			} else if(options.update && $.data(this, "plugin_" + pluginName)){
				$.data(this, "plugin_" + pluginName).updateHTML();
			}
		});
	}

})(jQuery, window, document);