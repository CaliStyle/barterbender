<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_NewsFeed
 * @version          2.04
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="sub_section_menu foxfeedpro-categories">
	{$html}
</div>

{literal}
<style type="text/css">
	
	.foxfeedspro_cat_active
	{
		background-color:#636363;
		color:#FFFFFF;	
		width:100%;
	}
	.foxfeedspro_cat_active:hover span.slider_title_link, ul#menu_news_category li ul li a.foxfeedspro_cat_active
	{
		color:#000;
	}

	ul#menu_news_category li .foxfeedspro_cat_active span.slider_arrow.up_arrow
	{
		border: 1px solid #000;
	}
	ul#menu_news_category li .foxfeedspro_cat_active span.slider_arrow
	{
		border: 1px solid #000;
	}
	.slider_arrow
	{
		position:absolute;
		right:10px;
	}
</style> 
{/literal}

{literal}
<script type="text/javascript">
	{/literal}
	{if !Phpfox::getParam('core.site_wide_ajax_browsing') }
	{literal}
	
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
	{/literal}
	{/if}
{literal}
    $Behavior.onLoadAdvancedCategory = function() {
        $('#js_block_border_foxfeedspro_advanced-categories-block ._moderator').remove();
        $('#js_block_border_foxfeedspro_advanced-categories-block .ulLevelMenu > li').css('padding-left','0');
        $('#js_block_border_foxfeedspro_advanced-categories-block .content').css('padding-bottom','0');
    }
</script>
{/literal}
