$(document).ready(function(){
	$('ul.ync_tabs > li > a').click(function(e) {
		var contentLocation = $(this).attr('href');
		console.log(contentLocation);
		if(contentLocation.charAt(0)=="#") {
			e.preventDefault();
			$(this).parent().siblings().children('a').removeClass('active');
			$(this).addClass('active');
			$(contentLocation).show().addClass('active').siblings().hide().removeClass('active');
		}
	});
});