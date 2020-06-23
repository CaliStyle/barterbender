<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if Phpfox::getParam('friend.load_friends_online_ajax') && !PHPFOX_IS_AJAX}
    <script type="text/javascript">
        $Behavior.setTimeoutFriends = function(){l}
            setTimeout('$.ajaxCall(\'friend.getOnlineFriends\', \'\', \'GET\')', 1000);
            $Behavior.setTimeoutFriends = function(){l}{r}
        {r}
    </script>
{else}
    {if count($aFriends)}
        <ul class="user_rows_mini core-friend-block friend-online-block">
            {foreach from=$aFriends name=friend item=aFriend}
                <li class="user_rows">
                    <div class="user_rows_image" data-toggle="tooltip" data-placement="bottom" title="{$aFriend.full_name}">
                        {if Phpfox::isModule('mail') && User_Service_Privacy_Privacy::instance()->hasAccess('' . $aFriend.user_id . '', 'mail.send_message')}
                            <a href="#" onclick="$Core.composeMessage({left_curly}user_id: {$aFriend.user_id}{right_curly}); return false;">
                                {img user=$aFriend suffix='_50_square' width=32 height=32 class="img-responsive" title=$aFriend.full_name no_link=true}
                            </a>
                        {else}
                            <a href="{url link=$aFriend.user_name}" class="ajax_link">
                                {if ($redis_enabled)}
                                    {$aFriend.photo_link}
                                {else}
                                    {img user=$aFriend suffix='_50_square' width=32 height=32 class="img-responsive" title=$aFriend.full_name no_link=true}
                                {/if}
                            </a>
                        {/if}
                    </div>
                    <div class="user_rows_name" style="display: none;">
                        {if Phpfox::isModule('mail') && User_Service_Privacy_Privacy::instance()->hasAccess('' . $aFriend.user_id . '', 'mail.send_message')}
                            <a href="#" onclick="$Core.composeMessage({left_curly}user_id: {$aFriend.user_id}{right_curly}); return false;">
                                {$aFriend.full_name}
                            </a>
                        {else}
                            <a class="ajax_link" href="{url link=$aFriend.user_name}">{$aFriend.full_name}</a>
                        {/if}
                    </div>
                </li>
            {/foreach}
            {if $iRemainCount > 0}
                <li class="user_rows view-friend-more">
                    <a href="{url link='profile.friend' view='online'}">+{$iRemainCount}</a>
                </li>
            {/if}
        </ul>
    {else}
        <div class="extra_info">
            {_p var='no_friends_online'}
        </div>
    {/if}
{/if}