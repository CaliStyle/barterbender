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

{module name='foxfeedspro.js-block-remove-add-button'}


<!-- Feed Details Page Listing Space -->
{ if !$bIsPageNotFound }
	{if $iPage == 0 }
	<!-- Feed Headline -->
	<h3 id="feed_headline_info">
		<!-- Feed Mini Logo -->
		<div class="yns feed_mini_logo">
			{if $aFeed.is_active_mini_logo }
				{if $aFeed.logo_mini_logo}
					<img style="max-height: 16px;  vertical-align: top;" src="{$aFeed.logo_mini_logo}" alt=""/>
				{else}
					<img style="max-height: 16px;  vertical-align: top;" src="{$sDefaultLogoLink}" alt=""/>
				{/if}
			{/if}
		</div>
		<!-- Feed Title -->
		<div class="yns feed_title">
			{if $bIsFriendlyUrl}
				<a href="{permalink module='foxfeedspro.feeddetails' id='feed_'.$aFeed.feed_id title=$aFeed.feed_name}">{$aFeed.feed_name|shorten:150:'...'}</a>
			{else}
				<a href="{permalink module='foxfeedspro.feeddetails' id='feed_'.$aFeed.feed_id}">{$aFeed.feed_name|shorten:150:'...'}</a>
			{/if}
			<!-- Favorite Space -->
		</div>
		{if $bCanSubcribe}
            {if $bIsSubscribed }
                <button id ="feed_unsubscribe_{$aFeed.feed_id}" class ="btn btn-warning btn-sm feed_unsubscribe_link" href="javascript:void(0);" onclick="updateSubscribeStatus({$aFeed.feed_id},0);" title="{phrase var='foxfeedspro.unsubscribe_this_feed'}">
                    {phrase var="foxfeedspro.unsubscribe"}
                </button>
            {else}
            <button id ="feed_subscribe_{$aFeed.feed_id}" class ="btn btn-success btn-sm feed_subscribe_link" href="javascript:void(0);" onclick="updateSubscribeStatus({$aFeed.feed_id},1);" title="{phrase var='foxfeedspro.subscribe_this_feed'}">
                {phrase var="foxfeedspro.subscribe"}
            </button>
            {/if}
		{/if}
		<!-- Feed Logo -->
		<div class="yns feed_logo">
			{if $aFeed.is_active_logo}
				{if $aFeed.feed_logo}
					<img src="{$sFilePath}{$aFeed.feed_logo}" alt=""/>
				{/if}
			{/if}
		</div>
	</h3>

	{if Phpfox::getParam('foxfeedspro.is_using_advanced_category') && Phpfox::getUserId()>0}
		<ul><a href="#" class="btn btn-primary btn-sm yns_btn_addcategory" onclick=" {if ($urlAddCate) } window.location.href = '{$urlAddCate}' {else} tb_show('{phrase var='foxfeedspro.add_to_your_category'}', $.ajaxBox('foxfeedspro.popup', 'item_id={$aFeed.feed_id}'));{/if} ">{phrase var='foxfeedspro.add_to_your_category'}</a><ul>
	{/if}
	{/if}
	
	{if count($aNewsItems) > 0}
		{foreach from = $aNewsItems item = aNews}
			{template file='foxfeedspro.block.news-items'}
		{/foreach}
		{pager}
	{else}
		<div class="extra_info">{phrase var='foxfeedspro.no_news_found'}</div>
	{/if}
{else}
	<div class="extra_info">{phrase var='foxfeedspro.page_not_found'}</div>	
{/if}
