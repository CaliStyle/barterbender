<div class="yndirectory-marketplace-list">
	{if count($aMarketplaces) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}

    <div class="yndirectory-content item-container market-app listing">
    {if $sModuleId == 'advancedmarketplace'}
        {foreach from=$aMarketplaces name=listings item=aListing}
            <?php $this->_aVars['aListing']['hasPermission'] = false?>
            {template file='directory.block.integrate-items.listing.advancedmarketplace'}
        {/foreach}
    {else}
        {foreach from=$aMarketplaces name=listings item=aListing}
            <?php $this->_aVars['aListing']['hasPermission'] = false?>
            {module name='marketplace.rows'}
        {/foreach}
    {/if}
	</div>

	<div class="clear"></div>
	{module name='directory.paging'}
	
</div>

{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}