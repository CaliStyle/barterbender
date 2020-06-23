{if Phpfox::isUser() && Phpfox::getUserId() != $aUser.user_id && Phpfox::isModule('friend')}
    <div class="ynmember_link_friendship_new_{$aUser.user_id} ynmember_link_friendship_new_general">
    {if (!$aUser.is_friend && !$aUser.is_friend_request)}
        {if Phpfox::getUserParam('friend.can_add_friends')}
        <a href="javascript:void(0)" class="btn btn-xs btn-success mt-1" onclick="return $Core.addAsFriend('{$aUser.user_id}')"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;{_p('Add friend')}</a>
        {/if}
    {else}
        <div class="ynmember_add_friend_dropdown dropdown {if $aUser.is_friend_request == 2}request{elseif $aUser.is_friend_request == 3}respond{else}friended{/if}">
            <i class="fa fa-user" aria-hidden="true"></i>
            {if $aUser.is_friend_request == 2}
                <span>{_p('Sent Request')}</span>
            {elseif $aUser.is_friend_request == 3}
                <span>{_p('Respond')}</span>
            {else}
                <span>{_p('Friend')}</span>
            {/if}

            <i class="fa fa-caret-down" aria-hidden="true"></i>
            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"></a>

            <ul class="dropdown-menu dropdown-menu-right">
            {if $aUser.is_friend_request == 2}
                <li class="cancel">
                    <a href="javascript:void(0)" onclick="ynmember.updateFriendship('{$aUser.user_id}', '{$aUser.is_friend_request_id}', 'cancel');">
                        <i class="fa fa-times" aria-hidden="true"></i>{_p('Cancel Request')}
                    </a>
                </li>
            {elseif $aUser.is_friend_request == 3}
                <li>
                    <a href="javascript:void(0)" onclick="ynmember.updateFriendship('{$aUser.user_id}', '{$aUser.is_friend_request_id}', 'confirm');"><i class="fa fa-user-plus" aria-hidden="true"></i>{_p('Confirm Request')}</a>
                </li>
            {else}
                {if (Phpfox::isModule('suggestion'))}
                    <li>
                        <a href="javascript:void(0)" onclick="ynmember.show_suggestfriend(this)" rel="{$aUser.user_id}">
                            <i class="fa fa-user" aria-hidden="true"></i>{_p('Suggest Friend')}
                        </a>
                    </li>
                {/if}
                <li class="cancel">
                    <a href="javascript:void(0)" onclick="ynmember.updateFriendship('{$aUser.user_id}', '0', 'delete');"><i class="fa fa-user-times" aria-hidden="true"></i>{_p('Unfriend')}</a>
                </li>
            {/if}
            </ul>
        </div>
    {/if}
    </div>
{/if}