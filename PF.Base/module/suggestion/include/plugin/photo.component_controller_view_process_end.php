<?php

if (Phpfox::isModule('suggestion') && Phpfox::isUser()){
    
    if (Phpfox::getService('suggestion')->isSupportModule('photo') && Phpfox::getUserParam('suggestion.enable_friend_suggestion')){

        $sTitle = $aPhoto['title'];
        $iItemId = $aPhoto['photo_id'];
        $_SESSION['suggestion']['photo']['title'] = $sTitle;
        $_SESSION['suggestion']['photo']['photo_id'] = $iItemId;
    }

}/*end check module*/
?>