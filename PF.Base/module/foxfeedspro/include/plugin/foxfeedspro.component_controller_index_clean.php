<?php
	// Get Create Options
$menusButton = Phpfox::getLib('template')->getMenu('foxfeedspro');
$menusAddFeed = array_filter($menusButton, function($menu) {
   return ($menu['url'] == 'foxfeedspro.addfeed');
});

$activeMenuAddFeed = count($menusAddFeed)?true:false;

$bCanCreateFeed = Phpfox::getUserParam('foxfeedspro.can_add_rss_provider') && $activeMenuAddFeed;

$bCanCreateNews = Phpfox::getUserParam('foxfeedspro.can_add_news_items');

$sTextFeedCreate = _p('foxfeedspro.add_rss_provider');	
$sTextNewsCreate = _p('foxfeedspro.add_news');
$sCorePath = Phpfox::getParam('core.path');
?>
<script type="text/javascript">
	$Behavior.loadMenuFoxFeedsPro = function(){
		var bodyId = $('body').attr('id');
		var patt1 = /^page_foxfeedspro/g;
		if (!patt1.test(bodyId)) {
			return false;
		}
		$('.breadcrumbs_menu').eq(0).find('ul> li> a').each(function(index) {
			var href = $(this).attr('href');
			var patt2 = /addfeed$/g;
    		if (patt2.test(href)) {
    			$(this).remove();
    		}
		});
		
		menus = $('.breadcrumbs_menu ul li.foxfeedpro-menu-item');
		for( i =0 ; i<menus.length ; i++) {
			menus[i].remove();
		}	
	 }
 </script>
<?php
	if(!Phpfox::getParam('foxfeedspro.is_using_advanced_category'))
	{
?>
	<script type="text/javascript">
	 $Behavior.removeUselessMenuFoxFeedsPro = function(){
		$('.page_breadcrumbs_menu').find('a').each(function(index) {
			var href = $(this).attr('href');
    		if (href.match(/news\/add\//g)) {
    			$(this).remove();
    		}
		});

	 }
	</script>
<?php
	}
?> 