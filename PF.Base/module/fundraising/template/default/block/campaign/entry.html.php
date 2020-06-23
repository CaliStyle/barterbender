<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_fundraising_entry{$aCampaign.campaign_id}"{if !isset($bFundraisingView)} class="js_fundraising_parent image_hover_holder {if ($phpfox.iteration.fundraising%3)==2}ynfr-item-mid{/if}{if $sTagType == 'fundraising_profile'} ynfr-profile{/if}{if $aCampaign.is_approved != 1} {/if}"{/if}>	
	<div class="ynfr-image-campaign">
        {if empty($bInHomepage) && (Phpfox::getUserParam('fundraising.can_approve_campaigns') || Phpfox::getUserParam('fundraising.delete_user_campaign'))}
        <div class="moderation_row">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aCampaign.campaign_id}" id="check{$aCampaign.campaign_id}" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
        {/if}
		{if $aCampaign.is_active == 0}
		     <div class="ynfr_tags_link ynfr_close_link">
			    {phrase var='s_inactive'}
		     </div>		
		{else}
	        {if $aCampaign.is_approved != 1}
			     <div class="ynfr_tags_link ynfr_pending_link">
				    {phrase var='pending'}
			     </div>
			{elseif $aCampaign.status == $aCampaignStatus.closed}
			     <div class="ynfr_tags_link ynfr_close_link">
				    {phrase var='closed'}
			     </div>		
	        {elseif $aCampaign.status == $aCampaignStatus.reached}
			     <div class="ynfr_tags_link ynfr_reached_link">
				    {phrase var='reached'}
			     </div>
	        {elseif $aCampaign.status == $aCampaignStatus.expired}
			     <div class="ynfr_tags_link ynfr_close_link">
				    {phrase var='expired'}
			     </div>
            {elseif $aCampaign.is_draft}
                <div class="ynfr_tags_link ynfr_draft_link">{phrase var='draft'}</div>
			{else}
				<div class="ynfr_tags_link ynfr_featured_link"{if !$aCampaign.is_featured || $aCampaign.is_highlighted} style="display:none;"{/if}>
					{phrase var='featured'}
				</div>

				<div class="ynfr_tags_link ynfr_hightlight_link"{if !$aCampaign.is_highlighted} style="display:none;"{/if}>
					{phrase var='highlight'}
				</div>
	        {/if}
		{/if}

		<a class="ynfr_coupon_img" href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" title="{$aCampaign.title|clean}">
			{if $aCampaign.image_path}
				<span style="background-image:url({img return_url=true server_id=$aCampaign.server_id path='core.url_pic' file=$aCampaign.image_path suffix='_600'})">			
				</span>
			{else}
				<span class="no_image_campaign">
					{img server_id=$aCampaign.server_id path='core.url_pic' file=$aCampaign.image_path suffix='_600'}
				</span>
			{/if}
		</a>

		<div class="ynfr_author">{phrase var='created_by'} <a href="javascript:void(0)">{$aCampaign|user}</a></div>
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
		<div id="js_fundraising_edit_title{$aCampaign.campaign_id}" class="ynfr-title">
			<a href="{permalink module='fundraising' id=$aCampaign.campaign_id title=$aCampaign.title}" id="js_fundraising_edit_inner_title{$aCampaign.campaign_id}" class="link ajax_link">
			    {$aCampaign.title}
			</a>
		</div>

        <p><i class="fa fa-line-chart"></i>&nbsp; {phrase var='raised_amount_entry_block' total_amount=$aCampaign.total_amount_text financial_goal=$aCampaign.financial_goal_text}<p>
		<p><i class="fa fa-clock-o"></i>&nbsp; {$aCampaign.remain_time}</p>
			{plugin call='fundraising.template_block_entry_date_end'}	

	</div>
</div>
{literal}
<script type="text/javascript">
    $Behavior.onInitActionMenuFR = function(){
        setTimeout(function(){
            $('.image_hover_menu_link').off('click').on('click',function(){

                var oMenu = $(this).parent().find('.image_hover_menu:first');

                if ($(this).hasClass('image_hover_active'))
                {
                    $(this).removeClass('image_hover_active');

                    oMenu.hide();

                    return false;
                }

                $('.image_hover_menu_link').each(function() {
                    if ($(this).hasClass('image_hover_active'))
                    {
                        $(this).removeClass('image_hover_active');
                        $(this).parent().find('.image_hover_menu:first').hide();
                        $(this).hide();
                    }
                });

                $(this).addClass('image_hover_active');

                oMenu.show();

                return false;
            });
        },500);
    }
</script>
{/literal}