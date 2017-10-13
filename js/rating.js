$(function(){
	
	$('.rating__button').on('click', function(e){
		var $t = $(this), // the clicked star
				$ct = $t.parent(); // the stars container
		
		// add .is--active to the user selected star 
		$t.siblings().removeClass('is--active').end().toggleClass('is--active');
		// add .has--rating to the rating container, if there's a star selected. remove it if there's no star selected.
		$ct.find('.rating__button.is--active').length ? $ct.addClass('has--rating') : $ct.removeClass('has--rating');
	});
	
});



