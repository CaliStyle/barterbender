<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{foreach from=$aDonors item=aUser name=aUser}
	{template file='fundraising.block.campaign.user-entry'}
{/foreach}
