{if Phpfox::isUser() && Phpfox::getUserId() != $aUser.user_id && Phpfox::isModule('friend')}
{if !$aUser.is_friend && !$aUser.is_friend_request}
    {if Phpfox::getUserParam('friend.can_add_friends')}
    <div class="dropdown ynmember_add_friend_option ynmember_link_friendship_{$aUser.user_id} {if $aUser.is_friend} friended{elseif $aUser.is_friend_request == 2} waiting{elseif $aUser.is_friend_request == 3} confirm{/if}">
        <i class="fa fa-user-plus dropdown-toggle" aria-hidden="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="ynmember_dropdown_add_friend">
            <li>
                <a href="javascript:void(0)" onclick="return $Core.addAsFriend('{$aUser.user_id}')">
                    <i class="fa fa-user-plus" aria-hidden="true"></i>{_p('Add Friend')}
                </a>
            </li>
        </ul>
    </div>
    {/if}
{else}
<div class="dropdown ynmember_add_friend_option ynmember_link_friendship_{$aUser.user_id} {if $aUser.is_friend} friended{elseif $aUser.is_friend_request == 2} waiting{elseif $aUser.is_friend_request == 3} confirm{/if}">
    <i class="fa fa-user-plus dropdown-toggle" aria-hidden="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="ynmember_dropdown_add_friend">
        {if $aUser.is_friend}
            {if (Phpfox::isModule('suggestion'))}
                <li>
                    <a href="javascript:void(0)" onclick="ynmember.show_suggestfriend(this)" rel="{$aUser.user_id}">
                        <i class="fa fa-user" aria-hidden="true"></i>{_p('Suggest Friend')}
                    </a>
                </li>
            {/if}
            <li class="cancel">
                <a href="javascript:void(0)" class="del_request" onclick="ynmember.updateFriendship('{$aUser.user_id}', '0', 'delete');">
                    <i class="fa fa-user-times" aria-hidden="true"></i>{_p('Unfriend')}
                </a>
            </li>
        {elseif $aUser.is_friend_request == 2}
            <li class="cancel">
                <a href="javascript:void(0)" class="del_request" onclick="ynmember.updateFriendship('{$aUser.user_id}', '{$aUser.is_friend_request_id}', 'cancel');">
                    <i class="fa fa-times" aria-hidden="true"></i>{_p('Cancel Request')}
                </a>
            </li>
        {elseif $aUser.is_friend_request == 3}
            <li>
                <a href="javascript:void(0)" onclick="ynmember.updateFriendship('{$aUser.user_id}', '{$aUser.is_friend_request_id}', 'confirm');">
                    <i class="fa fa-user-plus" aria-hidden="true"></i>{_p('Confirm Request')}
                </a>
            </li>
<!--            <li>-->
<!--                <a href="javascript:void(0)" class="del_request" onclick="ynmember.updateFriendship('{$aUser.user_id}', '{$aUser.is_friend_request_id}', 'deny');">-->
<!--                    <i class="fa fa-times" aria-hidden="true"></i>{_p('Delete Request')}-->
<!--                </a>-->
<!--            </li>-->
        {/if}
    </ul>
</div>
{/if}
{/if}