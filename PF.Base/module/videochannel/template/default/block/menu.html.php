<?php 
defined('PHPFOX') or exit('NO DICE!'); 
?>
{if ($aVideo.user_id == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_edit_own_video')) || (Phpfox::getUserParam('videochannel.can_edit_other_video') && $aVideo.user_id != Phpfox::getUserId())}
    <li>
        <a href="#" onclick="tb_show('{phrase var='videochannel.edit_this_video'}', $.ajaxBox('videochannel.edit', 'video_id={$aVideo.video_id}')); return false;">
            {phrase var='videochannel.edit'}
        </a>
    </li>
{/if}

{if Phpfox::getUserParam('videochannel.can_feature_videos_') && !defined('PHPFOX_IS_GROUP_VIEW')}
<li id="js_feature_{$aVideo.video_id}" class="" style="{if $aVideo.is_featured == 1}display:none;{/if}">
    <a href="#" onclick="$('#js_feature_{$aVideo.video_id}').hide(); $('#js_unfeature_{$aVideo.video_id}').show(); $.ajaxCall('videochannel.feature2','video_id={$aVideo.video_id}&type=1'); return false;">
        {phrase var='videochannel.feature'}
    </a>
</li>

<li id="js_unfeature_{$aVideo.video_id}" class="" style="{if $aVideo.is_featured != 1}display:none;{/if}">
    <a href="#" onclick="$('#js_feature_{$aVideo.video_id}').show(); $('#js_unfeature_{$aVideo.video_id}').hide(); $.ajaxCall('videochannel.feature2','video_id={$aVideo.video_id}&type=0'); return false;">
        {phrase var='videochannel.un_feature'}
    </a>
</li>
{/if}


{if ($aVideo.user_id == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_delete_own_video')) || (Phpfox::getUserParam('videochannel.can_delete_other_video') && $aVideo.user_id != Phpfox::getUserId())}
    <li class="item-delete">
        <a href="{url link='videochannel' delete=$aVideo.video_id}" class="sJsConfirm">
            {phrase var='videochannel.delete'}
        </a>
    </li>
{/if}

{plugin call='videochannel.template_block_menu'}