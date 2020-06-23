<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="ynfr-item-owner">
<div class="ynfr-img-owner">
	{$sCampaignOwnerImage}
</div>

<div class="ynfr campaign_owner full_name">
	<a href="{url link=''$aCampaign.user_name''}">
		{$aCampaign.full_name|shorten:20:'...'|split:20}
	</a>
</div>
</div>