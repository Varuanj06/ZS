$( document ).ready(function() {
    $('.flipper-container').on('click', function(e){
		e.preventDefault();
		$(this).find('.flipper').toggleClass('rotate-flipper');
	})
});
