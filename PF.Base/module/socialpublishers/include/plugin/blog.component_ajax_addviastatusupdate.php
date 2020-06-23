<?php    
if(phpfox::isModule('socialpublishers') && !Phpfox::getUserParam('blog.approve_blogs'))
{
    $sUrl = Phpfox::permalink('blog', $iBlogId, $aVals['title']);
    $sType = 'blog';
    $iUserId = Phpfox::getUserId();
    $sMessage = $aVals['text'];
    $aVals['url'] = $sUrl;
    $aVals['content'] = $sMessage;
    phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVals);
}
?>
