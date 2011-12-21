
jQuery(function(){
	
	// Amélioration de show/hide pour pouvoir fonctionner avec des <option> sous IE
	if (jQuery.browser.msie) {
		var ___show = jQuery.fn.show;
		jQuery.fn.show = function() {
			var self = this;
			var args = arguments;
			this.each(function() {
				if (jQuery(this).is('option')) {
					// <option>
					var parent = jQuery(this).parent(), elem = jQuery(this);
					if (jQuery(parent).is('span')) {
						jQuery(parent).replaceWith(elem);
					}
				} else if (jQuery(this).is('span') && jQuery(this).find(option).length) {
					// hidden <option>
					var elem = jQuery('option', jQuery(this));
					jQuery(this).replaceWith(elem);
				} else {
					// other element
					var elem = jQuery(this);
				}
				___show.apply(elem, args);
			});
			return self;
		}
		
		var ___hide = jQuery.fn.hide;
		jQuery.fn.hide = function() {
			var self = this;
			var args = arguments;
			jQuery(this).each(function() {
				if (!jQuery(this).is('option')) {
					// other element
					var elem = jQuery(this);
				} else if (!jQuery(this).parent().is('span')) {
					// visible <option>
					var elem = jQuery(this).wrap('<span>');
				} else {
					// hidden <option>
					var elem = jQuery(this).parent();
				}
				___hide.apply(elem, args);
			});
			return self;
		};
	}
	
});
