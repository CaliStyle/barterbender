<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="ynfr-user">
	{module name='fundraising.campaign.user-image-entry'}
	<div>
		<span> {if !isset($aUser.is_guest)}
				{$aUser|user|shorten:25:'...'}
			{* if guest is set -> in donor list *}
			{elseif !$aUser.is_guest && isset($aUser.is_anonymous) && !$aUser.is_anonymous}
				{$aUser|user|shorten:25:'...'}
			{else} 
				{if isset($aUser.is_anonymous) && $aUser.is_anonymous}
					{phrase var='anonymous_upper'}
				{else}
					{$aUser.donor_name} 
				{/if}
			{/if}
		</span>
		{if isset($aUser.amount) && $aUser.amount > 0 && !isset($aUser.total_donate)}
			<p>{phrase var='donated_upper'} {$aUser.amount_text}</p>
		{elseif isset($aUser.total_share)}
			<p>{$aUser.total_share} {if $aUser.total_share==1}{_p var='share_lower'}{else}{_p var = 'share_s_lower'}{/if}</p>
		{elseif isset($aUser.total_donate)}
			<div class="item-info-sm">{phrase var='donated_in_total_donate_campaigns' total_donate=$aUser.total_donate}</div>
		{/if}
	</div>
</div>
