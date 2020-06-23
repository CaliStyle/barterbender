<div class="ynmember_block_item_member_day">
    <!-- photo -->
    <div class="ynmember_avatar">
        {if $aUser.user_image}
        <a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" class="ynmember_avatar_thumb" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
        {else}
        {img user=$aUser suffix='_200_square' return_url=true}
        {/if}
    </div>
    <div class="ynmember_info">
        {$aUser|user}
        {template file='ynmember.block.entry_info_icon'}
    </div>
    <!-- list -->
    {if $aUser.total_mutual_friends}
    {if $aUser.total_mutual_friends > 1}
    <a href="javascript:void(0)" onclick="$Core.box('friend.getMutualFriends', 300, 'user_id={$aUser.user_id}'); return false;" title="{_p var='Show mutual friends'}" class="ynmember_title" >
        {if $aUser.total_mutual_friends == 1}{_p var='1_mutual_friend'}{else}{_p var='total_mutual_friends' total=$aUser.total_mutual_friends}{/if}
    </a>
    {/if}
    <ul class="ynmember_mutual_list clearfix">
        {foreach from=$aUser.mutual_friends name=ynmember_total_mutual_friends item=aMutualFriend}
        <li class="{if ($aUser.total_mutual_friends == 1)} one_member{/if}">
            <div class="ynmember_mutual_list_inner">
                    {if $aMutualFriend.user_image}
                <a href="{url link=$aMutualFriend.user_name}" title="{$aMutualFriend.full_name}" class="ynmember_avatar_thumb" style="background-image: url('{img user=$aMutualFriend suffix='_200_square' return_url=true}');"></a>
                    {else}
                    {img user=$aMutualFriend suffix='_200_square' return_url=true}
                    {/if}
                </div>
            {if $aUser.total_mutual_friends == 1}
                <a href="{url link=$aUser.mutual_friends[0].user_name}" class="max-width">{$aUser.mutual_friends[0].full_name}</a>
                <span class="overflow">&nbsp;{_p('is mutual friend')}</span>
            {/if}
            </li>
        {/foreach}
        <li class="ynmember_more_list">
            {if $aUser.total_mutual_friends > 6}
                <div class="ynmember_mutual_list_inner more_number fw-bold"><a href="{url link=''$aUser.user_name'.friend.mutual'}">+{$aUser.total_mutual_friends|ynmember_subtract:'5'}</a></div>
            {/if}
        </li>
    </ul>
    {/if}
    {if Phpfox::isUser()}
        {if Phpfox::isModule('friend') && Phpfox::getUserId() != $aUser.user_id}
            {template file='ynmember.block.entry_link_friendship_new'}
        {/if}
    {/if}
</div>