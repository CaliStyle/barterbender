<?php
if (Phpfox::isModule('suggestion') && Phpfox::isUser())
{
    if (Phpfox::getService('suggestion')->isSupportModule('photo') && Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
    {
        $_SESSION['suggestion']['ajax'] = true;
		Phpfox::setCookie("suggestion_ajax", '1');
		$ynsuggestion_ajax = Phpfox::getCookie('suggestion_ajax');
    }
}
?>
