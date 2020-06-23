<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<h1>{phrase var='thank_you_for_helping_out'}</h1>
<div class="ynfr donate campaign_info">
	<div class="ynfr donate image_header">
		{img server_id=$aCampaign.server_id title=$aCampaign.title path='core.url_pic' file=$aCampaign.image_path suffix='_600'}
	</div>

	<div class="ynfr donate description_header">
		<div><a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}">{$aCampaign.title}</a></div>
		<div class="ynfr donate description text">
			{$aCampaign.short_description_parsed |clean|convert}
		</div>
	</div>
	
</div>
<div class="clear"></div>
{literal}
<script type="text/css">
	$Behavior.globalInit();
</script>
{/literal}