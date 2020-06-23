
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
		var dropdown_menu = $(".ynresbusiness-navbar .visible-md.visible-lg .dropdown-menu.dropdown-menu-right");
		if(dropdown_menu.length){
			dropdown_menu.mCustomScrollbar({
				theme: "minimal-dark",
			});
		}
	}
});
$Behavior.buildMenu = function() {
  $('[data-component="menu"]:not(\'.built\')').each(function() {
    var th = $(this),
        firstMenuItem = $('li[rel!="menu1"]:first', th),
        lastMenuItem = $('li[rel!="menu1"]:not(.explorer):last', th);
        console.log("Adsad");
    if (typeof firstMenuItem.offset() === 'undefined' || typeof lastMenuItem.offset()  === 'undefined') {
      return;
    } else {
      var checkOffsetTop = firstMenuItem.offset().top + 20, // 20 for threshold
          lastItemOffsetTop = lastMenuItem.offset().top;
    }

    if (checkOffsetTop > lastItemOffsetTop) {
      $('>div', th).hide();
      th.addClass('built');
      th.css('overflow', 'visible');

      return;
    }

    var explorerItem = $('>li.explorer', th).removeClass('hide'),
        itemSize = $('>li:not(.explorer)', th).length,
        explorerMenu = $('ul', explorerItem);

    function shouldMoveMenuItem() {
      th.find('>li[rel!="menu1"]:not(.explorer):last').prependTo(explorerMenu);
      return checkOffsetTop < explorerItem.offset().top;
    }

    for (var i = 0; i < itemSize; i++) {
    	console.log(i, shouldMoveMenuItem());
      if (!shouldMoveMenuItem()) {
        $('>div', th).fadeOut();
        th.addClass('built');
        th.css('overflow', 'visible');

        return;
      }
    }
  });
};