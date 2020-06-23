<div id="js_fundraising_entry{$aCampaign.campaign_id}"{if !isset($bFundraisingView)} class="{if $aCampaign.is_approved != 1} {/if} ynfr-mobile-entry"{/if}>	
	<div class="ynfr-image-campaign">
           {if $aCampaign.is_approved != 1}
	     <div class="ynfr_pending_link">
		    {phrase var='pending'}
	     </div>
		{elseif $aCampaign.status == $aCampaignStatus.closed}
	     <div class="ynfr_close_link">
		    {phrase var='closed'}
	     </div>		
          {elseif $aCampaign.status == $aCampaignStatus.reached}
	     <div class="ynfr_reached_link">
		    {phrase var='reached'}
	     </div>
          {elseif $aCampaign.status == $aCampaignStatus.expired}
	     <div class="ynfr_close_link">
		    {phrase var='expired'}
	     </div>

		{else}
			<div class="js_featured_fundraising yn_featured_link"{if !$aCampaign.is_featured || $aCampaign.is_highlighted} style="display:none;"{/if}>
				{phrase var='featured'}
			</div>

			<div class="js_highlighted_fundraising yn_hightlight_link"{if !$aCampaign.is_highlighted} style="display:none;"{/if}>
				{phrase var='highlight'}
			</div>
        {/if}
		<a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" title="{$aCampaign.title|clean}">
			<span style="background:url({img return_url=true server_id=$aCampaign.server_id path='core.url_pic' file=$aCampaign.image_path suffix='_120'}) no-repeat top center;display:block;text-indent:-99999px;height:70px;width:95px;">
			
			</span>
		</a>
	</div>
	{if isset($bInHomepage) && !$bInHomepage}
		{if $aCampaign.having_action_button}
			<a href="#" class="image_hover_menu_link">{phrase var='link'}</a>
			<div class="image_hover_menu">
				<ul>
					
					{template file='fundraising.block.link'}
				</ul>			
			</div>
		{/if}
	{/if}

	<div class="ynfr_title_info">
		<p id="js_fundraising_edit_title{$aCampaign.campaign_id}" class="ynfr-title">
			<a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" id="js_fundraising_edit_inner_title{$aCampaign.campaign_id}" class="link ajax_link">{$aCampaign.title|clean|shorten:45:'...'|split:20} {if $aCampaign.is_draft} <span class="ynfr campaign-entry draft-text"> &lt;{phrase var='draft'}&gt;</span> {/if}</a>
		</p>
		<div class="extra_info">
			<p>{phrase var='created_by'} <a href="javascript:void(0)">{$aCampaign|user}</a></p>
            <p>{phrase var='raised_amount_entry_block' total_amount=$aCampaign.total_amount_text financial_goal=$aCampaign.financial_goal_text}</p>
			<p>{$aCampaign.remain_time}</p>
			{plugin call='fundraising.template_block_entry_date_end'}			
		</div>
	</div>
</div>
