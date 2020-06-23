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
{if $iPage == 0}
{module name='foxfeedspro.js-block-remove-add-button'}

<!-- News Item Search Form Layout -->
<form id="foxfeedspro_search_form" method="post" action="{url link='foxfeedspro.news'}">
	<!-- Form Header -->
	<h3>
	    {phrase var='foxfeedspro.search_filter'}
	</h3>
	<!-- RSS Provider Name Element --> 
	<div class="form-group">
        <label for="">{phrase var='foxfeedspro.keywords'}:</label>
	    <input class="form-control" type="text" name="search[item_title]" value = "{if isset($aForm.item_title)}{$aForm.item_title}{/if}"/>
	</div>
	<!-- RSS Provider Status Element -->
	<div class="form-group">
        <label for="">{phrase var='foxfeedspro.rss_provider_name'}</label>
        <select class="form-control" name="search[feed_id]">
            <option value = ''>{phrase var="foxfeedspro.all"}</option>
            {foreach from=$aFeedList item=aOption}
                <option value = "{$aOption.feed_id}" {if isset($aForm.feed_id) and $aForm.feed_id == $aOption.feed_id} selected {/if}>{$aOption.feed_name}</option>
            {/foreach}
        </select>
	</div>
	<!-- Submit and Reset Button -->
	<div class=" foxfeedspro-bottom-btn-group">
	    <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary btn-sm" />
	    <input type="submit" name="search[reset]" value="{phrase var='core.reset'}" class="btn btn-default btn-sm button_off" />
	</div>
</form>
{/if}
<!-- News Item Management Space -->
{if count($aNewsItems) > 0}
	<div class="foxfeedpro-listing-item">
		{foreach from=$aNewsItems key=iKey item=aNews}
			{template file='foxfeedspro.block.new-my-items'}
		{/foreach}
		{pager}
		{moderation}
	</div>
{else}
	{if $iPage == 0}
	<span class="extra_info">{phrase var="foxfeedspro.no_news_item_found"}</span>
	{/if}
{/if}

