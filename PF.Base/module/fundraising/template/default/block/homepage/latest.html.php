<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


?>
<div class="ynfr_grid_most_block clearfix">
{foreach from=$aLatestCampaigns item=aCampaign name=fundraising}
    {template file='fundraising.block.campaign.entry'}
{/foreach}
</div>
<div class="clear"></div>

<a href="{url link='fundraising' view='ongoing' sort='latest'}" class="ynfr-viewmore-r"> {phrase var='view_more'}</a>