<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="fundraising_large_image">
	{if $aCampaign.image_path}
	<a class="js_fundraising_click_image no_ajax_link" href="{img return_url=true server_id=$aCampaign.server_id title=$aCampaign.title path='core.url_pic' file=$aCampaign.image_path suffix='_600'}"></a>
	{else}
		<a class="no_image_campaign" href="javascript:void(0)">
	{/if}
    {img server_id=$aCampaign.server_id title=$aCampaign.title path='core.url_pic' file=$aCampaign.image_path suffix='_600' }
    {if $aCampaign.status == 4}
    <div class="ynfr_tags_link ynfr_close_link">
        {phrase var='closed'}
    </div>
    {elseif $aCampaign.status == 2}
     <div class="ynfr_tags_link ynfr_reached_link">
        {phrase var='reached'}
     </div>
    {elseif $aCampaign.status == 3}
         <div class="ynfr_tags_link ynfr_close_link">
            {phrase var='expired'}
         </div>
    {/if}
	</a>
</div>

<div class="ynfr profile fundraising_rate_body">
	<div class="ynfr profile fundraising_rate_display">
		{module name='rate.display'}
	</div>

</div>
<div class="ynfr profile detail_link">
    <ul>
    {if Phpfox::isUser()  && $aCampaign.status == $aCampaignStatus.ongoing && $aCampaign.is_approved == 1}
        <li><a href="#" onclick="$Core.box('fundraising.inviteBlock',800,'&id={$aCampaign.campaign_id}&url={$sUrl}'); return false;">{phrase var='invite_friends'}</a></li>
    {/if}

    {if $aCampaign.status == $aCampaignStatus.ongoing && $aCampaign.is_approved == 1}
        <li><a href="#" onclick="$Core.box('fundraising.getPromoteCampaignBox',650,'&id={$aCampaign.campaign_id}'); return false;">{phrase var='promote_campaign'}</a></li>
    {/if}

    {if Phpfox::isUser() && Phpfox::getUserId() != $aCampaign.user_id && $aCampaign.status == $aCampaignStatus.ongoing && $aCampaign.is_approved == 1}
        {if !$aCampaign.is_followed}
            <li id="ynfr_profile_follow_link">
                <div>
                    <a href="#" title ="{phrase var='follow_this_campaign'}" onclick="$.ajaxCall('fundraising.follow','campaign_id={$aCampaign.campaign_id}&amp;type=1', 'GET'); return false;">{phrase var='follow'}</a>
                </div>

                <div class="ynfr-icon-question" title="{phrase var='you_will_receive_updated_information'}">
                    <div class="ynfr-question-tooltip js_hover_title" ><i class="fa fa-question-circle"></i></div>
                </div>

            </li>

        {else}
                <li id="ynfr_profile_follow_link"><a href="#" title ="{phrase var='un_follow_this_campaign'}"onclick="$.ajaxCall('fundraising.follow','campaign_id={$aCampaign.campaign_id}&amp;type=0', 'GET'); return false;">{phrase var='un_follow'}</a></li>
        {/if}
        </ul>
    {/if}
</div>
<div class="clear"> </div>


