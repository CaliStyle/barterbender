<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_NewsFeed
 * @version        3.02p5
 * 
 */
?>
{if ($iPage == 0)}
{module name='foxfeedspro.js-block-remove-add-button'}
<!-- Feed Listing Index Space -->
<!-- Headline -->
	{if isset($sHeadline) && $sHeadline != ""}
	<h1 class = "foxfeedspro_page_headline">
		{$sHeadline}
	</h1>
	{/if}
{/if}
<!-- Content -->
{if count($aDataList) > 0}
	{if !$bIsSearched and !$sView and !$bIsValidTag }
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
	{if ($iPage == 0)}
	<div class="extra_info">{phrase var='foxfeedspro.no_news_found'}</div>
	{/if}
{/if}
