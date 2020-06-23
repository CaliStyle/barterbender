<?php

if (isset($iQuizId) && Phpfox::isModule('suggestion') && Phpfox::isUser()){
    $_SESSION['suggestion']['quiz']['quiz_id'] = $iQuizId;    
}
?>
