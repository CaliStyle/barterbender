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

<!-- News Item Content Block Space -->
<div class = "newsitem_block" id ="news_{$aNews.item_id}">
		<!-- News Title -->
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
		<!-- Cover Image -->
		<div class="image_content">
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
			<!-- Author -->
			<div class ="yns item_author">
				{$aNews.item_author}
			</div>
			<!-- News Description -->
			<div class="yns item_description">
				{$aNews.item_description_parse|strip_tags:'<p><b><i><u><br><br/>'|shorten:300:'...'}
			</div>
		</div>
		<div style="clear:both"></div>
		{if $sView == 'favorite'}
			<div class="yns foxfeedspro_favorite">
				<a id ="news_unfavorite_{$aNews.item_id}" class ="news_unfavorite_link" href="javascript:void(0);" onclick="tb_show('{phrase var='core.notice'}', $.ajaxBox(updateFavoriteStatus({$aNews.item_id},0), 'width=450&id={$aNews.item_id}'));"  title="{phrase var='foxfeedspro.remove_from_your_favorite'}">
					{phrase var="foxfeedspro.unfavorite"}
				</a>
			</div>
		{/if}
		<div style="clear:both"></div>
</div>