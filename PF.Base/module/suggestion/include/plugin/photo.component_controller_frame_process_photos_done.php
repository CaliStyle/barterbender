<?php
if (Phpfox::isModule('suggestion') && Phpfox::isUser())
{
    if (Phpfox::getService('suggestion')->isSupportModule('photo') && Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
    {
        $_SESSION['suggestion']['photo']['method'] = 'simple';
    }
}
?>
