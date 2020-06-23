<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="ynfr-campaign-goal clearfix">
	<div class="ynfr-campaign-goal-inner clearfix">
		<div class="ynfr-campaign-goal-left">
			<div class="ynfr-goal-detail">
				<div class="ynfr-mn">
					<span>{$aCampaign.total_amount_text}</span>

					<span>{phrase var='raised_upper'} {phrase var='of'}</span>
				</div>

				<span class="ynfr-span-bot">{$aCampaign.financial_goal_text} {phrase var='goal_upper'}</span>
			</div>

			<div class="ynfr-highligh-detail">
                {if $aCampaign.financial_goal}
                    <div class="meter-wrap">
                        <div class="meter-value" style="width: {$aCampaign.financial_percent}">
                            {$aCampaign.financial_percent}
                        </div>
                    </div>
                {/if}
                {if isset($aCampaign.remain_time)}
					<div class="ynfr-time"><i class="fa fa-clock-o"></i> {$aCampaign.remain_time}</div>
				{/if}
		    </div>
		</div>

		<div class="ynfr-campaign-goal-right">
			{if $aCampaign.status == 1}
			<div class="ynfr-donate">		
				<a href="{url link='fundraising.donate' id=$aCampaign.campaign_id}" >{phrase var='donate'}</a>
			</div>
			{/if}

			<div class="ynfr-info">
				<div class="ynfr-info-donor">
					<i class="fa fa-users"></i>&nbsp;
					{phrase var='total_donor' total_donor=$aCampaign.total_donor}
				</div>

				<div>{phrase var='total_like_liked' total_like=$aCampaign.total_like} . {phrase var='total_view_viewed' total_view=$aCampaign.total_view}</div>
			</div>
		</div>
	</div>
</div>
