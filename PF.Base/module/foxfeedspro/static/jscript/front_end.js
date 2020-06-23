$Behavior.initNewsToolTip = function()
{
    $(".tip_trigger").hover(function() {
    	tip = $(this).parent().parent().find('.foxfeedspro_tip');
        tip.show();
        $('.tip').css("z-index", 100000);
        $('.tip').css("position", "absolute");
    });
    
    $(".tip_trigger").mouseleave(function() {
    	tip = $(this).parent().parent().find('.foxfeedspro_tip');
      	tip.hide();
    });
};

function updateFavoriteStatus(id, status)
{
	if(status)
	{
		$('#news_favorite_'+id).hide();
		$('#news_unfavorite_'+id).show();
	}
	else
	{
		$('#news_favorite_'+id).show();
		$('#news_unfavorite_'+id).hide();
	}
	$.ajaxCall('foxfeedspro.updateFavorite','id=' + id + '&status=' + status);
}

function updateSubscribeStatus(id, status)
{
	if(status)
	{
		$('#feed_subscribe_'+id).hide();
		$('#feed_unsubscribe_'+id).show();
	}
	else
	{
		$('#feed_subscribe_'+id).show();
		$('#feed_unsubscribe_'+id).hide();
	}
	$.ajaxCall('foxfeedspro.updateSubscribe','id=' + id + '&status=' + status);
}

function initMenu() {
		$('.ulLevelMenu li ul').hide();
		$('.ulLevelMenu li').each(function() {
			if($(this).children("ul").size()) {
				$(this).children("span").addClass('arrow-down');
				//$(this).children("a").click(function(evt){evt.preventDefault();});
			} else {
				$(this).children("span").removeClass('arrow-down');
				//$(this).children("a").css('background','url('+logo+')');
			}
		});
		$('.ulLevelMenu span').unbind().click(function (evt) {
			if($(this).hasClass('ffpro_feed_link'))
			{
						
			}else{
				evt.preventDefault();
				if ($(this).hasClass('arrow-up')) {
				  $(this).siblings('ul').slideUp();
				  $(this).removeClass('arrow-up').addClass('arrow-down');
				} else if($(this).parent('li').children('ul').length !=0){

				  $(this).siblings('ul').slideDown();
				  $(this).removeClass('arrow-down').addClass('arrow-up');
				}	
			}
			
			
		});
		
	}
$Behavior.initNewsFeedMenu = function(){
	initMenu();
};

function addToCategories()
{
	$('#yn_btn_add_to_category').ajaxCall('foxfeedspro.addToCategories');
}
