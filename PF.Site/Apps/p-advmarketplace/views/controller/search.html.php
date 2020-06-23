<?php

defined('PHPFOX') or exit('NO DICE!');
?>
{if !PHPFOX_IS_AJAX}
{literal}
<script language="javascript" type="text/javascript">
	$Behavior.advmarket_indexaction = function(){
		if($("#jhslider").size() > 0) {
			$($(".header_bar_float").get(2)).hide();
			$($(".header_bar_float").get(1)).hide();
		}
	};
</script>
{/literal}
{/if}

{if !PHPFOX_IS_AJAX}
{template file='advancedmarketplace.block.adv_search'}
{/if}

{if isset($aListings) && count($aListings)}
    {if !PHPFOX_IS_AJAX}
    <div class="p-block">
        <div class="p-listing-container p-advmarketplace-listing-container {$pCustomClassName} col-6" data-mode-view="grid">
    {/if}
            {foreach from=$aListings name=listings item=aListing}
                {template file='advancedmarketplace.block.entry'}
            {/foreach}
            {if $canContinuePaging}
            {pager}
            {/if}
    {if !PHPFOX_IS_AJAX}
        </div>
    </div>
    {/if}
    {if !PHPFOX_IS_AJAX}
        {if Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings') || Phpfox::getUserParam('advancedmarketplace.can_feature_listings') || (Phpfox::getUserParam('advancedmarketplace.can_approve_listings') && $sListingView == 'pending')}
            {moderation}
        {/if}
    {/if}

{elseif !PHPFOX_IS_AJAX}
    <div class="extra_info">
        {phrase var='advancedmarketplace.no_advancedmarketplace_listings_found'}
    </div>
{/if}
