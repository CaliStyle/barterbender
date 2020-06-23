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
<div class = "featured_block_item" id ="news_{$aNews.item_id}">
	<!-- Cover Image -->
	<div class="featured_item_image">
		<!-- friend url mode -->
		{if $bIsFriendlyUrl }

			<a href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id title = $aNews.item_alias}" title ="{$aNews.item_title}">
				{if $aNews.item_image}
					<span style="background-image: url({$aNews.item_image})"></span>
				{else}
					<span class="news_default_image" style="background-image: url({$sDefaultImgLink})"></span>
				{/if}
			</a>	
		<!-- normal mode -->
		{else}
			<a href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id }" title ="{$aNews.item_title}">
				{if $aNews.item_image}
					<span style="background-image: url({$aNews.item_image})"></span>
				{else}
					<span class="news_default_image" style="background-image: url({$sDefaultImgLink})"></span>
				{/if}
			</a>
		{/if}
	</div>
	
	<div class="featured_item_block_info">
		<!-- News Title -->
		<div class ="featured_item_title">
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

		<!-- News Description -->
		<div class="featured_item_description">
			{$aNews.item_description_parse|strip_tags:'<p><b><i><u><br><br/>'}
		</div>
		<!-- News Posted Date -->
		<div class ="featured_item_date">
			{phrase var="foxfeedspro.posted"}: 
			{if $aNews.item_pubDate_parse}
				{$aNews.item_pubDate_parse}
			{else}
				<?php echo date("D,d M Y h:i:s e",$this->_aVars['aNews']["added_time"]);?>
			{/if}
		</div>

	</div>
</div>