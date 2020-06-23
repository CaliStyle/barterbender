<div class="yn-mobile-fund-featured">
	{foreach from=$aFeaturedCampaigns item=aCampaign name=fundraising}
		<div class="ynfr-mobile-item">
			<a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" title="{$aCampaign.title|clean}">		
				{img server_id=$aCampaign.server_id path='core.url_pic' file=$aCampaign.image_path suffix='_120' width='90' class='js_mp_fix_width'}
			</a>
			<div class="ynfr-item-right">
				<p id="js_fundraising_edit_title{$aCampaign.campaign_id}" class="ynfr-title">
					<a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" id="js_fundraising_edit_inner_title{$aCampaign.campaign_id}" class="link ajax_link"><strong>{$aCampaign.title|clean|shorten:40:'...'|split:20}</strong></a>
				</p>
				<p>{phrase var='created_by'} <a href="javascript:void(0)">{$aCampaign|user}</a></p>
				<p><span class="total_sign">{$aCampaign.total_donor}</span>{phrase var='total_donor' total_donor=''} - {phrase var='total_like_liked' total_like=$aCampaign.total_like} - {phrase var='total_view_viewed' total_view=$aCampaign.total_view}</p>
				<p> {phrase var='total_amount_raised_of_financial_goal_goal' total_amount=$aCampaign.total_amount_text financial_goal=$aCampaign.financial_goal_text} ({$aCampaign.financial_percent})</p>
				
				<span class="ynfr-mobile-progress-meter">
					<span class="ynfr-mobile-has-progress" style="width:{$aCampaign.financial_percent}"></span>
				</span>
				<p class="ynfr-remain">
					{if isset($aCampaign.remain_time)}
						{$aCampaign.remain_time}
					{/if}
				</p>
			</div>
		</div>
	{/foreach}
</div>
