	{if $aItem.can_edit_event}
		<li><a href="{url link='fevent.add' id=$aItem.event_id}"><span class="ico ico-pencilline-o mr-1"></span>{_p var='edit_event'}</a></li>
	{/if}
    {if $aItem.can_approve_event && ((isset($sView) && $sView == 'pending') || $showApproveAction)}
    <li><a href="javascript:void(0);" onclick="$.ajaxCall('fevent.approve', 'inline=false&event_id={$aItem.event_id}', 'POST'); return false;" title="{_p var='approve'}"><span class="ico ico-check-square-alt mr-1"></span>{_p var='approve'}</a></li>
    {/if}

	{if $aItem.view_id == 0 && $aItem.can_edit_event}
		<li><a href="{url link='fevent.add' id=$aItem.event_id tab='invite'}"><span class="ico ico-user-man-plus"></span>{_p var='invite_people_to_come'}</a></li>
		<li><a href="{url link='fevent.add' id=$aItem.event_id tab='email'}"><span class="ico ico-comment-o"></span>{_p var='mass_email_guests'}</a></li>
	{/if}
	{if $aItem.can_edit_event}
		<li><a href="{url link='fevent.add' id=$aItem.event_id tab='manage'}"><span class="ico ico-user-couple"></span>{_p var='manage_guest_list'}</a></li>
	{/if}

	{if $aItem.can_feature_event}
		<li id="js_feature_{$aItem.event_id}"{if $aItem.is_featured} style="display:none;"{/if}><a href="#" title="{_p var='feature_this_event'}" onclick="$(this).parent().hide(); $('#js_unfeature_{$aItem.event_id}').show(); $(this).parents('.js_event_parent:first').addClass('row_featured').find('.js_featured_event').show(); $.ajaxCall('fevent.feature', 'event_id={$aItem.event_id}&amp;type=1&amp;reload_content=1'); return false;"><span class="ico ico-diamond-o mr-1"></span>{_p var='feature'}</a></li>
		<li id="js_unfeature_{$aItem.event_id}"{if !$aItem.is_featured} style="display:none;"{/if}><a href="#" title="{_p var='un_feature_this_event'}" onclick="$(this).parent().hide(); $('#js_feature_{$aItem.event_id}').show(); $(this).parents('.js_event_parent:first').removeClass('row_featured').find('.js_featured_event').hide(); $.ajaxCall('fevent.feature', 'event_id={$aItem.event_id}&amp;type=0&amp;reload_content=1'); return false;"><span class="ico ico-diamond-o mr-1"></span>{_p var='unfeature'}</a></li>
	{/if}

	{if $aItem.can_sponsor_event}
    <li id="js_event_sponsor_{$aItem.event_id}" {if $aItem.is_sponsor}style="display:none;"{/if}><a href="#" onclick="$.ajaxCall('fevent.sponsor', 'event_id={$aItem.event_id}&type=1', 'GET'); return false;"><span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_this_event'}</a></li>
		<li id="js_event_unsponsor_{$aItem.event_id}" {if !$aItem.is_sponsor}style="display:none;"{/if}><a href="#" onclick="$.ajaxCall('fevent.sponsor', 'event_id={$aItem.event_id}&type=0', 'GET'); return false;"><span class="ico ico-sponsor mr-1"></span>{_p var='unsponsor_this_event'}</a></li>
	{elseif $aItem.can_purchase_sponsor && !defined('PHPFOX_IS_GROUP_VIEW')}
		<li id="js_event_sponsor_{$aItem.event_id}" {if $aItem.is_sponsor}style="display:none;"{/if}>
			<a href="{permalink module='ad.sponsor' id=$aItem.event_id title=$aItem.title section=fevent}">
				<span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_this_event'}
			</a>
		</li>
        <li id="js_event_unsponsor_{$aItem.event_id}" {if !$aItem.is_sponsor}style="display:none;"{/if}>
            <a href="javascript:void(0);" onclick="$.ajaxCall('fevent.sponsor', 'event_id={$aItem.event_id}&type=0', 'GET'); return false;"><span class="ico ico-sponsor mr-1"></span>{_p var='unsponsor_this_event'}
            </a>
        </li>
	{/if}

	{if $aItem.can_delete_event || (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))}
		<li role="separator" class="divider"></li>
		<li class="item_delete"><a href="javascript:void(0);" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('fevent.delete', 'id={$aItem.event_id}{if isset($bIsDetail)}&is_detail=1{/if}');{r}, function(){l}{r}); return false;" data-message="{_p var='are_you_sure_you_want_to_delete_this_event_permanently' phpfox_squote=true}" title="{_p var='delete'}"><span class="ico ico-trash-o mr-1"></span>{_p var='delete'}</a></li>
	{/if}