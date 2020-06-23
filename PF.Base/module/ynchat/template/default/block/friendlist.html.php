{if count($buddyList) > 0}
    <ul>
        {foreach from=$buddyList item=aFriend}
            <li id="ynchat-friend-{$aFriend.user_id}"
                data-user-id="{$aFriend.user_id}"
                data-full-name="{$aFriend.full_name}"
                data-user-name="{$aFriend.user_name}"
            >
                <div class="avatar"><img src="{$aFriend.avatar}" /></div>
                <div class="name">{$aFriend.full_name}</div>
                <div class="status {$aFriend.status}"></div>
            </li>
        {/foreach}
    </ul>
{else}
    {phrase var='ynchat.nothing_friend_s_found'}
{/if}