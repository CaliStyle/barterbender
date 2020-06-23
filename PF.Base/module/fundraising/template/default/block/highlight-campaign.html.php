<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>


	<div class="ynfr_title_info ynfr-highlight">

		<p class="ynfr-title"><a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" title="{$aCampaign.title|clean}" >{$aCampaign.title|clean|shorten:50:'...'|split:20}</a>
		</p>
		<div class="extra_info">
            <p>{phrase var='created_by'} {$aCampaign|user}</p>
			<p>
				<span class="total_sign">{$aCampaign.total_donor}</span>
				{phrase var='total_donor' total_donor=''}
				&nbsp;.
				{phrase var='total_like_liked' total_like=$aCampaign.total_like}
				&nbsp;.
				{phrase var='total_view_viewed' total_view=$aCampaign.total_view}
			</p>
		</div>
	</div>
	 <div class="ynfr-highlight-image">
        <a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" title="{$aCampaign.title|clean}" >
			{if $aCampaign.image_path}
				<span style="background:url({img return_url=true server_id=$aCampaign.campaign_server_id path='core.url_pic' file=$aCampaign.image_path suffix='_600'}) no-repeat top center;display:block;text-indent:-99999px">			
					{*img path='core.url_pic' file=$aCampaign.image_path suffix='_300' max_width=235 max_height=300*}
				</span>
			{else}
				<span class="no_image_campaign" style="no-repeat top center;display:block;height:180px;width:280px;text-indent:-99999px">
					{img path='core.url_pic' file=$aCampaign.image_path suffix='_300' max_width=235 max_height=300}
				</span>
			{/if}
		</a>
    </div>
	<div class="ynfr-highligh-detail">
        <div class="extra_info">
			{if $aCampaign.financial_goal}
			    <p style="text-align:center;font-weight: bold"> {phrase var='total_amount_raised_of_financial_goal_goal' total_amount=$aCampaign.total_amount_text financial_goal=$aCampaign.financial_goal_text}</p>
			{/if}
            {if $aCampaign.financial_goal}
                <div class="meter-wrap">
                    <div class="meter-value" style="{literal}width: {/literal}{$aCampaign.financial_percent}{literal};{/literal}">
                        {$aCampaign.financial_percent}
                    </div>
                </div>
            {/if}
			<p style="text-align:center">
			 {if isset($aCampaign.remain_time)}
				{$aCampaign.remain_time}
			  {/if}
			</p>
        </div>
		{if $iStatus == 0 || $iStatus == $aStatus.donors || $iStatus == $aStatus.both}
		<div class="ynfr-donor">		
			{foreach from=$aDonors item=aUser}
				{module name='fundraising.campaign.user-image-entry'}
			{/foreach}
			
		</div>     
		{/if}
    </div>
	<div style="clear:both"> </div>
	<div class="ynfr-short-des item_view_content">
		{$aCampaign.short_description|clean|shorten:160:'...'|split:60}
	</div>
	
	{if $aCampaign.can_donate == 1 && (($iStatus == 0) || ($iStatus == $aStatus.donate_button) || ($iStatus == $aStatus.both))}
		<div class="ynfr-donate">		
			<div id="sign_now_{$aCampaign.campaign_id}">
				<a  href="{url link='fundraising.donate' id=$aCampaign.campaign_id}" >{phrase var='donate'}</a>
			</div>
		
		</div>
	{/if}

	{if $iStatus !=0}
	<script type="text/javascript">
		var linkList = document.getElementsByTagName('a');

		 for (var i=0; i<linkList.length; i++){l}
			 linkList[i].setAttribute('target', '_blank');
		{r}
	</script>

	{/if}

