{if count($aUser.mutual_friends) == 1}
<div class="ynmember_one_mutual_friend ynmember_list_item">
    <a href="{url link=$aUser.mutual_friends[0].user_name}" class="max-width">{$aUser.mutual_friends[0].full_name}</a>
    <span class="overflow">{_p(' is mutual friend')}</span>
</div>
{elseif count($aUser.mutual_friends) > 1}
<div class="ynmember_one_mutual_friend ynmember_list_item">
    <a href="javascript:void(0)" onclick="$Core.box('friend.getMutualFriends', 300, 'user_id={$aUser.user_id}'); return false;" class="ynmember_many_mutual_friend">{_p var='total_mutual_friends' total=$aUser.total_mutual_friends}</a>
</div>
{/if}