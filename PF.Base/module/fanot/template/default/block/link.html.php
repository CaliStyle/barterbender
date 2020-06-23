<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if count($aNotifications)}
{foreach from=$aNotifications name=notification item=aNotification}
<div class="fanot_item" id="fanot_item_{$aNotification.notification_id}" fid="{$aNotification.notification_id}">
    <div class="fanot_content">
        <div class="fanot fanot_top fanot_bottom fanot_selected" style="opacity: 1; ">
            <span class="fanot_x" onclick=" $Core.fanot.hideFanot({$aNotification.notification_id});"><i class="fa fa-times"></i></span>
            <div class="fanot_icon">
                {if Phpfox::getParam('fanot.show_photo_in_notification')}
                    <i class="beeper_icon">
                        {img user=$aNotification max_width='30' max_height='30' suffix='_50_square' class="v_middle"}
                    </i>
                {elseif !empty($aNotification.icon) && Phpfox::getParam('fanot.show_photo_in_notification')}
                    <i class="beeper_icon">
                        <img src="{$aNotification.icon}" width="30" height="30">
                    </i>
                {else}
                    <i class="beeper_icon default_icon"></i>
                {/if}
            </div>
            <a href="{$aNotification.link}" onclick="return $Core.fanot.updateSeen({$aNotification.notification_id},this,1);">
            <div class="fanot_title">
                {$aNotification.message|convert}
            </div>
            </a>
        </div>
    </div>
</div>
{/foreach}
{/if}
{if Phpfox::getParam('fanot.enable_advanced_feed_notification_for_friend_request') && count($aFriendRequests)}
{foreach from=$aFriendRequests name=friends item=aFriend}
<div class="fanot_item" id="fanot_item_{$aFriend.request_id}" fid="{$aFriend.request_id}">
    <div class="fanot_content" href="{url link='friend.accept'}" onclick="return  $Core.fanot.updateSeen({$aFriend.request_id},this,2);" style="cursor: pointer">
        <div class="fanot fanot_top fanot_bottom fanot_selected" style="opacity: 1; ">                  
            <span class="fanot_x" onclick="$Core.fanot.hideFanot({$aFriend.request_id});"><i class="fa fa-times"></i></span>
            <div class="fanot_icon">			   
                <i class="beeper_icon">
                    {img user=$aFriend max_width='30' max_height='30' suffix='_50_square' class="v_middle"}
                </i>
            </div>
            <div class="fanot_title">
                {$aFriend.message|convert}
            </div>
        </div>
    </div>
</div>
{/foreach}
{/if}

{if $bIsActiveSoundAlert}
<div id="fanot_sound"></div>
{/if}
