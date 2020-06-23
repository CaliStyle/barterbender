
$Ready(function() {
	//smooth scroll to top
	$('.btn-scroll-top').on('click', function(event){
		event.preventDefault();
		$('body,html').animate({
			scrollTop: 0 ,
		 	}, 700
		);
	});

	if(typeof mCustomScrollbar != 'undefined'){
		var dropdown_menu = $(".ynrespassion-navbar .visible-md.visible-lg .dropdown-menu.dropdown-menu-right");
		if(dropdown_menu.length){
			dropdown_menu.mCustomScrollbar({
				theme: "minimal-dark",
			});
		}
	}
});
