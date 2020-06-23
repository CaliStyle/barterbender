<?php
    if (isset($_SESSION['signup_plugin']) && $_SESSION['signup_plugin'] == 1)
    {
        unset($_SESSION['signup_plugin']);
        $mess = _p('you_have_already_been_a_friend_of_people');
        phpfox::getLib('url')->send('contactimporter.invitionknow', null, $mess);
    }
?>
