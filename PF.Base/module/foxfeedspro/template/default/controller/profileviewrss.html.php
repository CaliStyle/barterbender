<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<!-- Feed Listing Index Space -->
<!-- Headline -->
{if $iPage == 0}
<h2 class = "foxfeedspro_page_headline">
	{$sHeadline}
</h2>
{/if}
<!-- Content -->
{if count($aDataList) > 0}
	{if !$bIsSearched and !$sView}
		<!-- Generate Feed and it 's related news list if it has news data -->
			{foreach from = $aDataList item = aData}
					{if count($aData.items) > 0 }
						{template file='foxfeedspro.block.feed-items'}
					{/if}
			{/foreach}			
	{else}
		<!-- Generate News Items List arcording to the search result -->
		{foreach from = $aDataList item = aNews}
			{template file='foxfeedspro.block.news-items'}
		{/foreach}
	{/if}
	{pager}
{else}
	{if $iPage == 0}
	<div class="extra_info">{phrase var='foxfeedspro.no_news_found'}</div>
	{/if}
{/if}

{literal}
<style>
	#page_foxfeedspro_profileviewrss .breadcrumbs_menu ul li.foxfeedpro-menu-item {
		display: inline-block !important;
	}
</style>
{/literal}

{if isset($sYnFfFrom) 
	&& ($sYnFfFrom == 'profile' || $sYnFfFrom == 'pages')
	&& ($bAddRssProvider || $bManageRssProvider)
}
	{literal}
		<script type="text/javascript">
			var bodyId = document.body.id;
			
			var patt1 = /^page_foxfeedspro/g;
			if (patt1.test(bodyId)) {
				{/literal}
				{if !Phpfox::getParam('core.site_wide_ajax_browsing') }
				{literal}
				$Behavior.ynffRemoveSectionMenu = function() { 
					if (jQuery('.breadcrumbs_menu').length == 0) {
						var content = '<div class="breadcrumbs_menu"><ul></ul></div>';
						jQuery('.profiles_banner').eq(0).append(content);
					}
					menus = jQuery('.breadcrumbs_menu ul li.foxfeedpro-menu-item');
					for( i =0 ; i<menus.length ; i++)
					{
						menus[i].remove();
					}
			
					var url = $(location).attr('href');
				}
	
				{/literal}
				{else}
				
				{/if}
				{literal}
			}
		</script>
	{/literal}
{/if}
{if !Phpfox::getUserParam('foxfeedspro.can_add_rss_provider_in_profile')}
{literal}
<script type="text/javascript">
    $Behavior.ffpHideAddRssMenu = function(){
        if($('#page_foxfeedspro_profileviewrss').length)
        {
            $('.page_breadcrumbs_menu a').each(function(){
                if(this.href.indexOf('addfeed') != -1)
                {
                    $(this).remove();
                }
            })
        }

    }
</script>
{/literal}
{/if}