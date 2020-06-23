<?php

if (Phpfox::isModule('suggestion') && Phpfox::isUser()){
    
    if (Phpfox::getService('suggestion')->isSupportModule('photo') && Phpfox::getUserParam('suggestion.enable_friend_suggestion')){        
        Phpfox::getService('suggestion.cache')->set('photo.added',1);
    }

}/*end check module*/
?>