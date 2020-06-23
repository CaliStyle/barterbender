<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if empty($feed_entry_be)}
    {if Phpfox::isModule('report') && isset($aFeed.report_module)  && !Phpfox::getService('user.block')->isBlocked(null, $aFeed.user_id)}
    {assign var=empty value=false}
    <li class="ynfeed_feed_option"><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type={$aFeed.report_module}&amp;id={$aFeed.item_id}" class="inlinePopup activity_feed_report" title="{_p var='report'}">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            {_p var='report'}</a>
    </li>
    {/if}
{else}
    {if $aFeed.type_id == "user_status" && ((Phpfox::getUserParam('feed.can_edit_own_user_status') && $aFeed.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_edit_other_user_status'))}
        <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="{_p var='edit_feed'}"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('{_p var='edit_feed'}', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}{if isset($aFeed.callback.module)}&module={$aFeed.callback.module}&item_id={$aFeed.callback.item_id}{if isset($aFeed.user_id)}&user_id={$aFeed.user_id}{/if}{/if}')); return false;">
                <i class="fa fa-pencil-square-o"></i> {_p var='edit_feed'}</a></li>
    {/if}

    {if $aFeed.type_id == 'pages_comment' && $aFeed.parent_user_id != 0 && ($aFeed.user_id == Phpfox::getUserId() || (Phpfox::getService('pages')->isAdmin($this->_aVars['aFeed']['parent_user_id'])))}
        <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="{_p var='edit_feed'}"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('{_p var='edit_feed'}', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}{if isset($aFeed.callback.module)}&module={$aFeed.callback.module}&item_id={$aFeed.callback.item_id}{if isset($aFeed.user_id)}&user_id={$aFeed.user_id}{/if}{/if}')); return false;">
                <i class="fa fa-pencil-square-o"></i> {_p var='edit_feed'}</a></li>
    {/if}

    {if $aFeed.type_id == 'groups_comment' && $aFeed.parent_user_id != 0 && ($aFeed.user_id == Phpfox::getUserId() || (Phpfox::getService('groups')->isAdmin($this->_aVars['aFeed']['parent_user_id'])))}
    <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="{_p var='edit_feed'}"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('{_p var='edit_feed'}', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}{if isset($aFeed.callback.module)}&module={$aFeed.callback.module}&item_id={$aFeed.callback.item_id}{if isset($aFeed.user_id)}&user_id={$aFeed.user_id}{/if}{/if}')); return false;">
            <i class="fa fa-pencil-square-o"></i> {_p var='edit_feed'}</a></li>
    {/if}

    {if $aFeed.type_id == 'feed_comment' && ($aFeed.user_id == Phpfox::getUserId() || Phpfox::isAdmin())}
        <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="{_p var='edit_feed'}"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('{_p var='edit_feed'}', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}{if isset($aFeed.callback.module)}&module={$aFeed.callback.module}&item_id={$aFeed.callback.item_id}{if isset($aFeed.user_id)}&user_id={$aFeed.user_id}{/if}{/if}')); return false;">
                <i class="fa fa-pencil-square-o"></i> {_p var='edit_feed'}</a></li>
    {/if}

    {if $aFeed.type_id == 'event_comment' && $aFeed.user_id == Phpfox::getUserId()}
        <li class="ynfeed_feed_option"><a href="javascript:void(0);" title="{_p var='edit_feed'}"  onclick="$Core.ynfeed.detachComposeForm(); tb_show('{_p var='edit_feed'}', $.ajaxBox('ynfeed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}{if isset($aFeed.callback.module)}&module={$aFeed.callback.module}&item_id={$aFeed.callback.item_id}{if isset($aFeed.user_id)}&user_id={$aFeed.user_id}{/if}{/if}')); return false;">
                <i class="fa fa-pencil-square-o"></i> {_p var='edit_feed'}</a></li>
    {/if}

    {plugin call='ynfeed.template_block_entry_2'}

    {if $aFeed.is_tagged}
    <li class="ynfeed_feed_option"><a href="#" class="" title="{_p var='remove_tag_from_feed'}" onclick="$Core.ynfeed.removeTag('feed_id={$aFeed.feed_id}&user_id={$aFeed.user_id}&feed_item_id={$aFeed.item_id}&feed_item_type={$aFeed.type_id}{if isset($aFeed.callback.module)}&module={$aFeed.callback.module}&item_id={$aFeed.callback.item_id}{if isset($aFeed.user_id)}{/if}{/if}');return false;">
            <i class="fa fa-pencil-square-o"></i> {_p var='remove_tag_from_feed'}</a></li>
    {/if}

    {if Phpfox::getUserId() && ((isset($aFeed.is_tagged) && $aFeed.is_tagged) || ($aFeed.user_id == Phpfox::getUserId() || $aFeed.parent_user_id == Phpfox::getUserId()))}
    {if isset($aFeed.is_noti_off) && $aFeed.is_noti_off}
    <li class="ynfeed_feed_option" id="ynfeed_btn_turnoff_noti_feed_{$aFeed.feed_id}"><a href="#" class="" title="{_p var='turnon_notifications_for_this_feed'}" onclick="$Core.ynfeed.turnonNotification({$aFeed.feed_id}, {$aFeed.item_id}, '{$aFeed.type_id}');return false;">
            <i class="fa fa-bell"></i> {_p var='turnon_notifications_for_this_feed'}</a></li>
    {else}
    <li class="ynfeed_feed_option" id="ynfeed_btn_turnoff_noti_feed_{$aFeed.feed_id}"><a href="#" class="" title="{_p var='turnoff_notifications_for_this_feed'}" onclick="$Core.ynfeed.turnoffNotification({$aFeed.feed_id}, {$aFeed.item_id}, '{$aFeed.type_id}');return false;">
            <i class="fa fa-bell-slash"></i> {_p var='turnoff_notifications_for_this_feed'}</a></li>
    {/if}
    {/if}
    {assign var=empty value=true}

    {if Phpfox::isModule('report') && isset($aFeed.report_module)  && $aFeed.user_id != Phpfox::getUserId() && !Phpfox::getService('user.block')->isBlocked(null, $aFeed.user_id)}
        {assign var=empty value=false}
        <li class="ynfeed_feed_option"><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type={$aFeed.report_module}&amp;id={$aFeed.item_id}" class="inlinePopup activity_feed_report" title="{_p var='report_feed'}">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                {_p var='report_feed'}</a>
        </li>
    {/if}
    {if Phpfox::getService('ynfeed.filter')->isSavedFilterEnabled()}
    {if isset($aFeed.is_saved) && $aFeed.is_saved}
    <li class="ynfeed_feed_option" id="ynfeed_btn_save_feed_{$aFeed.feed_id}">
        <a href="javascript:void(0);" class="" title="{_p var='unsave_feed'}" onclick="$.ajaxCall('ynfeed.unsave','{if isset($aFeed.callback.module)}module={$aFeed.callback.module}&{/if}id={$aFeed.feed_id}') ;return false;">
            <i class="fa fa-bookmark" aria-hidden="true"></i> {_p var='unsave_feed')}
        </a>
    </li>
    {else}
    <li class="ynfeed_feed_option" id="ynfeed_btn_save_feed_{$aFeed.feed_id}">
        <a href="javascript:void(0);" class="" title="{_p var='save_feed'}" onclick="$.ajaxCall('ynfeed.save', '{if isset($aFeed.callback.module)}module={$aFeed.callback.module}&{/if}{if isset($aFeed.callback.table_prefix)}table_prefix={$aFeed.callback.table_prefix}&{/if}{if isset($aFeed.type_id)}type={$aFeed.type_id}&{/if}id={$aFeed.feed_id}'); return false;">
            <i class="fa fa-bookmark-o" aria-hidden="true"></i> {_p var='save_feed'}
        </a>
    </li>
    {/if}
    {/if}

    {if Phpfox::getUserId() && (Phpfox::getUserId() != $aFeed.user_id)}
    <li class="ynfeed_feed_option" id="ynfeed_btn_hide_feed_{$aFeed.feed_id}">
        <a href="javascript:void(0);" class="" title="{_p var='hide_feed'}" onclick="$Core.ynfeed.prepareHideFeed([{$aFeed.feed_id}], []); $.ajaxCall('ynfeed.hideFeed', 'id=' + {$aFeed.feed_id}); return false;">
            <i class="fa fa-eye-slash" aria-hidden="true"></i> {_p var='hide_feed'}
        </a>
    </li>

    {if Phpfox::getUserBy('profile_page_id') == 0}
    <li class="ynfeed_feed_option" id="ynfeed_btn_hide_feed_{$aFeed.feed_id}">
        <a href="javascript:void(0);" class="" title="{_p var='hide_all_from_somebody_regular' somebody=$aFeed.full_name}" onclick="$Core.ynfeed.prepareHideFeed([], [{$aFeed.user_id}]); $.ajaxCall('ynfeed.hideAllFromUser', 'id=' + {$aFeed.user_id}); return false;">
            <i class="fa fa-eye-slash" aria-hidden="true"></i> {_p var='hide_all_from_somebody' somebody=$aFeed.full_name}
        </a>
    </li>
    {/if}
    {/if}


    {if ((defined('PHPFOX_FEED_CAN_DELETE')) || (Phpfox::getUserParam('feed.can_delete_own_feed') && $aFeed.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_delete_other_feeds') || ($aFeed.parent_user_id == Phpfox::getUserId()) )}
    <li class="ynfeed_feed_option item_delete"><a href="#" class="" title="{_p var='delete_feed'}"
        onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('ynfeed.delete', 'TB_inline=1&amp;type=delete&amp;id={$aFeed.feed_id}{if isset($aFeedCallback.module)}&amp;module={$aFeedCallback.module}&amp;item={$aFeedCallback.item_id}{/if}&amp;type_id={$aFeed.type_id}');{r}, function(){l}{r}); return false;">
            <i class="fa fa-trash"></i> {_p var='delete_feed'}</a></li>
    {/if}
{/if}