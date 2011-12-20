
jQuery(function(){
	
	jQuery.fn.enable = function() {
		return jQuery(this).attr('disabled', false);
	};
	
	jQuery.fn.disable = function() {
		return jQuery(this).attr('disabled', true);
	};
	
});
