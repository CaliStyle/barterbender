<?php

if (Phpfox::isModule('suggestion') && Phpfox::isUser())
{
    if (Phpfox::getService('suggestion')->isSupportModule('music') && Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
    {
        if (isset($aVals['iframe']))
        {
            $aPrivateData = Phpfox::getService('suggestion')->getPrivateData(Phpfox::getUserId());

            if (is_array($aPrivateData) && count($aPrivateData) > 0)
            {
                $aRet = unserialize(base64_decode($aPrivateData['message']));
                if (isset($aRet['item_id']))
                {
                    $_aFeed['item_id'] = $aRet['item_id'];
                    $_aFeed['sModule'] = $aRet['sModule'];
                    $sLinkCallback = urlencode($aRet['sLinkCallback']);
                    $sTitle = base64_encode(urlencode($aRet['title']));
                    $sPrefix = $aRet['prefix'];
                    $bShow = 1;

                    echo "<script type=\"text/javascript\">";
                    echo 'window.parent.suggestion_and_recommendation_tb_show("...", window.parent.$.ajaxBox(\'suggestion.friends\',\'iFriendId=' . $_aFeed['item_id'] . '&sSuggestionType=suggestion&sModule=suggestion_' . $_aFeed['sModule'] . '&sLinkCallback=' . $sLinkCallback . '&sTitle=' . $sTitle . '&sPrefix=' . $sPrefix . '&sExpectUserId=\'));';
                    echo "</script>";
                }
            }
            
        }
    }
}
?>
