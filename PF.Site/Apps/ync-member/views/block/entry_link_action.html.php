{if Phpfox::isUser() && (Phpfox::getUserId() != $aUser.user_id || Phpfox::getUserParam('user.can_feature'))}
<div class="ynmember_link_action ynmember_link_action_{$aUser.user_id} pull-right">
    <i class="fa fa-angle-down dropdown-toggle" data-toggle="dropdown" aria-hidden="true" aria-haspopup="true" aria-expanded="true"></i>
    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="ynmember_dropdown_option">
    {if Phpfox::getUserId() != $aUser.user_id}
        {if Phpfox::getUserParam('ynmember_follow_member') && Phpfox::getService('user.privacy')->hasAccess('' . $aUser.user_id . '', 'ynmember.follow')}
            <li>
                <a href="javascript:void(0)" onclick="ynmember.followMember('{$aUser.user_id}')">
                    {if $aUser.is_following}
                        <i class="ico ico-minus" aria-hidden="true"></i>{_p('Stop getting notification')}
                    {else}
                        <i class="ico ico-plus" aria-hidden="true"></i>{_p('Get notification')}
                    {/if}
                </a>
            </li>
        {/if}
        {if Phpfox::getUserParam('ynmember_share_member')}
            <li>
                <a href="javascript:void(0)" onclick="ynmember.shareMember('{$aUser.user_id}')">
                    <i class="ico ico-share" aria-hidden="true"></i>{_p('Share this user')}
                </a>
            </li>
        {/if}
        {if Phpfox::isModule('mail') && User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUser.user_id . '', 'mail.send_message')}
            <li>
                <a href="javascript:void(0)" onclick="ynmember.sendMessage('{$aUser.user_id}')">
                    <i class="ico ico-pencilline-o" aria-hidden="true"></i>{_p('Send message')}
                </a>
            </li>
        {/if}
        {if Phpfox::isModule('poke') && Phpfox::getService('poke')->canSendPoke('' . $aUser.user_id . '') && Phpfox::getService('user.privacy')->hasAccess('' . $aUser.user_id . '', 'poke.can_send_poke')}
            <li>
                <a href="javascript:void(0)" onclick="ynmember.poke('{$aUser.user_id}')">
                    <i class="ico ico-smile-o" aria-hidden="true"></i>{_p('Poke')}
                </a>
            </li>
        {/if}
        {if Phpfox::getService('ynmember.review')->canWriteReview('' . $aUser.user_id . '') && !$aUser.is_review_written}
        <li>
            <a href="{url link='ynmember.writereview' user_id=$aUser.user_id}" class="popup"><i class="ico ico-star-circle-o" aria-hidden="true">
                </i>{_p('Review & Rate')}
            </a>
        </li>
        {/if}
        {if Phpfox::getUserParam('user.can_block_other_members') && isset($aUser.user_group_id) && Phpfox::getUserGroupParam('' . $aUser.user_group_id . '', 'user.can_be_blocked_by_others')}
            <li>
                <a href="javascript:void(0)" onclick="ynmember.block('{$aUser.user_id}')">
                    <i class="ico ico-ban" aria-hidden="true"></i>{_p('Block this user')}
                </a>
            </li>
        {/if}
        {if Phpfox::getUserParam('core.can_gift_points')}
            <li>
                <a href="javascript:void(0)" onclick="ynmember.giftPoints('{$aUser.user_id}')">
                    <i class="ico ico-gift-o" aria-hidden="true"></i>{_p('Gift point')}
                </a>
            </li>
        {/if}
    {/if}
        {if Phpfox::getUserParam('user.can_feature')}
            <li>
                <a href="javascript:void(0)" onclick="ynmember.setFeatured('{$aUser.user_id}', {$aUser.is_featured})">
                    <i class="ico ico-diamond" aria-hidden="true"></i>{if $aUser.is_featured}{_p('Unfeature')}{else}{_p('Set to feature')}{/if}
                </a>
            </li>
        {/if}
    </ul>
</div>
{/if}