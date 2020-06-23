<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

{if $bInHomepage}
	{if count($aCampaigns) == 0}
		<div class="extra_info">
			{phrase var='no_fundraisings_found'}
		</div>
	{/if}
{else}
	{if !count($aCampaigns) && $iPage <= 1}
		<div class="extra_info">
			{phrase var='no_fundraisings_found'}
		</div>
		{else}
        {if !PHPFOX_IS_AJAX}
		<div class="fundraising-content">
			<div class="ynfr_grid_most_block clearfix">
        {/if}
				{foreach from=$aCampaigns  name=fundraising item=aCampaign}
                    {template file='fundraising.block.campaign.entry'}
				{/foreach}
                {if count($aCampaigns)}
                {pager}
                {/if}
        {if !PHPFOX_IS_AJAX}
			</div>
		</div>
		<div class="clear"></div>
        {/if}
		{if Phpfox::getUserParam('fundraising.can_approve_campaigns') || Phpfox::getUserParam('fundraising.delete_user_campaign')}
            {moderation}
		{/if}
	{/if}
{/if}