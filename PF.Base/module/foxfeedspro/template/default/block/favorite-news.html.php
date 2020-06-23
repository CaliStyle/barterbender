<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02p5
 * 
 */
?>

<!-- Top News Listing Space -->
<ul class = "yns" id = "news_block_list">
	{foreach from = $aNewsList item = aNews}
		<li class = "news_item">
			<!-- News cover image -->
			<div class ="yns image_content">
				<!-- friend url mode -->
				{if $bIsFriendlyUrl }
					<a href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id title = $aNews.item_alias}" title ="{$aNews.item_title}">
						{if $aNews.item_image}
							<img src="{$aNews.item_image}" alt=""/>
						{else}
							<img class ="news_default_image" src ="{$sDefaultImgLink}"/>
						{/if}
					</a>
				<!-- normal mode -->
				{else}
					<a href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id }" title ="{$aNews.item_title}">
						{if $aNews.item_image}
							<img src="{$aNews.item_image}" alt=""/>
						{else}
							<img class ="news_default_image" src ="{$sDefaultImgLink}"/>
						{/if}
					</a>
				{/if}
			</div>
			<!-- News content fields -->
			<div class = "yns description_content">
				<!-- News title -->
				<div class ="yns item_title">
					<!-- friend url mode -->
					{if $bIsFriendlyUrl }
					<a href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id title = $aNews.item_alias}" title ="{$aNews.item_title}">
						{$aNews.item_title}
					</a>
					<!-- normal mode -->
					{else}
						<a href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id }" title ="{$aNews.item_title}">
							{$aNews.item_title}
						</a>
					{/if}
				</div>
				<!-- Author -->
				<div class ="yns item_author">
					{$aNews.item_author}
				</div>
				<!-- Number of views -->
				<div class ="yns item_favorites">
					{phrase var="foxfeedspro.favorites"}: <strong>{$aNews.total_favorite}</strong>	
				</div>
			</div>
			<!-- clear -->
			<div class = "clear"></div>
		</li>	
	{/foreach}
</ul>
<!-- View all element -->
<div class = "clear"></div>
