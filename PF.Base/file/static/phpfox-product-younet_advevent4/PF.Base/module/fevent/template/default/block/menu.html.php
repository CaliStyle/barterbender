<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
	{if $aEvent.can_edit_event}
		<li><a href="{url link='fevent.add' id=$aEvent.event_id}">{_p var='fevent.edit_event'}</a></li>
	{/if}
	{if $aEvent.view_id == 0 && $aEvent.can_edit_event}
		<li><a href="{url link='fevent.add' id=$aEvent.event_id tab='invite'}">{_p var='fevent.invite_people_to_come'}</a></li>
		<li><a href="{url link='fevent.add' id=$aEvent.event_id tab='email'}">{_p var='fevent.mass_email_guests'}</a></li>
	{/if}
	{if $aEvent.can_edit_event}
		<li><a href="{url link='fevent.add' id=$aEvent.event_id tab='manage'}">{_p var='fevent.manage_guest_list'}</a></li>
	{/if}
	
	{if $aEvent.view_id == 0 && Phpfox::getUserParam('fevent.can_feature_events')}
		<li id="js_feature_{$aEvent.event_id}"{if $aEvent.is_featured} style="display:none;"{/if}><a href="#" title="{_p var='fevent.feature_this_event'}" onclick="$(this).parent().hide(); $('#js_unfeature_{$aEvent.event_id}').show(); $(this).parents('.js_event_parent:first').addClass('row_featured').find('.js_featured_event').show(); $.ajaxCall('fevent.feature', 'event_id={$aEvent.event_id}&amp;type=1'); return false;">{_p var='fevent.feature'}</a></li>
		<li id="js_unfeature_{$aEvent.event_id}"{if !$aEvent.is_featured} style="display:none;"{/if}><a href="#" title="{_p var='fevent.un_feature_this_event'}" onclick="$(this).parent().hide(); $('#js_feature_{$aEvent.event_id}').show(); $(this).parents('.js_event_parent:first').removeClass('row_featured').find('.js_featured_event').hide(); $.ajaxCall('fevent.feature', 'event_id={$aEvent.event_id}&amp;type=0'); return false;">{_p var='fevent.unfeature'}</a></li>
	{/if}	
	
	{if Phpfox::getUserParam('fevent.can_sponsor_fevent')}
		<li id="js_event_sponsor_{$aEvent.event_id}" {if $aEvent.is_sponsor}style="display:none;"{/if}><a href="#" onclick="$.ajaxCall('fevent.sponsor', 'event_id={$aEvent.event_id}&type=1', 'GET'); return false;">{_p var='fevent.sponsor_this_event'}</a></li>
		<li id="js_event_unsponsor_{$aEvent.event_id}" {if !$aEvent.is_sponsor}style="display:none;"{/if}><a href="#" onclick="$.ajaxCall('fevent.sponsor', 'event_id={$aEvent.event_id}&type=0', 'GET'); return false;">{_p var='fevent.unsponsor_this_event'}</a></li>
	{elseif Phpfox::getUserParam('fevent.can_purchase_sponsor') && !defined('PHPFOX_IS_GROUP_VIEW') 
		&& $aEvent.user_id == Phpfox::getUserId()
		&& $aEvent.is_sponsor != 1}
		<li> 
			<a href="{permalink module='ad.sponsor' id=$aEvent.event_id title=$aEvent.title section=fevent}"> 
				{_p var='fevent.sponsor_this_event'}
			</a>
		</li>
	{/if}
	
	{if $aEvent.can_delete_event
		|| (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))
	}
		<li class="item_delete"><a href="{url link='fevent' delete=$aEvent.event_id}" class="sJsConfirm">{_p var='fevent.delete_event'}</a></li>
	{/if}	