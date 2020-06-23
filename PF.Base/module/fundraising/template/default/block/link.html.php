<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $aCampaign.can_edit_campaign}
	{if !$aCampaign.is_closed }
	   {if $aCampaign.module_id == 'pages'}
		  <li><a href="{url link="fundraising.add" id=""$aCampaign.campaign_id"" module="pages" item=""$aCampaign.item_id""}">{phrase var='edit'}</a></li>
	   {else}
		  <li><a href="{url link="fundraising.add" id=""$aCampaign.campaign_id""}">{phrase var='edit'}</a></li>
	   {/if}

   {/if}
{/if}
{if $aCampaign.is_approved != 1 && !$aCampaign.is_draft && Phpfox::getUserParam('fundraising.can_approve_campaigns')}
<li id="js_fundraising_approve_{$aCampaign.campaign_id}">				
	<a href="#" onclick="$.ajaxCall('fundraising.approve', 'inline=true&amp;id={$aCampaign.campaign_id}'); return false;">{phrase var='approve'}</a>
</li>
{/if}
{if $aCampaign.can_email_to_all_donors}
<li> 
	<a href="#" title="{phrase var='send_mail_to_all_donors'}" onclick="$Core.box('fundraising.sendMailToAllDonors',800,'&campaign_id={$aCampaign.campaign_id}'); return false;">{phrase var='send_mail_to_all_donors'}</a>
</li>
{/if}

{if $aCampaign.can_view_statistic}
      <li><a href="{url link="fundraising.list."$aCampaign.campaign_id}">{phrase var='view_statistics'}</a></li>
 {/if}
{if $aCampaign.can_feature_campaign}
		 <li id="js_fundraising_feature_{$aCampaign.campaign_id}">
        {if $aCampaign.is_featured}
                <a href="#" title="{phrase var='un_feature_this_fundraising'}" onclick="$.ajaxCall('fundraising.feature', 'campaign_id={$aCampaign.campaign_id}&amp;type=0', 'GET'); return false;">{phrase var='un_feature'}</a>
        {else}
                <a href="#" title="{phrase var='feature_this_fundraising'}" onclick="$.ajaxCall('fundraising.feature', 'campaign_id={$aCampaign.campaign_id}&amp;type=1', 'GET'); return false;">{phrase var='feature'}</a>
        {/if}
        </li>
{/if}

{if $aCampaign.can_highlight_campaign}
        <li id="js_fundraising_highlight_{$aCampaign.campaign_id}">
        {if $aCampaign.is_highlighted}
                <a href="#" title="{phrase var='un_highlight_this_campaign'}" onclick="$.ajaxCall('fundraising.highlight', 'campaign_id={$aCampaign.campaign_id}&amp;type=0', 'GET'); return false;">{phrase var='un_highlight'}</a>
        {else}
                <a href="#" title="{phrase var='highlight_this_campaign'}" onclick="$.ajaxCall('fundraising.highlight', 'campaign_id={$aCampaign.campaign_id}&amp;type=1', 'GET'); return false;">{phrase var='highlight'}</a>
        {/if}
        </li>
{/if}

{if $aCampaign.can_close_campaign}
        <li id="js_fundraising_close_{$aCampaign.campaign_id}">
			{if $aCampaign.user_id == Phpfox::getUserId()}
				<a href="#" title="{phrase var='close_this_campaign'}" onclick="$Core.jsConfirm({l}message: '{_p var='are_you_sure_info'}'{r}, function(){l} $.ajaxCall('fundraising.closeCampaign', 'campaign_id={$aCampaign.campaign_id}&amp;is_owner=1'); {r},function(){l}{r}); return false;">{phrase var='close'}</a>
			{else}
                <a href="#" title="{phrase var='close_this_campaign'}" onclick="$Core.jsConfirm({l}message: '{_p var='are_you_sure_info'}'{r}, function(){l} $.ajaxCall('fundraising.closeCampaign', 'campaign_id={$aCampaign.campaign_id}&amp;is_owner=0'); {r},function(){l}{r}); return false;">{phrase var='close'}</a>
			{/if}
        </li>
{/if}

{if Phpfox::getUserParam('fundraising.can_active_campaign')}
        <li id="js_fundraising_setactive_{$aCampaign.campaign_id}">
        {if $aCampaign.is_active}
                <a href="#" title="{phrase var='s_inactive'}" onclick="$.ajaxCall('fundraising.setActive', 'campaign_id={$aCampaign.campaign_id}&amp;type=0', 'GET'); return false;">{phrase var='s_inactive'}</a>
        {else}
                <a href="#" title="{phrase var='s_active'}" onclick="$.ajaxCall('fundraising.setActive', 'campaign_id={$aCampaign.campaign_id}&amp;type=1', 'GET'); return false;">{phrase var='s_active'}</a>
        {/if}
        </li>
{/if}

{if $aCampaign.can_delete_campaign }
	   {if isset($bFundraisingView) && $bFundraisingView == true}
		  <li class="item_delete"><a href="{url link='fundraising' delete=$aCampaign.campaign_id}" class="sJsConfirm">{phrase var='delete'}</a></li>
	   {else}
		  <li class="item_delete">
              <a href="#" title="{phrase var='close_this_campaign'}" onclick="$Core.jsConfirm({l}message: '{_p var='are_you_sure_you_want_to_delete_this_fundraising'}'{r}, function(){l} $.ajaxCall('fundraising.inlineDelete', 'item_id={$aCampaign.campaign_id}'); {r},function(){l}{r}); return false;">{phrase var='delete'}</a>
          </li>
	   {/if}
{/if}
