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
<script language="javascript" type="text/javascript">
	$Behavior.onLoadMyCategory = function() {
		$('#js_block_border_foxfeedspro_advanced-categories-block ._moderator').remove();
		setTimeout(function() {
			$('#js_block_border_foxfeedspro_my-category-news .dropdown-menu').removeAttr('style');
			$('#js_block_border_foxfeedspro_my-category-news .dropdown-menu').css('left','0');
            $('#js_block_border_foxfeedspro_my-category-news .content').css('padding-bottom', '0');
		},400)
	}
</script>
{/literal}
{*

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
{if !$bIsProfile}
	<div class="sub_section_menu rwmenu">
		<ul id="menu_news_category">
		{foreach from=$aCategories key=iKey item=aCategory} 
		{if $aCategory.category_id==119}
			<li class="submenu category {if $aCategory.category_id == $iCategoryId}active{/if}" style="position:relative;">
				<a {if $aCategory.category_id eq $iCategoryId && $bInCat}class="foxfeedspro_cat_active"{else}class="slider_title"{/if}> 
					{if isset($aCategory.feeds) || ($aCategory.category_id==119 && count($aCategory.children) > 0)}
						<span class="slider_arrow" id="foxfeedspro_cat_up_arrow_{$aCategory.category_id}" onmousedown="return false;"></span>
					{/if} 
					<span class="slider_title_link yn_category_name" style="color:color:#000;" onclick="window.location='{url link='foxfeedspro.category.'.$aCategory.category_id.'.'.$aCategory.category_alias}';">
						{$aCategory.category_name}
					</span>
				</a>
				<ul class="foxfeedspro_category_showfeed_{$aCategory.category_id}">
				{if isset($aCategory.feeds)}
					
						{foreach from=$aCategory.feeds item=feed}
							<li>
								<a style="padding-left:10px;" {if $feed.feed_id eq $iFeedId && $bInFeed}class="foxfeedspro_cat_active"{else}class="slider_title"{/if} href="{url link='foxfeedspro.details.feed_'.$feed.feed_id}">
									<span>
									{if $feed.logo_mini_logo}
										<img src="{$feed.logo_mini_logo}" style="padding-left:0px;max-width:15px;padding-right:5px;" align="left" />
									{else}
										<img src="{$core_url}theme/frontend/default/style/default/image/rss/small.gif" style="padding-left:0px;max-width:15px;padding-right:5px;" align="left" />
									{/if}
									</span>
									<span style="line-height:14px;">{$feed.feed_name}</span>
								</a>
							</li>
						{/foreach}
					
				{/if}
				</ul>
				{if count($aCategory.children) > 0 && $aCategory.category_id == 119}
					<?php
						PHPFOX::getService("foxfeedspro")->buildSubCategory(
							$this->_aVars["aCategory"]["children"],
							$this->_aVars["iCurrentLevel"],
							$this->_aVars["iCurrentCategoryId"],
							$this->_aVars["iCurrentLevel"] + 1, null, true
						);
					?>
				{/if}
				
			</li>
			{/if}
		{/foreach}
		</ul>
	</div>
	{literal}
	<style type="text/css">
		.rwmenu .submenu {
			border-top: 1px solid #D7D7D7;
		}
	</style>
	<script language="javascript" type="text/javascript">
		$(".rwmenu").parent().css({
			"padding-top": "0px"
		});
	</script>
	{/literal}
{/if}
*}