<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $rsvpActionType == 'list'}
    {if isset($aItem.rsvp_id) && ($aItem.is_invited || $aItem.rsvp_id != 0)}
    <div class="dropdown js_rsvp_content" data-id="{$aItem.event_id}" data-phrase="{_p var='maybe_attending'}">
        <a data-toggle="dropdown" class="btn  btn-default btn-icon btn-sm">
            <span class="txt-label js_text_label">
                    {if $aItem.rsvp_id == 1}
                        <i class="ico ico-check-circle mr-1"></i><span class="item-text">{_p var='attending'}</span>
                    {elseif $aItem.rsvp_id == 2 || (!isset($aItem.rsvp_id) || (!$aItem.is_invited && $aItem.rsvp_id == 0))}
                        <i class="ico ico-star mr-1"></i><span class="item-text">{_p var='maybe_attending'}</span>
                    {elseif $aItem.rsvp_id == 3}
                        <i class="ico ico-ban mr-1"></i><span class="item-text">{_p var='not_attending'}</span>
                    {elseif $aItem.rsvp_id == 0 && $aItem.is_invited}
                        <i class="ico ico-question-circle-o"></i><span class="item-text">{_p var='confirm'}</span>
                    {/if}
            </span>
            <i class="ico ico-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li role="button">
                <a data-toggle="event_rsvp" rel="1"  {if isset($aItem.rsvp_id) && $aItem.rsvp_id == 1}class="is_active_image"{/if}>
                    <i class="ico ico-check-circle-o mr-1"></i><span class="item-text">{_p var='attending'}</span>
                </a>
            </li>
            <li role="button">
                <a data-toggle="event_rsvp" rel="2" {if isset($aItem.rsvp_id) && $aItem.rsvp_id == 2}class="is_active_image"{/if}>
                    <i class="ico ico-star-o mr-1"></i><span class="item-text">{_p var='maybe_attending'}</span>
                </a>
            </li>
            {if !$aItem.is_invited}
            <li role="separator" class="divider"></li>
            {/if}
            <li role="button">
                <a data-toggle="event_rsvp" rel="{if $aItem.is_invited}3{else}0{/if}" {if isset($aItem.rsvp_id) && $aItem.rsvp_id == 3 && $aItem.is_invited}class="is_active_image"{/if}>
                    <i class="ico ico-ban mr-1"></i><span class="item-text">{_p var='not_attending'}</span>
                </a>
            </li>
        </ul>
    </div>
    {else}
    <div class="js_rsvp_content" data-id="{$aItem.event_id}">
        <a class="btn btn-default btn-sm" data-toggle="event_rsvp" rel="2"><i class="ico ico-star-o mr-1"></i><span class="item-text">{_p var='maybe_attending'}</span></a>
    </div>
    {/if}
{else}
    {if $aEvent.rsvp_id != 0}
        <div class="item-event-option-dropdown-wrapper">
            <div class="dropdown">
                <div data-toggle="dropdown" class="btn btn-default btn-sm">
                    <div>
                        {if $aEvent.rsvp_id == 1}
                        <i class="ico ico-check-circle mr-1"></i>{_p var='attending'}
                        {elseif $aEvent.rsvp_id == 2}
                        <i class="ico ico-star mr-1"></i>{_p var='maybe_attending'}
                        {elseif $aEvent.rsvp_id == 3}
                        <i class="ico ico-ban mr-1"></i>{_p var='not_attending'}
                        {/if}
                    </div>
                    <i class="ico ico-caret-down ml-1"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li class="item-event-option {if $aEvent.rsvp_id == 1}active{/if}" data-rsvp="1">
                        <a href="javascript:void(0);"><i class="ico ico-check-circle-o"></i>{_p var='attending'}</a>
                    </li>
                    <li class="item-event-option {if $aEvent.rsvp_id == 2}active{/if}" data-rsvp="2">
                        <a href="javascript:void(0);"><i class="ico ico-star-o"></i>{_p var='maybe_attending'}</a>
                    </li>
                    {if $aEvent.is_invited}
                    <li class="item-event-option {if $aEvent.rsvp_id == 3}active{/if}" data-rsvp="3">
                        <a href="javascript:void(0);"><i class="ico ico-ban"></i>{_p var='not_attending'}</a>
                    </li>
                    {else}
                    <li role="separator" class="divider"></li>
                    <li class="item-event-option" data-rsvp="0">
                        <a href="javascript:void(0);">{_p var='cancel'}</a>
                    </li>
                    {/if}
                </ul>
            </div>
        </div>
    {else}
        <div class="item-event-option-wrapper {if $aEvent.is_invited}has-invite{/if}">
            <div class="item-event-option attending" data-rsvp="1">
                <span class="btn btn-sm btn-default btn-icon"><i class="ico ico-check-circle-o"></i>{_p var='attending'}</span>
            </div>

            <div class="item-event-option maybe_attending" data-rsvp="2">
                <span class="btn btn-sm btn-default btn-icon"><i class="ico ico-star-o"></i>{_p var='maybe_attending'}</span>
            </div>

            {if $aEvent.is_invited}
            <div class="item-event-option not_attending" data-rsvp="3">
                <span class="btn btn-sm btn-default btn-icon"><i class="ico ico-ban"></i>{_p var='not_attending'}</span>
            </div>
            {/if}
        </div>
    {/if}
{/if}
