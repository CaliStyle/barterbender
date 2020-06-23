<div class="ynfevent-content-item item-outer">
    <div class="ynfevent-content-item__photo">
		<a href="{permalink module='fevent' id=$aItem.event_id title=$aItem.title}" class="ynfevent-content-item__thumb" style="background-image:url({$aItem.image_path})"></a>
		{if !empty($bShowModerator)}
			<div class="moderation_row">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.event_id}" id="check{$aItem.event_id}" />
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
		{/if}
		<div class="ynfevent-content-item__label-status">
			<div class="sticky-label-icon sticky-pending-icon" {if !$aItem.view_id}style="display: none"{/if}>
		        <span class="flag-style-arrow"></span>
		        <i class="ico ico-clock"></i>
		    </div>
		    <div class="sticky-label-icon sticky-featured-icon" {if !$aItem.is_featured}style="display: none"{/if}>
		        <span class="flag-style-arrow"></span>
		        <i class="ico ico-diamond"></i>
		    </div>
		    <div class="sticky-label-icon sticky-sponsored-icon" {if !$aItem.is_sponsor}style="display: none"{/if}>
			    <span class="flag-style-arrow"></span>
			    <i class="ico ico-sponsor"></i>
		    </div>
		</div>
	</div>
	<div class="ynfevent-content-item__body">
        {if $aItem.can_edit_event
        || ($aItem.view_id == 0 && $aItem.can_edit_event)
        || $aItem.can_delete_event
        || (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))
        }
		    <a class="ynfevent-content-item__title edit-icon" href="{permalink module='fevent' id=$aItem.event_id title=$aItem.title}" title="{$aItem.title|clean}">{$aItem.title|clean}</a>
		{else}
            <a class="ynfevent-content-item__title" href="{permalink module='fevent' id=$aItem.event_id title=$aItem.title}" title="{$aItem.title|clean}">{$aItem.title|clean}</a>
        {/if}
        <div class="ynfevent-content-item__owner text-gray fz-12 mt-h1 pr-6">
			{_p var='fevent.by'} {$aItem|user}
			<div class="ynfevent-content-item__type">
				{if $aItem.d_type == 'past'}
					<span class="ync-label-status linear danger">{_p var='fevent.type_past'}</span>
				{/if}
				{if $aItem.d_type == 'ongoing'}
					<span class="ync-label-status linear success">{_p var='fevent.type_ongoing'}</span>
				{/if}
				{if $aItem.d_type == 'upcoming'}
					<span class="ync-label-status linear warning">{_p var='fevent.type_upcoming'}</span>
				{/if}
			</div>
		</div>
		{if $aItem.can_edit_event
			|| ($aItem.view_id == 0 && $aItem.can_edit_event)
			|| $aItem.can_delete_event
			|| (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))
		}
			<div class="dropdown fevent-actions edit-icon">
				<span data-toggle="dropdown" role="button" class="s-4 fevent-actions-toggle text-gray-dark"><i class="ico ico-gear-o"></i></span>
				<ul class="dropdown-menu dropdown-menu-right">
					{template file='fevent.block.action-link'}
				</ul>			
			</div>
		{/if}
        <time class="ynfevent-content-item__time">
            <p class="ynfevent-content-item__time__start mb-0 text-primary fz-12"><span class="fw-bold">{$aItem.date_start_time} - {$aItem.short_start_time}</span>{if (int)$aItem.isrepeat >= 0} <span class="text-gray-dark fz-12">({_p var ='Repeated'})</span>{/if}</p>
            <p class="ynfevent-content-item__time__end fz-12 mb-0 fw-bold">
                {if (int)((int)$aItem.end_time - (int)$aItem.start_time) > 86400}
                    {if ($aItem.check) > 0}
                        {_p var='fevent.end'}: {$aItem.date_end_time} - {$aItem.date_end_time_hour}
                    {else}
                        {_p var='fevent.end'}: {$aItem.date_end_time1} - {$aItem.date_end_time_hour}
                    {/if}
                {else}
                    {_p var='fevent.end'}: {$aItem.date_end_time_hour}
                {/if}
            </p>
        </time>

		<div class="ynfevent-content-item__info">
			<p class="ynfevent-content-item__location mb-0">{$aItem.location|clean|shorten:50:'...'}</p>
			<div class="hidden ynfevent-content-item__show-invite">
				<p class="ynfevent-content-item__description mb-0 text-gray-dark item_view_content">{$aItem.description_parsed|striptag|clean|shorten:100:'...'}</p>
				{if $sView == 'invites'}
                    <div class="dropdown" id="ynfevent-invite-option">
                        <span id="text-js" class="fz-12 dropdown-toggle text" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{_p var='fevent.yes'}</span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li id="ynfevent-invite-option_1" class="fz-12 ynfevent-invite-option-item ynfevent-invite-option-item-js"><a href="javascript:void(0)" onclick="$(this).parents('#ynfevent-invite-option').hide(); $('#js_event_rsvp_invite_image_{$aItem.event_id}').show(); $.ajaxCall('fevent.addRsvp', 'id={$aItem.event_id}&amp;rsvp=1&amp;inline=1'); return false;">{_p var='fevent.yes'}</a></li>
                            <li id="ynfevent-invite-option_2" class="fz-12 ynfevent-invite-option-item ynfevent-invite-option-item-js"><a href="javascript:void(0)" onclick="$(this).parents('#ynfevent-invite-option').hide(); $('#js_event_rsvp_invite_image_{$aItem.event_id}').show(); $.ajaxCall('fevent.addRsvp', 'id={$aItem.event_id}&amp;rsvp=2&amp;inline=1'); return false;">{_p var='fevent.maybe'}</a></li>
                            <li id="ynfevent-invite-option_3" class="fz-12 ynfevent-invite-option-item ynfevent-invite-option-item-js"><a href="javascript:void(0)" onclick="$(this).parents('#ynfevent-invite-option').hide(); $('#js_event_rsvp_invite_image_{$aItem.event_id}').show(); $.ajaxCall('fevent.addRsvp', 'id={$aItem.event_id}&amp;rsvp=3&amp;inline=1'); return false;">{_p var='fevent.no'}</a></li>
                        </ul>
                    </div>
                {/if}
			</div>
		</div>
	</div>
</div>