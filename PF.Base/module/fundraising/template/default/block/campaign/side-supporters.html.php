<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{foreach from=$aSupporters item=aUser name=aUser}
	{template file='fundraising.block.campaign.user-entry'}
{/foreach}

<div class="clear"> </div>
