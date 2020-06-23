<?php 

if (Phpfox::isModule('resume') && isset($iUserid) && $iUserid > 0)
{
    Phpfox::getService('resume.basic.process')->synchronisebyUserId($iUserid);
}

?>