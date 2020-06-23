
$Ready(function() {
	//smooth scroll to top
	$('.btn-scroll-top').on('click', function(event){
		event.preventDefault();
		$('body,html').animate({
			scrollTop: 0 ,
		 	}, 700
		);
	});
});