{if (isset($bShowModeration) && $bShowModeration) || (Phpfox::getUserId() == $aItem.user_id && user('ynuv_can_delete_own_video'))
|| (Phpfox::getUserId() == $aItem.user_id && user('ynuv_can_edit_own_video'))
|| user('ynuv_can_edit_video_of_other_user')
|| user('ynuv_can_delete_video_of_other_user')
|| ( isset($sView) && ($sView == 'history'))}
    {template file='ultimatevideo.block.link_video_edit'}
{/if}
{if Phpfox::getUserId() && isset($bIsPagesView) && !$bIsPagesView && $aItem.is_approved && $aItem.status}
    {template file='ultimatevideo.block.link_video_viewer'}
{/if}

