<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="ynfr-sponsor">
	<div class="ynfr-sponsor-title">
		<p>{phrase var='sponsor_levels'}</p>
		<p>{phrase var='click_to_donate'}</p>
	</div>
	<div class="ynfr-sponsor-content">
		
			{foreach from=$aCampaign.sponsor_level item=aSponsor}
			
				<a href="{url link='fundraising.donate' id=$aCampaign.campaign_id}amount_{$aSponsor.amount}">
					<div class="ynfr-sponsor-entry" class="js_hover_title" title='{$aSponsor.level_name}'>
						<div class="ynfr-sponsor-level-amount" href="#" >{$aSponsor.amount_text}</div>
						{*<div class='ynfr-sponsor-level-name'>{$aSponsor.level_name|clean|shorten:32:'...'}</div>*}
					</div>
				</a>
			{/foreach}
	</div>
</div>
