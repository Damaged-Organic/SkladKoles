;(function($, window){

	var pluginName = "countdown",
		defaults = {
			timestamp: new Date(),
			days: true,
			hours: true,
			minutes: true,
			seconds: true,
			onTimeUpdate: function(el, d, h, m, s){},
			onTimeOut: function(el, finished){},
			onCreate: function(el, digits){}
		},

		days = 24 * 60 * 60,
		hours = 60 * 60,
		minutes = 60;

	function Plugin(el, options){

		this.el = el;
		this.$el = $(el);

		this._options = $.extend(true, {}, defaults, options);
		this._defaults = defaults;
		this._pluginName = pluginName;

		this.initialize.apply(this, arguments);
	}
	Plugin.prototype = {
		initialize: function(){
			this.digits = null;
			this.timeToEvent = this.$el.data("timestamp") ? new Date(this.$el.data("timestamp")) : this._options.timestamp;

			this.ensureHTML();
			this.tick();
		},
		ensureHTML: function(){
			var self = this;

			this.$el.addClass("countdownHolder");

			$.each(["days", "hours", "minutes", "seconds"], function(i){
				$('<span class="'+ this + ' digitsHolder">').html(
					'<span class="'+ this +' digit"></span>\
					<span class="'+ this +' digit"></span>'
				).appendTo(self.$el);
			});

			this.digits = this.$el.find(".digit");
			this._options.onCreate(this.$el, this.digits);
		},
		tick: function(){
			var d, h, m, s, timeLeft;

			timeLeft = Math.floor((this.timeToEvent - (new Date())) / 1000);

			if(timeLeft <= 0){
				timeLeft = 0;
				this.updateDigits("all");
				this._options.onTimeOut(this.$el, true);
				return this;
			}

			d = Math.floor(timeLeft / days);
			this.updateDigits(".days", d);
			timeLeft -= d * days;

			h = Math.floor(timeLeft / hours);
			this.updateDigits(".hours", h);
			timeLeft -= h * hours;

			m = Math.floor(timeLeft / minutes);
			this.updateDigits(".minutes", m);		
			timeLeft -= m * minutes;

			s = timeLeft;
			this.updateDigits(".seconds", s);
			
			this._options.onTimeUpdate(this.$el, d, h, m, s);

			setTimeout($.proxy(this.tick, this), 1000);
		},
		updateDigits: function(type, value){
			if(type === "all"){
				this.digits.html(0);
			} else{
				this.digits.filter(type).eq(0).html(Math.floor(value / 10) % 10);
				this.digits.filter(type).eq(1).html(value % 10);
			}
		}
	}

	$.fn[pluginName] = function(options){

		return this.each(function(){
			if(!$.data(this, "plugin_" + pluginName)){
				$.data(this, "plugin_" + pluginName, new Plugin(this, options))
			}
		});
	}

})(jQuery, window);

