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


<!-- Add Feed Form Layout -->
{if !$bFeedNotFound}
	{$sCreateJs}
	<form method="post" enctype="multipart/form-data" action="
		{if isset($sYnFfFrom) && ($sYnFfFrom == 'profile' || $sYnFfFrom == 'pages')} 
			{if $sYnFfFrom == 'profile'}
				{url link='profile.foxfeedspro.profileaddrssprovider'}{if $bIsEdit }feed_{$aForm.feed_id} {/if}
			{else} 
				{$aParentModule.url}foxfeedspro/profileviewrss/go_profileaddrssprovider/{if $bIsEdit }feed_{$aForm.feed_id} {/if} 
			{/if} 
		{else} 
			{url link='foxfeedspro.addfeed'}{if $bIsEdit }feed_{$aForm.feed_id} {/if} 
		{/if}" 
		id="js_add_feed_form" onSubmit="{$sGetJsForm}">
		{if $bIsEdit}
		<input type="hidden" name="val[feed_id]" value="{if isset($aForm.feed_id)}{$aForm.feed_id}{/if}">
		{/if}
		<!-- Feed Provider Name Element -->
		<div class="form-group">
            <label for="">{required}{phrase var='foxfeedspro.rss_provider_name'}:</label>
		    <input class="form-control" type="text" size ="60" name="val[feed_name]" id ="name" value="{if isset($aForm.feed_name)}{$aForm.feed_name}{/if}"/>
		</div>
		<!-- Provider Category Element -->
		<div class="form-group">
            <label for="">{phrase var='foxfeedspro.rss_provider_category'}:</label>
		    {$sCategories}
		</div>
		<!-- Provider Logo Element -->
		<div class="form-group">
            <label for="">{phrase var='foxfeedspro.rss_provider_logo_url'}:</label>
		    <input class="form-control" type="text" size ="60" name="val[feed_logo]" value=""/>
            {if $bIsEdit and $aForm.feed_logo}
                <div class="clear"></div>
                <img src="{$aForm.feed_logo}" style="max-height: 100px;"/>
            {/if}
		</div>
		<!-- Upload logo from computer space -->
		<div class="form-group">
            {if !empty($aForm.logo_file) && !empty($aForm.feed_id)}
            {module name='core.upload-form' type='foxfeedspro_logo' current_photo=$aForm.current_image id=$aForm.feed_id}
                <input type="hidden" name="val[logo_file]" value="{value type='input' id='logo_file'}">
            {else}
                {module name='core.upload-form' type='foxfeedspro_logo' current_photo=''}
            {/if}
		</div>
	    <!-- Provider URL Element -->
	    <div class="form-group">
            <label for="">{required}{phrase var='foxfeedspro.rss_provider_url'}:</label>
		    <textarea class="form-control" type="text" id ="url" name="val[feed_url]" cols="60" rows="5" >{if isset($aForm.feed_url)}{$aForm.feed_url}{/if}</textarea>
		</div>
		<!-- Feed Language Element -->
		<div class="form-group">
            <label for="">{phrase var='foxfeedspro.language_package'}:</label>
            <select class="form-control" name="val[feed_language]">
                 <option value="any">{phrase var='foxfeedspro.any'}</option>
                 {foreach from = $aLanguages item = aLang}
                 <option value="{$aLang.language_id}" {if isset($aForm.feed_language) and $aForm.feed_language == $aLang.language_id} selected {/if}>{$aLang.title}</option>
                 {/foreach}
           </select>
		</div>
		
		{if isset($sYnFfFrom) && ($sYnFfFrom == 'profile' || $sYnFfFrom == 'pages')}
			<!-- Time Delete News -->
			<div class="form-group">
                <label for="">{phrase var='foxfeedspro.delete_news_after_every_x_days'}:</label>
			    <input class="form-control" type="text" size ="5" name="val[time_delete_news]" id ="time_delete_news" value="{if isset($aForm.time_delete_news)}{$aForm.time_delete_news}{/if}"/> {phrase var='foxfeedspro.day_s'}
			</div>
		{/if}
		
		<div class="form-group">
            <label for="">{phrase var='foxfeedspro.rss_feed_parse'}:</label>
            ({phrase var='foxfeedspro.parse_rss_feed_to_get_full_content'})
            <div class="extra_info"></div>
            <div>
                <input type="radio" name="val[rssparse]" {if isset($aForm.rssparse) && $aForm.rssparse==1}checked{/if} value="1"/> {phrase var='foxfeedspro.rss_feed_only'}
            </div>
            <div>
                <input type="radio" name="val[rssparse]" {if !isset($aForm.rssparse) || (isset($aForm.rssparse) && $aForm.rssparse==0)}checked{/if}  value="0"/> {phrase var='foxfeedspro.get_news_content_0_to_display_whole_news_other_numbers_to_display_limited_characters'}
            </div>
            <div style="padding-left: 5px;padding-top: 3px;">
                <input class="form-control" type="text" size ="20" name="val[lengthcontent]" id ="name" value="{if isset($aForm.lengthcontent)}{$aForm.lengthcontent}{else}0{/if}"/>
            </div>
		</div>

		<div class="form-group">
            <label for="">{phrase var='foxfeedspro.tags'}:</label>
            <input class="form-control" type="text" name="val[tag_list]" value="{if isset($aForm.tag_list)}{$aForm.tag_list}{/if}" size="30">
            <div class="extra_info">
                {phrase var='foxfeedspro.separate_multiple_topics_with_commas'}
            </div>
		</div>

		<!-- Feed Items Import Number Element -->
		<input type="hidden" id ="item_import" name="val[feed_item_import]" value="{if isset($aForm.feed_item_import)}{$aForm.feed_item_import}{else}10{/if}" />

		{if isset($sYnFfFrom) && ($sYnFfFrom == 'profile' || $sYnFfFrom == 'pages')}
			<input type="hidden" id ="sYnFfFrom" name="val[sYnFfFrom]" value="{$sYnFfFrom}" />
			{if $sYnFfFrom == 'pages'}
				<input type="hidden" id ="pageID" name="val[pageID]" value="{$aParentModule.item_id}" />
				<input type="hidden" id ="pageUrl" name="val[pageUrl]" value="{$aParentModule.url}" />
			{/if}
		{/if}

		<!-- Fields used in Edit Mode Only -->
		{if $bIsEdit}
			<!-- Display Order element -->
				<input type="hidden" name="val[order_display]" value="{if isset($aForm.order_display)}{$aForm.order_display}{/if}"/>
			<!-- News Item Per Page Element -->
				<input type="hidden" name="val[feed_item_display]" value="{if isset($aForm.feed_item_display)}{$aForm.feed_item_display}{/if}" />
			<!-- Number of News with full-description display Element -->
		        <input type="hidden" name="val[feed_item_display_full]" value="{if isset($aForm.feed_item_display_full)}{$aForm.feed_item_display_full}{/if}"/>
			<!-- Is Active Element -->
				<input type="hidden" name="val[is_active]" value="{if isset($aForm.is_active)}{$aForm.is_active}{else}1{/if}"/> 
		    <!-- Display mini logo Element -->  
	            <input type="hidden" name="val[is_active_mini_logo]" value="{if isset($aForm.is_active_mini_logo)}{$aForm.is_active_mini_logo}{else}1{/if}"/>               
		    <!-- Display Logo Element -->     
				<input type="hidden" name="val[is_active_logo]" value="{if isset($aForm.is_active_logo)}{$aForm.is_active_logo}{else}1{/if}"/>
		{/if}
		<!-- Submit Button -->
		<div class="">
		    <input type="submit" name="val[submit]" value="{phrase var='core.submit'}" class="btn btn-primary btn-sm" />
		</div>
	</form>
{else}
	<div class="public_message" id="public_message" style="display: block;">
		{phrase var="foxfeedspro.cannot_find_the_related_rss_provider"}
	</div>
{/if}

{if isset($sYnFfFrom) && ($sYnFfFrom == 'profile' || $sYnFfFrom == 'pages')}
	{literal}
		<script type="text/javascript">
			{/literal}
			{if !Phpfox::getParam('core.site_wide_ajax_browsing') }
			{literal}
				$Behavior.ynffRemoveSectionMenu = function() { 
					$('.breadcrumbs_menu').remove();
				}
			{/literal}
			{else}
			{literal}
				$('.breadcrumbs_menu')[0].innerHTML = '';		
			{/literal}
			{/if}
			{literal}
		</script>
	{/literal}
{/if}