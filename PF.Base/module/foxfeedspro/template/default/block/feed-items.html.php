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
<!-- Feed Content Block Space -->
<div class="foxfeedspro_block">
	<!-- Feed Headline -->
	<h3 id="feed_headline_info">
		<!-- Feed Mini Logo -->
		<div class="yns feed_mini_logo">
			{if $aData.feed.is_active_mini_logo }
				{if $aData.feed.logo_mini_logo}
					<img style="max-height: 16px;  vertical-align: top;" src="{$aData.feed.logo_mini_logo}" alt=""/>
				{else}
					<img style="max-height: 16px;  vertical-align: top;" src="{$sDefaultLogoLink}" alt="default"/>
				{/if}
			{/if}
		</div>
		<!-- Feed Title -->
		<div class="yns feed_title">
			{if $bIsFriendlyUrl}
				<a href="{permalink module='foxfeedspro.feeddetails' id='feed_'.$aData.feed.feed_id title=$aData.feed.feed_name}">{$aData.feed.feed_name|shorten:100:'...'}</a>
			{else}
				<a href="{permalink module='foxfeedspro.feeddetails' id='feed_'.$aData.feed.feed_id}">{$aData.feed.feed_name|shorten:100:'...'}</a>
			{/if}
		</div>
		<!-- Feed Logo -->
		<div class="yns feed_logo">
			{if $aData.feed.is_active_logo}
				{if $aData.feed.feed_logo}
					<img src="{$sFilePath}{$aData.feed.feed_logo}" alt=""/>
				{/if}
			{/if}
		</div>
	</h3>
	<div class="clear"></div>
	<!-- Feed Content -->
	<div id = "feed_news_list">
		<!-- Left Content -->
		<div class="feed_left">
			{foreach from = $aData.items key = iKey item = aNews }
				{if $aData.feed.feed_item_display_full > $iKey }
					<!-- Full News Items -->
					<div class="full_news_item" {if $aData.feed.feed_item_display_full  ==  $iKey + 1 } style="border-bottom: none;" {/if}>
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
								{$aNews.item_description_parse|strip_tags:'<p><b><i><u><br><br/>'|shorten:250:'...'}
							</div>
						</div>
						 <div style="clear:both"></div>
					</div>
				{/if}
			{/foreach}
		</div>
		<!-- Right Content -->
		<div class="feed_right">
			{foreach from = $aData.items key = iKey item = aNews}
				{if $iKey >= $aData.feed.feed_item_display_full }
					<div class="mini_description_content">
						<!-- News Title -->
						<div class ="yns item_title">
							<!-- friend url mode -->
							{if $bIsFriendlyUrl }
								<a class="tip_trigger" href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id title = $aNews.item_alias}">
									{$aNews.item_title}
								</a>
							<!-- normal mode -->
							{else}
								<a class="tip_trigger" href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id }">
									{$aNews.item_title}
								</a>
							{/if}
						</div>
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
						<!-- Tooltip -->
						<div class="foxfeedspro_tip">
			                    <div class = "image_content tip_img">
			                        <!-- friend url mode -->
									{if $bIsFriendlyUrl }
										<a href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id title = $aNews.item_alias}" title ="{$aNews.item_title}">
											{if $aNews.item_image}
												<img src="{$aNews.item_image}" alt=""/>
											{else}
												<img src ="{$sDefaultImgLink}"/>
											{/if}
										</a>	
									<!-- normal mode -->
									{else}
										<a href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id }" title ="{$aNews.item_title}">
											{if $aNews.item_image}
												<img src="{$aNews.item_image}" alt=""/>
											{else}
												<img src ="{$sDefaultImgLink}"/>
											{/if}
										</a>
									{/if}
			                    </div>
			                    <div class ="description_content">         
			                    	<span class="tip_title" style="margin-bottom: 10px;"><strong>{$aNews.item_title}</strong></span>
			                    	<br/>   
			                      	{if !empty($aNews.item_description_parse)}
			                      		<span class ="tip_description">{$aNews.item_description_parse|strip_tags:'<p><b><i><u><br><br/>'|shorten:250:'...'}</span>
			                      	{else}
			                      		<span class ="tip_description">{$aNews.item_title}</span>
			                      	{/if}
			                    </div> 
			                    <div class="clear"></div>  
			            </div>          
					</div>
				{/if}
			{/foreach}
		</div>
		<div class="clear" style="border-bottom: 1px solid #CCC;"></div>
	</div>
</div>