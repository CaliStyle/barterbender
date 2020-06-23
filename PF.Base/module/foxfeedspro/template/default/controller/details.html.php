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
{if !$bIsAddNews}
	{literal}

<script type="text/javascript">
	$(document).ready(function() {
		$('.breadcrumbs_menu')[0].find('li:first').hide();
	}); 
</script>
{/literal}
{/if}
{if !$bIsAddFeed}
{literal}
<script type="text/javascript">
	$(document).ready(function() {
		$('.breadcrumbs_menu')[0].find('li:first').next().hide();
	}); 
</script>
{/literal}
{/if}
{literal}
<style type="text/css">
	.js_box_content ul.action  li:nth-child(2) {
		display: none;
	}
	div#news_list .row_title h3 a {
		color: #3B5998;
		font-size: 11px;
	}
</style>
{/literal}

{if !empty($feeds_sum)}
<div id="news_list">
	<h3> {if count($feeds_sum)>0 && isset($feeds_sum[0].is_active_mini_logo) && $feeds_sum[0].is_active_mini_logo eq 1}
	{if count($feeds_sum)>0 && !empty($feeds_sum[0].logo_mini_logo)} <img class='mini_logo' src="{$feeds_sum[0].logo_mini_logo}" alt=""/> {else} <img class='mini_logo' src="{$core_url}theme/frontend/default/style/default/image/rss/small.gif" alt=""/> {/if}
	{/if}
	{ if $is_friendly_url eq 1} <a href = "{url link ='foxfeedspro.details.feed_'.$feeds_sum[0].feed_id.'.'.$feeds_sum[0].feed_alias}">{$feeds_sum[0].feed_name}</a> {else} <a href = "{url link ='foxfeedspro.details.feed_'.$feeds_sum[0].feed_id}">{$feeds_sum[0].feed_name}</a> {/if}
	{if count($feeds_sum)>0 && $feeds_sum[0].is_active_logo eq 1}{if count($feeds_sum)>0 && $feeds_sum[0].feed_logo} <img class='logo' style="float:right" src="{$feeds_sum[0].feed_logo}" alt="" height="20" onerror="this.src = '{$core_url}theme/frontend/default/style/default/image/rss/small.gif'"/> {/if}
	{/if} </h3>
	<div class="t_right">
		{pager}
	</div>
	<div id="news_list">
		{foreach from=$feeds_sum item=aItem}
		<div id="news_item" class="fnews_detail_content">
			<div class="blog_content">
				<div class="row_title">
					{if $type_view != 'item_details'}
					<h3> {if $is_friendly_url eq 1}
					{if $is_display_popup_item eq 0} <a href = "{url link='foxfeedspro.details.item_'.$aItem.item_id.'.'.$aItem.item_alias}">{$aItem.item_title}</a> {else} <a href="#?call=foxfeedspro.viewpopup&amp;height=200&amp;width=600&amp;id={$aItem.item_id}&amp;view=1" class="inlinePopup" title="{$aItem.item_title}">{$aItem.item_title}</a> {/if}
					{else}
					{if $is_display_popup_item eq 0} <a href = "{url link='foxfeedspro.details.item_'.$aItem.item_id}">{$aItem.item_title}</a> {else} <a href="#?call=foxfeedspro.viewpopup&amp;height=200&amp;width=600&amp;id={$aItem.item_id}&amp;view=1" class="inlinePopup" title="{$aItem.item_title}">{$aItem.item_title}</a> {/if}
					{/if} </h3>
					{else}
					<h1> {if $is_display_popup eq 0} <a target="_blank" href="{$aItem.item_url_detail}">{$aItem.item_title}</a> {else} <a href="#?call=foxfeedspro.viewpopup&amp;height=500&amp;width=full&amp;id={$aItem.item_id}" class="inlinePopup">{$aItem.item_title}</a> {/if} </h1>
					{/if}

				</div>
				<div class = "image_content">
					<?php Phpfox_Error::skip(true); ?>
					{if $type_view != 'item_details'}
					{if $aItem.item_image and filesize($aItem.item_image) > 0}
					{if $is_friendly_url eq 1}
					<a href="{url link='foxfeedspro.details.item_'.$aItem.item_id.'.'.$aItem.item_alias}"><img src="{$aItem.item_image}" alt=""/></a>
					{else}
					<a href="{url link='foxfeedspro.details.item_'.$aItem.item_id}"><img src="{$aItem.item_image}" alt=""/></a>
					{/if}
					{/if}
					{else}
					{if $aItem.item_image and filesize($aItem.item_image) > 0}
					{if $is_display_popup eq 0}
					<a href="{$aItem.item_url_detail}" target="_blank"><img src="{$aItem.item_image}" alt=""/></a>
					{else}
					<a href="#?call=foxfeedspro.viewpopup&amp;height=500&amp;width=full&amp;id={$aItem.item_id}" class="inlinePopup"><img src="{$aItem.item_image}" alt=""/></a>
					{/if}
					{/if}
					{/if}
					<?php Phpfox_Error::skip(true); ?>
				</div>
				{if $type_view == 'item_details' and $aItem.total_attachment > 0}
				{module name='attachment.list' sType=foxfeedspro iItemId=$aItem.item_id}
				{/if}
			</div>
			<div style="clear:both"></div>
			<div class="item_content item_view_content">
				<span class="datetime" >Posted {$aItem.item_pubDate} 
					<?php
					$aItem = Phpfox::getLib('template')->getVar('aItem');
					$owner=  Phpfox::getService('user')->getUser($aItem['owner_id']);
					if($owner):
					?>
					- by
					<?php echo($owner['full_name']);
						endif;
					?></span>
				<br/>
				{if $aItem.item_content !='' and $display_content}
					{$aItem.item_content}
				{else}
				{$aItem.item_description|strip_tags:'<img><br/><p><span>'}
				{/if}
			</div>
			<div class="view_more2" style="clear:both;position: relative;margin-bottom: 20px;">
				<div class="t_right">
					<ul class="item_menu_feed">
						{*<div class="ffp_news_share_button">
						{if Phpfox::isModule('share')}
						{module name='share.link' type='foxfeedspro' display='menu' url=$aItem.link_view title=$aItem.item_title}
						<script type="text/javascript">
							$('.ffp_news_share_button').css('background', 'url({$core_url}module/foxfeedspro/static/image/share.jpg) no-repeat left');
						</script>
						{/if}
						</div>*}
						{if phpfox::isModule('favorite')}
						{if $bIsFavorite}
						{module name='foxfeedspro.favorite-link' favorite_id=$aItem.favorite_id item_id=$aItem.item_id}
							{*{if isset($aFavorite) && count($aFavorite) > 0}
							<li style="background:url({$core_url}module/foxfeedspro/static/image/favorite.jpg) no-repeat left; padding-left:15px;padding-right:15px;">
								<a href="#" onclick="$.ajaxCall('favorite.delete', 'favorite_id={$aFavorite.favorite_id}');">Remove from your favorite</a>
							</li>
							{else}
							<li style="background:url({$core_url}module/foxfeedspro/static/image/favorite.jpg) no-repeat left; padding-left:15px;padding-right:15px;">
								<a href="#?call=favorite.add&amp;height=100&amp;width=400&amp;type=foxfeedspro&amp;id={$aItem.item_id}" class="inlinePopup" title="{phrase var='foxfeedspro.add_to_your_favorite'}">{phrase var='foxfeedspro.favorite'}</a>
							</li>
							{/if}*}
						{/if}
						{/if}
						{*<li style="background:url({$core_url}module/foxfeedspro/static/image/comment.jpg) no-repeat left; padding-left:15px;padding-right:15px;">
							{$aItem.total_comment} {phrase var='foxfeedspro.comment_s'}
						</li>*}
					</ul>
				</div>
			</div>
			<div style="clear:both"></div>
			{if $type_view eq 'item_details'}
			<div class="relate_news" id="toggleText" style="display:block">
				{if count ($itemsnewer)>0 || count ($itemsolder)>0}
				<h4 style="margin-bottom: 5px;font-weight: bold;">{phrase var='foxfeedspro.read_on'}:</h4>
				{/if}
				{*{if count ($itemsnewer)>0}
				<div class="newer_news">
					{foreach from=$itemsnewer name=feeditemnewer item=aItemnewer}
					<div id="link_only" style="background: url({$core_url}module/foxfeedspro/static/image/sprite-2.gif) no-repeat scroll;">
						{ if $is_friendly_url eq 1}
						<a class="tip_trigger"  title="" href = "{url link='foxfeedspro.details.item_'.$aItemnewer.item_id.'.'.$aItemnewer.item_alias}">{$aItemnewer.item_title}</a>
						{else}
						<a class="tip_trigger"  title="" href = "{url link='foxfeedspro.details.item_'.$aItemnewer.item_id}">{$aItemnewer.item_title}</a>
						{/if}
						<span class="datetime"> - {$aItemnewer.item_pubDate}</span>
					</div>
					{/foreach}

				</div>
				{/if}*}
				{if count ($itemsolder)>0}
				<div class="older_news">
					<!--<h4>Older News</h4>-->
					{foreach from=$itemsolder name=feeditemnewer item=aItemnewer}
					<div id="link_only" style="background: url({$core_url}module/foxfeedspro/static/image/sprite-2.gif) no-repeat scroll;">
						{if $is_friendly_url eq 1}
						<a class="tip_trigger" title="" href = "{url link='foxfeedspro.details.item_'.$aItemnewer.item_id.'.'.$aItemnewer.item_alias}">{$aItemnewer.item_title}</a>
						{else}
						<a  class="tip_trigger" title="" href = "{url link='foxfeedspro.details.item_'.$aItemnewer.item_id}">{$aItemnewer.item_title}</a>
						{/if}
						<span class="datetime"> - {$aItemnewer.item_pubDate}</span>
					</div>
					{/foreach}
				</div>
				{if count($itemsolder) < $iCountViewAll}
				<div style="float:left; font-weight:bold;padding-top:10px;" class="text-center">
					<a href="{url link='foxfeedspro.details.feed_'.$aItem.feed_id}">{phrase var='foxfeedspro.view_all'}</a>
				</div>
				<div style="clear:both"></div>
				{/if}
				{/if}

			</div>
			{/if}

		</div>
		{/foreach}
	</div>
	{unset var=$aItems}
	<div class="t_right">
		{pager}
	</div>
</div>
{else}
<div class="extra_info">
	{phrase var='foxfeedspro.no_news_found'}
</div>
{/if}
<div style="clear:both"></div>
{if $type_view eq 'item_details'}
{if count($feeds_sum)>0}
{module name='feed.comment'}
{/if}
{else}
{/if}
