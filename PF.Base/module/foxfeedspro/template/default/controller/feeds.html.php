<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if $iPage == 0}
{module name='foxfeedspro.js-block-remove-add-button'}
<!-- Feed Provider Search Form Layout -->
<form id="foxfeedspro_search_form" method="post" action="
		{if isset($sYnFfFrom) && ($sYnFfFrom == 'profile' || $sYnFfFrom == 'pages')} 
			{if $sYnFfFrom == 'profile'}
				{url link='profile.foxfeedspro.profilemanagerssprovider'} 
			{else} 
				{$aParentModule.url}foxfeedspro/profileviewrss/go_profilemanagerssprovider/
			{/if} 
		{else} 
			{url link='foxfeedspro.feeds'} 
		{/if}">
	<h3>
		{phrase var="foxfeedspro.search_filter"}	
	</h3>

	{if isset($sYnFfFrom) && ($sYnFfFrom == 'profile' || $sYnFfFrom == 'pages')}
		<input type="hidden" id ="sYnFfFrom" name="search[sYnFfFrom]" value="{$sYnFfFrom}" />
	{/if}

	<!-- RSS Provider Name Element --> 
	<div class="form-group">
        <label for="">{phrase var='foxfeedspro.rss_provider_name'}:</label>
	    <input class="form-control" type="text" size ="45" name="search[feed_name]" value = "{if isset($aForm.feed_name)}{$aForm.feed_name}{/if}"/>
	</div>
	<!-- RSS Provider Status Element -->
	<div class="form-group">
        <label for="">{phrase var='foxfeedspro.status'}:</label>
        <select class="form-control" name="search[status]">
            {foreach from=$aStatusOptions item=aOption}
                <option value = "{$aOption.value}" {if isset($aForm.status) and $aForm.status == $aOption.value} selected {/if}>{$aOption.name}</option>
            {/foreach}
        </select>
	</div>
	<!-- Category Search List Element -->
	<div class="form-group">
        <label for="">{phrase var='foxfeedspro.category_name'}:</label>
        <select class="form-control" name="search[category_id]">
            <option value = ''>{phrase var="foxfeedspro.all"}</option>
            {$sCategoryOptions}
        </select>
	</div>
	<!-- Submit and Reset Button -->
	<div class="foxfeedspro-bottom-btn-group">
	    <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary btn-sm" />
	    <input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-default btn-sm button_off" />
	</div>
	<div class="foxfeedspro-bottom-btn-group"></div>
</form>
{/if}
<!-- Rss Provider Management Space -->
{if count($aFeeds) > 0}
	{foreach from = $aFeeds item = aFeed}
		{template file='foxfeedspro.block.feed-my-items'}
	{/foreach}
	{pager}
	{if $bCanEdit && Phpfox::getUserParam('foxfeedspro.can_delete_approved_feed')}
	{moderation}
	{/if}
{else}
	{if $iPage == 0}
	<div class="extra_info">{phrase var="foxfeedspro.no_rss_provider_found"}</div>
	{/if}
{/if}

{if isset($sYnFfFrom) && ($sYnFfFrom == 'profile' || $sYnFfFrom == 'pages') && $iPage == 0}
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
				$('.breadcrumbs_menu').innerHTML = '';		
			{/literal}
			{/if}
			{literal}
		</script>
	{/literal}
{/if}