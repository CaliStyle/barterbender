<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<style type="text/css">
.ynfr-user p{l}
	color:#000;
	margin-bottom: 0;
    font-size: 13px;
{r}	
</style>
<div class="ynfr-alluser clearfix">
	{if $iPage == 0}
		<ul class="ynfr-user-option"> 
			<li><a {if $sView == 'donor'} class="active" {/if} href="{url link='fundraising.user' view='donor' id=$iCampaignId}">{phrase var='donors_upper'} </a></li>
			<li><a {if $sView == 'supporter'} class="active" {/if}  href="{url link='fundraising.user' view='supporter' id=$iCampaignId}">{phrase var='supporters'}</a></li>
		</ul>
	{/if}
	{if !count($aUsers)}
		{if $iPage == 0}
			<div class="extra_info">
				{phrase var='no_user_found'}
			</div>
		{/if}
	{else}
		<div class="clearfix">
			{foreach from=$aUsers item=aUser name=aUser}
				{template file='fundraising.block.campaign.user-entry'}
			{/foreach}
		</div>
		{pager}
	{/if}
</div>
