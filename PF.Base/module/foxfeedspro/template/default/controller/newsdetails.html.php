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

{literal}
<script type="text/javascript">
    $Behavior.onLoadHeader = function(){
        var parent = '';
        if ($("body[id^='page_foxfeedspro_newsdetails']").length)
            parent = "body[id^='page_foxfeedspro_newsdetails']";

        if (parent) {
            $('#top').css('display','none');
        }
    }

</script>
{/literal}

<!-- News Details Page Listing Space -->
{ if !$bIsPageNotFound }
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
		</div>
		<!-- Feed Logo -->
		<div class="yns feed_logo">
			{if $aFeed.is_active_logo}
				{if $aFeed.feed_logo}
					<img src="{$sFilePath}{$aFeed.feed_logo}" alt=""/>
				{/if}
			{/if}
		</div>
	</h3>
	<!-- News Details Space -->
	<div class = "newsitem_block" style="border-bottom:none;">
		<!-- News Title -->
		<div class ="yns item_detail_title">
			<!-- PopUp mode -->
			{if $bIsDisplayPopUp}
				<a href="javascript:void(0);" onclick="tb_show('',$.ajaxBox('foxfeedspro.viewPopup','height=500&amp;width=full&amp;id={$aNews.item_id}'));" title ="{$aNews.item_title}">
					{$aNews.item_title}
				</a>
			<!-- friend url mode -->
			{elseif $bIsFriendlyUrl }
				<a href="{$aNews.item_url_detail}" title ="{$aNews.item_title}" target="_blank">
					{$aNews.item_title}
				</a>
			<!-- normal mode -->
			{else}
				<a href="{$aNews.item_url_detail}" title ="{$aNews.item_title}" target="_blank">
					{$aNews.item_title}
				</a>
			{/if}
		</div>
		{if !$bIsFullContent }
			<!-- Cover Image -->
			<div class="image_content">
				{if $bIsDisplayPopUp}
					<a href="javascript:void(0);" onclick="tb_show('',$.ajaxBox('foxfeedspro.viewPopup','height=500&amp;width=full&amp;id={$aNews.item_id}'));" title ="{$aNews.item_title}">
						{if $aNews.item_image}
							<img src="{$aNews.item_image}" alt=""/>
						{else}
							<img class ="news_default_image" src ="{$sDefaultImgLink}"/>
						{/if}
					</a>	
				<!-- friend url mode -->
				{elseif $bIsFriendlyUrl }
					<a href="{$aNews.item_url_detail}" title ="{$aNews.item_title}" target="_blank">
						{if $aNews.item_image}
							<img src="{$aNews.item_image}" alt=""/>
						{else}
							<img class ="news_default_image" src ="{$sDefaultImgLink}"/>
						{/if}
					</a>	
				<!-- normal mode -->
				{else}
					<a href="{$aNews.item_url_detail}" title ="{$aNews.item_title}" target="_blank">
						{if $aNews.item_image}
							<img src="{$aNews.item_image}" alt=""/>
						{else}
							<img class ="news_default_image" src ="{$sDefaultImgLink}"/>
						{/if}
					</a>
				{/if}
			</div>
			<!-- Content -->
			<div class="yns description_content">
				<!-- News Posted Date -->
				<div class ="yns item_datetime">
					{phrase var="foxfeedspro.posted"}: 
					{if $aNews.item_pubDate_parse}
						{$aNews.item_pubDate_parse}
					{else}
						<?php echo date("D,d M Y h:i:s e",$this->_aVars['aNews']["added_time"]);?>
					{/if}
				</div>
				<!-- Original Link -->
				<div class ="yns item_original_link">
					<a href="{$aNews.item_url_detail}" title ="{$aNews.item_url_detail}" target="_blank">
						{$aNews.item_url_detail}
					</a>
				</div>
				<!-- News Description -->
				<div class="yns item_description">
					{$aNews.item_description_parse}
				</div>
			</div>
			<div style="clear:both"></div>
		 {else}
		 	<!-- News Posted Date -->
			<div class ="yns item_datetime" style="margin-bottom:5px;">
				{phrase var="foxfeedspro.posted"}: 
				{if $aNews.item_pubDate_parse}
					{$aNews.item_pubDate_parse}
				{else}
					<?php echo date("D,d M Y h:i:s e",$this->_aVars['aNews']["added_time"]);?>
				{/if}
			</div>
			
			 <!-- Original Link -->
			<div class ="yns item_original_link" style="margin-bottom:5px;">
				{phrase var="foxfeedspro.original_link"}:
				<a href="{$aNews.item_url_detail}" title ="{$aNews.item_url_detail}" target="_blank" style="word-wrap: break-word;">
					{$aNews.item_url_detail}
				</a>
			</div>
			
			<!-- Content -->
		 	<div class="foxfeedspro_content">
			 	{if $aNews.item_content_parse}
			 			{$aNews.item_content_parse}
			 	{else}
			 		{$aNews.item_description}
			 	{/if}
			 	<div style="clear:both"></div>
		 	</div>
		 {/if}

	</div>


	<div class="foxfeedspro_addthis_viewmore">
	    <div id='foxfeedspro_addthis'>
            {addthis url=$aNews.bookmark_url title=$aNews.item_title}
        </div>
		{if isset($aNews.tag_list)}			
		{module name='tag.item' sType=$sTagType sTags=$aNews.tag_list iItemId=$aNews.item_id iUserId=$aNews.user_id sMicroKeywords='keywords'}			
		{/if}

		<div class="view_more2">
			<div class="t_right clearfix">
				<ul class="item_menu_feed">
					<div class="ffp_news_share_button">
						{if Phpfox::isModule('share')}
						{module name='share.link' type='foxfeedspro' display='menu' url=$aNews.url_item title=$aNews.item_title sharemodule='foxfeedspro'}
						<script type="text/javascript">
	                                                $Behavior.new_share_button_foxfeedspro = function(){l}
	                                                    $('.ffp_news_share_button').css('background', 'url({$core_url}module/foxfeedspro/static/image/share.jpg) no-repeat left');
	                                                {r}
						</script>
						{/if}
					</div>

					<li style="background:url({$core_url}module/foxfeedspro/static/image/comment.jpg) no-repeat left; padding-left:15px;padding-right:15px;">
						{$aNews.total_comment} {phrase var='foxfeedspro.comment_s'}
					</li>
					<li>
					{if Phpfox::isUser()}
                    <div class="yns foxfeedspro_favorite">
                        <a id="news_favorite_{$aNews.item_id}" class="news_favorite_link" href="javascript:void(0);"
                           onclick="tb_show('', $.ajaxBox(updateFavoriteStatus({$aNews.item_id},1), 'width=450&amp;id={$aNews.item_id}&amp;status=1')); return false;"
                           title="{phrase var='foxfeedspro.add_to_your_favorite'}" {if $bIsFavorited
                           }style="display:none;" {/if}>
                        {phrase var="foxfeedspro.favorite"}
                        </a>

                        <a id="news_unfavorite_{$aNews.item_id}" class="news_unfavorite_link"
                           href="javascript:void(0);"
                           onclick="tb_show('', $.ajaxBox(updateFavoriteStatus({$aNews.item_id},0), 'width=450&amp;id={$aNews.item_id}&amp;status=0')); return false;"
                           title="{phrase var='foxfeedspro.remove_from_your_favorite'}" {if !$bIsFavorited
                           }style="display:none;" {/if}>
                        {phrase var="foxfeedspro.unfavorite"}
                        </a>
                    </div>
					{/if}
					</li>
				</ul>
			</div>
		</div>
		<!-- Favorite Space -->
	</div>
	

	
	<div style="clear:both"></div>
	<!-- Related News Space -->
		{if count($aNewerItems) > 0 or count($aOlderItems) > 0 }
	<div class="yns related_news_block">
			<h4>{phrase var='foxfeedspro.read_on'}:</h4>
			{ if count($aNewerItems) > 0 }
				 {foreach from = $aNewerItems item = aNewItem}
					 <div class="related_items">
		                    {if $bIsFriendlyUrl}   
		                        <a class="related_item_title" title="{$aNewItem.item_title}" href = "{permalink module='foxfeedspro.newsdetails' id='item_'$aNewItem.item_id title=$aNewItem.item_alias}">{$aNewItem.item_title}</a>
		                    {else}
		                        <a class="related_item_title" title="{$aNewItem.item_title}" href = "{permalink module='foxfeedspro.newsdetails' id='item_'$aNewItem.item_id}">{$aNewItem.item_title}</a>
		                    {/if}
		                    <span class="item_datetime"> - 
		                    	{if $aNewItem.item_pubDate_parse}
				            		{$aNewItem.item_pubDate_parse}
				            	{else}
				            		<?php echo date("D,d M Y h:i:s e",$this->_aVars['aNewItem']["added_time"]);?>
				            	{/if}
				            </span>
		             </div>
	             {/foreach}
			{/if}
			{ if count($aOlderItems) > 0 }
				 {foreach from = $aOlderItems item = aOldItem}
					 <div class="related_items">
		                    {if $bIsFriendlyUrl}   
		                        <a class="related_item_title" title="{$aOldItem.item_title}" href = "{permalink module='foxfeedspro.newsdetails' id='item_'$aOldItem.item_id title=$aOldItem.item_alias}">{$aOldItem.item_title}</a>
		                    {else}
		                        <a class="related_item_title" title="{$aOldItem.item_title}" href = "{permalink module='foxfeedspro.newsdetails' id='item_'$aOldItem.item_id}">{$aOldItem.item_title}</a>
		                    {/if}
		                    <span class="item_datetime"> - 
		                    	{if $aOldItem.item_pubDate_parse}
				            		{$aOldItem.item_pubDate_parse}
				            	{else}
				            		<?php echo date("D,d M Y h:i:s e",$this->_aVars['aOldItem']["added_time"]);?>
				            	{/if}
				            </span>
		             </div>
	             {/foreach}
			{/if}
	</div>
		{/if}
	<!-- Comment Space -->
	 {module name='feed.comment'}
{else}
	<div class="extra_info">{phrase var='foxfeedspro.page_not_found'}</div>	
{/if}

