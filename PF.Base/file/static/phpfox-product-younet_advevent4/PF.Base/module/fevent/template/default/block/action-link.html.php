	{if $aItem.can_edit_event}
		<li><a href="{url link='fevent.add' id=$aItem.event_id}">{_p var='edit_event'}</a></li>
	{/if}
	{if $aItem.view_id == 0 && $aItem.can_edit_event}
		<li><a href="{url link='fevent.add' id=$aItem.event_id tab='invite'}">{_p var='invite_people_to_come'}</a></li>
		<li><a href="{url link='fevent.add' id=$aItem.event_id tab='email'}">{_p var='mass_email_guests'}</a></li>
	{/if}		
	{if $aItem.can_edit_event}
		<li><a href="{url link='fevent.add' id=$aItem.event_id tab='manage'}">{_p var='manage_guest_list'}</a></li>
	{/if}
	
	{if $aItem.view_id == 0 && Phpfox::getUserParam('fevent.can_feature_events')}
		<li id="js_feature_{$aItem.event_id}"{if $aItem.is_featured} style="display:none;"{/if}><a href="#" title="{_p var='feature_this_event'}" onclick="$(this).parent().hide(); $('#js_unfeature_{$aItem.event_id}').show(); $(this).parents('.js_event_parent:first').addClass('row_featured').find('.js_featured_event').show(); $.ajaxCall('fevent.feature', 'event_id={$aItem.event_id}&amp;type=1'); return false;">{_p var='feature'}</a></li>
		<li id="js_unfeature_{$aItem.event_id}"{if !$aItem.is_featured} style="display:none;"{/if}><a href="#" title="{_p var='un_feature_this_event'}" onclick="$(this).parent().hide(); $('#js_feature_{$aItem.event_id}').show(); $(this).parents('.js_event_parent:first').removeClass('row_featured').find('.js_featured_event').hide(); $.ajaxCall('fevent.feature', 'event_id={$aItem.event_id}&amp;type=0'); return false;">{_p var='unfeature'}</a></li>
	{/if}	
	
	{if Phpfox::getUserParam('fevent.can_sponsor_fevent')}
		<li id="js_event_sponsor_{$aItem.event_id}" {if $aItem.is_sponsor}style="display:none;"{/if}><a href="#" onclick="$.ajaxCall('fevent.sponsor', 'event_id={$aItem.event_id}&type=1', 'GET'); return false;">{_p var='sponsor_this_event'}</a></li>
		<li id="js_event_unsponsor_{$aItem.event_id}" {if !$aItem.is_sponsor}style="display:none;"{/if}><a href="#" onclick="$.ajaxCall('fevent.sponsor', 'event_id={$aItem.event_id}&type=0', 'GET'); return false;">{_p var='unsponsor_this_event'}</a></li>
	{elseif Phpfox::getUserParam('fevent.can_purchase_sponsor') && !defined('PHPFOX_IS_GROUP_VIEW') 
		&& $aItem.user_id == Phpfox::getUserId()
		&& $aItem.is_sponsor != 1}
		<li> 
			<a href="{permalink module='ad.sponsor' id=$aItem.event_id title=$aItem.title section=fevent}"> 
				{_p var='sponsor_this_event'}
			</a>
		</li>
	{/if}
	
	{if $aItem.can_delete_event
		|| (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))
	}
		{if isset($currentUrl)}
			<li class="item_delete"><a href="{$currentUrl}&delete={$aItem.event_id}" class="sJsConfirm">{_p var='delete_event'}</a></li>
		{else}
			<li class="item_delete"><a href="{url link='fevent' delete=$aItem.event_id}" class="sJsConfirm">{_p var='delete_event'}</a></li>
		{/if}		
	{/if}	